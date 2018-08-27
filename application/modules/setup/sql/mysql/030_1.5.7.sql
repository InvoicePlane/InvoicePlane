ALTER TABLE `ip_merchant_responses`
  ADD `merchant_response_successful` TINYINT(1) DEFAULT 1
  AFTER `invoice_id`;
