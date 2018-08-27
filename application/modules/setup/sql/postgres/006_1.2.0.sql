-- /* Customize quote / invoice groups #5 */
ALTER TABLE ip_invoice_groups
  ADD COLUMN invoice_group_identifier_format VARCHAR(255);

-- /* convert fields to format string
--  * Schema: Prefix{{{YEAR}}}{{{MONTH}}}{{{ID}}}
--  * (based on existing schema)
--  */
UPDATE ip_invoice_groups
SET invoice_group_identifier_format = 
    invoice_group_prefix ||
    CASE invoice_group_prefix_year
    WHEN 1
      THEN '{{{year}}}'
    ELSE ''
    END  ||
    CASE invoice_group_prefix_month
    WHEN 1
      THEN '{{{month}}}'
    ELSE ''
    END ||
    '{{{id}}}'
;
-- Add NOT NULL constraint and drop some columns
ALTER TABLE ip_invoice_groups
  ALTER COLUMN invoice_group_identifier_format SET NOT NULL,
  DROP invoice_group_prefix,
  DROP invoice_group_prefix_month,
  DROP invoice_group_prefix_year;

-- Module "families"
CREATE TABLE IF NOT EXISTS ip_families (
  family_id   SERIAL,
  family_name VARCHAR(50) DEFAULT NULL
);

-- Module "products"
CREATE TABLE IF NOT EXISTS ip_products (
  product_id          SERIAL,
  family_id           INTEGER      NOT NULL,
  product_sku         VARCHAR(15)  NOT NULL,
  product_name        VARCHAR(50)  NOT NULL,
  product_description TEXT         NOT NULL,
  product_price       FLOAT(2)     NOT NULL,
  purchase_price      FLOAT(2)     NOT NULL,
  tax_rate_id         INTEGER      NOT NULL
);

-- Move lookup items to products
INSERT INTO ip_products (
  family_id,
  product_sku,
  product_name,
  product_description,
  product_price,
  purchase_price,
  tax_rate_id
)
  SELECT
    0                              AS family_id,
    -- default to 0 (no family)
    ('sku-' || item_lookup_id) AS product_sku,
    -- use ip_item_lookup primary key as new SKU
    item_name,
    item_description,
    item_price,
    0                              AS product_purchase_price,
    -- default purchase price to 0
    0                              AS tax_rate_id -- default tax rate ID
  FROM ip_item_lookups;

-- /* Add the Invoice Sign */
CREATE TYPE intsign AS ENUM ('1', '-1');
ALTER TABLE ip_invoice_amounts
  ADD invoice_sign intsign NOT NULL DEFAULT '1';

ALTER TABLE ip_invoices
  ADD creditinvoice_parent_id INTEGER,
  ADD is_read_only SMALLINT;
