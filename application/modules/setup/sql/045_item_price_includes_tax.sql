# Feature: enter an item's price as the final, tax-inclusive amount (GH issue #1887).
# When set, Mdl_item_amounts::calculate() / Mdl_quote_item_amounts::calculate() derive the
# base price and tax from item_price instead of adding tax on top of it.
ALTER TABLE `ip_invoice_items`
  ADD COLUMN `item_price_includes_tax` TINYINT(1) NOT NULL DEFAULT '0'
  AFTER `item_price`;

ALTER TABLE `ip_quote_items`
  ADD COLUMN `item_price_includes_tax` TINYINT(1) NOT NULL DEFAULT '0'
  AFTER `item_price`;
