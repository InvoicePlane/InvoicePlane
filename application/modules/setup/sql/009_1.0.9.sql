CREATE TABLE `client_notes` (
`client_note_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`client_id` INT NOT NULL ,
`client_note_date` DATE NOT NULL ,
`client_note` LONGTEXT NOT NULL ,
INDEX ( `client_id` , `client_note_date` )
) ENGINE = MYISAM ;