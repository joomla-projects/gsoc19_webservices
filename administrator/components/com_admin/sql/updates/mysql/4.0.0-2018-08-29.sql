ALTER TABLE `#__modules` MODIFY `publish_up` datetime NULL DEFAULT NULL;
ALTER TABLE `#__modules` MODIFY `publish_down` datetime NULL DEFAULT NULL;
ALTER TABLE `#__modules` MODIFY `checked_out_time` datetime NULL DEFAULT NULL;

-- Use 0 instead of '0000-00-00 00:00:00' if you get 'Invalid default value for ...'
UPDATE `#__modules` SET
	`publish_up` = CASE WHEN `publish_up` IN ('0000-00-00 00:00:00', '1000-01-01 00:00:00') THEN NULL ELSE `publish_up` END,
	`publish_down` = CASE WHEN `publish_down` IN ('0000-00-00 00:00:00', '1000-01-01 00:00:00') THEN NULL ELSE `publish_down` END,
	`checked_out_time` = CASE WHEN `checked_out_time` IN ('0000-00-00 00:00:00', '1000-01-01 00:00:00') THEN NULL ELSE `checked_out_time` END;
