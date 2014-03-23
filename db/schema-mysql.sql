SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `account`;
CREATE TABLE `account`
(
    `id`        		INTEGER       	PRIMARY KEY AUTO_INCREMENT,
    `group_id` 			INTEGER 		NOT NULL REFERENCES `group`.`id`,
	`email`      		VARCHAR(100)  	NOT NULL,
	`password_hash`		VARCHAR(200) 	NOT NULL,
	`is_active` 		TINYINT(1) UNSIGNED NOT NULL DEFAULT 1,
	`created_at` 		DATETIME 		NOT NULL,
	`phone` 			VARCHAR(50) 	NOT NULL,
	`cellphone` 		VARCHAR(50) 	DEFAULT NULL,
	`fax` 				VARCHAR(50) 	DEFAULT NULL,
	`serialized_cart` 	MEDIUMTEXT	 	DEFAULT NULL,
	`is_email_activated`INTEGER		 	DEFAULT 0,
	`email_activation_key` VARCHAR(50) 	NOT NULL
)ENGINE=InnoDB CHARACTER SET utf8;
ALTER TABLE `account` ADD INDEX (`group_id`);
ALTER TABLE `account` ADD UNIQUE INDEX (`email`);
ALTER TABLE `account` ADD UNIQUE INDEX (`email_activation_key`);
ALTER TABLE `account` ADD INDEX (`is_active`);
ALTER TABLE `account` ADD INDEX (`is_email_activated`);
ALTER TABLE `account` ADD CONSTRAINT `account_group_id_fk` FOREIGN KEY (`group_id`) REFERENCES `group` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;


DROP TABLE IF EXISTS `address_book`;
CREATE TABLE `address_book`
(
       `id`       			INTEGER          PRIMARY KEY AUTO_INCREMENT,
       `account_id`       	INTEGER          DEFAULT 0 REFERENCES `account`.`id`,
       `country_id`        	INTEGER     	NOT NULL REFERENCES `country`.`id`,
       `city` 				VARCHAR(255) 	NOT NULL,
       `first_name`   		VARCHAR(255)     NOT NULL,
       `last_name`    		VARCHAR(255)     DEFAULT NULL,
       `company`			VARCHAR(200) 	 DEFAULT NULL,
       `street`      		VARCHAR(255)     NOT NULL,
       `suburb`      		VARCHAR(255)     NOT NULL,
       `post_code`   		VARCHAR(100)     DEFAULT NULL,
       `is_primary` 		TINYINT(1) UNSIGNED NOT NULL DEFAULT 1,
       `is_locked` 			TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
       `longitude` 			DECIMAL(11,6) 	 DEFAULT NULL,
       `latitude` 			DECIMAL(11,6) 	 DEFAULT NULL
)ENGINE=InnoDB CHARACTER SET utf8;
ALTER TABLE `address_book` ADD INDEX (`account_id`);
ALTER TABLE `address_book` ADD INDEX (`country_id`);
ALTER TABLE `address_book` ADD INDEX (`is_primary`);
ALTER TABLE `address_book` ADD INDEX (`is_locked`);
ALTER TABLE `address_book` ADD INDEX (`first_name`);
ALTER TABLE `address_book` ADD INDEX (`last_name`);
ALTER TABLE `address_book` ADD INDEX (`longitude`);
ALTER TABLE `address_book` ADD INDEX (`latitude`);
ALTER TABLE `address_book` ADD INDEX (`city`);
ALTER TABLE address_book ADD CONSTRAINT `address_book_account_id_fk` FOREIGN KEY (`account_id`) REFERENCES `account` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE address_book ADD CONSTRAINT `address_book_country_id_fk` FOREIGN KEY (`country_id`) REFERENCES `country` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;


DROP TABLE IF EXISTS `category`;
CREATE TABLE `category`
(
    `id`       		INTEGER          PRIMARY KEY AUTO_INCREMENT,
    `category_id` 	INTEGER 	 	 NOT NULL,
    `name`         	VARCHAR(255)     NOT NULL,
	`description`  	TEXT             DEFAULT NULL,
	`url_name`  	VARCHAR(255)     NOT NULL,
	`sort_order` 	INTEGER 		 DEFAULT 0,
	`created_at` 	DATETIME 		 NOT NULL
)ENGINE=InnoDB CHARACTER SET utf8;
ALTER TABLE `category` ADD INDEX (`category_id`);
ALTER TABLE `category` ADD INDEX (`sort_order`);
ALTER TABLE `category` ADD UNIQUE INDEX (`url_name`);


