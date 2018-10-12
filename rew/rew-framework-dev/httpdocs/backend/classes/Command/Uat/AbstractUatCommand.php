<?php

namespace REW\Backend\Command\Uat;

use REW\Core\Interfaces\Factories\DBFactoryInterface;
use \Symfony\Component\Console\Command\Command;
use \Container;
use \PDO;

/**
 * AbstractUatCommand
 * @package REW\Backend\Command\Uat
 */
abstract class AbstractUatCommand extends Command
{

    /**
     * @return PDO
     */
    protected function getDbConnection()
    {
        return Container::getInstance()->get(DBFactoryInterface::class)->get();
    }
}
