# Project.md — DM-Log Rebuild (Laravel + Livewire + Tailwind)

## 0) Goal
Rebuild **DM-Log** as a modern, secure, and elegant web app using:
- **Latest stable Laravel (Laravel 12)** :contentReference[oaicite:0]{index=0}
- **Livewire (latest docs = v4.x)** :contentReference[oaicite:1]{index=1}
- **Tailwind CSS** (custom “Ocean” theme, **default dark mode**)

The new app must fully cover the old system’s capabilities discovered via the legacy route map and dependencies:
- Auth (login/logout)
- Issue tracking (open/closed, search, create/update/delete, close/reopen, show)
- Issue export
- Departments CRUD
- Issue Types CRUD
- User management (CRUD + profile update + roles + permissions)
- Reports (index, monthly, yearly, logbook)
- Graphs (issues)
- Statistics dashboard
- API endpoints for datatables/report bars (legacy includes: cars, users, expenses, rents + monthly/yearly bar endpoints) :contentReference[oaicite:2]{index=2}

> Note: The legacy app was Laravel 4.2 + Sentry. Rebuild will use Laravel-native auth + modern role/permission patterns. :contentReference[oaicite:3]{index=3}
>
> Note: Legacy databases have no `users`, `roles`, or `permissions` tables — auth was handled by Sentry. These are new tables for the rebuild.

---

## 1) Product Requirements (What the app must do)

### 1.1 Authentication
- Login page (email/username + password)
- Logout
- “Remember me”
- Rate limiting + lockout rules (security)
- Password reset flow (email-based)
- Optional: 2FA-ready structure (feature flag; not required day-1)

**Legacy parity:** `/login`, POST `/login`, `/logout` :contentReference[oaicite:4]{index=4}

---

### 1.2 Issues (Core Module)
#### Views/flows
- **Dashboard / Issues Index** (default landing after login)
  - Tabs: **Open Issues**, **Closed Issues**
  - Search (keyword)
  - Filters:
    - Department
    - Issue type
    - Priority
    - Date range (created/closed)
    - Assigned-to (optional)
  - Sorting + pagination
  - Bulk actions:
    - Close
    - Reopen
    - Export
- **Create issue**
- **Edit issue**
- **Show issue** (detail page)
- **Delete issue** (soft delete by default; hard delete only for SuperAdmin)

#### State transitions
- Close issue
- Reopen issue

**Legacy parity endpoints:**  
`/` issues.index, `/open-issues`, `/closed-issues`, `/search`, create/store/edit/update/delete, close/reopen, show, export :contentReference[oaicite:5]{index=5}

#### Issue Export
- Export single issue as:
  - PDF (preferred)
  - (Optional) DOCX if needed (legacy used PHPWord + dompdf) :contentReference[oaicite:6]{index=6}
- Export includes: issue metadata + description + status + department + issue type + timestamps + (optional) activity log

---

### 1.3 Departments
- Departments CRUD
- Prevent deletion if it is referenced by issues (or require reassignment)
- Optional: Department “active/inactive” toggle

**Legacy parity:** resource `departments` + delete shortcut :contentReference[oaicite:7]{index=7}

---

### 1.4 Issue Types
- Issue Types CRUD
- Prevent deletion if referenced by issues (or require reassignment)
- Optional: “active/inactive” toggle

**Legacy parity:** resource `issue-types` + delete shortcut :contentReference[oaicite:8]{index=8}

---

### 1.5 User Management (Admin Module)
#### Must include:
- Users CRUD
- Profile page (self-service)
  - Personal info update
  - Account update (email/password)
- Roles management screen
- Permissions management screen

**Legacy parity:** `profile`, `profile/personal`, `profile/account`, `users/roles`, `users/permissions`, `resource users`, and `users/{id}/delete` :contentReference[oaicite:9]{index=9}

#### Roles (minimum)
- SuperAdmin
- Admin
- Staff

*(Legacy referenced these group names in comments/filters.)* :contentReference[oaicite:10]{index=10}

---

### 1.6 Reports
#### Pages
- Reports index
- Monthly report
- Yearly report
- Logbook report

**Legacy parity:** `/reports` (IssuesController@reports) and also `reports.index/monthly/yearly`, plus `reports/logbook` :contentReference[oaicite:11]{index=11}