DROP TABLE IF EXISTS `region`;
CREATE TABLE `region` (
  `id` int(11) NOT NULL auto_increment,
  `region` varchar(255) default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `region` (`region`)
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `country`;
CREATE TABLE `country` (
  `id` int(11) NOT NULL auto_increment,
  `region_id` int(11) NOT NULL,
  `name` varchar(20) character set latin1 NOT NULL,
  `formal_name` varchar(255) character set latin1 default NULL,
  `capital` varchar(255) character set latin1 default NULL,
  `currency_code` varchar(20) character set latin1 default NULL,
  `currency_name` varchar(100) character set latin1 default NULL,
  `telephone_prefix` varchar(20) character set latin1 default NULL,
  `domain_extension` varchar(20) character set latin1 default NULL,
  `sort_order` smallint(6) unsigned default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `id` (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `region_id` (`region_id`),
  CONSTRAINT `country_fk` FOREIGN KEY (`region_id`) REFERENCES `region` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=195 DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `content`;
CREATE TABLE `content`
(
	`id`        	INTEGER         PRIMARY KEY AUTO_INCREMENT,
    `title`       	VARCHAR(255)    NOT NULL,
    `page_title`    VARCHAR(255)    DEFAULT NULL,
    `url_name`     	VARCHAR(255)    NOT NULL,
    `keywords`     	VARCHAR(255)    DEFAULT NULL,
    `description`  	TEXT		    DEFAULT NULL,
    `content`      	TEXT            DEFAULT NULL,
    `is_system_content` TINYINT(1) UNSIGNED DEFAULT 0
)ENGINE=InnoDB CHARACTER SET utf8;
ALTER TABLE `content` ADD INDEX (`title`);
ALTER TABLE `content` ADD UNIQUE INDEX (`url_name`);
ALTER TABLE `content` ADD INDEX (`is_system_content`);


DROP TABLE IF EXISTS `email`;
CREATE TABLE `email`
(
	`id` 		INTEGER 		PRIMARY KEY AUTO_INCREMENT,
	`subject` 	VARCHAR(255) 	DEFAULT NULL,
	`message` 	TEXT		 	NOT NULL,
	`system_variables`	VARCHAR(255)    DEFAULT NULL,
	`to` 		VARCHAR(255)    DEFAULT NULL,
	`cc` 		VARCHAR(255)    DEFAULT NULL,
	`bcc` 		VARCHAR(255)    DEFAULT NULL,
	`from` 		VARCHAR(255) 	NOT NULL,
	`format` 	ENUM('plain','html') DEFAULT 'plain',
	`reply_to` 	VARCHAR(255) 	NOT NULL
)ENGINE=InnoDB CHARACTER SET utf8;


DROP TABLE IF EXISTS `group`;
CREATE TABLE `group`
(
	`id` 		INTEGER 		PRIMARY KEY AUTO_INCREMENT,
	`name` 		VARCHAR(255) 	NOT NULL
)ENGINE=InnoDB CHARACTER SET utf8;
ALTER TABLE `group` ADD UNIQUE INDEX (`name`);


DROP TABLE IF EXISTS `mime_type`;
CREATE TABLE `mime_type`
(
	`id` 			INTEGER 		PRIMARY KEY AUTO_INCREMENT,
	`extension` 	VARCHAR(255) 	NOT NULL,
	`type` 			VARCHAR(255) 	DEFAULT NULL
)ENGINE=InnoDB CHARACTER SET utf8;
ALTER TABLE `mime_type` ADD INDEX (`extension`);
ALTER TABLE `mime_type` ADD UNIQUE INDEX (`type`);


DROP TABLE IF EXISTS `image`;
CREATE TABLE `image`
(
    `id`       		INTEGER          PRIMARY KEY AUTO_INCREMENT,
	`mime_type_id` 	INTEGER 		 NOT NULL REFERENCES,
    `caption`  		VARCHAR(255)     DEFAULT NULL,
    `filename` 		VARCHAR(255)     DEFAULT NULL,
    `size`     		DOUBLE           DEFAULT NULL
)ENGINE=InnoDB CHARACTER SET utf8;
ALTER TABLE `image` ADD INDEX (`mime_type_id`);
ALTER TABLE image ADD CONSTRAINT `image_mime_type_id_fk` FOREIGN KEY (`mime_type_id`) REFERENCES `mime_type` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;


DROP TABLE IF EXISTS `banner`;
CREATE TABLE `banner`
(
	`id`        	INTEGER       PRIMARY KEY AUTO_INCREMENT,
    `name`	 		VARCHAR(255)  NOT NULL,
    `image_id`    	INTEGER 	  NOT NULL,
    `is_active`  	TINYINT(1) UNSIGNED DEFAULT 0
)ENGINE=InnoDB CHARACTER SET utf8;
ALTER TABLE `banner` ADD INDEX (`image_id`);
ALTER TABLE `banner` ADD INDEX (`is_active`);
ALTER TABLE banner ADD CONSTRAINT `banner_image_id_fk` FOREIGN KEY (`image_id`) REFERENCES `image` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;


DROP TABLE IF EXISTS `ip_bruteforce_ban`;
CREATE TABLE `ip_bruteforce_ban`
(
	`id`       		INTEGER       	PRIMARY KEY AUTO_INCREMENT,
	`ip`   			VARCHAR(200)  	NOT NULL,
    `failed_attempts` INTEGER  	  	NOT NULL DEFAULT 0,
    `banned_until`	INTEGER  		DEFAULT NULL,
    `last_attempt_at`	INTEGER  	DEFAULT NULL,
    `action`		ENUM('login','email-password')  NOT NULL
)ENGINE=InnoDB CHARACTER SET utf8;
ALTER TABLE `ip_bruteforce_ban` ADD UNIQUE INDEX (`ip`, `action`);


DROP TABLE IF EXISTS `keyword`;
CREATE TABLE `keyword`
(
    `id`       	INTEGER          PRIMARY KEY AUTO_INCREMENT,
	`keyword`   VARCHAR(255)     NOT NULL
)ENGINE=InnoDB CHARACTER SET utf8;
ALTER TABLE `keyword` ADD UNIQUE INDEX (`keyword`);


DROP TABLE IF EXISTS `order`;
CREATE TABLE `order`
(
       `id`       			INTEGER         PRIMARY KEY AUTO_INCREMENT,
       `account_id`       	INTEGER         NOT NULL,
       `delivery_address_id`INTEGER         NOT NULL,
       `billing_address_id`	INTEGER         NOT NULL,
       `referer_id`			INTEGER         DEFAULT NULL,
       `created_at`   		DATETIME        NOT NULL,
       `status`      		ENUM('pending','processed','shipped','cancelled') DEFAULT 'pending',
       `email_address`		VARCHAR(255)	DEFAULT NULL,
       `tracking_number`	VARCHAR(100)	DEFAULT NULL,
       `comments` 			MEDIUMTEXT 		DEFAULT NULL,
       `payment_method` 	ENUM('credit_card','bank_deposit') NOT NULL,
       `transaction_reference` VARCHAR(100) DEFAULT NULL,
       `reference_code` 	VARCHAR(20) 	NOT NULL,
       `shipping_total` 	DECIMAL(19,4) 	NOT NULL,
       `gst_component`		DECIMAL(19,4) 	NOT NULL,
       `amount_paid`		DECIMAL(19,4) 	NOT NULL,
       `total` 				DECIMAL(19,4) 	NOT NULL
)ENGINE=InnoDB CHARACTER SET utf8;
ALTER TABLE `order` ADD INDEX (`account_id`);
ALTER TABLE `order` ADD INDEX (`referer_id`);
ALTER TABLE `order` ADD INDEX (`status`);
ALTER TABLE `order` ADD INDEX (`delivery_address_id`);
ALTER TABLE `order` ADD INDEX (`billing_address_id`);
ALTER TABLE `order` ADD UNIQUE INDEX (`reference_code`);
ALTER TABLE `order` ADD CONSTRAINT `order_referer_id_fk` FOREIGN KEY (`referer_id`) REFERENCES `referer` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `order` ADD CONSTRAINT `order_account_id_fk` FOREIGN KEY (`account_id`) REFERENCES `account` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `order` ADD CONSTRAINT `order_delivery_address_id_fk` FOREIGN KEY (`delivery_address_id`) REFERENCES `address_book` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `order` ADD CONSTRAINT `order_billing_address_id_fk` FOREIGN KEY (`billing_address_id`) REFERENCES `address_book` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;


DROP TABLE IF EXISTS `order_product`;
CREATE TABLE `order_product`
(
       `id`       		INTEGER          PRIMARY KEY AUTO_INCREMENT,
       `order_id`  		INTEGER          NOT NULL,
       `product_id` 	INTEGER          NOT NULL,
       `quantity` 		INTEGER 		 NOT NULL DEFAULT 1,
       `cost_price` 	DECIMAL(19,4)    NOT NULL,
       `sale_price` 	DECIMAL(19,4)    NOT NULL,
       `serialized_variations` TEXT 	 DEFAULT NULL
)ENGINE=InnoDB CHARACTER SET utf8;
ALTER TABLE `order_product` ADD UNIQUE INDEX (`order_id`,`product_id`);
ALTER TABLE `order_product` ADD CONSTRAINT `order_product_order_id_fk` FOREIGN KEY (`order_id`) REFERENCES `order` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `order_product` ADD CONSTRAINT `order_product_order_billing_address_id_fk` FOREIGN KEY (`product_id`) REFERENCES `product` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;


DROP TABLE IF EXISTS `product`;
CREATE TABLE `product`
(
	`id`       		INTEGER          PRIMARY KEY AUTO_INCREMENT,
    `category_id`   INTEGER          NOT NULL,
    `image_id` 		INTEGER 		 NOT NULL DEFAULT 1,
    `name`        	VARCHAR(255)     NOT NULL,
    `description` 	MEDIUMTEXT       DEFAULT NULL,
    `cost_price` 	DECIMAL(19,4) 	 NOT NULL,
    `sale_price` 	DECIMAL(19,4) 	 NOT NULL,
    `weight`     	DOUBLE           NOT NULL,
    `code`        	VARCHAR(255)     DEFAULT NULL,
    `quantity_in_stock`	INTEGER	     DEFAULT NULL,
    `is_active`	 	TINYINT(1) UNSIGNED DEFAULT 1,
    `created_at` 	DATETIME 	 	 DEFAULT NULL,
    `url_name` 	 	VARCHAR(255) 	 NOT NULL,
    `is_featured` 	TINYINT(1) UNSIGNED DEFAULT 0
)ENGINE=InnoDB CHARACTER SET utf8;
ALTER TABLE `product` ADD INDEX (`is_active`);
ALTER TABLE `product` ADD INDEX (`is_featured`);
ALTER TABLE `product` ADD INDEX (`category_id`);
ALTER TABLE `product` ADD INDEX (`image_id`);
ALTER TABLE `product` ADD UNIQUE INDEX (`url_name`);
ALTER TABLE `product` ADD INDEX (`name`);
ALTER TABLE `product` ADD CONSTRAINT `product_category_id_fk` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `product` ADD CONSTRAINT `product_image_id_fk` FOREIGN KEY (`image_id`) REFERENCES `image` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

DROP TABLE IF EXISTS `product_keyword`;
CREATE TABLE `product_keyword`
(
	`id`       		INTEGER          PRIMARY KEY AUTO_INCREMENT,
    `keyword_id`    INTEGER          NOT NULL,
    `product_id`    INTEGER          NOT NULL,
    `frequency`   	INTEGER          NOT NULL
)ENGINE=InnoDB CHARACTER SET utf8;
ALTER TABLE `product_keyword` ADD INDEX (`keyword_id`);
ALTER TABLE `product_keyword` ADD INDEX (`product_id`);
ALTER TABLE `product_keyword` ADD UNIQUE INDEX (`product_id`,`keyword_id`);
ALTER TABLE `product_keyword` ADD CONSTRAINT `product_keyword_keyword_id_fk` FOREIGN KEY (`keyword_id`) REFERENCES `keyword` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `product_keyword` ADD CONSTRAINT `product_keyword_product_id_fk` FOREIGN KEY (`product_id`) REFERENCES `product` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;


DROP TABLE IF EXISTS `product_variant`;
CREATE TABLE `product_variant`
(
	`id`       			INTEGER          PRIMARY KEY AUTO_INCREMENT,
    `variant_group_id` 	INTEGER          NOT NULL,
    `name`        		VARCHAR(255)     NOT NULL,
    `value` 			VARCHAR(200)     NOT NULL
)ENGINE=InnoDB CHARACTER SET utf8;
ALTER TABLE `product_variant` ADD INDEX (`variant_group_id`);
ALTER TABLE `product_variant` ADD CONSTRAINT `product_variant_variant_group_id_fk` FOREIGN KEY (`variant_group_id`) REFERENCES `variant_group` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;


DROP TABLE IF EXISTS `product_variant_group`;
CREATE TABLE `product_variant_group`
(
	`id`       			INTEGER          PRIMARY KEY AUTO_INCREMENT,
    `product_id`       	INTEGER          NOT NULL,
    `variant_group_id` 	INTEGER          NOT NULL
)ENGINE=InnoDB CHARACTER SET utf8;
ALTER TABLE `product_variant_group` ADD INDEX (`product_id`);
ALTER TABLE `product_variant_group` ADD INDEX (`variant_group_id`);
ALTER TABLE `product_variant_group` ADD CONSTRAINT `product_variant_group_variant_group_id_fk` FOREIGN KEY (`variant_group_id`) REFERENCES `variant_group` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `product_variant_group` ADD CONSTRAINT `product_variant_group_product_id_fk` FOREIGN KEY (`product_id`) REFERENCES `product` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;


DROP TABLE IF EXISTS `product_view`;
CREATE TABLE `product_view`
(
	`id`       			INTEGER          PRIMARY KEY AUTO_INCREMENT,
    `product_id` 		INTEGER          NOT NULL REFERENCES `product`.`id`,
    `view_count`   		INTEGER 	     NOT NULL DEFAULT 0,
    `viewed_on`			DATE 	     	 NOT NULL
)ENGINE=InnoDB CHARACTER SET utf8;
ALTER TABLE `product_view` ADD INDEX (`product_id`);
ALTER TABLE `product_view` ADD UNIQUE INDEX (`product_id`,`viewed_on`);
ALTER TABLE `product_view` ADD CONSTRAINT `product_view_product_id_fk` FOREIGN KEY (`product_id`) REFERENCES `product` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;


-- Product review
DROP TABLE IF EXISTS `product_review`;
CREATE TABLE `product_review`
(
	`id`       			INTEGER          PRIMARY KEY AUTO_INCREMENT,
    `product_id` 		INTEGER          NOT NULL REFERENCES `product`.`id`,
    `account_id` 		INTEGER          NOT NULL REFERENCES `account`.`id`,
    `rating`   			INTEGER 	     NOT NULL,
    `comment`			TEXT 	     	 NOT NULL,
    `reviewed_at` 		DATETIME 		 NOT NULL
)ENGINE=InnoDB CHARACTER SET utf8;
ALTER TABLE product_review ADD INDEX (`product_id`);
ALTER TABLE product_review ADD INDEX (`account_id`);
ALTER TABLE product_review ADD INDEX (`reviewed_at`);
ALTER TABLE product_review ADD INDEX (`rating`);
ALTER TABLE product_review ADD UNIQUE INDEX (`product_id`,`account_id`);
ALTER TABLE `product_review` ADD CONSTRAINT `product_review_product_id_fk` FOREIGN KEY (`product_id`) REFERENCES `product` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `product_review` ADD CONSTRAINT `product_review_account_id_fk` FOREIGN KEY (`account_id`) REFERENCES `account` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;


DROP TABLE IF EXISTS `referer`;
CREATE TABLE `referer`
(
    `id`       		INTEGER          PRIMARY KEY AUTO_INCREMENT,
    `url`         	VARCHAR(255)     NOT NULL,
    `hits` 			INTEGER 		 DEFAULT 1
)ENGINE=InnoDB CHARACTER SET utf8;
ALTER TABLE `referer` ADD UNIQUE INDEX (`url`);
ALTER TABLE `referer` ADD INDEX (`hits`);


DROP TABLE IF EXISTS `shipping_tier`;
CREATE TABLE `shipping_tier`
(
	`id`       		INTEGER       	PRIMARY KEY AUTO_INCREMENT,
	`shipping_zone_id` INTEGER 		NOT NULL REFERENCES `shipping_zone`.`id`,
	`max_weight` 	DOUBLE  		NOT NULL,
	`amount` 	 	DECIMAL(19,4) 	NOT NULL
)ENGINE=InnoDB CHARACTER SET utf8;
ALTER TABLE `shipping_tier` ADD INDEX (`shipping_zone_id`,`max_weight`);
ALTER TABLE `shipping_tier` ADD CONSTRAINT `shipping_tier_shipping_zone_id_fk` FOREIGN KEY (`shipping_zone_id`) REFERENCES `shipping_zone` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;


DROP TABLE IF EXISTS `shipping_zone`;
CREATE TABLE `shipping_zone`
(
	`id`       	INTEGER       PRIMARY KEY AUTO_INCREMENT,
	`name`   	VARCHAR(200)  NOT NULL
)ENGINE=InnoDB CHARACTER SET utf8;
ALTER TABLE `shipping_zone` ADD UNIQUE INDEX (`name`);


DROP TABLE IF EXISTS `variant_group`;
CREATE TABLE `variant_group`
(
	`id`       		INTEGER          PRIMARY KEY AUTO_INCREMENT,
    `name`        	VARCHAR(255)     NOT NULL,
    `label`			VARCHAR(255)     NOT NULL,
    `description`	TEXT             DEFAULT NULL
)ENGINE=InnoDB CHARACTER SET utf8;
ALTER TABLE `variant_group` ADD INDEX (`name`);
ALTER TABLE variant_group ADD INDEX (`label`)