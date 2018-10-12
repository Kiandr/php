<?php
namespace REW\Model\Idx\Search\Field;

use REW\Model\Idx\Search\FieldInterface;

class Field implements FieldInterface
{
    /**
     * @var array
     */
    protected $dbFields;

    /**
     * @var string
     */
    protected $formFieldName;

    /**
     * @var string
     */
    protected $displayName;

    /**
     * @var string
     */
    protected $dataType;

    /**
     * @var array
     */
    protected $allowedValues;

    /**
     * @var string
     */
    protected $searchOperation;

    /**
     * @var mixed
     */
    protected $searchValue;

    /**
     * @param array $dbFields
     * @return self
     */
    public function withDbFields(array $dbFields)
    {
        $clone = clone $this;
        $clone->dbFields = $dbFields;
        return $clone;
    }

    /**
     * @return array
     */
    public function getDbFields()
    {
        return $this->dbFields;
    }

    /**
     * Immutable setter for the external (search) field name.
     * @param string $formFieldName
     * @return self
     */
    public function withFormFieldName($formFieldName)
    {
        $clone = clone $this;
        $clone->formFieldName = $formFieldName;
        return $clone;
    }

    /**
     * Returns the external (search) field name.
     * @return string
     */
    public function getFormFieldName()
    {
        return $this->formFieldName;
    }

    /**
     * @param $displayName
     * @return self
     */
    public function withDisplayName($displayName)
    {
        $clone = clone $this;
        $clone->displayName = $displayName;
        return $clone;
    }

    /**
     * @return string
     */
    public function getDisplayName()
    {
        return $this->displayName;
    }

    /**
     * @param string $datatype
     * @return self
     */
    public function withDataType($datatype)
    {
        $clone = clone $this;
        $clone->dataType = $datatype;
        return $clone;
    }

    /**
     * @return string
     */
    public function getDataType()
    {
        return $this->dataType;
    }

    /**
     * @param array $values
     * @return self
     */
    public function withAllowedValues(array $values)
    {
        $clone = clone $this;
        $clone->allowedValues = $values;
        return $clone;
    }

    /**
     * @return array
     */
    public function getAllowedValues()
    {
        return $this->allowedValues;
    }

    /**
     * Immutable setter for the database comparison that this field needs.
     * @param string $searchOp
     * @return self
     */
    public function withSearchOperation($searchOp)
    {
        $clone = clone $this;
        $clone->searchOperation = $searchOp;
        return $clone;
    }

    /**
     * Returns the database comparison that this field needs.
     * @return string
     */
    public function getSearchOperation()
    {
        return $this->searchOperation;
    }

    /**
     * @param mixed $searchValue
     * @return self
     */
    public function withSearchValue($searchValue)
    {
        $clone = clone $this;
        $clone->searchValue = $searchValue;
        return $clone;
    }

    /**
     * @return mixed
     */
    public function getSearchValue()
    {
        return $this->searchValue;
    }

    public function jsonSerialize()
    {
        $jsonFields =  [
            self::FLD_SEARCH_DISPLAY_NAME => $this->displayName,
            self::FLD_DATA_TYPE => $this->dataType,
        ];
        if (!empty($this->allowedValues)) {
            $jsonFields[self::FLD_ALLOWED_VALUES] = $this->allowedValues;
        }
        return $jsonFields;
    }
}
