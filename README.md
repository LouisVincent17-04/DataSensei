# DataSensei

DataSensei is a learning and analytics platform for data-science education. It includes lessons, challenges, instructor assignments and manual assessments, ILO mastery, competency monitoring, rule-based performance/risk segmentation, an isolated Python/SQL workspace, gamification, and a Hybrid Machine Learning laboratory.

This README is intentionally written as a defense-day checklist.

## What you need

- PHP 8.2 or newer
- Composer
- Node.js 22.12.x and npm 10.x (the pinned version is in `.nvmrc`)
- MySQL 5.5.19 or newer within the existing 5.5-compatible deployment
- Docker Desktop when demonstrating the isolated Python runner or Hybrid ML training
- A modern web browser
- Optional: Ollama for the AI code-review feature
- Optional: working SMTP settings for password-reset email

Do not share your `.env` file. It contains machine-specific configuration and may contain passwords.

## First setup on an empty database

1. Copy `.env.example` to `.env`.
2. Put your MySQL database name, username, and password in `.env`.
3. Run these commands from the DataSensei folder:

```text
composer install
node --version
npm --version
npm ci
npm run build
php artisan key:generate
php artisan migrate
php artisan db:seed
php artisan storage:link
```

Never copy `node_modules` from another computer or include it in a release. `npm ci` installs the correct native Rollup/esbuild packages for the current operating system. The release script includes only the versioned files produced in `public/build`.

`public/storage` must not be a normal directory in the source or release. If an old copy exists, remove that directory without deleting `storage/app/public`, then run `php artisan storage:link`. The preflight verifies both the resolved target and an actual write/read probe.

4. If you will demonstrate Python or Machine Learning, build the Docker runner once:

```text
docker build -t datasensei-python-runner:latest docker/python-runner
```

5. Check everything without showing secrets:

```text
php artisan datasensei:preflight
```

Fix every `FAIL` before the defense. `WARN` means the main system can start, but the named optional feature may not be ready.

## The first superadmin account

`php artisan db:seed` loads reference data only. It creates no accounts, and the sign-up form always creates a learner, so an empty database has nobody who can open the superadmin or admin workspace. Create the first superadmin once, from the DataSensei folder:

```text
php artisan tinker
```

Then paste one line and press Enter:

```text
App\Models\User::create(['name' => 'Platform Owner', 'email' => 'owner@example.com', 'password' => Illuminate\Support\Facades\Hash::make('ChangeThisPassword!1'), 'role' => App\Models\User::ROLE_SUPERADMIN, 'status' => 'active']);
```

Type `exit` to leave tinker, sign in with that email, and change the password from the Profile page. Everything else is done from the interface: the superadmin creates institutions, appoints an institution admin with the `Inst. Admin` button on the Users page, and promotes an account to Admin with the `Promote` button.

Use `php artisan db:seed --class=DefenseDemoSeeder` instead if you want the ready-made demo accounts described below. That seeder refuses to run on a database that already has accounts, so it cannot be used to add a superadmin to a live installation.

## Easiest defense-day start

Double-click:

```text
start-defense.bat
```

It first runs the preflight check. If required checks pass, it opens four windows:

1. Laravel web server
2. Normal background queue worker
3. Machine Learning queue worker
4. Laravel scheduler

Keep all four windows open. Then open:

```text
http://127.0.0.1:8000
```

## Safe defense demo database

Use this only on a separate empty demo database:

```text
php artisan migrate
php artisan db:seed --class=DefenseDemoSeeder
```

`DefenseDemoSeeder` refuses to run in production and refuses to run when it finds user or academic-history data. It does not erase a live database.

The demo includes a small class, two learners, one five-minute timed assignment with explicit ILO mappings, one concise curated MCQ challenge, saved analytics evidence, curriculum samples, and bundled Hybrid ML reference assets.

Demo password for every account below:

```text
Demo!2026Secure
```

Demo accounts:

```text
superadmin@datasensei.test
admin@datasensei.test
instadmin@datasensei.test
instructor@datasensei.test
learner@datasensei.test
sample@datasensei.test
```

`instadmin@datasensei.test` is the institution admin of DataSensei Demo University, whose institution code is `DEMO26`. The seeded learners already belong to that institution, so to show the instructor application flow, register a new account on the sign-up form, open Profile, then Institution, and enter `DEMO26`. The institution admin can then approve or reject it.

These credentials are intentionally public demo credentials. Never reuse this password for a real account.

## MySQL 5.5.19 compatibility rules

The application keeps its database-dependent implementation compatible with the existing MySQL 5.5.19 deployment:

