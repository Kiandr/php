<?php

class Helper_Uploads
{

    /**
     * Database Object
     * @var DB
     */
    private $db;

    /**
     * Website Configuration
     * @var Settings
     */
    private $settings;

    public function __construct(DB $db, Settings $settings)
    {
        $this->db = $db;
        $this->settings = $settings;
    }

    /**
     * Removes A Set Of Uploads From The System
     * @param integer $row_id
     * @param string $type
     * @throws Exception on failure
     */
    public function remove($row_id, $type)
    {
        try {
            // Get List Of Images
            $query = "SELECT `u`.`id`, `u`.`file`"
                    . "FROM `" . $this->settings->TABLES['UPLOADS'] . "` `u` "
                    . "WHERE `u`.`row` = :row "
                    . "AND `u`.`type` = :type;";

            $files = $this->db->fetchAll($query, [
                'row'  => $row_id,
                'type' => $type
            ]);

            foreach ($files as $file) {
                $path = $this->settings->DIRS['UPLOADS'] . $file['file'];

                if (!file_exists($path)) {
                    throw new Exception("Attempted to delete " . $file['file'] . " but it does not exist.");
                }

                // Delete File
                unlink($path);

                // Remove Reference From Database
                $stmt = $this->db->prepare("DELETE FROM `" . $this->settings->TABLES['UPLOADS'] . "` WHERE `id` = :id;");
                $stmt->execute(['id' => $file['id']]);
            }
        } catch (PDOException $e) {
            throw new Exception_UploadDeleteError("Unable to remove " . $type . " upload from the database. Please try again.");
        } catch (Exception $e) {
            throw new Exception_UploadDeleteError($e->getMessage());
        }
    }
}
