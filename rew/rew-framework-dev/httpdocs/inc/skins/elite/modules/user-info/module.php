<?php
$user = User_Session::get();

if ($user->isValid()) {
    // Use a callable. This is so that these queries don't actually execute unless the template asks for them.
    $count = function ($what) use ($user) {
        switch ($what) {
            case 'favorites':
                $table = TABLE_SAVED_LISTINGS;
                break;
            case 'saved_searches':
                $table = TABLE_SAVED_SEARCHES;
                break;
            default:
                throw new Exception("Don't know how to count " . $what);
        }

        $db = Db::get('cms');
        $stmt = $db->prepare("SELECT COUNT(*) FROM `" . $table . "` WHERE `user_id` = :user_id AND `idx` = :feed");
        try {
            $stmt->execute(array('user_id' => $user->info('id'), 'feed' => Settings::getInstance()->IDX_FEED));
            list ($count) = $stmt->fetch(PDO::FETCH_NUM);
            $stmt->closeCursor();

            return $count;
        } catch (PDOException $ex) {
            error_log($ex->getMessage());
            return 0;
        }
    };
} else {
    $count = function () {
        return 0;
    };
}
