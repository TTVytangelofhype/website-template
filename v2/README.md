# Habbo Fansite V2 — Hybrid Design (developer preview)
Not affiliated with or endorsed by Habbo or Sulake. This work is isolated in `v2/` on branch `habbo-fansite-v2`; original site on `main` is unchanged.

## Features
- Public Habbo-inspired home with pixel-style hotel, event previews, radio player and live calendar
- Public song requests stored in MySQL, DJ staff request moderation
- One-time owner setup page protected by a secret setup key
- Secure staff sign-in, role-gated approval queue for DJ and event bookings, audit logs
- Calendar publishes approved slots only; member and DJ roles cannot approve their own bookings

## Deploy to cPanel
1. Create MySQL database and user with suitable privileges in cPanel.
2. Run `database/schema.sql`, then `database/002_radio.sql` in phpMyAdmin.
3. Copy `config.example.php` to **`config.php`**, configure DB credentials, a unique random `setup_key` of 32+ characters, site branding and radio stream URL.
4. Set your domain or test subdomain document root to **`v2/public`**, never the repository root. Keep config and app source out of publicly served files.
5. Use HTTPS and visit `/setup.php`. Supply the setup key and create a 14+ character owner password. Setup becomes unavailable when an account exists. After completion, delete the setup key from production configuration and remove or block setup.php.
6. Visit `/login.php` for staff tools, `/index.php` for the public fansite. Existing members/roles currently require owner-managed provisioning through the database (UI pending).
7. Confirm PHP 8.3+, PDO MySQL, mbstring, and TLS are enabled.

## Booking and data notes
Times are interpreted and stored as **Europe/London local time**. DST boundary ambiguity needs improvement before production use. DJ shows and events have separate exclusive approval calendars. Approvals reject overlapping confirmed slots of the same kind. Public calendar displays approved bookings only. Request submissions have a session-level cooldown; a stronger IP/user rate limit and anti-spam solution are needed prior to public deployment.

## Important known limitations
This is a developer preview, not yet production audited. Install and test on a staging subdomain first. Current login has no brute-force limiter; staff management/roles editor, recurring calendar bookings, granular event room conflicts and automated stream now-playing integration remain future milestones. Staff management presently means existing role-controlled staff access rather than a complete staff-admin UI. Legacy requests in JSON are not imported automatically. Keep `main` deployed until migration testing passes.