#### Required report content
- Monthly:
  - Issue counts by status
  - Issue counts by department
  - Issue counts by issue type
  - Avg. time to close (if closing timestamps exist)
- Yearly:
  - Same as monthly but grouped by month
- Logbook:
  - Printable list of issues with key info (date, dept, type, description, status)
  - Export to PDF

---

### 1.7 Graphs + Statistics
- Graphs page:
  - “Issues graph” (trendline) and breakdown charts
- Statistics page:
  - KPI cards (open issues, closed today/week, avg close time)
  - “Top departments with issues”
  - “Top issue types”
  - Trend charts

**Legacy parity:** `/graph`, `/graph/issues`, `/statistic` :contentReference[oaicite:12]{index=12}

---

### 1.8 API (Datatable & Reporting endpoints)
Legacy routes include:
- `GET /api/users` (datatable)
- `GET /api/cars`
- `GET /api/expenses`
- `GET /api/rents`
- `ANY /api/reports/month` (monthly bar data)
- `ANY /api/reports/year` (yearly bar data) :contentReference[oaicite:13]{index=13}

**Rebuild requirement:**
- Keep these endpoints (or provide backward-compatible equivalents) because existing front-end workflows may depend on them.
- If cars/expenses/rents are unused in your actual usage, still implement minimal scaffolding:
  - list + datatable API + basic CRUD screens hidden behind a feature flag (`FEATURE_FLEET=1`) to match legacy parity without forcing UI exposure.

---

## 2) UX / UI Requirements

### 2.1 Design goals
- Elegant, “executive dashboard” look
- Fast interactions, minimal page reloads (Livewire)
- Keyboard-friendly:
  - `/` focuses search
  - `n` new issue
  - `g` then `d` go dashboard
- Accessible:
  - WCAG-friendly contrast
  - focus rings
  - semantic forms and error states

### 2.2 Interaction patterns
- Livewire-powered:
  - Debounced search
  - Filter chips (click to remove)
  - Inline status badge updates
  - Slide-over panel for issue details (optional) + full page detail
- Toast notifications:
  - Success, warning, error
- Confirm dialogs for destructive actions
- Skeleton loaders while filtering/paging
- “Saved views” for filters (optional but recommended)

---

## 3) Tech Stack & Implementation Decisions

### 3.1 Framework versions
- **Laravel 12** :contentReference[oaicite:14]{index=14}
- **Livewire 4.x** :contentReference[oaicite:15]{index=15}
- Tailwind CSS (latest)
- Alpine.js (light usage only, where needed: menus, dialogs, charts)

### 3.2 Authorization
Use policy-based authorization:
- Roles + permissions:
  - Prefer a proven approach (e.g., role/permission tables + Gate/Policy)
- Every action must be guarded:
  - `issues.create`, `issues.update`, `issues.delete`, `issues.close`, `issues.reopen`, `issues.export`
  - `admin.users.*`, `admin.roles.*`, `admin.permissions.*`
  - `admin.departments.*`, `admin.issue-types.*`
  - `reports.*`, `graphs.*`, `statistics.*`

### 3.3 Activity logging (recommended)
Log key actions:
- Issue created/updated/closed/reopened/deleted/exported
- User created/updated/disabled
- Role/permission changes
Stored in `activity_logs` table.

---

## 4) Data Model (Target Schema)

> Create migrations with strict foreign keys + indexes.

### 4.1 Core tables

#### users
- id (uuid or big int)
- name
- email (unique)
- password
- remember_token (nullable, for "Remember me" — §1.1)
- is_active (bool)
- last_login_at (nullable)
- timestamps

#### roles
- id
- name (unique: SuperAdmin, Admin, Staff)
- description

#### permissions
- id
- name (unique, slug: issues.create)
- description

#### role_user (pivot)
- user_id
- role_id

#### permission_role (pivot)
- role_id
- permission_id

#### departments
- id
- name (unique)
- code (nullable)
- is_active (bool)
- timestamps

#### issue_types
- id
- name (unique)
- severity_default (nullable enum/int)
- is_active (bool)
- timestamps

