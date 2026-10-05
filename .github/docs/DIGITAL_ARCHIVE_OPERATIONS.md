# Digital Archive Operations

This runbook describes the operational procedures for the InvoicePlane
document archive. It covers the local registry, encrypted storage, S3 Object
Lock, audit anchors, and RFC 3161 timestamp evidence.

The archive is only as strong as its complete chain of custody. Database
backups, archive files, encryption keys, S3 configuration, and operational
logs must be protected and tested together.

## Initial deployment

Before enabling production archiving:

1. Apply setup migrations `054` through `060` through the InvoicePlane setup
   upgrade process. InvoicePlane uses CodeIgniter migrations; do not use
   `php artisan migrate`.
2. Configure `ENCRYPTION_KEY`, or configure the versioned archive keyring with
   `ARCHIVE_ENCRYPTION_KEYS` and
   `ARCHIVE_ENCRYPTION_ACTIVE_KEY_VERSION`. Keep secrets outside source
   control.
3. If S3 Object Lock is used, create a dedicated bucket with versioning and
   Object Lock enabled before the first upload. Use a dedicated IAM identity
   that can write objects and read their versions, but cannot shorten
   retention or bypass compliance controls.
4. Configure the RFC 3161 TSA endpoint only through protected configuration.
   The endpoint must use HTTPS. Qualification is determined by the TSA and
   its trust policy, not by the endpoint URL alone.
5. Create a test document, seal it, verify it, export it, and validate the
   resulting local and remote evidence before opening the archive to users.

## Daily operations

The integration synchronization can be run from cron:

```text
php index.php integrations/cli/sync
```

Process encryption key rotation in bounded batches:

```text
php index.php integrations/cli/rotate_archive_keys 100
```

Repeat the rotation command until `scanned` and `failed_ids` are zero. Do
not remove an old key from `ARCHIVE_ENCRYPTION_KEYS` before every document
using that key has been rotated and verified.

For archive maintenance, operators should periodically:

- check that the archive filesystem is available and not unexpectedly
  writable by the web-server account beyond the required directories;
- verify sealed document hashes;
- verify audit chains and local audit anchors;
- verify that S3 objects still have `COMPLIANCE` Object Lock metadata and a
  future retention date;
- review retention candidates before any disposal policy is applied;
- review synchronization and timestamp failures without deleting evidence.

The model operations used by maintenance tooling are
`verify_integrity()`, `verify_chain()`, `verify_local_anchor()`,
`verify_external_timestamp()`, and `get_retention_candidates()`.

## Backup procedure

Back up the database and archive files as one consistent recovery point.

1. Pause archive writes and scheduled synchronization, or use a maintenance
   window.
2. Create a transactional database dump including triggers and routines. A
   representative command is:

```text
mysqldump --single-transaction --routines --triggers --hex-blob \
  --databases INVOICEPLANE_DATABASE > invoiceplane-YYYYMMDD-HHMMSS.sql
```

   Replace the database name with the production value. Store the dump in an
   encrypted backup repository and record its SHA-256 digest.
3. Back up `uploads/archive/` with file contents, relative paths, ownership,
   permissions, and timestamps preserved. Do not use a process that follows
   symlinks outside the archive directory.
4. Back up the active and retained archive encryption keys separately from
   the database and archive files. Losing the keys makes encrypted documents
   unrecoverable.
5. Keep S3 Object Lock objects in the versioned bucket. If a second copy is
   required, use the provider's version-aware replication or backup feature;
   a normal mirror that deletes old versions is not a WORM backup.
6. Record the backup identifier, database position or timestamp, key-ring
   versions, and verification result in the operations log.
7. Resume writes only after the backup result has been recorded.

Backups must follow the same retention, access-control, and legal-hold rules
as the primary archive. Never place encryption keys in the same unprotected
location as the encrypted backup.

## Restore procedure

Restoration must be performed in an isolated environment first whenever
possible.

1. Stop web, cron, and queue workers that can write invoices or archive data.
2. Preserve the current database, `uploads/archive/`, configuration, and
   logs before replacing anything.
3. Restore the database dump and the matching `uploads/archive/` snapshot.
4. Restore the exact encryption key versions referenced by
   `encryption_key_version`. Do not rotate keys during the first restore.
5. Confirm that migrations `054` through `060` are present and that the audit
   triggers exist.
6. Verify representative documents from each storage mode:
   - local plaintext storage;
   - AES-256-GCM encrypted storage;
   - exported package storage;
   - S3 Object Lock storage, when configured.
7. Verify the audit chain and every available local anchor. A mismatch is an
   incident, not a reason to overwrite the restored evidence.
8. Check the restored S3 version IDs and retention dates against the local
   registry. Never recreate a remote object under the same key merely because
   the local row is missing.
9. Record the restore result, verified document count, failed document IDs,
   key versions, and remaining legal holds.
10. Resume services only after the application and evidence checks succeed.

## Incident response

If a hash, audit chain, timestamp token, local anchor, or S3 retention check
fails:

1. Put the affected workflow in read-only or maintenance mode.
2. Preserve the database, archive files, S3 version IDs, logs, and current
   configuration. Do not delete or overwrite the suspect evidence.
3. Record the affected archive document IDs, event sequence, expected digest,
   observed digest, and time of detection.
4. Compare the local chain with the most recent external S3 anchor and any
   RFC 3161 token.
5. Restrict credentials that may have been exposed and preserve the old key
   versions until the investigation is complete.
6. Restore to an isolated environment only after preserving the original
   evidence, then document the decision to resume or quarantine the records.

## Security responsibilities

InvoicePlane provides application-level validation, authenticated encryption,
audit chaining, SQL append-only triggers, S3 Object Lock integration, and
timestamp-token imprint checks. Infrastructure owners remain responsible for
database roles, filesystem permissions, IAM policies, key custody, network
TLS, S3 bucket governance, backup retention, monitoring, and the legal or
qualified status of the selected TSA/SAE services.
