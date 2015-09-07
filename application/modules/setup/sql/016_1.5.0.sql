# Feature IP-162
# Allow invoice item to refer to a table `products` or to table `tasks`
ALTER TABLE `ip_invoice_items`
ADD COLUMN `item_task_id` int(11) DEFAULT NULL
AFTER `item_date_added`;

# Add default hourly rate for tasks
INSERT INTO `ip_settings` (`setting_key`, `setting_value`)
VALUES ('default_hourly_rate', '0.00');

# Add tax rate to tasks
ALTER TABLE `ip_tasks`
ADD COLUMN `tax_rate_id` int(11) NOT NULL
AFTER `task_status`;
