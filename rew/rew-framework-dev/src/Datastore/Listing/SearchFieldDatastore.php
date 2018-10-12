<?php
namespace REW\Datastore\Listing;

use REW\Core\Interfaces\Factories\DBFactoryInterface;
use REW\Core\Interfaces\Factories\IDXFactoryInterface;
use REW\Core\Interfaces\IDXInterface;
use REW\Factory\Idx\Search\FieldFactoryInterface;
use REW\Model\Idx\FeedInfoInterface;

class SearchFieldDatastore implements SearchFieldDatastoreInterface
{
    const FLD_SEARCH_LOCATION = 'search_location';

    /**
     * @var DBFactoryInterface
     */
    protected $dbFactory;

    /**
     * @var IDXFactoryInterface
     */
    protected $idxFactory;

    /**
     * @var FieldFactoryInterface
     */
    protected $fieldFactory;

    /**
     * SearchFieldDatastore constructor.
     * @param DBFactoryInterface $dbFactory
     * @param IDXFactoryInterface $idxFactory
     * @param FieldFactoryInterface $fieldFactory
     */
    public function __construct
    (
        DBFactoryInterface $dbFactory,
        IDXFactoryInterface $idxFactory,
        FieldFactoryInterface $fieldFactory
    ) {
        $this->dbFactory = $dbFactory;
        $this->idxFactory = $idxFactory;
        $this->fieldFactory = $fieldFactory;
    }

    /**
     * @param \REW\Model\Idx\FeedInfoInterface $feedInfo
     * @return \REW\Model\Idx\FeedInfoInterface
     * @throws \Exception
     */
    public function getFieldsForFeed(FeedInfoInterface $feedInfo)
    {
        if (empty($feedInfo->getName())) {
            throw new \RuntimeException('$feedInfo must have a name at this point!');
        }

        $db = $this->dbFactory->get($feedInfo->getName());
        $idx = $this->idxFactory->getIdx($feedInfo->getName());

        $idxFields = $this->getAvailableFieldsArray($feedInfo, $idx);

        /** @var \REW\Model\Idx\Search\FieldInterface[][] $fields */
        $fields = [];

        foreach ($idxFields as $idxField => $values) {
            $field = $this->fieldFactory->createFromArray($values);
            $idxField = $field->getDbFields();

            if (is_array($idxField)) {
                $idxField = array_shift($idxField);
            }

            if (empty($idx->field($idxField))) {
                continue;
            }

            if (!isset($fields[$idxField])) {
                $fields[$idxField] = [$field];
            } else {
                $fields[$idxField][] = $field;
            }
        }

        $params = implode(',', array_fill(0, count($fields), '?'));

        $sql = sprintf('SELECT `COLUMN_NAME`, `DATA_TYPE` FROM `INFORMATION_SCHEMA`.`COLUMNS` WHERE `TABLE_SCHEMA` = \'%s\' AND `TABLE_NAME` = \'%s\' AND `COLUMN_NAME` IN (%s)',
            $db->getDatabase(),
            $idx->getTable(),
            $params
        );

        $stmt = $db->prepare($sql);
        $stmt->execute(array_keys($fields));
        $dataTypes = $stmt->fetchAll();

        foreach ($dataTypes as $dataType) {
            $fieldDataType = $dataType['DATA_TYPE'];
            $permissibleValues = [];
            if ($dataType['DATA_TYPE'] == 'enum') {
                $enumSql = sprintf('SHOW COLUMNS FROM %s WHERE `Field` = ?', $idx->getTable());
                $stmt = $db->prepare($enumSql);
                $stmt->execute([$dataType['COLUMN_NAME']]);
                $enumTypes = $stmt->fetchAll();

                foreach ($enumTypes as $enumType) {
                    $typesTmp = preg_replace('/^enum/', 'array', $enumType['Type']);
                    if ($typesTmp !== $enumType['COLUMN_NAME']) {
                        eval(sprintf('$permissibleValues = %s;', $typesTmp));
                    }
                    if (count(array_diff(array_merge($permissibleValues, ['Y', 'N']), array_intersect($permissibleValues, ['Y','N']))) === 0) {
                        $permissibleValues = [true, false];
                    }
                }
            }
            else if ($dataType['COLUMN_NAME'] == 'ListingType') {
                $sql = sprintf('SELECT DISTINCT `ListingType` FROM %s', $idx->getTable());
                $stmt = $db->prepare($sql);
                $stmt->execute();
                $permissibleValues = array_values(array_filter($stmt->fetchAll(\PDO::FETCH_COLUMN), function ($elem) {
                    if ($elem !== '') return true;
                    return false;
                }));
                $fieldDataType = 'enum';
            }
            else if ($dataType['COLUMN_NAME'] == 'ListingSubType') {
                $sql = sprintf('SELECT DISTINCT `ListingSubType` FROM %s', $idx->getTable());
                $stmt = $db->prepare($sql);
                $stmt->execute();
                $permissibleValues = array_values(array_filter($stmt->fetchAll(\PDO::FETCH_COLUMN), function ($elem) {
                    if ($elem !== '') return true;
                    return false;
                }));
                $fieldDataType = 'enum';
            }
            // @todo use a factory to build these results.
            foreach ($fields[$dataType['COLUMN_NAME']] as &$field) {
                $field = $field->withDataType($this->abstractFieldType($fieldDataType));
                $field = $field->withAllowedValues($permissibleValues);
                $feedInfo = $feedInfo->withAdditionalSearchField($field);
            }
        }
        return $feedInfo;
    }

