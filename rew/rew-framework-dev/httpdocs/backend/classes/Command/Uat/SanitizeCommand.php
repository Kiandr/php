<?php

namespace REW\Backend\Command\Uat;

use REW\Core\Interfaces\Factories\IDXFactoryInterface;
use REW\Core\Interfaces\IDXInterface;
use \Symfony\Component\Console\Input\InputOption;
use \Symfony\Component\Console\Input\InputArgument;
use \Symfony\Component\Console\Input\InputInterface;
use \Symfony\Component\Console\Output\OutputInterface;
use \Symfony\Component\Console\Style\SymfonyStyle;
use \Faker\Factory as FakerFactory;
use \Container;
use \Exception;
use \PDO;

/**
 * SanitizeCommand
 * @package REW\Backend\Command\Uat
 */
class SanitizeCommand extends AbstractUatCommand
{

    /**
     * @var \Faker\Generator
     */
    protected $faker;

    /**
     * @var array
     */
    protected $usernames;

    /**
     * @var array
     */
    protected $emails = [];


    /**
     * @var array
     */
    protected $listings = [];

    /**
     * @var string
     */
    const EMAIL_DOMAIN = '@rewdemo.com';

    /**
     * @var string[]
     */
    const IDX_FEEDS = ['uat', 'uat2'];

    /**
     * @var string
     */
    const PASSWORD = 'rew';

    /**
     * @var string
     */
    const IMAGE = 'placeholder.png';

    /**
     * @var string
     */
    const PRIMARY_KEY = 'primary-key';

    /**
     * @var string
     */
    const QUERY_SELECT = 'query-select';

    /**
     * @var string
     */
    const QUERY_WHERE = 'query-where';

    /**
     * @var string
     */
    const FORCE_EMPTY = 'force-empty';

    /**
     * @var string
     */
    const RULE_IMAGE = 'image-file';

    /**
     * @var string
     */
    const RULE_FNAME = 'random-fname';

    /**
     * @var string
     */
    const RULE_LNAME = 'random-lname';

    /**
     * @var string
     */
    const RULE_EMAIL = 'random-email';

    /**
     * @var string
     */
    const RULE_PHONE = 'random-phone';

    /**
     * @var string
     */
    const RULE_PASSWORD = 'set-password';

    /**
     * @var string
     */
    const RULE_USERNAME = 'random-username';

    /**
     * @var string
     */
    const RULE_ADDRESS = 'random-address';

    /**
     * @var string
     */
    const RULE_STATE = 'random-state';

    /**
     * @var string
     */
    const RULE_CITY = 'random-city';

    /**
     * @var string
     */
    const RULE_ZIP = 'random-zip';

    /**
     * @var string
     */
    const RULE_URL = 'random-url';

