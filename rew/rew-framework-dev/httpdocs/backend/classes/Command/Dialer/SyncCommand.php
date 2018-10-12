<?php

namespace REW\Backend\Command\Dialer;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use \Partner_Espresso;
use \Container;
use \Exception;

/**
 * SyncCommand
 * @package REW\Backend\Command\Dialer
 */
class SyncCommand extends Command
{

    /**
     * Configure "sync" command
     * @return void
     */
    protected function configure()
    {
        $this->setName('dialer:sync')
            ->setDescription('Synchronise REW Dialer accounts & phone lines.')
            ->addArgument('domain', InputArgument::REQUIRED, 'The website\'s domain name (eg: rewdemo.com)')
            ->setHelp('This command uses the rewdialer.com API to enable/disable accounts & phone lines.')
        ;
    }

    /**
     * Execute "sync" command
     * @param InputInterface $input
     * @param OutputInterface $output
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {

        // Require website domain argument
        $domain = $input->getArgument('domain');

        // Write to console
        $output->writeln([
            'Synchronizing REW Dialer Lines',
            '==============================',
            sprintf('<info>Website: %s</info>', $domain)
        ]);

        // Load REW Dialer API client
        $container = Container::getInstance();
        $espresso = $container->get(Partner_Espresso::class);

        try {
            // Synchronize site's REW Dialer accounts
            $espresso->synchronizeAccounts($domain, 1);

            // Throw exception if any errors
            $error = $espresso->getLastError();
            if (!empty($error)) {
                throw new Exception($error);
            }

        // Unexpected error
        } catch (Exception $e) {
            throw new Exception(sprintf(
                'REW Dialer failed to synchronize:\n %s',
                $e->getMessage()
            ));
        }
    }
}
