# GymXBook CRM

A polished, Laravel-only CRM for managing gym sales leads, follow-ups, meetings, and trial sessions. Pages are server-rendered with Blade; there is no React/Vue SPA.

## Stack

- Laravel Framework 12 (`^12.0`; lockfile currently resolves `v12.69.3`)
- PHP `^8.2`; Composer resolves dependencies against PHP 8.2.12 for XAMPP compatibility
- Blade, Eloquent, policies, Form Requests, session authentication
- SQLite by default; MySQL/PostgreSQL-compatible schema
- Directly served CSS and vanilla JavaScript in `public/` (no frontend build step required)
- PhpSpreadsheet for CSV, XLS, and XLSX import; streamed CSV export

The version `12.12.2` is a Laravel application-skeleton release, not a `laravel/framework` package version. This project targets the Laravel 12 framework line.

## Local setup (Windows / XAMPP)

Use PHP 8.2 or newer and enable GD in the **CLI** `php.ini` (the path is shown by `php --ini`). PhpSpreadsheet requires GD. In XAMPP's `php.ini`, uncomment `extension=gd`, then restart PowerShell. Check `php -v` and `php -m | findstr /I gd` before installing Composer dependencies.

```powershell
Copy-Item .env.example .env
composer install
php artisan key:generate
if (-not (Test-Path .\database\database.sqlite)) { New-Item -ItemType File .\database\database.sqlite | Out-Null }
php artisan migrate --seed
php artisan serve
```

Open `http://127.0.0.1:8000`. The CRM serves its stylesheet from `public/css/app.css` and vanilla JavaScript from `public/js/app.js`; Poppins and Space Grotesk are self-hosted under `public/fonts/`. These are ordinary static assets, so Node.js, npm, Vite, and `public/build/` are not required to run the app. Edit `public/css/app.css` directly and refresh the page to see CSS changes. If deployed behind a reverse proxy/load balancer, set `TRUSTED_PROXIES` to the proxy IP/CIDR values so HTTPS links and secure redirects use the public host.

## Demo logins (local seed only)

- Admin: `admin@gymxbook.test` / `password`
- Manager: `manager@gymxbook.test` / `password`
- Sales: `isha@gymxbook.test` / `password`

Change seeded credentials before using a deployed environment. Registration is intentionally closed; workspace users are provisioned by an administrator.

## Included capabilities

Dashboard KPIs and pipeline; searchable/filterable lead list with bulk deletion and CSV export; modal-based lead create, preview, and edit; AJAX live search, filters, pagination, status updates, and deletion with custom confirmations; assignment, call/WhatsApp links, notes, and timeline; scheduled/completed follow-ups with outcome, meeting and trial detail; month/week/day calendar; staged spreadsheet import wizard; conversion and activity reports; AJAX staff management with modal create/edit, protected removal, last-login and live online status; Google Places gym prospecting with live, tiered API details and Place-ID-only persistence; Admin/Manager workspace-wide access and Sales rep direct-assignment-only access; profile/workspace settings; Indian demo data.

## Google Places prospecting setup

An Admin can save an encrypted, masked Google Maps Platform key in **Settings → Google Places integration**. Enable **Places API (New)** and **Geocoding API**, restrict the key to the server's IP address and those APIs, and verify the connection from Settings. The verification action itself consumes one Geocoding request and one Places Text Search Pro request.

Gym searches use Pro fields and a local hard stop of **25,000 search-page requests/month**; location lookups have a separate 25,000-request guard. Phone and review count are fetched only when a user opens **Load live details**, using an explicit Enterprise Place Details request with a **5,000-request/month** local hard stop. Search pages contain up to 20 results; the user must explicitly request one additional page for a maximum of 40 results per search (not exhaustive).

GymXBook stores Google **Place IDs** plus GymXBook-owned assignment/status metadata. Assigned prospects appear under **Leads** with a **Google Maps** source tag, scoped to the manager or assigned teammate; assigning prospects takes the manager to that Leads section. Google-sourced names, addresses, Maps links, phone numbers, and review counts are presented live and are not retained as CRM prospect fields or included in prospect exports. The lead-conversion form requires an explicit confirmation that contact details were independently verified; it does not prefill Google fields. The verified-conversion form defaults the standard lead source to **Google Maps**.

These are per-application request counters, not a Google Cloud invoice meter. They assume an India-billed account and cannot see other projects/APIs on a shared billing account, so they do not guarantee zero charges. Review the [Google India pricing page](https://developers.google.com/maps/billing-and-pricing/pricing-india) and configure Cloud quotas lower than the account's remaining free usage allowance; budgets are alerts, not hard stops.

See [`docs/architecture.md`](docs/architecture.md), [`docs/database-design.md`](docs/database-design.md), and [`docs/design-system.md`](docs/design-system.md).

## Verification

Composer resolves Laravel Framework 12.69.3 with the dependency lock targeting PHP 8.2.12. Earlier backend verification passed migrations, seeders, route registration, Blade compilation, and 16 PHPUnit tests (87 assertions). For the Google Places phase, JavaScript syntax passed `node --check`; feature tests now cover tiered field masks, page-token searches, authorization, encrypted key management, both monthly hard stops, Place-ID-only assignment, live details, and verified lead conversion. PHP lint and PHPUnit were not run because PHP CLI is unavailable and dependencies were not reinstalled. The app was not started and no npm/build step was used. Runtime assets are served directly from `public/`; the package excludes `vendor/`, `.env`, and the local SQLite database.
