-- Give all permissions to super admin
UPDATE `agents` SET `permissions_admin` = 18446744073709551615, `permissions_user` = 18446744073709551615 WHERE `id` = 1;