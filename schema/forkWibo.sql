# ************************************************************
# Sequel Pro SQL dump
# Version 4096
#
# http://www.sequelpro.com/
# http://code.google.com/p/sequel-pro/
#
# Host: 127.0.0.1 (MySQL 5.6.14)
# Database: forkWibo
# Generation Time: 2014-10-19 02:36:32 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table timing
# ------------------------------------------------------------

DROP TABLE IF EXISTS `timing`;

CREATE TABLE `timing` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` varchar(256) DEFAULT NULL,
  `screen_name` varchar(256) DEFAULT NULL,
  `access_token` varchar(256) DEFAULT NULL,
  `to_uid` varchar(256) DEFAULT NULL,
  `to_screen_name` varchar(256) DEFAULT NULL,
  `to_profile_image_url` varchar(256) DEFAULT NULL,
  `timing` varchar(256) DEFAULT '1 天',
  `created_time` timestamp NULL DEFAULT NULL,
  `state` tinyint(4) DEFAULT '0',
  `since_id` bigint(22) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table user
# ------------------------------------------------------------

DROP TABLE IF EXISTS `user`;

CREATE TABLE `user` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` varchar(256) DEFAULT NULL,
  `screen_name` varchar(256) DEFAULT NULL,
  `profile_image_url` varchar(256) DEFAULT NULL,
  `access_token` varchar(256) DEFAULT NULL,
  `expires_in` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table weibo
# ------------------------------------------------------------

DROP TABLE IF EXISTS `weibo`;

CREATE TABLE `weibo` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` varchar(256) DEFAULT NULL,
  `to_uid` varchar(256) DEFAULT NULL,
  `to_screen_name` varchar(256) DEFAULT NULL,
  `to_profile_image_url` varchar(256) DEFAULT NULL,
  `weibo` text,
  `created_time` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;




/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
