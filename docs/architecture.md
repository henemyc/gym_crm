# GymXBook CRM — architecture & delivery plan

## Project inspection (2026-10-07)

- The workspace contained no existing Laravel app, source files, or dependency manifests.
- No project-specific UI kit or connected MCP/UI component library was exposed. The UI uses a purpose-built Blade + CSS design system, small vanilla-JS interactions, and inline SVG icons. Static assets are served directly from `public/css/` and `public/js/`; there is no SPA framework, Node/npm build requirement, or external CDN dependency.
- PHP/Composer were absent during initial inspection; PHP 8.4 CLI, Composer, and the needed SQLite/PHP extensions were installed in the ephemeral authoring environment so migrations, seeders, routes, Blade views, and tests could be exercised. Those system packages are not bundled with the project.
- The compatibility target is Laravel 12 with PHP 8.2. The Composer constraint is `laravel/framework:^12.0` and the lockfile is resolved against PHP 8.2.12. The lock currently selects Laravel Framework 12.69.3. The `12.12.2` label refers to the `laravel/laravel` starter-skeleton release, rather than a `laravel/framework` release.

## Product architecture

- **Framework:** Laravel 12, PHP `^8.2`, Blade-rendered pages, session authentication, Eloquent, policies, Form Requests, and directly served CSS/vanilla JavaScript assets; Node/npm is not needed at runtime.
- **Persistence:** SQLite out of the box for local development; the schema is portable to MySQL/PostgreSQL. Database migrations reuse the Laravel 12 skeleton's `users`, `cache`, and `jobs` base tables; the CRM adds its user-profile fields and domain tables separately.
- **Spreadsheet import:** PhpSpreadsheet for CSV/XLS/XLSX parsing; CSV export streams directly from Laravel. GD must be enabled for PhpSpreadsheet.
- **Authorization:** Admin and Manager roles retain workspace-wide lead access; Sales users can access only leads assigned directly to them. Lead scopes and policies enforce the same rule across lead, follow-up, calendar, dashboard, report, and import workflows.
- **Presence:** Successful sign-ins update `last_login_at`; authenticated requests and a lightweight visible-tab heartbeat refresh `last_activity_at`. The staff panel treats activity within five minutes as online.
- **Audit trail:** immutable-style `LeadActivity` records are created for lead, status, follow-up, note, and import actions.

## Data model

`User 1—many Lead (assignee)`; `User 1—many Lead (creator)`; `Lead 1—many FollowUp`; `Lead 1—many LeadActivity`; `User 1—many LeadActivity`; `User 1—many LeadImport`.

Lead statuses: New, Contacted, Follow-up, Meeting Fixed, Trial, Negotiation, Converted, Lost. `Converted` is included as a terminal sales outcome because the dashboard explicitly tracks conversions. Priority is Hot, Warm, or Cold. Follow-ups are scheduled records; completion writes an outcome and may schedule a subsequent record. Meeting and trial dates live on the follow-up so the same data powers the calendar and reporting.

## Phases

1. Project analysis, architecture, schema, and visual design tokens.
2. Authentication, app shell, dashboard.
3. Lead management and lead detail/activity timeline.
4. Follow-ups, completion workflow, and calendar.
5. CSV/XLS/XLSX import, export, and reports.
6. Settings, staff roles, responsive polish, and accessibility.
7. Laravel 12 / PHP 8.2 compatibility port, static verification, tests, and documentation.

## Verification

Composer resolved the framework and transitive dependencies with `config.platform.php=8.2.12`; the framework currently locked is Laravel 12.69.3 under the `^12.0` constraint. On the authoring runner (PHP 8.4.26), `composer validate --strict`, fresh in-memory SQLite migrations + seeders, route registration, Blade compilation, and the PHPUnit suite (16 tests / 87 assertions) passed before the asset pipeline was switched to direct static files. The XAMPP target should have GD enabled for spreadsheet support. The source package intentionally excludes `vendor/`, `.env`, and the local SQLite database.
