# Delete all lead data

From the deployed Laravel project directory, take a database backup and pause web/API writes and queue/scheduler workers while executing the purge. The command targets the database configured for that application.

Preview (also the default when no flags are supplied):

```sh
php artisan leads:purge-all --dry-run
```

Permanently delete:

```sh
php artisan leads:purge-all --force
```

The command deletes all leads, lead contacts, notes, tasks, opportunities, logs, check-ins, lead notifications (including soft-deleted and bulk-assignment notifications), and lead-owned address/media database records. General tasks with a positive `lead_id` are deleted together with their assignments, comments, status logs, notifications and media/address records. Visit reports with a positive `lead_id` are deleted when that column exists.

Customers (including converted leads), users, products, PO/SO, master statuses, and general tasks without a lead are retained. Files stored on disk/S3 are retained: this command deletes database records only. Numeric IDs are not reset.

Deletion uses a transaction and keeps foreign-key checks enabled. MySQL tables involved must use InnoDB. Missing optional tables are skipped. Unknown tables containing positive `lead_id`, `lead_task_id`, `lead_contact_id`, or `lead_opportunity_id` references block the command until their cleanup is added. Review preview counts against the live schema before execution; a foreign-key error rolls the transaction back rather than disabling constraints. Historical visit reports with only a `checkin_id` cannot safely be identified as lead-owned because customer and lead check-in IDs overlap.

After execution, run the preview again to verify zero remaining scoped rows, then resume workers and traffic.
