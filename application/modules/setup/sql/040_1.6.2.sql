# Feature Request IP-763 INSERT 'CRI{{{id}}}' to ip_invoice_groups table

INSERT INTO `ip_invoice_groups` (`invoice_group_name`, `invoice_group_identifier_format`, `invoice_group_next_id`, `invoice_group_left_pad`)
VALUES ('Credit Invoice Default', 'CRI{{{id}}}', 1, 0);
