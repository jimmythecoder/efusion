-- Newsletter list/category e.g. test list, latest products, special offers, spam
CREATE TABLE newsletter_list
(
	id 			INT 	PRIMARY KEY AUTO_INCREMENT,
	name 		VARCHAR(255) NOT NULL,
	description TEXT 	DEFAULT NULL,
	is_active 	TINYINT(1) UNSIGNED DEFAULT 1
)ENGINE=InnoDB CHARACTER SET utf8;

ALTER TABLE newsletter_list ADD UNIQUE INDEX (name);


-- Each newsletter email
CREATE TABLE newsletter
(
	id 			INT 	PRIMARY KEY AUTO_INCREMENT,
	newsletter_list_id 	INT 	NOT NULL,
	name 		VARCHAR(255) 	NOT NULL,
	subject 	VARCHAR(255) 	NOT NULL,
	text_content 	BLOB 		NOT NULL,
	html_content	BLOB 		NOT NULL,
	sent_at 	DATETIME 		DEFAULT NULL,
	created_at 	DATETIME 		NOT NULL
)ENGINE=InnoDB CHARACTER SET utf8;

ALTER TABLE newsletter ADD INDEX (newsletter_list_id);
ALTER TABLE newsletter ADD INDEX (sent_at);
ALTER TABLE newsletter ADD CONSTRAINT newsletter_newsletter_list_id_fk FOREIGN KEY (newsletter_list_id) REFERENCES newsletter_list (id) ON DELETE CASCADE ON UPDATE CASCADE;


-- User newsletter subscriber
CREATE TABLE subscriber
(
	id 	INT PRIMARY KEY AUTO_INCREMENT,
	first_name 	VARCHAR(255) NOT NULL,
	last_name 	VARCHAR(255) DEFAULT NULL,
	email 		VARCHAR(255) NOT NULL,
	created_at 	DATETIME 	NOT NULL
)ENGINE=InnoDB CHARACTER SET utf8;

ALTER TABLE subscriber ADD INDEX (first_name, last_name);
ALTER TABLE subscriber ADD UNIQUE INDEX (email);

-- Subscribers which were selected to receive this email and also flags which ones got a confirmed read
CREATE TABLE newsletter_subscriber
(
	newsletter_id 	INT NOT NULL,
	subscriber_id 	INT NOT NULL,
	is_sent 		TINYINT(1) UNSIGNED DEFAULT 0,
	is_read 		TINYINT(1) UNSIGNED DEFAULT 0
)ENGINE=InnoDB CHARACTER SET utf8;

ALTER TABLE newsletter_subscriber ADD UNIQUE INDEX (newsletter_id, subscriber_id);
ALTER TABLE newsletter_subscriber ADD INDEX (is_sent);
ALTER TABLE newsletter_subscriber ADD INDEX (is_read);
ALTER TABLE newsletter_subscriber ADD CONSTRAINT newsletter_subscriber_newsletter_id_fk FOREIGN KEY (newsletter_id) REFERENCES newsletter (id) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE newsletter_subscriber ADD CONSTRAINT newsletter_subscriber_subscriber_id_fk FOREIGN KEY (subscriber_id) REFERENCES subscriber (id) ON DELETE CASCADE ON UPDATE CASCADE;


-- Subscribers to a particular newsletter list, e.g. john doe subscribed to special offers
CREATE TABLE newsletter_list_subscriber
(
 	id 	INT PRIMARY KEY AUTO_INCREMENT,
	newsletter_list_id 	INT NOT NULL,
	subscriber_id 	INT NOT NULL
)ENGINE=InnoDB CHARACTER SET utf8;

ALTER TABLE newsletter_list_subscriber ADD UNIQUE INDEX (newsletter_list_id, subscriber_id);
ALTER TABLE newsletter_list_subscriber ADD CONSTRAINT newsletter_list_subscriber_newsletter_list_id_fk FOREIGN KEY (newsletter_list_id) REFERENCES newsletter_list (id) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE newsletter_list_subscriber ADD CONSTRAINT newsletter_list_subscriber_subscriber_id_fk FOREIGN KEY (subscriber_id) REFERENCES subscriber (id) ON DELETE CASCADE ON UPDATE CASCADE;