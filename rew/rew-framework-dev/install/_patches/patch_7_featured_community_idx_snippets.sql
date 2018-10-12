--
-- Table structure changes for `featured_communities`
--
ALTER TABLE `featured_communities` 
    ADD `idx_snippet` int(10) NULL default NULL AFTER `anchor_two_link`
;
