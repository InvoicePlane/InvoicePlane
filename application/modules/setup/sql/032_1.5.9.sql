# Added for versioning

# Expense module
CREATE TABLE `ip_expense_custom` (
	`expense_custom_id` INT(11) NOT NULL AUTO_INCREMENT,
	`expense_id` INT(11) NOT NULL,
	PRIMARY KEY (`expense_custom_id`),
	INDEX `expense_id` (`expense_id`)
)
AUTO_INCREMENT=1;

CREATE TABLE `ip_expenses` (
	`expense_id` INT(11) NOT NULL AUTO_INCREMENT,
	`payment_method_id` INT(11) NOT NULL DEFAULT '0',
	`expense_date` DATE NOT NULL,
	`expense_amount` DECIMAL(10,2) NOT NULL,
	`expense_note` LONGTEXT NOT NULL,
	`client_id` INT(11) NOT NULL,
	PRIMARY KEY (`expense_id`),
	INDEX `payment_method_id` (`payment_method_id`)
);