#### issues
- id
- title (varchar — maps to legacy `issue` column)
- description (text)
- name (varchar — guest name)
- room_number (varchar)
- checkin_date (date)
- checkout_date (date)
- issue_date (date — when the issue occurred)
- source (varchar — origin/source of the issue)
- nationality (varchar — guest nationality)
- contact (varchar — guest contact info)
- recovery (text — recovery action taken)
- recovery_cost (int, nullable — cost of recovery)
- training (varchar, nullable — training notes/action)
- priority (enum: low/medium/high/urgent — new field, not in legacy)
- status (varchar — e.g. open/closed; consider expanding to match dm_log_book: open/in_progress/resolved/closed)
- created_by (varchar — maps to legacy; consider fk users in new app)
- updated_by (int, nullable — who last updated)
- assigned_to_user_id (nullable fk users — new field)
- closed_at (nullable datetime)
- closed_by_user_id (nullable fk users)
- deleted_at (soft delete)
- timestamps

> **Note:** Legacy uses `issue` (varchar) as the short title and `description` (text) as the long body. In the rebuild, these map to `title` and `description` respectively.

#### department_issue (pivot — many-to-many)
- id
- department_id (fk departments)
- issue_id (fk issues)
- timestamps

> An issue can belong to **multiple** departments (legacy uses a pivot table, not a direct FK).

#### issue_issue_type (pivot — many-to-many)
- id
- issue_id (fk issues)
- issue_type_id (fk issue_types)
- timestamps

> An issue can have **multiple** issue types (legacy uses a pivot table, not a direct FK).

#### dm_log_book
- id
- guest_name (varchar)
- room_number (varchar, nullable)
- category (varchar — indexed)
- description (text)
- priority (enum: low/medium/high/urgent — default: medium, indexed)
- assigned_to (varchar, nullable)
- status (enum: open/in_progress/resolved/closed — default: open, indexed)
- action_taken (text, nullable)
- created_by (varchar)
- created_at (timestamp, indexed)
- updated_at (timestamp)

> Daily Manager's Log Book — a separate log from Issues. Exists in production DB (`anvq7281_dm-log`). Used for general daily operational logging with its own status workflow (open → in_progress → resolved → closed).

#### issue_comments (optional but recommended for logbook richness)
- id
- issue_id (fk)
- user_id (fk)
- body
- timestamps

#### activity_logs
- id
- actor_user_id (nullable fk users)
- subject_type (string)
- subject_id (string/int)
- action (string)
- meta (json)
- created_at

### 4.2 Laravel Infrastructure Tables
These are provided by Laravel's default migrations but listed here for completeness:

#### password_reset_tokens
- email (primary)
- token
- created_at

> Required by the password reset flow (§1.1).

#### sessions
- id (primary)
- user_id (nullable, indexed)
- ip_address (nullable)
- user_agent (nullable)
- payload (long text)
- last_activity (indexed)

> Laravel 12 uses database sessions by default.

#### cache / cache_locks
- key (primary)
- value (medium text)
- expiration (int)

> Used by rate limiting (§1.1) and general caching.

#### jobs / failed_jobs
- Standard Laravel queue tables

> Needed if PDF export (§10) or password-reset emails (§1.1) are dispatched to queues.

### 4.3 Legacy "Fleet/Finance" scaffolding tables (feature-flagged)
Implement minimal models to satisfy `/api/cars|expenses|rents` parity:
- cars (id, name/plate, meta json, timestamps)
- expenses (id, amount, date, note, timestamps)
- rents (id, renter_name, start/end, meta, timestamps)

---

## 5) Route Map (New App)
All routes use Laravel 12 conventions + middleware.

### Public
- GET `/login` → Login page
- POST `/login`
- POST `/logout`
- GET `/forgot-password`
- POST `/forgot-password`
- GET `/reset-password/{token}`
- POST `/reset-password`

### Authenticated
- GET `/` → Issues dashboard (open issues by default)
- GET `/issues` → (optional alias)
- GET `/open-issues`
- GET `/closed-issues`
- GET `/search` → (optional: redirect to `/` with query)
- GET `/issues/create`
- GET `/issues/{issue}`
- GET `/issues/{issue}/edit`
- POST `/issues`
- PUT/PATCH `/issues/{issue}`
- DELETE `/issues/{issue}` (soft delete)
- POST `/issues/{issue}/close`
- POST `/issues/{issue}/reopen`
- GET `/issues/{issue}/export`

