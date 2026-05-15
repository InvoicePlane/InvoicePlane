# Add reply-to field to email templates
ALTER TABLE ip_email_templates
  ADD COLUMN email_template_reply_to TEXT DEFAULT NULL;
