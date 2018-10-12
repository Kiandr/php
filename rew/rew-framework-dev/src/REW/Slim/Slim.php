<?php
namespace REW\Slim;

use REW\Api\Exception\APIExceptionInterface;

class Slim extends \Slim\Slim
{
    /**
     * @see \Slim\Slim::error()
     * @inheritdoc \Slim\Slim::error()
     * @param mixed $argument Callable|\Exception
     */
    public function error($argument = null)
    {
        if (is_callable($argument)) {
            //Register error handler
            $this->error = $argument;
        } else {
            //Invoke error handler
            $this->response->status(500);
            if ($argument instanceof APIExceptionInterface) {
                $this->response->status($argument->getStatusCode());
            }
            $this->response->body('');
            $this->response->write($this->callErrorHandler($argument));
            $this->stop();
        }
    }
}