### Admin
- CRUD `/departments`
- CRUD `/issue-types`
- CRUD `/users`
- GET `/profile`
- PUT `/profile/personal`
- PUT `/profile/account`
- GET/PUT `/users/roles`
- GET/PUT `/users/permissions`

### Reports & Insights
- GET `/reports`
- GET `/reports/monthly`
- GET `/reports/yearly`
- GET `/reports/logbook`
- GET `/graph`
- GET `/graph/issues`
- GET `/statistic`

### API
- GET `/api/users`
- GET `/api/cars` (feature flag)
- GET `/api/expenses` (feature flag)
- GET `/api/rents` (feature flag)
- GET `/api/reports/month`
- GET `/api/reports/year`

> Match legacy names where feasible to reduce friction. :contentReference[oaicite:16]{index=16}

---

## 6) Livewire Component Plan

### Layout / Shell
- `AppLayout` (sidebar, topbar, breadcrumbs, dark-mode default)
- `CommandPalette` (optional)

### Issues
- `Issues/Index` (tabs open/closed, search, filters, pagination, bulk actions)
- `Issues/Form` (create/edit; shared)
- `Issues/Show` (detail; includes activity log/comments)
- `Issues/ExportButton` (handles export UX)
- `Issues/QuickClose` (inline close/reopen)

### Admin
- `Admin/Departments/Index`, `Admin/Departments/Form`
- `Admin/IssueTypes/Index`, `Admin/IssueTypes/Form`
- `Admin/Users/Index`, `Admin/Users/Form`, `Admin/Users/Show`
- `Admin/RolesPermissions/ManageRoles`
- `Admin/RolesPermissions/ManagePermissions`

### Reports
- `Reports/Index`
- `Reports/Monthly`
- `Reports/Yearly`
- `Reports/Logbook`

### Insights
- `Graphs/Index`
- `Graphs/Issues`
- `Statistics/Index`

### API helpers
- For datatables: prefer server-side pagination JSON endpoints
- If you adopt Livewire tables, still keep `/api/*` endpoints for parity.

---

## 7) Tailwind Theme — “Ocean” (Dark default)

### 7.1 Dark mode
- Tailwind config: `darkMode: 'class'`
- Default theme should apply `class="dark"` at the HTML root on first load.
- Provide a toggle in UI (persist in DB per user + localStorage fallback)

### 7.2 Ocean palette (recommended)
Define CSS variables to support both modes cleanly.

**Light**
- background: `#F5FAFF` (mist)
- surface: `#FFFFFF`
- border: `#D7E6F5`
- text: `#0B2239`
- muted: `#4B647A`
- primary (ocean): `#1677C8`
- primary-strong: `#0E5EA3`
- accent (seafoam): `#1FB7A6`
- danger: `#E24A4A`
- warning: `#F0A202`

**Dark (default)**
- background: `#071723` (deep ocean)
- surface: `#0B2232`
- surface-2: `#0E2B40`
- border: `#12374F`
- text: `#E6F2FF`
- muted: `#A6C0D6`
- primary (ocean): `#3AA3FF`
- primary-strong: `#1B7ED6`
- accent (seafoam): `#2EE6D1`
- danger: `#FF6B6B`
- warning: `#FFC857`

### 7.3 Component styling rules
- Cards: subtle borders + soft shadow in light; border-forward in dark
- Buttons:
  - Primary: ocean gradient optional (very subtle)
  - Secondary: surface with border
  - Danger: red
- Badges:
  - Open: seafoam
  - Closed: muted gray-blue
  - Urgent: danger
- Tables:
  - Sticky header
  - Row hover highlight
  - Compact density toggle

---

## 8) Dashboard Layout (Elegant + Fast)
### Sidebar (left)
- Issues
  - Open
  - Closed
- Reports
  - Monthly
  - Yearly
  - Logbook
- Insights
  - Graphs
  - Statistics
- Admin (role-gated)
  - Users
  - Departments
  - Issue Types
  - Roles & Permissions
- Fleet/Finance (feature flagged)
  - Cars
  - Expenses
  - Rents

### Topbar
- Search
- Quick add (+ Issue)
- Notifications (optional)
- User menu (Profile, Logout)
- Theme toggle

---

## 9) Reporting & Charts
- Use a chart library compatible with Tailwind + Livewire (minimal JS):
  - Chart.js or ApexCharts
- Charts:
  - Issues over time
  - Issues by department
  - Issues by type
  - Avg close time trend

