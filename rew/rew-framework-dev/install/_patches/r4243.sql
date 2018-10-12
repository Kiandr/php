--
-- Add colums for bounced and flagged email
--
ALTER TABLE  `users` 
  ADD  `bounced` ENUM( 'true', 'false' ) NOT NULL DEFAULT 'false' AFTER  `opt_searches` ,
  ADD  `fbl` ENUM( 'true', 'false' ) NOT NULL DEFAULT 'false' AFTER  `bounced`;
