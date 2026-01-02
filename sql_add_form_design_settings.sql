-- Add column to store form design settings
ALTER TABLE `forms` ADD `design_settings` LONGTEXT NULL DEFAULT NULL AFTER `publishdata`;

