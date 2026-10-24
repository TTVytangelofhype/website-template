# Habbo Fansite V2 — Hybrid Staff Hub (early development)
This is the **new development application**, separate from the legacy PHP homepage at the repository root. Not affiliated with or endorsed by Habbo or Sulake.
## Included in first milestone
- Modern responsive staff dashboard with a Habbo-inspired look
- PHP session login, password verification and CSRF tokens
- Role-based access for site owners, admins, radio managers, event managers, DJs, event hosts and members
- Shared DJ/event booking schedule; requests require manager approval
- Booking overlap checks at approval, activity audit trail, cancel/reject actions
- MySQL schema and example configuration
## Installation (development)
1. Use PHP 8.3+ with PDO MySQL and mbstring, and MySQL 8+/MariaDB.
2. Create a private database and import `database/schema.sql`.
3. Copy `config.example.php` to `config.php` and enter DB credentials. Do **not** commit config.php.
4. Create the first owner with a **one-off local administrator script**, generating the password hash with `password_hash('YOUR_LONG_PASSWORD', PASSWORD_DEFAULT)` and inserting `username,password_hash,role='owner'` into `users`. Remove the script after execution.
5. Point the web document root to `v2/public` (not the repository root or `v2`), and use HTTPS.
6. Log in at `index.php`.
## Next milestones
- Owner-only guided installer / initial account creation
- Real calendar UI, shift swap requests, recurring bookings and room-specific conflicts
- Public fansite and public approved rota
- Secure rate-limiting for login and production security review
- AzuraCast integration, song requests and Discord announcements
**Status:** developer preview. Not yet a finished production release. Keep legacy `main` deployed until tested.
