# 🏨 Habbo Fansite — Radio, Events & Staff Management

An **independent Habbo-inspired fansite platform** with a retro hotel-style public website and a modern unified staff panel. Built with **PHP + MySQL/MariaDB** for community radio stations, DJs, event hosts, and fansite owners.

> **Status: Developer preview / beta preparation.** The V2 project has been merged into `main`, but live deployment and security checks are still required. This project is not affiliated with, sponsored by, or endorsed by Habbo or Sulake.

## ✨ Features

| Area | Included functionality |
| --- | --- |
| Public fansite | Habbo-inspired homepage, branding, themes and event calendar |
| Radio | HTML5 stream player, listener song requests and DJ moderation |
| Bookings | DJ shows and event requests, manager approvals, rota and cancellations |
| Staff | Owner-managed accounts, ranks, suspensions and password resets |
| Recruitment | Public DJ/event-host applications and management review |
| Notifications | In-site booking alerts, reminders and optional Discord webhook alerts |
| Discord access | Optional OAuth account linking and required Discord server role verification |
| Security | Staff-session IP audit and revocation; optional Frank-style denied-access patch |
| Installation | Browser installer that creates tables and writes a private config file |

### Repository layout

The **current V2 application lives inside `v2/`**, even though it has been merged into `main`:

```text
website-template/
├── README.md                 ← You are here
├── v2/
│   ├── app/                  ← PHP helpers and authentication
│   ├── cron/                 ← Scheduled jobs
│   ├── database/             ← SQL schema and migrations
│   ├── public/               ← Website document root
│   ├── config.example.php
│   └── README.md             ← Full installation and support guide
└── ...                       ← Original/legacy template files
```

**[📖 Complete installation, security and troubleshooting guide](v2/README.md)**

## 🚀 Quick start — install with cPanel

You do **not** need to edit `config.php` manually for a fresh installation.

1. **Hosting:** Use PHP 8.3+ (recommended), MySQL/MariaDB, HTTPS and PHP extensions `pdo_mysql`, `mbstring`, and `curl` for Discord features.
2. **Database:** In cPanel → **MySQL Databases**, create a new empty database and database user; assign the user the required privileges. Save the complete database name, username and password.
3. **Upload:** Put the contents of `v2/` in a **private application directory**. Set the domain or subdomain document root to that directory's **`public/` folder only**, not to the repository root.
4. **Installer:** Visit `https://YOUR-DOMAIN/setup.php`. In cPanel File Manager, read the generated one-time `install-token.txt` from the private application directory.
5. **Customise:** Enter the database details, fansite name, tagline, optional HTTPS radio stream, and owner username/password (minimum 14 characters).
6. **Finish:** The installer creates the database tables and private `config.php`, and locks further installation. Open `/login.php` to manage your fansite.

**After installation:** Restrict permissions on the private application directory, keep `config.php` outside public web access, use HTTPS and test the entire installation on a staging domain before launching.

> **Frank migration note:** The current installer in `main` imports SQL files through `008_security.sql`. If you want the newer `009_access_denials.sql` table, import it once in phpMyAdmin or update the installer's SQL import list *before* running a fresh install.

## 🎧 Staff panel and ranks

Owners can create accounts and assign the local fansite ranks: **Owner**, **Admin**, **Radio Manager**, **Events Manager**, **DJ**, **Event Host**, and **Member**. Local ranks determine the actions each account can perform, including booking approvals, application reviews and site administration.

The DJ and event calendar publishes **approved** shows publicly. Staff can request bookings and cancellations, and managers can review them.

## 🔒 Discord login security

Discord role enforcement is **optional and OFF by default**. Discord integration requires both an authorised local fansite account and a matching Discord role when enabled. A Discord webhook by itself cannot verify roles.

To configure it:

