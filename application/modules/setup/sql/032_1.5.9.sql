# Added for versioning

# Car module
CREATE TABLE `ip_car_custom` (
	`car_custom_id` INT(11) NOT NULL AUTO_INCREMENT,
	`car_id` INT(11) NOT NULL,
	PRIMARY KEY (`car_custom_id`),
	INDEX `car_id` (`car_id`)
)
AUTO_INCREMENT=1;

CREATE TABLE `ip_cars` (
	`car_id` INT(11) NOT NULL AUTO_INCREMENT,
	`car_client_id` INT(11),
	`car_vehicle` TEXT,
	`car_builddate` DATE,
	`car_licenseplate` TEXT,
	`car_kmstand` INT,
	`car_auhu` TEXT,
	`car_date_created` DATE,
	`car_date_modified` DATE,
	`car_chassnr` TEXT,
	`car_note` TEXT,
	`car_active` INT(1) DEFAULT '1',
	PRIMARY KEY (`car_id`),
	INDEX `car_id` (`car_id`)
);

ALTER TABLE `ip_invoices` ADD COLUMN (
        `car_id` INT(11) NOT NULL,
        `car_vehicle` TEXT,
        `car_licenseplate` TEXT,
        `car_kmstand` INT,
        `car_auhu` TEXT,
        `car_chassnr` TEXT
);

ALTER TABLE `ip_quotes` ADD COLUMN (
        `car_id` INT(11) NOT NULL,
        `car_vehicle` TEXT,
        `car_licenseplate` TEXT,
        `car_kmstand` INT,
        `car_auhu` TEXT,
        `car_chassnr` TEXT
);
