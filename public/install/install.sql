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

INSERT INTO `category` (`id`, `category_id`, `name`, `description`, `url_name`, `sort_order`) VALUES (1,0,'Example','Example category','example',0);


DROP TABLE IF EXISTS `region`;
CREATE TABLE `region` (
  `id` int(11) NOT NULL auto_increment,
  `region` varchar(255) default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `region` (`region`)
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8;

INSERT INTO `region` (`id`, `region`) VALUES 
  (6,'Africa'),
  (1,'Asia'),
  (10,'Central America and the Caribbean'),
  (4,'Europe'),
  (22,'Middle East'),
  (29,'North America'),
  (7,'Oceania'),
  (13,'South America'),
  (38,'Southeast Asia');


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


INSERT INTO `country` (`id`, `region_id`, `name`, `formal_name`, `capital`, `currency_code`, `currency_name`, `telephone_prefix`, `domain_extension`, `sort_order`) VALUES 
  (1,1,'Afghanistan','Islamic State of Afghanistan','Kabul','AFN','Afghani','93','.af',0),
  (2,4,'Albania','Republic of Albania','Tirana','ALL','Lek','355','.al',0),
  (3,6,'Algeria','People''s Democratic Republic of Algeria','Algiers','DZD','Dinar','213','.dz',0),
  (4,4,'Andorra','Principality of Andorra','Andorra la Vella','EUR','Euro','376','.ad',0),
  (5,6,'Angola','Republic of Angola','Luanda','AOA','Kwanza','244','.ao',0),
  (7,13,'Argentina','Argentine Republic','Buenos Aires','ARS','Peso','54','.ar',0),
  (8,1,'Armenia','Republic of Armenia','Yerevan','AMD','Dram','374','.am',0),
  (9,7,'Australia','Commonwealth of Australia','Canberra','AUD','Dollar','61','.au',3),
  (10,4,'Austria','Republic of Austria','Vienna','EUR','Euro','43','.at',0),
  (11,1,'Azerbaijan','Republic of Azerbaijan','Baku','AZN','Manat','994','.az',0),
  (12,10,'Bahamas','Commonwealth of The Bahamas','Nassau','BSD','Dollar','-241','.bs',0),
  (13,22,'Bahrain','Kingdom of Bahrain','Manama','BHD','Dinar','973','.bh',0),
  (14,1,'Bangladesh','People''s Republic of Bangladesh','Dhaka','BDT','Taka','880','.bd',0),
  (15,10,'Barbados','','Bridgetown','BBD','Dollar','-245','.bb',0),
  (16,1,'Belarus','Republic of Belarus','Minsk','BYR','Ruble','375','.by',0),
  (17,4,'Belgium','Kingdom of Belgium','Brussels','EUR','Euro','32','.be',0),
  (18,10,'Belize','','Belmopan','BZD','Dollar','501','.bz',0),
  (19,6,'Benin','Republic of Benin','Porto-Novo','XOF','Franc','229','.bj',0),
  (20,1,'Bhutan','Kingdom of Bhutan','Thimphu','BTN','Ngultrum','975','.bt',0),
  (21,13,'Bolivia','Republic of Bolivia','La Paz (administrative/legislative) and Sucre (judical)','BOB','Boliviano','591','.bo',0),
  (22,4,'Bosnia and Herzegovi','','Sarajevo','BAM','Marka','387','.ba',0),
  (23,6,'Botswana','Republic of Botswana','Gaborone','BWP','Pula','267','.bw',0),
  (24,13,'Brazil','Federative Republic of Brazil','Brasilia','BRL','Real','55','.br',0),
  (25,38,'Brunei','Negara Brunei Darussalam','Bandar Seri Begawan','BND','Dollar','673','.bn',0),
  (26,4,'Bulgaria','Republic of Bulgaria','Sofia','BGN','Lev','359','.bg',0),
  (27,6,'Burkina Faso','','Ouagadougou','XOF','Franc','226','.bf',0),
  (28,6,'Burundi','Republic of Burundi','Bujumbura','BIF','Franc','257','.bi',0),
  (29,38,'Cambodia','Kingdom of Cambodia','Phnom Penh','KHR','Riels','855','.kh',0),
  (30,6,'Cameroon','Republic of Cameroon','Yaounde','XAF','Franc','237','.cm',0),
  (31,29,'Canada','','Ottawa','CAD','Dollar','1','.ca',0),
  (34,6,'Chad','Republic of Chad','N''Djamena','XAF','Franc','235','.td',0),
  (35,13,'Chile','Republic of Chile','Santiago (administrative/judical) and Valparaiso (legislative)','CLP','Peso','56','.cl',0),
  (37,13,'Colombia','Republic of Colombia','Bogota','COP','Peso','57','.co',0),
  (38,6,'Comoros','Union of Comoros','Moroni','KMF','Franc','269','.km',0),
  (40,6,'Congo','Republic of the Congo','Brazzaville','XAF','Franc','242','.cg',0),
  (41,10,'Costa Rica','Republic of Costa Rica','San Jose','CRC','Colon','506','.cr',0),
  (43,4,'Croatia','Republic of Croatia','Zagreb','HRK','Kuna','385','.hr',0),
  (44,10,'Cuba','Republic of Cuba','Havana','CUP','Peso','53','.cu',0),
  (45,22,'Cyprus','Republic of Cyprus','Nicosia','CYP','Pound','357','.cy',0),
  (46,4,'Czech Republic','','Prague','CZK','Koruna','420','.cz',0),
  (47,4,'Denmark','Kingdom of Denmark','Copenhagen','DKK','Krone','45','.dk',0),
  (48,6,'Djibouti','Republic of Djibouti','Djibouti','DJF','Franc','253','.dj',0),
  (49,10,'Dominica','Commonwealth of Dominica','Roseau','XCD','Dollar','-766','.dm',0),
  (50,10,'Dominican Republic','','Santo Domingo','DOP','Peso','-808','.do',0),
  (51,13,'Ecuador','Republic of Ecuador','Quito','USD','Dollar','593','.ec',0),
  (52,6,'Egypt','Arab Republic of Egypt','Cairo','EGP','Pound','20','.eg',0),
  (53,10,'El Salvador','Republic of El Salvador','San Salvador','USD','Dollar','503','.sv',0),
  (54,6,'Equatorial Guinea','Republic of Equatorial Guinea','Malabo','XAF','Franc','240','.gq',0),
  (55,6,'Eritrea','State of Eritrea','Asmara','ERN','Nakfa','291','.er',0),
  (56,4,'Estonia','Republic of Estonia','Tallinn','EEK','Kroon','372','.ee',0),
  (57,6,'Ethiopia','Federal Democratic Republic of Ethiopia','Addis Ababa','ETB','Birr','251','.et',0),
  (58,7,'Fiji','Republic of the Fiji Islands','Suva','FJD','Dollar','679','.fj',0),
  (59,4,'Finland','Republic of Finland','Helsinki','EUR','Euro','358','.fi',0),
  (60,4,'France','French Republic','Paris','EUR','Euro','33','.fr',0),
  (61,6,'Gabon','Gabonese Republic','Libreville','XAF','Franc','241','.ga',0),
  (62,6,'Gambia','Republic of The Gambia','Banjul','GMD','Dalasi','220','.gm',0),
  (63,29,'Georgia','Republic of Georgia','Tbilisi','GEL','Lari','995','.ge',0),
  (64,4,'Germany','Federal Republic of Germany','Berlin','EUR','Euro','49','.de',0),
  (65,6,'Ghana','Republic of Ghana','Accra','GHS','Cedi','233','.gh',0),
  (66,4,'Greece','Hellenic Republic','Athens','EUR','Euro','30','.gr',0),
  (67,10,'Grenada','','Saint George''s','XCD','Dollar','-472','.gd',0),
  (68,10,'Guatemala','Republic of Guatemala','Guatemala','GTQ','Quetzal','502','.gt',0),
  (69,6,'Guinea','Republic of Guinea','Conakry','GNF','Franc','224','.gn',0),
  (70,6,'Guinea-Bissau','Republic of Guinea-Bissau','Bissau','XOF','Franc','245','.gw',0),
  (71,13,'Guyana','Co-operative Republic of Guyana','Georgetown','GYD','Dollar','592','.gy',0),
  (72,10,'Haiti','Republic of Haiti','Port-au-Prince','HTG','Gourde','509','.ht',0),
  (73,10,'Honduras','Republic of Honduras','Tegucigalpa','HNL','Lempira','504','.hn',0),
  (74,4,'Hungary','Republic of Hungary','Budapest','HUF','Forint','36','.hu',0),
  (75,1,'Iceland','Republic of Iceland','Reykjavik','ISK','Krona','354','.is',0),
  (76,1,'India','Republic of India','New Delhi','INR','Rupee','91','.in',0),
  (77,38,'Indonesia','Republic of Indonesia','Jakarta','IDR','Rupiah','62','.id',0),
  (78,22,'Iran','Islamic Republic of Iran','Tehran','IRR','Rial','98','.ir',0),
  (79,22,'Iraq','Republic of Iraq','Baghdad','IQD','Dinar','964','.iq',0),
  (80,4,'Ireland','','Dublin','EUR','Euro','353','.ie',0),
  (81,22,'Israel','State of Israel','Jerusalem','ILS','Shekel','972','.il',0),
  (82,4,'Italy','Italian Republic','Rome','EUR','Euro','39','.it',0),
  (83,10,'Jamaica','','Kingston','JMD','Dollar','-875','.jm',0),
  (84,1,'Japan','','Tokyo','JPY','Yen','81','.jp',0),
  (85,22,'Jordan','Hashemite Kingdom of Jordan','Amman','JOD','Dinar','962','.jo',0),
  (86,1,'Kazakhstan','Republic of Kazakhstan','Astana','KZT','Tenge','7','.kz',0),
  (87,6,'Kenya','Republic of Kenya','Nairobi','KES','Shilling','254','.ke',0),
  (88,7,'Kiribati','Republic of Kiribati','Tarawa','AUD','Dollar','686','.ki',0),
  (89,1,'Korea','Democratic People''s Republic of Korea','Pyongyang','KPW','Won','850','.kp',0),
  (90,1,'Korea, Republic of  ','Republic of Korea','Seoul','KRW','Won','82','.kr',0),
  (91,22,'Kuwait','State of Kuwait','Kuwait','KWD','Dinar','965','.kw',0),
  (92,1,'Kyrgyzstan','Kyrgyz Republic','Bishkek','KGS','Som','996','.kg',0),
  (93,38,'Laos','Lao People''s Democratic Republic','Vientiane','LAK','Kip','856','.la',0),
  (94,4,'Latvia','Republic of Latvia','Riga','LVL','Lat','371','.lv',0),
  (95,22,'Lebanon','Lebanese Republic','Beirut','LBP','Pound','961','.lb',0),
  (96,6,'Lesotho','Kingdom of Lesotho','Maseru','LSL','Loti','266','.ls',0),
  (97,6,'Liberia','Republic of Liberia','Monrovia','LRD','Dollar','231','.lr',0),
  (98,6,'Libya','Great Socialist People''s Libyan Arab Jamahiriya','Tripoli','LYD','Dinar','218','.ly',0),
  (99,4,'Liechtenstein','Principality of Liechtenstein','Vaduz','CHF','Franc','423','.li',0),
  (100,4,'Lithuania','Republic of Lithuania','Vilnius','LTL','Litas','370','.lt',0),
  (101,4,'Luxembourg','Grand Duchy of Luxembourg','Luxembourg','EUR','Euro','352','.lu',0),
  (102,4,'Macedonia','Republic of Macedonia','Skopje','MKD','Denar','389','.mk',0),
  (103,6,'Madagascar','Republic of Madagascar','Antananarivo','MGA','Ariary','261','.mg',0),
  (104,6,'Malawi','Republic of Malawi','Lilongwe','MWK','Kwacha','265','.mw',0),
  (105,38,'Malaysia','','Kuala Lumpur (legislative/judical) and Putrajaya (administrative)','MYR','Ringgit','60','.my',0),
  (106,1,'Maldives','Republic of Maldives','Male','MVR','Rufiyaa','960','.mv',0),
  (107,6,'Mali','Republic of Mali','Bamako','XOF','Franc','223','.ml',0),
  (108,4,'Malta','Republic of Malta','Valletta','MTL','Lira','356','.mt',0),
  (109,7,'Marshall Islands','Republic of the Marshall Islands','Majuro','USD','Dollar','692','.mh',0),
  (110,6,'Mauritania','Islamic Republic of Mauritania','Nouakchott','MRO','Ouguiya','222','.mr',0),
  (112,29,'Mexico','United Mexican States','Mexico','MXN','Peso','52','.mx',0),
  (113,7,'Micronesia','Federated States of Micronesia','Palikir','USD','Dollar','691','.fm',0),
  (114,1,'Moldova','Republic of Moldova','Chisinau','MDL','Leu','373','.md',0),
  (115,4,'Monaco','Principality of Monaco','Monaco','EUR','Euro','377','.mc',0),
  (116,1,'Mongolia','','Ulaanbaatar','MNT','Tugrik','976','.mn',0),
  (117,4,'Montenegro','Republic of Montenegro','Podgorica','EUR','Euro','382','.me',0),
  (118,6,'Morocco','Kingdom of Morocco','Rabat','MAD','Dirham','212','.ma',0),
  (119,6,'Mozambique','Republic of Mozambique','Maputo','MZM','Meticail','258','.mz',0),
  (120,38,'Myanmar (Burma)','Union of Myanmar','Naypyidaw','MMK','Kyat','95','.mm',0),
  (121,6,'Namibia','Republic of Namibia','Windhoek','NAD','Dollar','264','.na',0),
  (122,7,'Nauru','Republic of Nauru','Yaren','AUD','Dollar','674','.nr',0),
  (123,1,'Nepal','','Kathmandu','NPR','Rupee','977','.np',0),
  (124,4,'Netherlands','Kingdom of the Netherlands','Amsterdam (administrative) and The Hague (legislative/judical)','EUR','Euro','31','.nl',0),
  (125,7,'New Zealand','','Wellington','NZD','Dollar','64','.nz',5),
  (126,10,'Nicaragua','Republic of Nicaragua','Managua','NIO','Cordoba','505','.ni',0),
  (127,6,'Niger','Republic of Niger','Niamey','XOF','Franc','227','.ne',0),
  (128,6,'Nigeria','Federal Republic of Nigeria','Abuja','NGN','Naira','234','.ng',0),
  (129,4,'Norway','Kingdom of Norway','Oslo','NOK','Krone','47','.no',0),
  (130,22,'Oman','Sultanate of Oman','Muscat','OMR','Rial','968','.om',0),
  (131,1,'Pakistan','Islamic Republic of Pakistan','Islamabad','PKR','Rupee','92','.pk',0),
  (132,7,'Palau','Republic of Palau','Melekeok','USD','Dollar','680','.pw',0),
  (133,10,'Panama','Republic of Panama','Panama','PAB','Balboa','507','.pa',0),
  (134,7,'Papua New Guinea','Independent State of Papua New Guinea','Port Moresby','PGK','Kina','675','.pg',0),
  (135,13,'Paraguay','Republic of Paraguay','Asuncion','PYG','Guarani','595','.py',0),
  (136,13,'Peru','Republic of Peru','Lima','PEN','Sol','51','.pe',0),
  (137,38,'Philippines','Republic of the Philippines','Manila','PHP','Peso','63','.ph',0),
  (138,4,'Poland','Republic of Poland','Warsaw','PLN','Zloty','48','.pl',0),
  (139,4,'Portugal','Portuguese Republic','Lisbon','EUR','Euro','351','.pt',0),
  (140,22,'Qatar','State of Qatar','Doha','QAR','Rial','974','.qa',0),
  (141,4,'Romania','','Bucharest','RON','Leu','40','.ro',0),
  (142,1,'Russia','Russian Federation','Moscow','RUB','Ruble','7','.ru',0),
  (143,6,'Rwanda','Republic of Rwanda','Kigali','RWF','Franc','250','.rw',0),
  (145,10,'Saint Lucia','','Castries','XCD','Dollar','-757','.lc',0),
  (147,7,'Samoa','Independent State of Samoa','Apia','WST','Tala','685','.ws',0),
  (148,4,'San Marino','Republic of San Marino','San Marino','EUR','Euro','378','.sm',0),
  (150,22,'Saudi Arabia','Kingdom of Saudi Arabia','Riyadh','SAR','Rial','966','.sa',0),
  (151,6,'Senegal','Republic of Senegal','Dakar','XOF','Franc','221','.sn',0),
  (152,1,'Serbia','Republic of Serbia','Belgrade','RSD','Dinar','381','.rs',0),
  (153,6,'Seychelles','Republic of Seychelles','Victoria','SCR','Rupee','248','.sc',0),
  (154,6,'Sierra Leone','Republic of Sierra Leone','Freetown','SLL','Leone','232','.sl',0),
  (155,38,'Singapore','Republic of Singapore','Singapore','SGD','Dollar','65','.sg',0),
  (156,4,'Slovakia','Slovak Republic','Bratislava','SKK','Koruna','421','.sk',0),
  (157,4,'Slovenia','Republic of Slovenia','Ljubljana','EUR','Euro','386','.si',0),
  (158,7,'Solomon Islands','','Honiara','SBD','Dollar','677','.sb',0),
  (159,6,'Somalia','','Mogadishu','SOS','Shilling','252','.so',0),
  (160,6,'South Africa','Republic of South Africa','Pretoria (administrative), Cape Town (legislative), and Bloemfontein (judical)','ZAR','Rand','27','.za',0),
  (161,4,'Spain','Kingdom of Spain','Madrid','EUR','Euro','34','.es',0),
  (162,1,'Sri Lanka','Democratic Socialist Republic of Sri Lanka','Colombo (administrative/judical) and Sri Jayewardenepura Kotte (legislative)','LKR','Rupee','94','.lk',0),
  (163,6,'Sudan','Republic of the Sudan','Khartoum','SDG','Pound','249','.sd',0),
  (164,13,'Suriname','Republic of Suriname','Paramaribo','SRD','Dollar','597','.sr',0),
  (165,6,'Swaziland','Kingdom of Swaziland','Mbabane (administrative) and Lobamba (legislative)','SZL','Lilangeni','268','.sz',0),
  (166,4,'Sweden','Kingdom of Sweden','Stockholm','SEK','Kronoa','46','.se',0),
  (167,4,'Switzerland','Swiss Confederation','Bern','CHF','Franc','41','.ch',0),
  (168,22,'Syria','Syrian Arab Republic','Damascus','SYP','Pound','963','.sy',0),
  (169,1,'Tajikistan','Republic of Tajikistan','Dushanbe','TJS','Somoni','992','.tj',0),
  (170,6,'Tanzania','United Republic of Tanzania','Dar es Salaam (administrative/judical) and Dodoma (legislative)','TZS','Shilling','255','.tz',0),
  (171,38,'Thailand','Kingdom of Thailand','Bangkok','THB','Baht','66','.th',0),
  (172,38,'East Timor','Democratic Republic of Timor-Leste','Dili','USD','Dollar','670','.tp',0),
  (173,6,'Togo','Togolese Republic','Lome','XOF','Franc','228','.tg',0),
  (174,7,'Tonga','Kingdom of Tonga','Nuku''alofa','TOP','Pa''anga','676','.to',0),
  (175,10,'Trinidad and Tobago','Republic of Trinidad and Tobago','Port-of-Spain','TTD','Dollar','-867','.tt',0),
  (176,6,'Tunisia','Tunisian Republic','Tunis','TND','Dinar','216','.tn',0),
  (177,22,'Turkey','Republic of Turkey','Ankara','TRY','Lira','90','.tr',0),
  (178,1,'Turkmenistan','','Ashgabat','TMM','Manat','993','.tm',0),
  (179,7,'Tuvalu','','Funafuti','AUD','Dollar','688','.tv',0),
  (180,6,'Uganda','Republic of Uganda','Kampala','UGX','Shilling','256','.ug',0),
  (181,1,'Ukraine','','Kiev','UAH','Hryvnia','380','.ua',0),
  (182,22,'United Arab Emirates','United Arab Emirates','Abu Dhabi','AED','Dirham','971','.ae',0),
  (183,4,'United Kingdom','United Kingdom of Great Britain and Northern Ireland','London','GBP','Pound','44','.uk',0),
  (184,29,'United States','United States of America','Washington','USD','Dollar','1','.us',4),
  (185,13,'Uruguay','Oriental Republic of Uruguay','Montevideo','UYU','Peso','598','.uy',0),
  (186,1,'Uzbekistan','Republic of Uzbekistan','Tashkent','UZS','Som','998','.uz',0),
  (187,7,'Vanuatu','Republic of Vanuatu','Port-Vila','VUV','Vatu','678','.vu',0),
  (188,4,'Vatican City','State of the Vatican City','Vatican City','EUR','Euro','379','.va',0),
  (189,13,'Venezuela','Bolivarian Republic of Venezuela','Caracas','VEB','Bolivar','58','.ve',0),
  (190,38,'Vietnam','Socialist Republic of Vietnam','Hanoi','VND','Dong','84','.vn',0),
  (191,22,'Yemen','Republic of Yemen','Sanaa','YER','Rial','967','.ye',0),
  (192,6,'Zambia','Republic of Zambia','Lusaka','ZMK','Kwacha','260','.zm',0),
  (193,6,'Zimbabwe','Republic of Zimbabwe','Harare','ZWD','Dollar','263','.zw',0),
  (194,1,'China','Republic of China','Taipei','TWD','Dollar','886','.tw',0);


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


INSERT INTO `content` (`id`, `title`, `page_title`, `url_name`, `keywords`, `description`, `content`, `is_system_content`) VALUES 
  (1,'Business with eCommerce',NULL,'home','efusion, ecommerce, products, online, free','The simple way to get your business selling online','eFusion is the all in one integrated solution for your online selling market. eFusion offers best of bread security and ease of use.  If you wish to know more, have a browse around this online demo site. Just some of the features include, product catalogue, shopping cart, content management, visitor and order statistics, advanced search and integrated credit card processing which make it one of the most modern and simple to setup solutions on the market today.',1),
  (2,'Terms & Conditions',NULL,'terms-and-conditions','Terms, Conditions, Privacy, Copyright','Terms and conditions of use','<div align=\"justify\">The following Terms and Conditions / Privacy Statement apply \r\n  to your use of the eFusion ecommerce website. </div>\r\n<p align=\"justify\"><strong>Intellectual Property Rights</strong><br />\r\n  This website is owned and operated by eFusion. The rights in the designs, the \r\n  pictures, photographs and content of this website are owned by eFusion, and are \r\n  protected by intellectual property rights. You are permitted to view the content \r\n  of this website on screen. You are also permitted to print or download extracts \r\n  to your hard disk for your own private and domestic use or private study only, \r\n  unless otherwise expressly stated. You must not use any extracts of this website \r\n  for any commercial purpose or on any other website.</p>\r\n All material on this website is copyright of eFusion, except where otherwise expressly \r\n  stated. The name \'eFusion\' together with the following have been registered as \r\n  trade marks of eFusion:<br />\r\n  Not withstanding any other provision of these terms and conditions / privacy statement, \r\n  these trademarks must not be used for any purpose, without the prior written \r\n  consent of eFusion.</p>\r\n<p align=\"justify\"><strong>Accuracy of information</strong><br />\r\n  The information contained in this website is for information purposes only and \r\n  is provided free of charge. Whilst reasonable skill and care has been exercised \r\n  in its compilation, no representation or warranty (express or implied) is given \r\n  as to its accuracy, freedom from error or reliability.<br />\r\n  eFusion shall have no liability for any direct, indirect, incidental or consequential \r\n  damage resulting from the use of this website, the inability to use this website \r\n  or any defect in it, including but not limited to any loss of profits, reputation, \r\n  or data, even if they have been advised of the possibility of such damage. Save \r\n  that the exclusions of liability set out in this clause shall not apply to personal \r\n  injury or death of any person resulting from the negligence of eFusion or its \r\n  servants or agents.</p>\r\n<p align=\"justify\"> <strong>Governing law and jurisdiction</strong><br />\r\n  The terms and your use of this website are governed by and construed in accordance \r\n  with the laws of New Zealand and any disputes will be decided only by the Courts \r\n  of New Zealand.<br />\r\n  Disclaimer for other websites<br />\r\n  eFusion offer hyperlinks to other websites for your convenience. In using one \r\n  of these hyperlinks it is considered that you are leaving the eFusion website, \r\n  therefore eFusion does not endorse any other websites or offer any guarantees \r\n  or accept any responsibility for any such websites.</p>\r\n<p align=\"justify\"><strong>Privacy / Personal information</strong><br />\r\n  eFusion is committed to safeguarding your privacy online. This policy only covers \r\n  this website. Links within this website to other websites are not covered by \r\n  this policy. Users should check the Terms and Conditions of any other website \r\n  they visit.<br />\r\n  You can access our website home page and browse our website without disclosing \r\n  your personal data. Our website does not enable our visitors to communicate \r\n  with other visitors or to post information to be accessed by others.<br />\r\n  eFusion collects the following information regarding visitors to our website: \r\n  IP addresses, information regarding what pages are accessed and when. We use \r\n  your IP address to help us to track usage behaviour and compile aggregate data \r\n  that will allow content and navigation improvement of our website.<br />\r\n  Our website uses email forms for visitors to request information on services \r\n  and policy information. We collect visitors\' contact information (i.e. email \r\n and postal address) and unique identifiers (i.e. National Insurance Number).<br />\r\n  Contact information from any email form is used to send information to our visitors \r\n  as specifically requested by them. Visitors\' contact information and unique \r\n  identities will not be shared with third parties other than eFusion.<br />\r\n  eFusion places great importance on the security of all personally identifiable \r\n  information associated with our visitors. We have security measures in place \r\n  to protect against the loss, misuse and alteration of personal data under our \r\n  control. We have organisational and technical security measures in place to \r\n  safeguard your personal information, and we are registered data controllers \r\n  under the Data Protection Act 1998.<br />\r\n  Emails are stored for a maximum of 90 days for reference purposes.</p>\r\n<p align=\"justify\"> <strong>Consent</strong><br />\r\n  You agree to use our website only for lawful purposes. You agree that you will \r\n  not bring or use on our website any viruses or any other computer programming \r\n  routine which damage, interfere with, surreptitiously intercept or expropriate \r\n  any system, data or personal information.<br />\r\n  By agreeing to these Terms and Conditions you agree that all information provided \r\n  by you is complete, accurate and honest and that you will at all times use the \r\n  website with common sense and care. eFusion reserves the right to change these \r\n  Terms and Conditions from time to time.<br />\r\n  By continuing to use this website it is implied that you accept the Terms and \r\n  Conditions / Privacy Statement set out above. </p>',1),
  (3,'Contact Us',NULL,'contact-us','Contact, Phone, Email','Contact the company','Please fill out the form below and we will be in contact with you as soon as possible.',1),
  (4,'Privacy Policy',NULL,'privacy-policy','Privacy, Policy','Product returns information','For each visitor to our Web page, our Web server automatically recognizes only the customer\'s domain name (where possible), but not the e-mail address. We collect the domain name, but not the e-mail address of visitors to our web page, the e-mail addresses of those who communicate with us via e-mail, aggregate information on what pages customers access or visit and information volunteered by the customer, such as survey information and/or site registrations. The information we collect is used for such purposes as improving our Web site, notifying customers about updates to our Web site or information, and contacting customers for marketing purposes. It is not shared with other persons or organizations for commercial purposes. If you do not want to receive e-mail from us in the future, please let us know by sending an email to us and telling us that you do not want to receive e-mail from our company. If you supply us with your postal address on-line you may receive periodic mailings from us with information on new products and services or upcoming events. If you do not wish to receive such mailings, please let us know by sending an email to us at the above address. Please provide us with your exact name and address. Persons who supply us with their telephone numbers on-line may receive telephone contact from us with information regarding orders they have placed on-line. Please provide us with your correct phone number. Please keep us informed of any changes or corrections to your information and/or status.',1),
  (5,'404 - Page not found',NULL,'page-not-found',NULL,'Page not found, 404 error','<h3>We’re sorry, but we can’t find what you’re looking for.</h3><p><p>The page or file you are looking for was not found on our site. It is possible that you clicked a link that is out of date, or typed in the address incorrectly.</p>\r\n\r\n<ul class=\"spaced\">\r\n<li>If you typed in the address, please double check the spelling.</li>\r\n<li>If you followed a link from somewhere, please let us know at <a href=\"mailto:webmaster@efusion.co.nz\" title=\"Page Not Found on Mozilla.com\">webmaster@efusion.co.nz</a>. Tell us where you came from and what you were looking for, and we will do our best to fix it.</li>\r\n</ul></p>',1),
  (6,'Confirm Your Order',NULL,'confirm-order',NULL,NULL,'Please review your order details below before submitting. Your order and payment will be processed immediately. If paying by credit-card, please allow up to 15 seconds for payment processing. If paying by direct bank deposit, the bank details and payment instructions will be sent to you by email after you place this order.',1),
  (7,'Order completed','Order Complete','order-complete',NULL,NULL,'Your order has been completed. A receipt for your order has been sent to <b>{email_address}</b> for your records. You can also view your previous orders in the My Account section of the site at any time.\r\n\r\n<br /><br />\r\nThank you for shopping with us!\r\n\r\n',1);


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

INSERT INTO `email` (`id`, `subject`, `message`, `system_variables`, `to`, `cc`, `bcc`, `from`, `format`, `reply_to`) VALUES 
  (1,'Password has been reset','Your password has been reset to {password} \r\nYou can now login with this password and change it in your members area.\r\n\r\nRegards, {site_title} - {domain_name}','{password}, {site_title}, {domain_name}',NULL,NULL,NULL,'support@efusion.co.nz','plain','support@efusion.co.nz'),
  (2,'Store enquiry','A visitor to your site has made the following enquiry\r\n\r\nContact Details\r\n..........................\r\nFull Name: {name}\r\nEmail: {email}\r\nPhone: {phone}\r\n\r\nEnquiry information\r\n..........................\r\nSubject: {subject} Enquiry\r\nEnquiry: {enquiry}','{name}, {email}, {phone}, {subject}, {enquiry}',NULL,NULL,NULL,'enquiries@efusion.co.nz','plain','enquiries@efusion.co.nz'),
  (3,'Account has been created','Welcome to {site_title}.\r\n\r\nPlease validate your email address by clicking this link: http://{domain_name}/store/activate-account/{activation_key}\r\nAlternately you can simply copy and paste the above URL into your browser to validate your email. \r\n\r\nOnce your email is validated, you can take advantage of the following features:\r\n\r\n1. Permanent Shopping Cart - Any products added to your online cart remain there until you remove them, or check them out.\r\n2. Order History and status - View your history of purchases that you have made with us and track the status of your current orders.\r\n3. Products Reviews - Share your opinions on products with our other customers (requires activation).\r\n\r\nFor help with any of our online services, please email the store-owner: {site_email}.\r\n\r\nThank you for choosing {site_title} and we hope you enjoy your online shopping experience!\r\n\r\nhttp://{domain_name}','{site_title}, {site_email}, {domain_name}','',NULL,NULL,'support@efusion.co.nz','plain','support@efusion.co.nz'),
  (4,'Order receipt','Your order #{order_number} has been placed. Below is a summary of your order, please keep a copy of this email for your records.\r\n\r\n{order_summary}\r\n\r\nThankyou for your order!','{order_summary}, {order_number}',NULL,NULL,'orders@efusion.co.nz','orders@efusion.co.nz','plain','orders@efusion.co.nz'),
  (5,'Your order has been updated','Your order #{order_number} has been updated. \r\n\r\nAmount Paid: ${amount_paid}\r\nTracking Number: {tracking_number}\r\nOrder Status: {status}\r\nComments: {comments}\r\n\r\nYou can view your order status online at any time by simply logging into your account and clicking \r\non the My Order link.','{order_number}, {amount_paid}, {tracking_number}, {status}, {comments}',NULL,NULL,NULL,'orders@efusion.co.nz','plain','orders@efusion.co.nz'),
  (6,'Activate your E-Mail address','Hi {first_name},\r\n\r\nYou have requested to activate this E-Mail address, to validate, please click on the following link\r\n\r\nhttp://{domain_name}/store/activate-account/{activation_key}\r\nAlternately you can simply copy and paste the above URL into your browser to validate your email.\r\n\r\nKind Regards, \r\n\r\n\r\n\r\n{site_title}','{activation_key}, {first_name}, {site_title}, {domain_name}',NULL,NULL,NULL,'support@efusion.co.nz','plain','support@efusion.co.nz');

DROP TABLE IF EXISTS `group`;
CREATE TABLE `group`
(
	`id` 		INTEGER 		PRIMARY KEY AUTO_INCREMENT,
	`name` 		VARCHAR(255) 	NOT NULL
)ENGINE=InnoDB CHARACTER SET utf8;
ALTER TABLE `group` ADD UNIQUE INDEX (`name`);

INSERT INTO `group` (`id`, `name`) VALUES 
  (2,'members'),
  (1,'administrators');


DROP TABLE IF EXISTS `mime_type`;
CREATE TABLE `mime_type`
(
	`id` 			INTEGER 		PRIMARY KEY AUTO_INCREMENT,
	`extension` 	VARCHAR(255) 	NOT NULL,
	`type` 			VARCHAR(255) 	DEFAULT NULL
)ENGINE=InnoDB CHARACTER SET utf8;
ALTER TABLE `mime_type` ADD INDEX (`extension`);
ALTER TABLE `mime_type` ADD UNIQUE INDEX (`type`);


INSERT INTO `mime_type` (`id`, `extension`, `type`) VALUES 
  (2,'aif','audio/x-aiff'),
  (6,'au','audio/basic'),
  (7,'avi','video/x-msvideo'),
  (8,'bcpio','application/x-bcpio'),
  (9,'bin','application/octet-stream'),
  (10,'bmp','image/bmp'),
  (11,'cdf','application/x-netcdf'),
  (13,'cpio','application/x-cpio'),
  (14,'cpt','application/mac-compactpro'),
  (15,'csh','application/x-csh'),
  (16,'css','text/css'),
  (17,'dcr','application/x-director'),
  (19,'djv','image/vnd.djvu'),
  (23,'doc','application/msword'),
  (24,'dvi','application/x-dvi'),
  (26,'eps','application/postscript'),
  (27,'etx','text/x-setext'),
  (29,'ez','application/andrew-inset'),
  (30,'gif','image/gif'),
  (31,'gtar','application/x-gtar'),
  (32,'hdf','application/x-hdf'),
  (33,'hqx','application/mac-binhex40'),
  (35,'html','text/html'),
  (36,'ice','x-conference/x-cooltalk'),
  (37,'ief','image/ief'),
  (39,'igs','model/iges'),
  (42,'jpg','image/jpeg'),
  (43,'js','application/x-javascript'),
  (45,'latex','application/x-latex'),
  (48,'m3u','audio/x-mpegurl'),
  (49,'man','application/x-troff-man'),
  (50,'me','application/x-troff-me'),
  (51,'mesh','model/mesh'),
  (53,'midi','audio/midi'),
  (54,'mif','application/vnd.mif'),
  (55,'mov','video/quicktime'),
  (56,'movie','video/x-sgi-movie'),
  (58,'mp3','audio/mpeg'),
  (60,'mpeg','video/mpeg'),
  (63,'ms','application/x-troff-ms'),
  (65,'mxu','video/vnd.mpegurl'),
  (67,'oda','application/oda'),
  (68,'pbm','image/x-portable-bitmap'),
  (69,'pdb','chemical/x-pdb'),
  (70,'pdf','application/pdf'),
  (71,'pgm','image/x-portable-graymap'),
  (72,'pgn','application/x-chess-pgn'),
  (73,'png','image/png'),
  (74,'pnm','image/x-portable-anymap'),
  (75,'ppm','image/x-portable-pixmap'),
  (76,'ppt','application/vnd.ms-powerpoint'),
  (79,'ra','audio/x-realaudio'),
  (81,'ras','image/x-cmu-raster'),
  (82,'rgb','image/x-rgb'),
  (83,'rm','audio/x-pn-realaudio'),
  (84,'roff','application/x-troff'),
  (85,'rpm','audio/x-pn-realaudio-plugin'),
  (86,'rtf','text/rtf'),
  (87,'rtx','text/richtext'),
  (89,'sgml','text/sgml'),
  (90,'sh','application/x-sh'),
  (91,'shar','application/x-shar'),
  (93,'sit','application/x-stuffit'),
  (94,'skd','application/x-koan'),
  (98,'smi','application/smil'),
  (102,'spl','application/x-futuresplash'),
  (103,'src','application/x-wais-source'),
  (104,'sv4cpio','application/x-sv4cpio'),
  (105,'sv4crc','application/x-sv4crc'),
  (106,'swf','application/x-shockwave-flash'),
  (108,'tar','application/x-tar'),
  (109,'tcl','application/x-tcl'),
  (110,'tex','application/x-tex'),
  (111,'texi','application/x-texinfo'),
  (113,'tif','image/tiff'),
  (116,'tsv','text/tab-separated-values'),
  (117,'txt','text/plain'),
  (118,'ustar','application/x-ustar'),
  (119,'vcd','application/x-cdlink'),
  (120,'vrml','model/vrml'),
  (121,'wav','audio/x-wav'),
  (122,'wbmp','image/vnd.wap.wbmp'),
  (123,'wbxml','application/vnd.wap.wbxml'),
  (124,'wml','text/vnd.wap.wml'),
  (125,'wmlc','application/vnd.wap.wmlc'),
  (126,'wmls','text/vnd.wap.wmlscript'),
  (127,'wmlsc','application/vnd.wap.wmlscriptc'),
  (129,'xbm','image/x-xbitmap'),
  (131,'xhtml','application/xhtml+xml'),
  (132,'xls','application/vnd.ms-excel'),
  (133,'xml','text/xml'),
  (134,'xpm','image/x-xpixmap'),
  (136,'xwd','image/x-xwindowdump'),
  (137,'xyz','chemical/x-xyz'),
  (138,'zip','application/zip');



DROP TABLE IF EXISTS `image`;
CREATE TABLE `image`
(
    `id`       		INTEGER          PRIMARY KEY AUTO_INCREMENT,
	`mime_type_id` 	INTEGER 		 NOT NULL,
    `caption`  		VARCHAR(255)     DEFAULT NULL,
    `filename` 		VARCHAR(255)     DEFAULT NULL,
    `size`     		DOUBLE           DEFAULT NULL
)ENGINE=InnoDB CHARACTER SET utf8;
ALTER TABLE `image` ADD INDEX (`mime_type_id`);
ALTER TABLE image ADD CONSTRAINT `image_mime_type_id_fk` FOREIGN KEY (`mime_type_id`) REFERENCES `mime_type` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

INSERT INTO `image` (`id`, `mime_type_id`, `caption`, `filename`, `size`) VALUES (1,42,'default product image','image_not_available.jpg',1299);
INSERT INTO `image` (`id`, `mime_type_id`, `caption`, `filename`, `size`) VALUES (2,42,'default banner','efusion.jpg',16803);


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

INSERT INTO `banner` (`id`, `name`, `image_id`, `is_active`) VALUES (1,'eFusion (default)',2,1);


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


INSERT INTO `product` (`id`, `category_id`, `image_id`, `name`, `description`, `cost_price`, `sale_price`, `weight`, `code`, `quantity_in_stock`, `is_active`, `created_at`, `url_name`, `is_featured`) VALUES 
  (1,1,1,'Example Product','Description of example product',100.0000,200.0000,3.0000,'EXAMP01',100,1,'2007-01-01 12:00:00','example',1);


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


DROP TABLE IF EXISTS shipping_zone;
CREATE TABLE shipping_zone
(
	id       	INTEGER       PRIMARY KEY AUTO_INCREMENT,
	name   		VARCHAR(255)  NOT NULL,
	display_name VARCHAR(255) DEFAULT NULL
)ENGINE=InnoDB CHARACTER SET utf8;
ALTER TABLE shipping_zone ADD UNIQUE INDEX (name);


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


INSERT INTO `shipping_zone` (`id`, `name`, `display_name`) VALUES 
  (1,'Across Town','Across Town'),
  (2,'Same Country','Same Country'),
  (3,'Same Region','International'),
  (6,'Africa','International'),
  (7,'Asia','International'),
  (8,'Central America and the Caribbean','International'),
  (9,'Europe','International'),
  (10,'Middle East','International'),
  (11,'North America','International'),
  (12,'Oceania','International'),
  (13,'South America','International'),
  (14,'Southeast Asia','International');

INSERT INTO `shipping_tier` (`id`, `shipping_zone_id`, `max_weight`, `amount`) VALUES 
  (1,1,1,5),
  (2,1,5,7),
  (3,1,10,7),
  (4,1,15,7),
  (5,1,20,7),
  (6,2,1,5.5),
  (7,2,5,15),
  (8,2,10,30),
  (9,2,15,80),
  (10,2,20,110),
  (11,3,1,12.9),
  (12,3,5,41.26),
  (13,3,10,60.84),
  (14,3,15,74.51),
  (15,3,20,85.34),
  (16,6,1,32.19),
  (17,6,5,120.13),
  (18,6,10,198.69),
  (19,6,15,264.58),
  (20,6,20,323.52),
  (21,1,9999,50),
  (22,2,9999,200),
  (23,3,9999,250),
  (24,6,9999,500),
  (25,7,1,27.07),
  (26,7,5,97.72),
  (27,7,10,162.93),
  (28,7,15,220.71),
  (29,7,20,274.34),
  (30,7,9999,400),
  (31,8,1,32.19),
  (32,8,5,120.13),
  (33,8,10,198.69),
  (34,8,15,264.58),
  (35,8,20,323.52),
  (36,8,9999,500),
  (37,9,1,29.9),
  (38,9,5,107.62),
  (39,9,10,175.95),
  (40,9,15,235.22),
  (41,9,20,289.53),
  (42,9,9999,400),
  (43,10,1,32.19),
  (44,10,5,120.13),
  (45,10,10,198.69),
  (46,10,15,264.58),
  (47,10,20,323.52),
  (48,10,9999,500),
  (49,11,1,27.07),
  (50,11,5,97.72),
  (51,11,10,162.93),
  (52,11,15,220.71),
  (53,11,20,274.34),
  (54,11,9999,400),
  (55,12,1,12.9),
  (56,12,5,41.26),
  (57,12,10,60.84),
  (58,12,15,74.51),
  (59,12,20,85.34),
  (60,12,9999,250),
  (61,13,1,32.19),
  (62,13,5,120.13),
  (63,13,10,198.69),
  (64,13,15,264.58),
  (65,13,20,323.53),
  (66,13,9999,500),
  (67,14,1,27.07),
  (68,14,5,97.72),
  (69,14,10,162.93),
  (70,14,15,220.71),
  (71,14,20,274.34),
  (72,14,9999,400);


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