    /**
     * @var array
     */
    const TABLE_RULES = [
        'featured_offices' => [
            self::PRIMARY_KEY => 'id',
            self::QUERY_WHERE => ["`email` NOT LIKE '%@rewdemo.com'"],
            self::RULE_EMAIL => ['email'],
            self::RULE_IMAGE => ['image'],
            self::RULE_PHONE => ['phone', 'fax'],
            self::RULE_ADDRESS => ['address'],
            self::RULE_STATE => ['state'],
            self::RULE_CITY => ['city'],
            self::RULE_ZIP => ['zip']
        ],
        'auth' => [
            self::PRIMARY_KEY => 'id',
            self::QUERY_SELECT => ['type'],
            self::QUERY_WHERE => ["`username` != 'admin'", "`username` NOT REGEXP 'agent\\\\d'"],
            self::RULE_USERNAME => ['username'],
            self::RULE_PASSWORD => ['password']
        ],
        'agents' => [
            self::PRIMARY_KEY => 'id',
            self::QUERY_WHERE => ["`email` NOT LIKE '%@rewdemo.com'"],
            self::RULE_FNAME => ['first_name'],
            self::RULE_LNAME => ['last_name'],
            self::RULE_EMAIL => ['email', 'ar_cc_email', 'ar_bcc_email'],
            self::RULE_URL => ['website'],
            self::RULE_IMAGE => ['image'],
            self::RULE_PHONE => ['cell_phone', 'office_phone', 'home_phone', 'fax'],
            self::FORCE_EMPTY => [
                'sms_email', 'network_google', 'network_microsoft', 'notifications', 'partners',
                'remax_launchpad_username', 'remax_launchpad_url', 'showing_suite_email',
                //'blog_picture', 'blog_profile', 'blog_signature', 'signature',
                //'agent_id', 'cms_idxs', 'cms_link', 'remarks'
            ]
        ],
        'associates' => [
            self::PRIMARY_KEY => 'id',
            self::QUERY_WHERE => ["`email` NOT LIKE '%@rewdemo.com'"],
            self::RULE_FNAME => ['first_name'],
            self::RULE_LNAME => ['last_name'],
            self::RULE_EMAIL => ['email'],
            self::RULE_PHONE => ['cell_phone', 'office_phone', 'home_phone', 'fax'],
            self::FORCE_EMPTY => ['signature'],
            self::RULE_ADDRESS => ['address'],
            self::RULE_STATE => ['state'],
            self::RULE_CITY => ['city'],
            self::RULE_ZIP => ['zip']
        ],
        'lenders' => [
            self::PRIMARY_KEY=> 'id',
            self::QUERY_WHERE => ["`email` NOT LIKE '%@rewdemo.com'"],
            self::RULE_FNAME => ['first_name'],
            self::RULE_LNAME => ['last_name'],
            self::RULE_EMAIL => ['email'],
            self::RULE_PHONE => ['cell_phone', 'office_phone', 'home_phone', 'fax'],
            self::RULE_ADDRESS => ['address'],
            self::RULE_STATE => ['state'],
            self::RULE_CITY => ['city'],
            self::RULE_ZIP => ['zip']
        ],
        'users' => [
            self::PRIMARY_KEY => 'id',
            self::QUERY_WHERE => ["`email` NOT LIKE '%@rewdemo.com'"],
            self::RULE_PASSWORD => ['password'],
            self::RULE_FNAME => ['first_name'],
            self::RULE_LNAME => ['last_name'],
            self::RULE_EMAIL => ['email', 'email_alt'],
            self::RULE_PHONE => ['phone', 'phone_cell', 'phone_work', 'phone_fax'],
            self::RULE_ADDRESS => ['address1', 'address2', 'address3'],
            self::RULE_STATE => ['state'],
            self::RULE_CITY => ['city'],
            self::RULE_ZIP => ['zip'],
            self::FORCE_EMPTY => [
                'happygrasshopper_data_id',
                'oauth_facebook', 'oauth_google', 'oauth_microsoft', 'oauth_linkedin', 'oauth_twitter', 'oauth_yahoo',
                'network_facebook', 'network_google', 'network_microsoft', 'network_linkedin', 'network_twitter', 'network_yahoo',
                //'comments', 'remarks', 'notes',
            ]
        ],
        // TODO...
        //users_forms
        //users_searches
        //users_viewed_searches
        //users_notes
        //users_pages
        //blog_comments
    ];

