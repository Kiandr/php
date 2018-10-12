
--
-- Alter agent table to support RE/MAX Integra's Launchpad SSO Tool
--

ALTER TABLE  `agents` 
ADD  `remax_launchpad_username` VARCHAR( 100 ) NOT NULL COMMENT  'Username used for Remax Integra''s SSO System',
ADD  `remax_launchpad_url` VARCHAR( 100 ) NOT NULL COMMENT  'Stored the URL that the username was assigned to';
