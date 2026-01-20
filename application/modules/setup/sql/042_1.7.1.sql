# Feature: Continuous (Recurring) Invoice Generation
# Add setting to control whether recurring invoices should be generated even if previous invoice is unpaid
# Default value is '1' (always generate) to maintain backward compatibility with current behavior

INSERT INTO `ip_settings` (`setting_key`, `setting_value`) 
VALUES ('generate_recurring_if_unpaid', '1');
