DROP TABLE IF EXISTS `blocks`;

CREATE TABLE `blocks` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `title` varchar(128) NOT NULL,
  `content_block` text,
  `show_title` BOOLEAN NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=UTF8;