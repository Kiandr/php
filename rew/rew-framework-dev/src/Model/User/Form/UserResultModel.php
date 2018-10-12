<?php
namespace REW\Model\User\Form;

use \InvalidArgumentException;

/**
 * Class UserResultModel
 * @package REW\Model\User\
 */
class UserResultModel
{
    const FLD_DATA = 'data';

    const FLD_SUCCESS = 'success';

    const FLD_ERRORS = 'errors';

    const FLD_PPC = 'ppc';

    /**
     * @var array
     */
    protected $data;

    /**
     * @var string
     */
    protected $success;

    /**
     * @var array
     */
    protected $errors;

    /**
     * @var array
     */
    protected $ppc;

    /**
     * @param string $data
     * @return UserRequestModel
     */
    public function withData($data)
    {
        $clone = clone $this;
        $clone->data = $data;
        return $clone;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param string $success
     * @return UserRequestModel
     */
    public function withSuccess($success)
    {
        $clone = clone $this;
        $clone->success = $success;
        return $clone;
    }

    /**
     * @return string
     */
    public function getSuccess()
    {
        return $this->success;
    }

    /**
     * @param string $errors
     * @return UserRequestModel
     */
    public function withErrors($errors)
    {
        $clone = clone $this;
        $clone->errors = $errors;
        return $clone;
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @param array $ppc
     * @return UserRequestModel
     */
    public function withPpc($ppc)
    {
        $clone = clone $this;
        $clone->ppc = $ppc;
        return $clone;
    }

    /**
     * @return array
     */
    public function getPpc()
    {
        return $this->ppc;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            self::FLD_DATA => $this->data,
            self::FLD_SUCCESS => $this->success,
            self::FLD_ERRORS => $this->errors,
            self::FLD_PPC => $this->ppc
        ];
    }

}
