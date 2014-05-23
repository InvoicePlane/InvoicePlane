CREATE TABLE `fi_imports` (
  `import_id` int(11) NOT NULL AUTO_INCREMENT,
  `import_date` datetime NOT NULL,
  PRIMARY KEY (`import_id`)
) ENGINE = MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `fi_import_details` (
`import_detail_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`import_id` INT NOT NULL ,
`import_lang_key` VARCHAR( 35 ) NOT NULL ,
`import_table_name` VARCHAR( 35 ) NOT NULL ,
`import_record_id` INT NOT NULL ,
INDEX ( `import_id` , `import_record_id` )
) ENGINE = MyISAM DEFAULT CHARSET=utf8;