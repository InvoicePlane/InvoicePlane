CREATE TABLE `fi_user_clients` (
`user_client_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`user_id` INT NOT NULL ,
`client_id` INT NOT NULL ,
INDEX ( `user_id` , `client_id` )
) ENGINE = MyISAM;