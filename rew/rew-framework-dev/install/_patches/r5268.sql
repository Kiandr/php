-- Update Primary Key 
ALTER TABLE `rewidx_quicksearch`
	DROP PRIMARY KEY,
	ADD PRIMARY KEY (`agent` , `idx`)
;