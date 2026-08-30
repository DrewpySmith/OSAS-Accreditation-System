# AI Agent Guidelines: OSAS Accreditation System

This document outlines the core architecture, logic, and patterns of the OSAS Accreditation System. Any AI agent modifying this codebase MUST adhere to these standards.

## 1. System Architecture
- **Framework**: CodeIgniter 4 (PHP 8.x)
- **Database**: SQLite3 (using CI4 Query Builder and Models)
- **Frontend**: Tailwind CSS v4 + shadcn/ui dark theme (React for admin org list)
- **PDF Generation**: Dompdf
- **Build Tool**: Vite (for React components and asset bundling)

## 2. Core Accreditation Logic
The accreditation of an organization is strictly bound to **9 mandatory requirements**. Progress is calculated based on these items verified in the `organization_checklists` table.

### Mandatory Requirements:
1. `application_letter` (Application Letter Form)
2. `officer_list` (Lists of Officers)
3. `commitment_forms` (Commitment Forms of Officers and Advisers)
4. `constitution_bylaws` (Constitution and By-Laws)
5. `org_structure` (Organizational Structure)
6. `calendar_activities` (Plan and Calendar of Activities)
7. `financial_report` (Audited Financial Report)
8. `program_expenditures` (Program of Expenditures)
9. `accomplishment_report` (Accomplishment Reports)

> [!IMPORTANT]
> The `other` field in `document_submissions` MUST NOT be included in the accreditation progress calculation. Progress hits 100% ONLY when the 9 items above are verified.

## 3. Campus Management
The system supports exactly **7 campuses**. This list is centralized in `App\Models\OrganizationModel::CAMPUSES`.

### Official Campus List:
- Palimbang, Isulan, Access, Tacurong, Lutayan, Bagumbayan, Kalamansig

### Pattern for Filtering:
- All organization and document lists MUST support filtering by `campus`.
- Print reports (PDF/HTML) should respect the `campus` filter from the URL.

## 4. Security & AJAX Patterns
### CSRF Protection
CodeIgniter 4 CSRF protection is enabled. CSRF token **regenerates on every request** (`Security::$regenerate = true`). All `POST/PUT/DELETE` requests via AJAX MUST include the CSRF token in the headers.

**Standard Pattern:**
```javascript
headers: {
    'X-Requested-With': 'XMLHttpRequest',
    [csrfHeaderName]: csrfHash // csrfHeaderName is 'X-CSRF-TOKEN'
}
```

**Critical: Update CSRF token from AJAX responses**
```javascript
.then(data => {
    if (data.csrf) {
        const meta = document.querySelector('meta[name="X-CSRF-TOKEN"]');
        if (meta) meta.content = data.csrf;
    }
})
```

### Rate Limiting
A `ThrottleFilter` is applied to login, registration, password reset, and adviser signature endpoints. Limits to **5 attempts per 15 minutes** (per IP).

### Delete Routes
All delete operations use **POST** (not GET). Never add `$routes->get('...delete...')`.

### Session Security
- `Session::$matchIP = true` — sessions bound to IP
- `Security::$regenerate = true` — CSRF token regenerates on each request

### Password Policy
Minimum password length is **8 characters**.

### File Uploads
Uploads MUST validate both:
- `$file->getMimeType()` — MIME type whitelist
- `$file->getSize()` — max file size (typically 10MB)

## 5. View Conventions
- **Admin Side**: Located in `app/Views/admin/`. Uses a sidebar/navbar with dark blue `#2c3e50` primary colors.
- **Organization Side**: Located in `app/Views/organization/`. Uses green `#2f855a` primary colors.
- **Printing**: Print-friendly views should use `@media print` or dedicated minimalist layouts (e.g., `app/Views/admin/organizations/print.php`).

## 6. Model Responsibilities
- **OrganizationChecklistModel**: Central source for progress logic (`calculateProgress`) and completion status (`isComplete`).
- **DocumentSubmissionModel**: Handles file paths and provides the standard `DOCUMENT_TYPES` list.
- **AnnouncementModel**: CRUD + `getForOrg()`, `getUnreadCount()`.
- **AnnouncementReadModel**: Read tracking with unique constraint.
- **ChatMessageModel**: Conversation + inbox + unread queries.

## 7. Database
- **Driver**: SQLite3 (stored at `writable/database/usg_accreditation.db`)
- **Path**: Configured in `app/Config/Database.php` using `WRITEPATH` constant
- **Migration Note**: Foreign key constraints are added via a dedicated migration. On SQLite, the MySQL-specific DDL is wrapped in a `DBDriver === 'MySQLi'` check.

## 8. Additional Features
### Announcements
- Implemented in `Admin\Announcements` and `Organization\Announcements` controllers
- Views at `app/Views/admin/announcements/` and `app/Views/organization/announcements/`
- Targeted by campus or specific organizations

### Chat (1:1 Messaging)
- Implemented in `Admin\Chat` and `Organization\Chat` controllers
- Views at `app/Views/admin/chat/` and `app/Views/organization/chat/`
- Polling-based (3s interval via `setInterval`)
- Admin sees a split-pane inbox + conversation; org sees single conversation

## 9. Future Framework Transitions
- **UI Consistency**: If the system is rebuilt or transitioned to a different framework (e.g., Laravel, React, etc.), every UI component MUST remain consistent with the established design language.
- **Visual Identity**: The distinct color schemes for Admin (Blue `#2c3e50`) and Organization (Green `#2f855a`) must be preserved to maintain user familiarity.

---
*Created on 2026-02-21. Last updated 2026-06-09 to reflect SQLite migration, security hardening, announcements, and chat features.*
