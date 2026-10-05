-- Database tamper resistance and external audit-chain anchors.
CREATE TABLE IF NOT EXISTS `ip_archive_audit_anchors` (
  `archive_audit_anchor_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `archive_document_id` BIGINT UNSIGNED NOT NULL,
  `sequence_number` INT UNSIGNED NOT NULL,
  `last_event_hash` CHAR(64) NOT NULL,
  `chain_sha256` CHAR(64) NOT NULL,
  `payload_json` LONGTEXT NOT NULL,
  `external_storage_provider` VARCHAR(50) NOT NULL,
  `external_storage_key` VARCHAR(500) NOT NULL,
  `external_storage_version_id` VARCHAR(255) NULL,
  `created_at` DATETIME NOT NULL,
  PRIMARY KEY (`archive_audit_anchor_id`),
  UNIQUE KEY `uq_archive_audit_anchor_document_sequence` (`archive_document_id`, `sequence_number`),
  KEY `idx_archive_audit_anchor_chain` (`archive_document_id`, `chain_sha256`),
  CONSTRAINT `fk_archive_audit_anchor_document`
    FOREIGN KEY (`archive_document_id`) REFERENCES `ip_archive_documents` (`archive_document_id`)
    ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TRIGGER `trg_archive_audit_events_no_update`
BEFORE UPDATE ON `ip_archive_audit_events`
FOR EACH ROW SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Archive audit events are append-only';

CREATE TRIGGER `trg_archive_audit_events_no_delete`
BEFORE DELETE ON `ip_archive_audit_events`
FOR EACH ROW SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Archive audit events are append-only';

CREATE TRIGGER `trg_archive_audit_anchors_no_update`
BEFORE UPDATE ON `ip_archive_audit_anchors`
FOR EACH ROW SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Archive audit anchors are immutable';

CREATE TRIGGER `trg_archive_audit_anchors_no_delete`
BEFORE DELETE ON `ip_archive_audit_anchors`
FOR EACH ROW SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Archive audit anchors are immutable';
