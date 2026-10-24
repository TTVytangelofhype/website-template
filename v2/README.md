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

## Staff Management
Owners can now use **Staff Management** in the dashboard to create accounts, choose staff roles, suspend/reactivate members and reset passwords. Account changes increment a session version, invalidating old login sessions. The owner account is protected against modification through the panel.

For **new installations**, the primary schema includes `is_active` and `session_version`. For **existing V2 installs created before this update**, run `database/004_staff.sql` once using phpMyAdmin before deploying the updated authentication code. Do **not** run 004 on a fresh installation whose schema already contains those columns. Until you run the migration, the new login will fail. Temporary passwords must be shared securely; self-service password changes and email invites are future work.

## Staff applications and rota (Phase 5)
- Public `apply.php` accepts DJ/Event Host applications. It contains a CSRF token, basic honeypot and session cooldown. Apply additional IP-based abuse protection before a large public launch.
- `applications.php` shows pending applications to owner/admin and the relevant radio/events manager. Accepting an application does **not** grant an account or login privileges. Owner can create staff credentials separately in Staff Management.
- `rota.php` shows the current Monday–Sunday staff schedule with approved and pending booking status. Public calendar continues to show approved bookings only.
- Fresh installations import `005_applications.sql` automatically. Existing V2 installs must run `database/005_applications.sql` through phpMyAdmin once.

## Notifications and shift changes (Phase 6)
- In-app notification inbox for staff, including booking decisions and manager alerts on new booking requests.
- Staff can request cancellation of an approved upcoming shift; managers review the cancellation before it is removed from the approved rota.
- New installs import `006_notifications.sql`. Existing V2 installs should apply it once **before deploying these PHP updates**.
- Notifications currently remain **inside the site**: email/Discord delivery and automatic timed reminders are not implemented yet.
- Staff can now select a date/time on the rota page to prefill a booking request on the dashboard. Drag/drop editing, free-slot conflict highlighting, email/Discord delivery and timed reminders remain future milestones.

## Scheduled reminders (Phase 7)
The private PHP CLI task at `v2/cron/reminders.php` creates one in-app notification per approved booking around 30 minutes before its start. It has a uniqueness constraint to avoid repeated alerts. Set up a cPanel cron job to run every five minutes, for example:

`*/5 * * * * /usr/local/bin/php /home/ACCOUNT/private/v2/cron/reminders.php`

Replace both executable and path to match your hosting provider. Keep `v2/cron` outside the public document root. New installs include `007_reminders.sql`; existing installs must import it once before enabling the task. Discord/webhook delivery is **not yet included** and will require a separate opt-in integration. This code requires staging verification with MySQL, PHP CLI, and the host timezone.

## Phase 8: booking concurrency and Discord reminders
Booking approvals now acquire a stable owner-row lock **before** inspecting a booking or approved overlaps, serialising concurrent approvals in the InnoDB transaction. The app continues to allow only one approved booking per type for overlapping times (DJ and event calendars are independent). This does not yet address all DST boundaries or room-specific scheduling.

The owner can optionally enter a Discord webhook URL at **Fansite Settings** and explicitly enable reminders. The private `cron/reminders.php` worker delivers a short message approximately 30 minutes ahead, only for approved bookings, with mentions disabled. The webhook is stored in the private database, should be treated as a secret, and never belongs in source control or screenshots. Discord delivery is best-effort: failures are logged and cannot be retried with the current once-only reminder key. If you need guaranteed delivery, use a persistent outbox in a future milestone.

## Staging validation before release
- Run new installation against a fresh MySQL database: check config and installation lock; confirm setup cannot be accessed again.
- Confirm the owner can sign in, save branding, create staff and view applications.
- Confirm suspended users and users with changed roles lose access immediately.
- Test two managers approving simultaneous overlapping DJ bookings and confirm only one is approved.
- Verify members cannot access staff management or another manager's application queues.
- Approve a DJ booking and confirm its appearance on the public calendar; pending bookings should be private.
- Test shift cancellation workflow and manager approval.
- Schedule an approved booking for 30 minutes ahead, run private CLI cron twice, and confirm exactly one in-site reminder.
- With Discord turned **off**, confirm no webhook requests are sent. Enable and test in a private Discord channel only.
- Confirm that DST changeover days and concurrent installer requests are covered before making the app public.

This milestone was committed remotely; these end-to-end checks still require a deployed PHP/MySQL staging environment.

## Phase 9: Discord rank gate and owner session security
New installs include `008_security.sql`. **For existing V2 deployments import this migration before deploying these files**, or logins will fail.

Staff login records a session hash (never the raw session cookie), direct client IP from `REMOTE_ADDR`, user agent, login timestamp and last-seen time. The owner can review and revoke sessions from **Session Audit**. Use a trusted web server or Cloudflare proxy configuration when determining real visitor addresses; do not blindly trust `X-Forwarded-For`. IP addresses are personal data: restrict access to the owner, publish a privacy notice, establish a retention limit and purge old records.

**Discord rank enforcement is optional and disabled by default.** The owner must create a Discord application and bot, add the bot to the selected server, supply Discord OAuth client ID/secret, bot token, guild ID, required role ID and exactly registered HTTPS OAuth redirect URI (usually `https://YOUR_DOMAIN/discord-callback.php`). The OAuth flow requests `identify` and binds Discord IDs to local accounts after a successful bot-based member role lookup. Connect your own account first from Site Settings, then enable enforcement. Once enabled, protected endpoints recheck Discord membership periodically (5-minute cache in PHP session) and fail closed when the Discord API cannot confirm the role. Existing local roles still determine permissions; having the Discord role alone does not create an account or confer owner permissions.

**Limitations and release blockers:** Bot token and OAuth client secret currently live in private database settings, visible to the owner: secure MySQL backups and consider encrypted-at-rest credential storage or file-based environment secrets before production. Role verification may allow up to five minutes of access after removal due to session caching. Owner account recovery requires securely disabling `discord_access_enabled` in the database if the bot becomes unavailable. The session viewer lacks automatic retention cleanup. Login brute-force rate-limiting, MFA and end-to-end Discord OAuth/role tests are still pending; do not call this production-hardened yet. Keep all installation/config files outside public web root and enforce HTTPS.
