-- ============================================================================
-- PayTheFly Pro Integration — Database Migration
-- ============================================================================
-- This migration adds the default settings for the PayTheFly Pro crypto
-- payment gateway to the ip_settings table.
--
-- Run this after installing the PayTheFly integration files.
-- These settings can also be configured via the Admin Settings UI.
-- ============================================================================

-- Insert default PayTheFly gateway settings
-- (These will be overwritten when the admin configures them in Settings)

INSERT INTO `ip_settings` (`setting_key`, `setting_value`) VALUES
    ('gateway_paythefly_enabled', '0'),
    ('gateway_paythefly_projectId', ''),
    ('gateway_paythefly_contractAddress', ''),
    ('gateway_paythefly_privateKey', ''),
    ('gateway_paythefly_projectKey', ''),
    ('gateway_paythefly_defaultChain', 'BSC'),
    ('gateway_paythefly_deadlineMinutes', '30'),
    ('gateway_paythefly_currency', 'USD'),
    ('gateway_paythefly_payment_method', '0')
ON DUPLICATE KEY UPDATE `setting_key` = `setting_key`;

-- ============================================================================
-- Note: The ip_merchant_responses table already exists and is used to log
-- all payment gateway interactions. No schema changes are needed.
--
-- The ip_payments table is also unchanged — PayTheFly payments are recorded
-- using the standard payment recording mechanism.
-- ============================================================================