1. Create an application in the [Discord Developer Portal](https://discord.com/developers/applications).
2. Register your fansite's exact **HTTPS OAuth callback URL**, typically `https://YOUR-DOMAIN/discord-callback.php`.
3. Create a bot, add it to your Discord server, and record the **Client ID**, **Client Secret**, **Bot Token**, **Guild ID** and **required Staff Role ID**.
4. Enter the details under **Fansite Settings** while role enforcement is still disabled.
5. **Connect and verify your own Discord account first.** Check the role from an additional test account before enabling the gate.
6. Enable **Enforce required Discord role** only after confirming the connection works.

A Discord role is a **second gate**, not a substitute for the local account and local permissions. Discord API outages or removed roles may deny access; the project currently caches successful role checks for approximately five minutes.

**Never publish** bot tokens, client secrets, webhook URLs or `config.php`. See the [full Discord setup guide](v2/README.md#d-discord-roles-and-access) before enabling enforcement.

## 🛎️ Frank — Access Denied & IP logging

The planned Habbo-inspired Frank stop screen explains that a staff member cannot enter without the required Discord role. Owners can review denied access attempts including **date/time, account, IP address, user agent, reason and requested page**.

**Current main-branch verification:** `v2/database/009_access_denials.sql` exists, but the following expected patch files were **not found at their expected GitHub paths** during this README update:

- `v2/app/access-denial.php`
- `v2/public/access-denied.php`

Do not assume the Frank redirect or denial log is active just because its SQL file is present. If you uploaded the patch to your web host rather than GitHub, follow the [Frank integration instructions](v2/README.md#f-connect-the-frank-security-patch) and verify the PHP files, logging hook, database table and owner audit page on staging.

For existing logged-in staff, the separate **Session Audit** system stores a hashed session identifier, IP address, browser, start time, last-seen time and revocation state. **Only the owner should see IP security records.** IP addresses can be personal data; disclose the logging in your privacy notice and apply sensible retention/deletion.

## ⏰ Scheduled jobs

For approved bookings, the PHP CLI reminder worker can add an in-site notification roughly 30 minutes before a shift. In cPanel → **Cron Jobs**, use the correct PHP binary and your actual private directory:

```cron
*/5 * * * * /usr/local/bin/php /home/ACCOUNT/habbo-v2/cron/reminders.php
```

For the optional Frank denied-access cleanup patch, the daily job is:

```cron
15 3 * * * /usr/local/bin/php /home/ACCOUNT/habbo-v2/cron/purge-access-logs.php
```

**Replace the example paths** with the correct paths on your hosting provider. Confirm that the scripts exist and the required tables have been created before enabling cron jobs. The denied-access cleanup does not automatically clean up staff session logs.

## 🧰 Updating an existing fansite

Back up **both** the entire application directory and MySQL database before upgrading. Apply only missing migrations from `v2/database/`, in order. **Do not re-run the full base schema on an existing database.** In particular, `004_staff.sql` alters the `users` table and must not be rerun when those columns already exist.

See [database migration instructions](v2/README.md#b-update-an-existing-v2-installation) for the full table and import steps.

## ❓ Troubleshooting

| Problem | Check first |
| --- | --- |
| Installer displays 404 | Domain document root must point to `v2/public` |
| Installer says already configured | Use `/login.php`; do not reinstall over your data |
| Database login fails | cPanel-prefixed DB name/user, DB host and user privileges |
| Fatal error about `staff_sessions` | Confirm migration `008_security.sql` was applied |
| Missing `panel_access_denials` | Import `009_access_denials.sql` once |
| No Frank stop page | Confirm the patch PHP files and bootstrap redirect hook are installed |
| Discord says access denied | Check Guild/Role IDs, bot membership, OAuth redirect and user role |
| Owner is locked out | Use the documented private database recovery method to disable enforcement temporarily |
| Cron reminders are missing | Check PHP CLI path, job schedule, booking approval and server error logs |
| IP address shows the proxy | Ask your host about trusted reverse-proxy IP restoration |

**[🆘 Full troubleshooting and help instructions](v2/README.md#h-troubleshooting-quick-reference)**

### Reporting an issue

Open a [GitHub issue](https://github.com/TTVytangelofhype/website-template/issues) with the affected page, reproduction steps, expected and actual behaviour, PHP version, and **sanitised** error logs or screenshots.

Never share passwords, Discord tokens, webhook URLs, private config files, session cookies, installation tokens or unredacted visitor IP logs.

## 🧪 Before any production release

The project needs live PHP/MySQL tests, rate limiting for the login page, session expiry and retention policies, owner account recovery, Discord OAuth tests, and an independent security review. Existing functionality in the repository should be treated as a developer preview until those checks pass.

---

**Repository:** https://github.com/TTVytangelofhype/website-template  
**Full documentation:** [v2/README.md](v2/README.md)  
**License:** Review the repository's [LICENSE](LICENSE) for terms.

*Habbo and its associated marks belong to their respective owners. This is an independent fan project.*
