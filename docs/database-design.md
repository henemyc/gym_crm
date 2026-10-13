# Database design

## `users`
Laravel authentication identity plus workspace role (`admin`, `manager`, `sales`), active flag, phone, timezone, notification preference, `last_login_at`, and indexed `last_activity_at`. Online presence means activity within the previous five minutes; authenticated page requests and a visible-tab heartbeat refresh the activity timestamp.

## `leads`
`name`, `gym_name`, `phone`, `location`, `source`, `priority`, `status`, `notes`, `assigned_to`, `created_by`, timestamps, soft-delete timestamp. Indexed for status, priority, source, location, assignee, phone, and creation date.

## `follow_ups`
`lead_id`, `assigned_to`, `created_by`, `kind`, `due_at`, `outcome`, `remark`, `completed_at`, `next_follow_up_at`, `meeting_at`, `meeting_with`, `trial_at`, `trial_details`. Open/completed state is determined by `completed_at`; event dates are indexed for calendar queries.

## `lead_activities`
`lead_id`, `user_id`, event type, title, description, JSON metadata, timestamps. Used by the detail timeline and does not replace the structured follow-up records.

## `lead_imports`
Owner, original filename, private storage path, lifecycle status, mapped source columns, preview headers, validation/import summary, timestamps. Uploaded files are stored on Laravel's private local disk.

## Operational conventions

- All writes use Form Request validation and authorization policies.
- Multi-row import commits run in a database transaction and retain row-level validation errors in the import summary.
- Soft-deleted leads are excluded from normal queries; related records remain available for audit unless retention policy is configured separately.
- Database enum-like values use strings, keeping migrations portable and allowing future workflow changes without an enum migration.
