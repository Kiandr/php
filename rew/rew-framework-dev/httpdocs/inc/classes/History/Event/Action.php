<?php

use REW\Core\Interfaces\DBInterface;

/**
 * History_Event_Update is an abstract class that extends History_Event. This class is used to tracking general Agent / Lead actions.
 *
 * @package History
 */
abstract class History_Event_Action extends History_Event
{
    use History_Trait_LastActionEvent;

    /**
     * Save History Event Action into Database
     *
     * @param DBInterface $db - DB Connection (Optional, DBInterface)
     * @return int
     */
    public function save(DBInterface $db = null) {
        $db = is_null($db) ? $this->db : $db;
        if ($save = parent::save($db)) {
            $this->saveLastActionEvent();
        }
        return $save;
    }

}
