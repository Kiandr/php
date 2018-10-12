--
-- Update Agents Table To Allow Default Ordering Of Leads
--
ALTER TABLE `agents`
    ADD COLUMN `default_order` varchar(100) NOT NULL default 'score' AFTER `default_filter`,
    ADD COLUMN `default_sort` varchar(100) NOT NULL default 'DESC' AFTER `default_order`;
