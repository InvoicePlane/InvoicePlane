-- Append-only audit trail for archived documents.
-- The application never updates or deletes rows from this table.
CREATE TABLE IF NOT EXISTS `ip_archive_audit_events` (
    `archive_audit_event_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `archive_document_id` BIGINT UNSIGNED NOT NULL,
    `sequence_number` INT UNSIGNED NOT NULL,
    `event_type` VARCHAR(50) NOT NULL,
    `actor_type` VARCHAR(20) NOT NULL DEFAULT 'system',
    `actor_user_id` INT UNSIGNED NULL,
    `event_at` DATETIME NOT NULL,
    `payload_json` LONGTEXT NULL,
    `previous_event_hash` CHAR(64) NULL,
    `event_hash` CHAR(64) NOT NULL,
    PRIMARY KEY (`archive_audit_event_id`),
    UNIQUE KEY `uq_archive_audit_document_sequence` (`archive_document_id`, `sequence_number`),
    UNIQUE KEY `uq_archive_audit_event_hash` (`event_hash`),
    KEY `idx_archive_audit_document_event_at` (`archive_document_id`, `event_at`),
    CONSTRAINT `fk_archive_audit_document`
        FOREIGN KEY (`archive_document_id`) REFERENCES `ip_archive_documents` (`archive_document_id`)
        ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
