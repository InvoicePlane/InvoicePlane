CREATE TABLE `fi_email_templates` (
`email_template_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`email_template_title` VARCHAR( 255 ) NOT NULL ,
`email_template_body` LONGTEXT NOT NULL ,
`email_template_footer` LONGTEXT NOT NULL
) ENGINE = MYISAM ;