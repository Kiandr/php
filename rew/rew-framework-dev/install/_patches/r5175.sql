-- Adding support for legacy notes for upgraded sites

ALTER TABLE `history_events` CHANGE `type` `type` ENUM( 'Action', 'Create', 'Update', 'Delete',
 'Email', 'Phone', 'LegacyNote' );