    /**
     * @param \REW\Model\Idx\FeedInfoInterface $feedInfo
     * @return \REW\Model\Idx\FeedInfoInterface
     * @throws \Exception
     */
    public function getMissingFieldInfo(FeedInfoInterface $feedInfo)
    {
        if (empty($feedInfo->getName())) {
            throw new \RuntimeException('$feedInfo must have a name at this point!');
        }

        $providedFields = $feedInfo->getFields();
        $idx = $this->idxFactory->getIdx($feedInfo->getName());

        $idxFields = $this->getAvailableFieldsArray($feedInfo, $idx);


        /** @var \REW\Model\Idx\Search\FieldInterface[][] $fields */
        $offeredFields = [];

        foreach ($idxFields as $idxField => $values) {
            $offeredFields[$values['form_field']] = $this->fieldFactory->createFromArray($values);
        }

        foreach ($providedFields as &$providedField) {
            if (isset($offeredFields[$providedField->getFormFieldName()])) {
                $providedField = $offeredFields[$providedField->getFormFieldName()]->withSearchValue($providedField->getSearchValue());
            }
        }

        return $feedInfo->withFields($providedFields);
    }

    /**
     * Takes in a data type and maps it to something more generic.
     * @param string $type
     * @return string
     */
    protected function abstractFieldType($type)
    {
        // Numbers
        if (stristr($type, 'int')) {
            return 'number';
        }
        if (stristr($type, 'double')) {
            return 'number';
        }
        if (stristr($type, 'float')) {
            return 'number';
        }
        if (stristr($type, 'decimal')) {
            return 'number';
        }

        // String
        if (stristr($type, 'varchar')) {
            return 'string';
        }
        if (stristr($type, 'text')) {
            return 'string';
        }

        // Something else
        return $type;
    }

    /**
     * Get an array of the fields offered by the specified feed.
     * @param FeedInfoInterface $feedInfo
     * @param IDXInterface $idx
     * @return array
     */
    protected function getAvailableFieldsArray(FeedInfoInterface $feedInfo, IDXInterface $idx)
    {
        $idxFields = [];

        if (function_exists('\search_fields')) {
            $standardSearchFields = \search_fields($idx);
            $locationSearchFields = array_filter($standardSearchFields, function($elem) {
                if ($elem['form_field'] === self::FLD_SEARCH_LOCATION) {
                    return true;
                }
            });
            $idxFields = array_filter($standardSearchFields, function($elem) {
                if ($elem['form_field'] !== self::FLD_SEARCH_LOCATION) {
                    return true;
                }
            });

            $locationIdxFields = [];

            foreach ($locationSearchFields as $locationSearchField) {
                $locationIdxFields[] = $locationSearchField['idx_field'];
            }
            $idxFields[self::FLD_SEARCH_LOCATION] = array_pop($locationSearchFields);
            $idxFields[self::FLD_SEARCH_LOCATION]['idx_field'] = $locationIdxFields;
        }

        $idxFunction = sprintf('%s_search_fields', $feedInfo->getName());
        if (function_exists($idxFunction)) {
            $idxFields = array_merge($idxFields, $idxFunction($idx));
        }

        return $idxFields;
    }
}
