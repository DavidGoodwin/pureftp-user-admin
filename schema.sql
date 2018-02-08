CREATE TABLE `logins` (
	`id` INT NOT NULL AUTO_INCREMENT,
	`uid` INT NOT NULL ,
	`gid` INT NOT NULL ,
	`email` VARCHAR( 255 ) NULL ,
	`password` VARCHAR( 255 ) NOT NULL ,
	`username` VARCHAR( 255 ) NOT NULL ,
	`dir`	VARCHAR( 255 ) NOT NULL,
	PRIMARY KEY (  `id` )
) ENGINE = INNODB COMMENT =  'pure-user-ftpadmin'