    /**
     * @return void
     */
    protected function configure()
    {
        $this->setName('uat:sanitize')
            ->setDescription('Sanitize database records to remove private data.')
            ->setHelp(implode(PHP_EOL, [
                'Applies set of defined rules against DB tables to replace confidential information.',
                'Also includes replacing MLS listing/search records with a controlled IDX feed.'
            ]))
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @throws Exception
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        // Site database to import to
        $db = $this->getDbConnection();

        try {
            // Use a db transaction
            $db->query('START TRANSACTION;');

            // Require faker generator
            $this->faker = FakerFactory::create();

            // Require existing usernames
            $this->usernames = array_fill_keys($this->getExistingUsernames($db), 1);

            // Sanitize tables based on rules
            foreach (self::TABLE_RULES as $tableName => $ruleset) {
                // Write output for debugging
                $io->newLine();
                $io->title(sprintf(' ===  <info>Sanitizing `%s`</info>', $tableName));

                // Debug
                $io->table(
                    ['Ruleset', 'Definition'],
                    array_map(function ($ruleset, $value) {
                        return [$ruleset, is_string($value) ? $value : implode(PHP_EOL, $value)];
                    }, array_keys($ruleset), $ruleset)
                );

                // Require primary ID to be defined
                $primaryKey = $ruleset[self::PRIMARY_KEY];
                if (empty($primaryKey)) {
                    throw new Exception(sprintf(
                        'Missing "%s" definition.',
                        self::PRIMARY_KEY
                    ));
                }

                // Fix columns
                $columns = [];
                foreach ($ruleset as $ruleType => $cols) {
                    if ($ruleType === self::QUERY_WHERE) {
                        continue;
                    }
                    if ($ruleType === self::FORCE_EMPTY) {
                        continue;
                    }
                    $cols = is_string($cols) ? [$cols] : $cols;
                    $columns = array_merge($columns, $cols);
                }

                // Limit to subset of data
                $queryWhere = $ruleset[self::QUERY_WHERE] ?: [];
                $queryWhere = $queryWhere ? sprintf(' WHERE %s', implode(' AND ', $queryWhere)) : '';

                // Fetch data from source database
                $queryCols = implode('`, `', $columns);
                $queryString = sprintf("SELECT `%s` FROM `%s`%s;", $queryCols, $tableName, $queryWhere);
                $query = $db->query($queryString);

                // Check if table has records
                $rowCount = $query->rowCount();
                if (empty($rowCount)) {
                    $output->writeLn('Records: <comment>[No Matches Found]</comment>');
                    continue;
                }

                // Perform query to set empty values
                if (isset($ruleset[self::FORCE_EMPTY])) {
                    $emptyColumns = $ruleset[self::FORCE_EMPTY];
                    $queryUpdate = array_map(function ($colName) {
                        return sprintf("`%s` = ''", $colName);
                    }, $emptyColumns);
                    $queryString = sprintf("UPDATE `%s` SET %s;", $tableName, implode(', ', $queryUpdate));
                    $db->query($queryString);
                }

                // Prepare UPDATE query
                $queryCols = sprintf('`%s` = ?', implode('` = ?, `', $columns));
                $queryString = sprintf("UPDATE `%s` SET %s WHERE `%s` = ?;", $tableName, $queryCols, $primaryKey);
                $updateQuery = $db->prepare($queryString);

                // Start fixing
                $io->progressStart($rowCount);
                while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                    // Sanitize table record based on defined result
                    $saneRow = $this->sanitize($tableName, $ruleset, $row);
                    $primaryKeyValue = $saneRow[$primaryKey];
                    $queryParams = array_values($saneRow);
                    $queryParams[] = $primaryKeyValue;

                    // Update database record
                    $updateQuery->execute($queryParams);
                    ;
                    $io->progressAdvance();
                }
                $io->newLine();
            }

            // UAT saved listings
            $this->sanitizeListings($io, $db);

            // Cleanup history data
            $this->cleanupHistory($io, $db);

            // Commit DB changes
            $db->query('COMMIT;');

        // Database error occurred
        } catch (\PDOException $e) {
            $db->query('ROLLBACK;');
            throw $e;
        }
    }

    /**
     * @param SymfonyStyle $io
     * @param PDO $db
     * @return void
     */
    protected function sanitizeListings(SymfonyStyle $io, PDO $db)
    {
        $listingFeeds = ['uat', 'uat2'];
        $listingTables = ['users_listings', 'users_listings_dismissed', 'users_viewed_listings'];
        $selectQueryString = "SELECT `id`, `mls_number` FROM `%s` WHERE `idx` NOT IN (?, ?) ORDER BY `mls_number`;";
        foreach ($listingTables as $tableName) {
            $io->newLine();
            $io->title(sprintf(' ===  <info>Sanitizing `%s`</info>', $tableName));
            $query = $db->prepare(sprintf($selectQueryString, $tableName));
            if ($query->execute($listingFeeds)) {
                $io->progressStart($query->rowCount());
                while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                    $io->progressAdvance();
                    // Get random fake listing record from UAT feed
                    $listing = $this->getFakeListingData($row['mls_number']);
                    // Update listing record
                    $queryParams = array_values($listing);
                    $queryParams[] = $row['id'];
                    try {
                        $updateQueryString = sprintf(
                            "UPDATE `%s` SET %s WHERE `id` = ?;",
                            $tableName,
                            implode(', ', array_map(function ($column) {
                                return sprintf("`%s` = ?", $column);
                            }, array_keys($listing)))
                        );
                        $updateQuery = $db->prepare($updateQueryString);
                        $updateQuery->execute($queryParams);
                    } catch (\PDOException $e) {
                        $io->error($updateQueryString);
                        $io->note(print_r($queryParams, 1));
                        throw $e;
                    }
                }
            }
            $io->newLine();
        }
    }

    /**
     * @param SymfonyStyle $io
     * @param PDO $db
     * @return void
     */
    protected function cleanupHistory(SymfonyStyle $io, PDO $db)
    {
        $io->newLine();
        $io->title('Cleaning history event data');

        // Sanitize history event records by cleaning up saved event data
        $updateQuery = $db->prepare("UPDATE `history_data` SET `data` = ? WHERE `event` = ?;");
        $queryString = "SELECT `he`.`id`, `he`.`type`, `he`.`subtype`, `hd`.`data`"
            . " FROM `history_events` `he` LEFT JOIN `history_data` `hd` ON `he`.`id` = `hd`.`event`"
            . " ORDER BY `he`.`type` ASC, `he`.`subtype` ASC;";
        if ($query = $db->query($queryString)) {
            $io->progressStart($query->rowCount());
            while ($event = $query->fetch(PDO::FETCH_ASSOC)) {
                $io->progressAdvance();

                // Remove history data
                $data = unserialize($event['data']);
                if (empty($data)) {
                    continue;
                }
                unset($event['data']);

                // Sanitize event row
                $clean = $this->sanitizeEventData($event['type'], $event['subtype'], $data);
                $updateQuery->execute([serialize($clean), $event['id']]);
            }
            $io->newLine();
        }

        // Delete orphaned normalized history data records
        $db->query("DELETE FROM `history_data_normal` WHERE `id` NOT IN (
            SELECT `norm_id` FROM `history_data` WHERE `norm_id` IS NOT NULL
        );");

        // Remove message body from email history events
        $db->query("UPDATE `history_data_normal` SET `data` = '[REMOVED]' WHERE `id` IN (
            SELECT DISTINCT `hd`.`norm_id` FROM `history_data` `hd`
            LEFT JOIN `history_events` `he` ON `hd`.`event` = `he`.`id`
            WHERE `he`.`type` = 'Email' AND `hd`.`norm_id` IS NOT NULL
        );");

        // TODO: IDX listing history events
    }

    /**
     * @param string $tableName
     * @param array $ruleset
     * @param array $record
     * @return array
     */
    protected function sanitize($tableName, array $ruleset, array $record)
    {
        $clean = $record;
        foreach ($ruleset as $rule => $columns) {
            if (in_array($rule, [
                self::PRIMARY_KEY,
                self::QUERY_SELECT,
                self::QUERY_WHERE,
                self::FORCE_EMPTY
            ])) {
                continue;
            }
            foreach ($columns as $column) {
                $value = $clean[$column];
                $clean[$column] = $this->sanitizeData(
                    $tableName,
                    $rule,
                    $value,
                    $clean
                );
            }
        }
        return $clean;
    }

    /**
     * @param string $tableName
     * @param string $rule
     * @param mixed $value
     * @param array $data
     * @throws Exception
     * @return mixed
     */
    protected function sanitizeData($tableName, $rule, $value, array $data)
    {
        switch ($rule) {
            // Primary key as-is
            case self::PRIMARY_KEY:
                return (int) $value;

            // Placeholder image
            case self::RULE_IMAGE:
                if (empty($value)) {
                    return $value;
                }
                return self::IMAGE;

            // Username generator
            case self::RULE_USERNAME;
                return $this->getUsername($data);

            // Password generator
            case self::RULE_PASSWORD:
                return password_hash(self::PASSWORD, PASSWORD_DEFAULT, ['cost' => 10]);

            // First name fixer
            case self::RULE_FNAME:
                return $this->faker->firstName;

            // Last name fixer
            case self::RULE_LNAME:
                return $this->faker->lastName;

            // Generate fake email
            case self::RULE_EMAIL:
                if (empty($value)) {
                    return $value;
                }
                return $this->getEmailAddress($tableName);

            // Fake phone numbers
            case self::RULE_PHONE:
                if (empty($value)) {
                    return $value;
                }
                return $this->faker->phoneNumber;

            // Fake street address
            case self::RULE_ADDRESS:
                if (empty($value)) {
                    return $value;
                }
                return $this->faker->streetAddress;

            // Fake state name
            case self::RULE_STATE:
                if (empty($value)) {
                    return $value;
                }
                return $this->faker->state;

            // Fake city name
            case self::RULE_CITY:
                if (empty($value)) {
                    return $value;
                }
                return $this->faker->city;

            // Fake zip/postal code
            case self::RULE_ZIP:
                if (empty($value)) {
                    return $value;
                }
                return $this->faker->postcode;

            // Fake website URL
            case self::RULE_URL:
                if (empty($value)) {
                    return $value;
                }
                return $this->faker->url;

            // ERROR
            default:
                throw new Exception(sprintf(
                    'Invalid rule: %s',
                    $rule
                ));
        }
    }

    /**
     * @param string $type
     * @param string $subtype
     * @param mixed $data
     * @return mixed
     */
    protected function sanitizeEventData($type, $subtype, $data)
    {
        // Remove information from lead form submissions
        if ($type === 'Action' && $subtype === 'FormSubmission') {
            // Fake referrer URL
            if (isset($data['page'])) {
                $data['page'] = $this->faker->url;
            }

            // Remove contact information
            if (isset($data['data']['mi0moecs'])) {
                $data['data']['mi0moecs'] = ''; // Email Address
            }
            if (isset($data['data']['onc5khko'])) {
                $data['data']['onc5khko'] = ''; // First name
            }
            if (isset($data['data']['sk5tyelo'])) {
                $data['data']['sk5tyelo'] = ''; // Last name
            }
            if (isset($data['data']['telephone'])) {
                $data['data']['telephone'] = ''; // Phone #
            }
            if (isset($data['data']['phonenumber'])) {
                $data['data']['phonenumber'] = ''; // Phone #
            }
            if (isset($data['data']['comments'])) {
                $data['data']['comments'] = ''; // Comments
            }
            if (isset($data['data']['subject'])) {
                $data['data']['subject'] = ''; // Subject
            }

            // Listing information needed
            if (in_array($data['form'], ['Quick Showing', 'Quick Inquire'])) {
                if (isset($data['data']['inquire']['comments'])) {
                    $data['data']['inquire']['comments'] = '';
                }
                if (isset($data['data']['showing']['comments'])) {
                    $data['data']['showing']['comments'] = '';
                }
                if (isset($data['data']['mls_number'])) {
                    $data['data']['mls_number'] = '';
                }
                if (isset($data['data']['address'])) {
                    $data['data']['address'] = '';
                }
                if (isset($data['data']['price'])) {
                    $data['data']['price'] = '';
                }
            }

        // Email event data
        } elseif ($type === 'Phone') {
            if (isset($data['details'])) {
                $data['details'] = $this->faker->sentence();
            }

        // Email event data
        } elseif ($type === 'Email') {
            // Remove email tags
            if (isset($data['tags'])) {
                $data['tags'] = [];
            }

            // Replace email subject line
            if ($subtype === 'Verification') {
                $data['subject'] = 'Email Verification Code for rewdemo.com';
            }

        // Actions: Login/Logout/Unsubscribe
        } elseif ($type === 'Action' && in_array($subtype, ['Login', 'Logout', 'Unsubscribe'])) {
            if (isset($data['ip'])) {
                $data['ip'] = $this->faker->ipv4;
            }

        // Saved/viewed search events
        } elseif ($type === 'Action' && in_array($subtype, ['SavedSearch', 'ViewedSearch'])) {
            $data['search'] = array_merge($data['search'], $this->getFakeSearchData());

        // Lead note/reminder created events
        } elseif ($type === 'Create' && in_array($subtype, ['LeadNote', 'LeadReminder'])) {
            if (isset($data['details'])) {
                $data['details'] = $this->faker->sentence();
            }

        // Lead update events
        } elseif ($type === 'Update' && $subtype === 'Lead') {
            if (!in_array($data['field'], ['search_maximum_price', 'search_minimum_price', 'phone_cell_status', 'phone_work_status', 'phone_home_status', 'heat'])) {
                if (in_array($data['field'], ['comments', 'remarks', 'notes'])) {
                    if (!empty($data['new'])) {
                        $data['new'] = $this->faker->sentence();
                    }
                    if (!empty($data['old'])) {
                        $data['old'] = $this->faker->sentence();
                    }
                } else if ($data['field'] === 'first_name') {
                    if (!empty($data['new'])) {
                        $data['new'] = $this->faker->firstName;
                    }
                    if (!empty($data['old'])) {
                        $data['old'] = $this->faker->firstName;
                    }
                } else if ($data['field'] === 'last_name') {
                    if (!empty($data['new'])) {
                        $data['new'] = $this->faker->lastName;
                    }
                    if (!empty($data['old'])) {
                        $data['old'] = $this->faker->lastName;
                    }
                } else if ($data['field'] === 'password') {
                    if (!empty($data['new'])) {
                        $data['new'] = self::PASSWORD;
                    }
                    if (!empty($data['old'])) {
                        $data['old'] = self::PASSWORD;
                    }
                } else if ($data['field'] === 'address1') {
                    if (!empty($data['new'])) {
                        $data['new'] = $this->faker->address;
                    }
                    if (!empty($data['old'])) {
                        $data['old'] = $this->faker->address;
                    }
                } else if ($data['field'] === 'state') {
                    if (!empty($data['new'])) {
                        $data['new'] = $this->faker->state;
                    }
                    if (!empty($data['old'])) {
                        $data['old'] = $this->faker->state;
                    }
                } else if ($data['field'] === 'city') {
                    if (!empty($data['new'])) {
                        $data['new'] = $this->faker->city;
                    }
                    if (!empty($data['old'])) {
                        $data['old'] = $this->faker->city;
                    }
                } else if ($data['field'] === 'zip') {
                    if (!empty($data['new'])) {
                        $data['new'] = $this->faker->postcode;
                    }
                    if (!empty($data['old'])) {
                        $data['old'] = $this->faker->postcode;
                    }
                } else if (in_array($data['field'], ['email', 'email_alt'])) {
                    if (!empty($data['new'])) {
                        $data['new'] = $this->faker->userName . self::EMAIL_DOMAIN;
                    }
                    if (!empty($data['old'])) {
                        $data['old'] = $this->faker->userName . self::EMAIL_DOMAIN;
                    }
                } else if (in_array($data['field'], ['phone', 'phone_cell', 'phone_work', 'phone_fax'])) {
                    if (!empty($data['new'])) {
                        $data['new'] = $this->faker->phoneNumber;
                    }
                    if (!empty($data['old'])) {
                        $data['old'] = $this->faker->phoneNumber;
                    }
                } else if (in_array($data['field'], ['search_type', 'search_city', 'search_subdivision'])) {
                    if (!empty($data['new'])) {
                        $data['new'] = $this->faker->sentence();
                    }
                    if (!empty($data['old'])) {
                        $data['old'] = $this->faker->sentence();
                    }
                } else if (in_array($data['field'], ['keywords', 'referer'])) {
                    if (!empty($data['new'])) {
                        $data['new'] = $this->faker->words(rand(1, 5), true);
                    }
                    if (!empty($data['old'])) {
                        $data['old'] = $this->faker->words(rand(1, 5), true);
                    }
                } else {
                    if (!empty($data['new'])) {
                        $data['new'] = $this->faker->words(rand(1, 5), true);
                    }
                    if (!empty($data['old'])) {
                        $data['old'] = $this->faker->words(rand(1, 5), true);
                    }
                }
            }
        }
        return $data;
    }

    /**
     * @param OutputInterface $output
     * @param string $command
     */
    protected function runCommand(OutputInterface $output, $command)
    {
        $process = popen($command, 'r');
        while (!feof($process)) {
            $response = fread($process, 4096);
            $output->writeLn($response);
            @flush();
        }
    }

    /**
     * @param array $data
     * @return string
     */
    protected function getUsername(array $data)
    {
        if ($type = $data['type']) {
            $num = $this->usernames[$type]++;
            $base = strtolower($type);
            $username = $base . $num ?: 1;
            while (isset($this->usernames[$username])) {
                $username = $base . $num++;
            }
            $this->usernames[$username]++;
            return $username;
        }
        return $this->faker->username;
    }

    /**
     * @param string $tableName
     * @return string
     */
    protected function getEmailAddress($tableName)
    {
        $user = preg_replace('/s$/', '', strtolower($tableName));
        if ($tableName === 'featured_offices') {
            $user = 'office';
        }
        if ($tableName === 'users') {
            $user = 'lead';
        }
        if (!isset($this->emails[$tableName])) {
            $this->emails[$tableName] = 1;
        }
        $num = $this->emails[$tableName]++;
        $email = $user . $num . self::EMAIL_DOMAIN;
        return $email;
    }

    /**
     * @param PDO $db
     * @return array
     */
    protected function getExistingUsernames(PDO $db)
    {
        $query = $db->query("SELECT `username` FROM `auth`;");
        return $query->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * @return array
     */
    protected function getFakeSearchData()
    {
        $feed = $this->faker->randomElement(self::IDX_FEEDS);
        $idx = $this->getIdxFactory()->getIdx($feed);
        $title = 'All Properties In All Cities';
        $criteria = ['feed' => $idx->getLink()];
        $query = ['idx' => $idx->getLink()];
        return [
            'title' => $title,
            'idx' => $idx->getName(),
            'table' => $idx->getTable(),
            'url' => http_build_query($query),
            'criteria' => serialize($criteria),
            'refine' => true
        ];
    }

    /**
     * @param string $mlsNumber
     * @return array
     */
    protected function getFakeListingData($mlsNumber)
    {
        if (isset($this->listings[$mlsNumber])) {
            return $this->listings[$mlsNumber];
        }
        $feed = $this->faker->randomElement(self::IDX_FEEDS);
        $factory = $this->getIdxFactory();
        $idx = $factory->getIdx($feed);
        $row = $this->getIdxListing($idx);
        $this->listings[$mlsNumber] = [
            'idx' => $idx->getName(),
            'table' => $idx->getTable(),
            'mls_number' => $row['ListingMLS'],
            'subdivision' => $row['AddressSubdivision'] ?: null,
            'bathrooms' => $row['NumberOfBathrooms'] ?: null,
            'bedrooms' => $row['NumberOfBedrooms'] ?: null,
            'price' => $row['ListingPrice'] ?: null,
            'sqft' => $row['NumberOfSqFt'] ?: null,
            'city' => $row['AddressCity'] ?: null,
            'type' => $row['ListingType'] ?: null
        ];
        return $this->listings[$mlsNumber];
    }

    /**
     * @param IDXInterface $idx
     * @return array
     */
    protected function getIdxListing(IDXInterface $idx)
    {
        $factory = $this->getIdxFactory();
        $idxDb = $factory->getDatabase($idx->getName());
        $queryWhere = implode(', ', array_map(function ($mlsNumber) use ($idxDb) {
            $value = $idxDb->cleanInput($mlsNumber['mls_number']);
            return sprintf("'%s'", $value);
        }, $this->listings));
        $queryCols = $idx->selectColumns(null, $this->getIdxListingColumns());
        $queryString = "SELECT %s FROM `%s` WHERE `%s` NOT IN (%s) ORDER BY RAND() LIMIT 1;";
        $queryString = sprintf($queryString, $queryCols, $idx->getTable(), $idx->field('ListingMLS'), $queryWhere ?: "''");
        return $idxDb->fetchQuery($queryString);
    }

    /**
     * @return array
     */
    protected function getIdxListingColumns()
    {
        return ['ListingMLS', 'AddressSubdivision', 'NumberOfBathrooms', 'NumberOfBedrooms', 'ListingPrice', 'NumberOfSqFt', 'AddressCity', 'ListingType'];
    }

    /**
     * @return IDXFactoryInterface
     */
    protected function getIdxFactory()
    {
        $container = Container::getInstance();
        return $container->get(IDXFactoryInterface::class);
    }
}
