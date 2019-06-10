#
# Table structure for table 'tx_logmatrix_domain_model_produkt'
#
CREATE TABLE tx_contentdock_domain_model_process (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	agent varchar(255) DEFAULT '' NOT NULL,
	container varchar(255) DEFAULT '' NOT NULL,
	command varchar(255) DEFAULT '' NOT NULL,
	operation varchar(255) DEFAULT '' NOT NULL,
	data longtext,
	result longtext,
	finished int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
	PRIMARY KEY (uid),
	KEY parent (pid)

);

