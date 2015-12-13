SET NAMES utf8;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
--  Table structure for `pw_app_dimdb_cache`
-- ----------------------------
DROP TABLE IF EXISTS `pw_app_dimdb_cache`;
CREATE TABLE `pw_app_dimdb_cache` (
  `cache_key` varchar(32) NOT NULL,
  `cache_value` text,
  `cache_expire` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`cache_key`)
) AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

SET FOREIGN_KEY_CHECKS = 1;
