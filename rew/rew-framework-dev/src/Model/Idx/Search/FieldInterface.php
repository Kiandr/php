<?php
namespace REW\Model\Idx\Search;

interface FieldInterface extends \JsonSerializable
{
    /** @var string */
    const FLD_SEARCH_DISPLAY_NAME = 'display_name';

    /** @var string */
    const FLD_DATA_TYPE = 'type';

    /** @var string */
    const FLD_ALLOWED_VALUES = 'allowed_values';

    /**
     * Immutable setter for the internal (db) field name.
     * @param array $dbFields
     * @return self
     */
    public function withDbFields(array $dbFields);

    /**
     * Gets the internal (db) field name.
     * @return array
     */
    public function getDbFields();

    /**
     * Immutable setter for the external (search) field name.
     * @param string $formFieldName
     * @return self
     */
    public function withFormFieldName($formFieldName);

    /**
     * Returns the external (search) field name.
     * @return string
     */
    public function getFormFieldName();

    /**
     * Immutable setter for the name that should be displayed in a form.
     * @param $displayName
     * @return string
     */
    public function withDisplayName($displayName);

    /**
     * Returns the name that should be displayed in a form.
     * @return string
     */
    public function getDisplayName();

    /**
     * Immutable setter for the type of data this field should store.
     * @param string $datatype
     * @return self
     */
    public function withDataType($datatype);

    /**
     * Returns the datatype that this field should store.
     * @return string
     */
    public function getDataType();

    /**
     * Immutable setter for the allowed values.
     * @param array $values
     * @return self
     */
    public function withAllowedValues(array $values);

    /**
     * Get permissible values for this field.
     * @return array
     */
    public function getAllowedValues();

    /**
     * Immutable setter for the database comparison that this field needs.
     * @param string $searchOp
     * @return self
     */
    public function withSearchOperation($searchOp);

    /**
     * Returns the database comparison that this field needs.
     * @return string
     */
    public function getSearchOperation();

    /**
     * Immutable setter for this field's search value.
     * @param mixed $searchValue
     * @return self
     */
    public function withSearchValue($searchValue);

    /**
     * Get the value to search against this field.
     * @return mixed
     */
    public function getSearchValue();

    /**
     * Returns a JSON representation of this model.
     * @return mixed
     */
    public function jsonSerialize();
}
