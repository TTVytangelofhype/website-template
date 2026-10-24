# Habbo Fansite V2 — Hybrid (developer preview)

Unofficial Habbo fansite; not endorsed by Habbo or Sulake. Branch `habbo-fansite-v2` preserves legacy `main`.

## Easy first-time installation (no config.php editing)
1. Use PHP 8.3+ with PDO MySQL and mbstring and a MySQL/MariaDB server.
2. Upload the **contents of v2**, keeping `app/`, `database/` and configuration outside the public document root. Point the HTTPS domain or staging subdomain document root at `v2/public`.
3. In cPanel **MySQL Databases**, create a **new, empty database** and database user, grant the user database permissions, and note the database name, username and password.
4. Browse to `https://your-fansite.example/setup.php`. The installer automatically creates `v2/install-token.txt` in the **private parent v2 directory**. Read this one-time key using cPanel File Manager and enter it into the installer.
5. Fill out database connection, **fansite name**, **tagline**, optional HTTPS radio stream, and **owner username/password** (14+ characters).
6. Click **Create my fansite**. The installer imports both SQL files, creates the first owner, writes a private `config.php`, writes `.installed`, deletes the installation key, and signs in as owner.
7. Visit `/index.php` for the public site or `/login.php` for staff. Remove write permission from the application directory after installation. Retain a safe backup of config.php.

**Security:** The installation token is required to prevent unauthorised first-owner creation on a public server. Do not upload the `v2` directory into a publicly served parent folder with unrestricted access. Setup refuses to run if `config.php` or `.installed` exists. No existing installs are migrated automatically. Keep `main` deployed until staging tests pass. Do not use a populated production database for a new installation.

## Features included
- Habbo-inspired homepage, monthly calendar, DJ radio stream and song request form
- Owner and staff authentication, role-controlled DJ/event booking approvals, audit logs
- MySQL-backed song request moderation and booking data
- Browser-based installation wizard

## Remaining work
This is not production audited. Login throttling, robust multi-process booking conflict locks, DST edge handling, staff account/role editor, spam mitigation, recurring booking features and live radio metadata integration are pending.

## Owner settings after installation
Log into `/login.php` as the owner and open **Fansite Settings** from the dashboard. Set fansite name, tagline, optional HTTPS logo URL and radio stream, and select Classic Hotel, Neon Radio or Midnight theme. Saved options live in MySQL `site_settings`, not in `config.php`. The installer creates this table on new installs.

**Existing V2 developer-preview installs:** import `database/003_settings.sql` once through phpMyAdmin before opening the homepage or settings panel. This update does not touch legacy `main`.
