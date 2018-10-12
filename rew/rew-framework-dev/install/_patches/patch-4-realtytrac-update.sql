-- Renaming tables from %pdx% to %rt% --
RENAME TABLE
		`rewpdx_defaults` TO `rewrt_defaults`,
		`rewpdx_details` TO `rewrt_details`,
		`rewpdx_system` TO `rewrt_system`,
		`users_viewed_pdx_properties` TO `users_viewed_rt_properties`;

-- Adding in new field to defaults --
ALTER TABLE  `rewrt_defaults` ADD  `locations` LONGTEXT NOT NULL AFTER  `criteria` ;