API endpoints `/api/reports/month` and `/api/reports/year` must return JSON suitable for bar charts (legacy parity). :contentReference[oaicite:17]{index=17}

---

## 10) Exporting
### Issue export
- PDF generation service class:
  - `IssueExportService`
- Template-driven (Blade → PDF)
- File naming convention:
  - `issue-{id}-{YYYYMMDD}.pdf`

### Logbook export
- PDF with page headers/footers
- Filters applied (month/year/department/status)

---

## 11) Security & Compliance Requirements
- DO NOT commit secrets (legacy repo contains sensitive config patterns; new repo must not). :contentReference[oaicite:18]{index=18}
- Use `.env` only
- CSRF everywhere
- Auth middleware + authorization policies
- Audit logging for admin actions
- Rate limiting on login
- Optional: IP allowlist for admin routes (configurable)

---

## 12) Seed Data & Local Dev
### Seeders
- Roles: SuperAdmin, Admin, Staff
- Permissions: full set (see section 3.2)
- Demo departments + issue types
- Demo users:
  - superadmin@example.com / password
  - admin@example.com / password
  - staff@example.com / password

### Local dev tooling
- Docker Compose recommended:
  - app (php-fpm)
  - nginx
  - mysql/postgres
  - redis (optional)
- `php artisan migrate:fresh --seed`

---

## 13) Testing Requirements
- Feature tests:
  - Auth flow
  - Role-gated routes
  - Issue create/update/close/reopen/export
  - Reports endpoints
- Unit tests:
  - Issue close time calculations
  - Report aggregation services
- Livewire tests for key components:
  - Filtering/pagination works
  - Authorization denies correctly

---

## 14) Implementation Plan (Phased)
### Phase 1 — Foundation
- Laravel 12 base app
- Auth scaffolding
- Tailwind Ocean theme (dark default)
- Layout shell + navigation
- Roles/permissions core

### Phase 2 — Core Issues
- Issues CRUD + open/closed + search/filter
- Close/reopen
- Show page
- Activity logs

### Phase 3 — Master Data + Users
- Departments CRUD
- Issue Types CRUD
- Users CRUD + profile + roles + permissions screens

### Phase 4 — Reports, Graphs, Statistics
- Reports index/monthly/yearly/logbook
- Graphs pages
- Statistics dashboard
- API endpoints for month/year bars

### Phase 5 — Export + API parity + Polish
- PDF exports
- Datatable endpoints
- Fleet/Finance scaffolding (feature-flag)
- UX polish: keyboard shortcuts, toasts, skeleton loaders

---

## 15) Acceptance Criteria Checklist
- [ ] All legacy routes/features are available (or mapped 1:1 with documented redirects) :contentReference[oaicite:19]{index=19}
- [ ] User management includes profile + roles + permissions :contentReference[oaicite:20]{index=20}
- [ ] Issues: open/closed/search/create/edit/delete/show/close/reopen/export :contentReference[oaicite:21]{index=21}
- [ ] Reports: index/monthly/yearly/logbook + month/year API endpoints :contentReference[oaicite:22]{index=22}
- [ ] Graphs + statistics pages :contentReference[oaicite:23]{index=23}
- [ ] Tailwind “Ocean” theme implemented for light/dark; default dark
- [ ] Elegant dashboard + interactive UX (Livewire-first)
- [ ] Tests cover critical flows
- [ ] No secrets committed; production-safe configuration

---

## 16) Notes for Claude Code (How to build it)
When generating code:
- Use Livewire full-page components for main screens
- Keep business logic in Service classes:
  - `IssueService` (state transitions)
  - `ReportService` (aggregations)
  - `ExportService` (PDF)
- Enforce policies at the component level and in controllers/endpoints
- Keep JSON endpoints fast + indexed queries
- Use DTOs or query objects for filters (avoid messy controller code)

---

## 17) References (Legacy Source of Truth)
Legacy route definitions and feature map are derived from:
- `app/routes.php` :contentReference[oaicite:24]{index=24}
- `composer.json` dependencies indicating export/report tooling :contentReference[oaicite:25]{index=25}
And target framework choices:
- Laravel 12 release docs :contentReference[oaicite:26]{index=26}
- Livewire v4 docs & releases :contentReference[oaicite:27]{index=27}
