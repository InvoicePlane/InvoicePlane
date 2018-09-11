# IP-636 - Convert quote status to invoiced if it has a linked invoice
UPDATE `ip_quotes` SET `quote_status_id` = 7
  WHERE `invoice_id` != 0;
