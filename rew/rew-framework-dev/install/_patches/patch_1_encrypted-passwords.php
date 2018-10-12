<?php

// Require Composer Vendor Auto loader
require_once __DIR__ . '/../../boot/app.php';

// Console output
$_title = 'Running Encrypted Passwords Patch (' . basename(__FILE__) . ')';

// DB connection
$db = DB::get();

// Auth Instance
$authuser = Auth::get();

//Get auth table rows before changing column size to avoid trucating passwords
$auths = $db->fetchAll("SELECT `id`, `username`, `password` FROM `auth`;");

//Prepare Password Version on Rollback (ALTER COLUMN is automatically commited)
$password_length = $db->fetch("SHOW FIELDS FROM `auth` WHERE Field ='password'");
$rollBackPassword = $db->prepare("ALTER TABLE `auth` MODIFY COLUMN `password` ".$password_length['Type']." NOT NULL;");

//Update passwords column to varchar(255).  Blowfish will always produce a failure or 60 characters, but PASSWORD_DEFAULT may change and generate different sized hashes
echo 'Updating Auth Table Column Password: ' . PHP_EOL . PHP_EOL;
$find_col = $db->query("SHOW COLUMNS FROM `auth` LIKE 'password';");
if ($col = $find_col->fetch()) {
	if( $col['Type'] == 'varchar(255)' ) {
		echo "\t" . 'Password already of correct type. - DO NOT CONTINUE' . PHP_EOL;
		return;
	}

	else {
		$db->query("ALTER TABLE `auth` MODIFY COLUMN `password` varchar(255) NOT NULL;");
		echo "\t" . 'Password updated to varchar(255).' . PHP_EOL;
	}
} else {
	throw new Exception ('Password column does not exist in database.');
}

//Begin Transaction
$db->beginTransaction();

// Output
echo PHP_EOL . 'Updating Auth:' . PHP_EOL . PHP_EOL;

// Update featurd communities search criteria
if (!empty($auths)) {
	$update = $db->prepare("UPDATE `auth` SET `password` = :password WHERE `id` = :id;");
	foreach ($auths as $auth) {

		//Get Encrypted Password  and Soap
		$encryptedPassword = $authuser->encryptPassword($auth['password']);

		// Update community
		if ($update->execute(array(
			'password' => $encryptedPassword,
			'id' => $auth['id']
		))) {

			//Check Authentication
			if (!$authuser->authenticate($auth['username'], $auth['password'])) {
				//Rollback Transaction
				$db->rollBack();
				$rollBackPassword->execute();
				echo "\t" . 'Authentication Failed After Update.  Rolling Back.' . PHP_EOL;
				return;
			}

			// Output
			echo "\t" . '#' . $auth['id'] . ': '.PHP_EOL;
			echo "\tUsername:" . $auth['username'] . PHP_EOL;
			echo "\tPassword:" . $auth['password'] . PHP_EOL;
			echo "\tEncrypted Password:" . $encryptedPassword . PHP_EOL;

		} else {

			//Rollback Transaction
			$db->rollBack();
			$rollBackPassword->execute();
			echo "\t" . 'Database Update Failed.  Rolling Back.' . PHP_EOL;
			return;
		}
	}
} else {

	//Rollback Transaction
	$db->rollBack();
	$rollBackPassword->execute();
	echo "\t" . 'No Auth Entries Found.  Rolling Back.' . PHP_EOL;
	return;
}

// Commit the changes
$db->commit();

echo PHP_EOL;