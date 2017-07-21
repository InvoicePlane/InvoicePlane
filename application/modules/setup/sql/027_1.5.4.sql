# Fix the recurring invoices frequency
ALTER TABLE `ip_invoices_recurring`
  CHANGE `recur_frequency` `recur_frequency` VARCHAR(255)
CHARACTER SET utf8
COLLATE utf8_general_ci NOT NULL;
