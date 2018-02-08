CREATE TABLE `logins` (
	`username` VARCHAR( 255 ) NOT NULL ,
	`uid` INT NOT NULL ,
	`gid` INT NOT NULL ,
	`email` VARCHAR( 255 ) NULL ,
	`password` VARCHAR( 255 ) NOT NULL ,
	`dir`	VARCHAR( 255 ) NOT NULL,
	PRIMARY KEY ( `username` )
) ENGINE = INNODB COMMENT =  'pure-user-ftpadmin'