- JSON-like application data is stored in `LONGTEXT` and encoded/decoded by the application, not native MySQL JSON columns.
- Unspecified `VARCHAR` fields use a 189-character schema default. This leaves room for an 8-byte key beside an utf8mb4 string in composite indexes and stays under the older InnoDB 767-byte limit.
- Compatibility migrations query basic `information_schema` columns directly and do not depend on newer `generation_expression` metadata.
- New repairs do not use window functions, CTEs, generated columns, expression defaults, or newer `ALTER TABLE ... IF EXISTS` syntax.
- Existing executed migrations are left intact; repairs that change stored data use new forward-only safe migrations.

The preflight prints the connected database server version so the defense machine can be verified before presenting.

## Important behavior to know for the defense

### Timed assignments

Assignment and TOS-assessment time is enforced on the server from the saved attempt start time. Refreshing the browser does not reset it. Answers are versioned and auto-saved on the server while the attempt is active, then restored after refresh. At the exact deadline, new snapshots stop, the form auto-submits, and the latest saved/current answers are graded instead of being replaced with an empty answer set. `timed_out_at` records expiry separately from the existing grading and late-submission status.

### ILO mastery

An assignment question contributes to ILO mastery only when an administrator explicitly maps that question to the ILO. The system no longer assumes that every question in a module proves every ILO. Instructor-built TOS assessments use the ILO already attached to each assessment question. Only the learner's latest completed attempt for an activity contributes to the current mastery calculation.

### Analytics and risk

Opening Analytics, Risk, or Competency pages only reads the last saved snapshot. It does not silently recalculate data. Instructors use the visible `Recalculate` button when they want new snapshots. This avoids expensive writes on normal page loads.

The student performance labels are rule-based performance segments, not machine-learning clusters. The separate Hybrid ML laboratory still supports genuine K-Means clustering.

### Hybrid ML and competencies

Completed class-linked Hybrid ML training can contribute model-development evidence to competency monitoring. Private models with no class are excluded from class reports. Predictions do not artificially increase the model score or evidence score. Classification percentages, regression R², and clustering silhouette metrics are normalized according to their actual scales.

### Account deactivation

Learners deactivate their account instead of deleting their database row. Login is disabled, tokens are invalidated, and academic submissions/history remain linked for institutional integrity. Staff accounts remain administrator-controlled.

## Clean package for another defense PC

On Windows PowerShell:

```text
powershell -ExecutionPolicy Bypass -File scripts/package-defense.ps1
```

The wrapper runs `npm ci` and `npm run build` on the current computer, then calls `scripts/package-release.py` to create `DataSensei-defense-clean.zip`. The allow-list leaves out `.env`, Git history, `node_modules`, `vendor`, user IDE workspaces, SQL sandbox databases, uploads, user ML artifacts, sessions, cache, and logs. It includes only Vite's versioned `public/build` output, keeps the immutable bundled ML system assets, writes `RELEASE_MANIFEST.sha256`, scans for high-confidence secrets, and verifies every ZIP member before publishing the archive.

The script refuses to overwrite an existing ZIP; rename or remove the old archive first if you intentionally want a new one.

Linux/macOS or direct Python usage:

```text
node --version
npm ci
npm run build
python3 scripts/package-release.py --output DataSensei-release-clean.zip --include-built-assets
python3 scripts/package-release.py --verify-only DataSensei-release-clean.zip
```

Never distribute a ZIP made directly from the working folder. If an earlier archive contained `.env`, sessions, logs, uploads, workspaces, or sandbox databases, treat it as compromised: stop sharing it, rotate the database and SMTP credentials, generate a new `APP_KEY`, invalidate active sessions, review the exposed logs/files, and create a new archive only with the clean packager. Changing the repository cannot revoke credentials that have already escaped.

## Forward-only migration safety

`2026_08_01_000002_create_hybrid_ml_module_tables.php`, `2026_08_01_000003_create_queue_tables_for_ml_training.php`, and `2026_08_30_000001_preserve_timed_submission_answers.php` are intentionally forward-only. They conditionally adopt tables or columns that may pre-date their migration records, so a rollback cannot prove ownership and must not delete them. Back up the database before deployment and restore a verified backup if these changes must be reversed; do not use `migrate:rollback` as a data-removal mechanism for these migrations.

## Quick defense flow

1. Run `php artisan datasensei:preflight` about 30 minutes before presenting.
2. Start Docker Desktop if Python/ML is part of the demo.
3. Run `start-defense.bat`.
4. Log in as the instructor and show the class, timed assignment, ILO mapping, saved analytics, and competency matrix.
5. Use `Recalculate Now` so judges can see that analytics refresh is explicit.
6. Log in as `learner@datasensei.test`, start the timed assignment, refresh once to prove the timer does not reset, then submit.
7. Return to the instructor view and recalculate analytics/competencies.
8. Demonstrate a bundled ML dataset/model first. Start live training only if the ML worker and Docker checks passed.

For a defense, reliability beats improvisation: use the prepared demo path first, then answer deeper questions with the live features.
