INSERT INTO `#__extensions` (`package_id`, `name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES
(0, 'plg_extension_finder', 'plugin', 'finder', 'extension', 0, 1, 1, 0, '', '', 0, '0000-00-00 00:00:00', 0, 0);

TRUNCATE TABLE `#__finder_filters`;
ALTER TABLE `#__finder_filters` MODIFY `created_by` int(10) unsigned NOT NULL DEFAULT 0;
ALTER TABLE `#__finder_filters` MODIFY `created_by_alias` varchar(255) NOT NULL DEFAULT '';

TRUNCATE TABLE `#__finder_links`;
ALTER TABLE `#__finder_links` CHANGE `language` `language` CHAR(7) NOT NULL DEFAULT '' AFTER `access`;
ALTER TABLE `#__finder_links` MODIFY `state` int(5) NOT NULL DEFAULT 1;
ALTER TABLE `#__finder_links` MODIFY `access` int(5) NOT NULL DEFAULT 0;
ALTER TABLE `#__finder_links` ADD INDEX `idx_language` (`language`);

CREATE TABLE `#__finder_links_terms` (
	`link_id` INT(10) UNSIGNED NOT NULL,
	`term_id` INT(10) UNSIGNED NOT NULL,
	`weight` FLOAT UNSIGNED NOT NULL DEFAULT 0,
	PRIMARY KEY (`link_id`, `term_id`),
	INDEX `idx_term_weight` (`term_id`, `weight`),
	INDEX `idx_link_term_weight` (`link_id`, `term_id`, `weight`)
) COLLATE='utf8mb4_general_ci' ENGINE=InnoDB;

DROP TABLE `#__finder_links_terms0`;
DROP TABLE `#__finder_links_terms1`;
DROP TABLE `#__finder_links_terms2`;
DROP TABLE `#__finder_links_terms3`;
DROP TABLE `#__finder_links_terms4`;
DROP TABLE `#__finder_links_terms5`;
DROP TABLE `#__finder_links_terms6`;
DROP TABLE `#__finder_links_terms7`;
DROP TABLE `#__finder_links_terms8`;
DROP TABLE `#__finder_links_terms9`;
DROP TABLE `#__finder_links_termsa`;
DROP TABLE `#__finder_links_termsb`;
DROP TABLE `#__finder_links_termsc`;
DROP TABLE `#__finder_links_termsd`;
DROP TABLE `#__finder_links_termse`;
DROP TABLE `#__finder_links_termsf`;

CREATE TABLE IF NOT EXISTS `#__finder_logging` (
  `searchterm` VARCHAR(255) NOT NULL DEFAULT '',
  `md5sum` VARCHAR(32) NOT NULL DEFAULT '',
  `query` BLOB NOT NULL,
  `hits` INT(11) NOT NULL DEFAULT 1,
  `results` INT(11) NOT NULL DEFAULT 0,
  PRIMARY KEY `md5sum` (`md5sum`),
  INDEX `searchterm` (`searchterm`(191))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_general_ci;

DROP TABLE `#__finder_taxonomy`;
CREATE TABLE IF NOT EXISTS `#__finder_taxonomy` (
	`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`parent_id` INT(10) UNSIGNED NOT NULL DEFAULT '0',
	`lft` INT(11) NOT NULL DEFAULT '0',
	`rgt` INT(11) NOT NULL DEFAULT '0',
	`level` INT(10) UNSIGNED NOT NULL DEFAULT '0',
	`path` VARCHAR(400) NOT NULL DEFAULT '',
	`title` VARCHAR(255) NOT NULL DEFAULT '',
	`alias` VARCHAR(400) NOT NULL DEFAULT '',
	`state` TINYINT(1) UNSIGNED NOT NULL DEFAULT '1',
	`access` TINYINT(1) UNSIGNED NOT NULL DEFAULT '1',
	`language` CHAR(7) NOT NULL DEFAULT '',
	PRIMARY KEY (`id`),
	INDEX `idx_state` (`state`),
	INDEX `idx_access` (`access`),
	INDEX `idx_path` (`path`(100)),
	INDEX `idx_left_right` (`lft`, `rgt`),
	INDEX `idx_alias` (`alias`(100)),
	INDEX `idx_language` (`language`),
	INDEX `idx_parent_published` (`parent_id`, `state`, `access`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_general_ci;
INSERT INTO `#__finder_taxonomy` (`id`, `parent_id`, `lft`, `rgt`, `level`, `path`, `title`, `alias`, `state`, `access`, `language`) VALUES
(1, 0, 0, 1, 0, '', 'ROOT', 'root', 1, 1, '*');

TRUNCATE TABLE `#__finder_terms`;
ALTER TABLE `#__finder_terms` CHANGE `language` `language` CHAR(7) NOT NULL DEFAULT '' AFTER `links`;
ALTER TABLE `#__finder_terms` MODIFY `stem` varchar(75) NOT NULL DEFAULT '';
ALTER TABLE `#__finder_terms` MODIFY `soundex` varchar(75) NOT NULL DEFAULT '';
ALTER TABLE `#__finder_terms` DROP INDEX `idx_term`, ADD INDEX `idx_stem` (`stem`), ADD INDEX `idx_language` (`language`), ADD INDEX `language` (`language`), ADD UNIQUE INDEX `idx_term` (`term`, `language`);

DROP TABLE IF EXISTS `#__finder_terms_common`;
CREATE TABLE `#__finder_terms_common` (
  `term` varchar(75) NOT NULL DEFAULT '',
  `language` char(7) NOT NULL DEFAULT '',
  `custom` int(11) NOT NULL DEFAULT '0',
  UNIQUE KEY `idx_word_lang` (`term`,`language`),
  KEY `idx_lang` (`language`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_bin;
INSERT INTO `#__finder_terms_common` (`term`, `language`, `custom`) VALUES
	('i', 'en', 0),
	('me', 'en', 0),
	('my', 'en', 0),
	('myself', 'en', 0),
	('we', 'en', 0),
	('our', 'en', 0),
	('ours', 'en', 0),
	('ourselves', 'en', 0),
	('you', 'en', 0),
	('your', 'en', 0),
	('yours', 'en', 0),
	('yourself', 'en', 0),
	('yourselves', 'en', 0),
	('he', 'en', 0),
	('him', 'en', 0),
	('his', 'en', 0),
	('himself', 'en', 0),
	('she', 'en', 0),
	('her', 'en', 0),
	('hers', 'en', 0),
	('herself', 'en', 0),
	('it', 'en', 0),
	('its', 'en', 0),
	('itself', 'en', 0),
	('they', 'en', 0),
	('them', 'en', 0),
	('their', 'en', 0),
	('theirs', 'en', 0),
	('themselves', 'en', 0),
	('what', 'en', 0),
	('which', 'en', 0),
	('who', 'en', 0),
	('whom', 'en', 0),
	('this', 'en', 0),
	('that', 'en', 0),
	('these', 'en', 0),
	('those', 'en', 0),
	('am', 'en', 0),
	('is', 'en', 0),
	('are', 'en', 0),
	('was', 'en', 0),
	('were', 'en', 0),
	('be', 'en', 0),
	('been', 'en', 0),
	('being', 'en', 0),
	('have', 'en', 0),
	('has', 'en', 0),
	('had', 'en', 0),
	('having', 'en', 0),
	('do', 'en', 0),
	('does', 'en', 0),
	('did', 'en', 0),
	('doing', 'en', 0),
	('would', 'en', 0),
	('should', 'en', 0),
	('could', 'en', 0),
	('ought', 'en', 0),
	('i\'m', 'en', 0),
	('you\'re', 'en', 0),
	('he\'s', 'en', 0),
	('she\'s', 'en', 0),
	('it\'s', 'en', 0),
	('we\'re', 'en', 0),
	('they\'re', 'en', 0),
	('i\'ve', 'en', 0),
	('you\'ve', 'en', 0),
	('we\'ve', 'en', 0),
	('they\'ve', 'en', 0),
	('i\'d', 'en', 0),
	('you\'d', 'en', 0),
	('he\'d', 'en', 0),
	('she\'d', 'en', 0),
	('we\'d', 'en', 0),
	('they\'d', 'en', 0),
	('i\'ll', 'en', 0),
	('you\'ll', 'en', 0),
	('he\'ll', 'en', 0),
	('she\'ll', 'en', 0),
	('we\'ll', 'en', 0),
	('they\'ll', 'en', 0),
	('isn\'t', 'en', 0),
	('aren\'t', 'en', 0),
	('wasn\'t', 'en', 0),
	('weren\'t', 'en', 0),
	('hasn\'t', 'en', 0),
	('haven\'t', 'en', 0),
	('hadn\'t', 'en', 0),
	('doesn\'t', 'en', 0),
	('don\'t', 'en', 0),
	('didn\'t', 'en', 0),
	('won\'t', 'en', 0),
	('wouldn\'t', 'en', 0),
	('shan\'t', 'en', 0),
	('shouldn\'t', 'en', 0),
	('can\'t', 'en', 0),
	('cannot', 'en', 0),
	('couldn\'t', 'en', 0),
	('mustn\'t', 'en', 0),
	('let\'s', 'en', 0),
	('that\'s', 'en', 0),
	('who\'s', 'en', 0),
	('what\'s', 'en', 0),
	('here\'s', 'en', 0),
	('there\'s', 'en', 0),
	('when\'s', 'en', 0),
	('where\'s', 'en', 0),
	('why\'s', 'en', 0),
	('how\'s', 'en', 0),
	('a', 'en', 0),
	('an', 'en', 0),
	('the', 'en', 0),
	('and', 'en', 0),
	('but', 'en', 0),
	('if', 'en', 0),
	('or', 'en', 0),
	('because', 'en', 0),
	('as', 'en', 0),
	('until', 'en', 0),
	('while', 'en', 0),
	('of', 'en', 0),
	('at', 'en', 0),
	('by', 'en', 0),
	('for', 'en', 0),
	('with', 'en', 0),
	('about', 'en', 0),
	('against', 'en', 0),
	('between', 'en', 0),
	('into', 'en', 0),
	('through', 'en', 0),
	('during', 'en', 0),
	('before', 'en', 0),
	('after', 'en', 0),
	('above', 'en', 0),
	('below', 'en', 0),
	('to', 'en', 0),
	('from', 'en', 0),
	('up', 'en', 0),
	('down', 'en', 0),
	('in', 'en', 0),
	('out', 'en', 0),
	('on', 'en', 0),
	('off', 'en', 0),
	('over', 'en', 0),
	('under', 'en', 0),
	('again', 'en', 0),
	('further', 'en', 0),
	('then', 'en', 0),
	('once', 'en', 0),
	('here', 'en', 0),
	('there', 'en', 0),
	('when', 'en', 0),
	('where', 'en', 0),
	('why', 'en', 0),
	('how', 'en', 0),
	('all', 'en', 0),
	('any', 'en', 0),
	('both', 'en', 0),
	('each', 'en', 0),
	('few', 'en', 0),
	('more', 'en', 0),
	('most', 'en', 0),
	('other', 'en', 0),
	('some', 'en', 0),
	('such', 'en', 0),
	('no', 'en', 0),
	('nor', 'en', 0),
	('not', 'en', 0),
	('only', 'en', 0),
	('own', 'en', 0),
	('same', 'en', 0),
	('so', 'en', 0),
	('than', 'en', 0),
	('too', 'en', 0),
	('very', 'en', 0);

ALTER TABLE `#__finder_tokens` CHANGE `language` `language` CHAR(7) NOT NULL DEFAULT '' AFTER `context`;
ALTER TABLE `#__finder_tokens` MODIFY `stem` varchar(75) NOT NULL DEFAULT '';
ALTER TABLE `#__finder_tokens` ADD INDEX `idx_stem` (`stem`);
ALTER TABLE `#__finder_tokens` ADD INDEX `idx_language` (`language`);

ALTER TABLE `#__finder_tokens_aggregate` DROP COLUMN `map_suffix`;
ALTER TABLE `#__finder_tokens_aggregate` CHANGE `language` `language` CHAR(7) NOT NULL DEFAULT '' AFTER `total_weight`;
ALTER TABLE `#__finder_tokens_aggregate` MODIFY `stem` varchar(75) NOT NULL DEFAULT '';
ALTER TABLE `#__finder_tokens_aggregate` MODIFY `term_weight` float unsigned NOT NULL DEFAULT 0;
ALTER TABLE `#__finder_tokens_aggregate` MODIFY `context_weight` float unsigned NOT NULL DEFAULT 0;
ALTER TABLE `#__finder_tokens_aggregate` MODIFY `total_weight` float unsigned NOT NULL DEFAULT 0;

ALTER TABLE `#__finder_types` MODIFY `mime` varchar(100) NOT NULL DEFAULT '';
