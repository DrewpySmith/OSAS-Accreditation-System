# OSAS UI Design Document
**USG Accreditation System — Complete Interface Reference**

---

## 1. Design Philosophy

The system uses a **glassmorphism-inspired dark-mode aesthetic** as the default, with a toggleable light mode. The UI is split into two distinct color identities:

- **Admin Portal** — Deep blue navy: primary hue `#2c3e50`  
- **Organization Portal** — Forest green: primary hue `#2f855a`

All surfaces use Tailwind CSS utility classes with a shared design token system defined in `app.css`.

---

## 2. Design Tokens

### Color Palette

| Token | Dark Mode | Light Mode | Usage |
|---|---|---|---|
| `--color-background` | `hsl(222.2 84% 4.9%)` | `hsl(0 0% 100%)` | Page background |
| `--color-foreground` | `hsl(210 40% 98%)` | `hsl(222.2 84% 4.9%)` | Body text |
| `--color-card` | Same as background | White | Card/panel surface |
| `--color-muted` | `hsl(217.2 32.6% 17.5%)` | `hsl(210 40% 96.1%)` | Subtle backgrounds |
| `--color-muted-foreground` | `hsl(215 20.2% 65.1%)` | `hsl(215.4 16.3% 46.9%)` | Secondary text |
| `--color-border` | `hsl(217.2 32.6% 17.5%)` | `hsl(214.3 31.8% 91.4%)` | Card/input borders |
| `--color-destructive` | `hsl(0 62.8% 30.6%)` | `hsl(0 84.2% 60.2%)` | Danger/delete actions |

### Accent Colors (Context-Specific)

| Color | Hex / Tailwind | Usage |
|---|---|---|
| Admin Sidebar | `#2c3e50` | Sidebar background |
| Admin Accent | `#3498db` / `blue-500` | Primary CTAs |
| Org Sidebar | `#2f855a` / `green-700` | Org sidebar |
| Success | `green-500` / `#22c55e` | Active status, success toasts |
| Warning | `yellow-500` | Suspended status |
| Danger | `red-500` | Inactive, errors |
| Blue CTA | `#2563eb` / `blue-600` | Primary buttons, modal headers |

---

## 3. Typography

- **Font Family**: `Inter` (Google Fonts), fallback `sans-serif`
- **Weights used**: 300 (light), 400 (regular), 500 (medium), 600 (semibold), 700 (bold)

| Style | Class | Usage |
|---|---|---|
| Page Title | `text-3xl font-bold tracking-tight` | Main `<h2>` on each page |
| Section Heading | `text-lg font-semibold` | Cards, panel titles |
| Sub-heading | `text-base font-semibold` | Table headers, modal sections |
| Body | `text-sm` | Default body text in tables and forms |
| Caption / Label | `text-xs font-medium text-muted-foreground` | Filter labels, meta text |
| Acronym / Tag | `text-sm text-muted-foreground` | Org acronym under name |

---

## 4. Layout System

### Admin Layout (`layouts/admin_modern.php`)

```
┌─────────────────────────────────────────────────────┐
│  Sidebar (fixed, w-56 / collapsed w-18)             │
│  ┌──────────────────────────────────────────────┐   │
│  │  Logo + System Name                          │   │
│  │  ─────────────────                           │   │
│  │  Overview section                            │   │
│  │  [→] Dashboard                               │   │
│  │  Manage section                              │   │
│  │  [→] Organizations                           │   │
│  │  [→] Documents                               │   │
│  │  Insights section                            │   │
│  │  [→] Statistics                              │   │
│  └──────────────────────────────────────────────┘   │
│                                                     │
│  Top Navbar (w-full, sticky)                        │
│  [ ☀/🌙 Theme Toggle ]  [ Welcome, admin ]   [A]  │
│                                                     │
│  Main Content Area (flex-1, p-6)                    │
│  ─────────────────────────────────────────────      │
└─────────────────────────────────────────────────────┘
```

- Sidebar is **collapsible** — clicking a toggle icon shrinks it to icon-only mode
- Active nav items are highlighted with `bg-white/10` + blue text tint
- Sidebar persists `collapsed` state in `localStorage`

### Organization Layout (`layouts/org_modern.php`)

Same 2-column structure but with green sidebar color scheme:

```
Sidebar sections: Overview (Dashboard, Notifications)
                  Accreditation (Accreditation Checklist)
                  Documents (Document Submissions)
                  Forms (Calendar, Program Expenditures,
                         Financial Reports, Accomplishment Reports,
                         Commitment Forms)
```

---

## 5. Shared Component Library

### Buttons

| Variant | Classes | Use Case |
|---|---|---|
| Primary | `bg-blue-600 text-white hover:bg-blue-700 rounded-md` | Create, Submit, Save |
| Secondary | `border border-white/10 bg-card hover:bg-muted rounded-md` | Cancel, Print, Back |
| Danger | `hover:text-red-400 hover:bg-red-500/10 rounded-md` | Delete |
| Icon Button | `h-8 w-8 rounded-md flex items-center justify-center` | Table action cells |

All buttons include: `transition-all hover:-translate-y-0.5 active:scale-95`

### Status Badges

| Status | Classes |
|---|---|
| Active | `bg-green-500/10 text-green-500 border border-green-500/20` |
| Inactive | `bg-red-500/10 text-red-500 border border-red-500/20` |
| Suspended | `bg-yellow-500/10 text-yellow-500 border border-yellow-500/20` |
| Pending | `bg-yellow-500/10 text-yellow-500` |
| Approved | `bg-green-500/10 text-green-500` |
| Rejected | `bg-red-500/10 text-red-500` |
| Reviewed | `bg-blue-500/10 text-blue-500` |
| Submitted | `bg-green-500/10 text-green-500 border border-green-500/20 rounded-full` |
| Draft | `bg-yellow-500/10 text-yellow-500 border border-yellow-500/20 rounded-full` |

### Cards

Standard panel: `rounded-xl border bg-card p-6 shadow-sm`

### Data Tables

```
rounded-xl border bg-card overflow-hidden
└── overflow-x-auto
    └── <Table>
        ├── <TableHeader> bg-muted/50
        │   └── <TableHead> h-10 px-4 text-muted-foreground font-medium
        └── <TableBody>
            └── <TableRow> hover:bg-muted/30 transition-colors
                └── <TableCell> px-4 py-3
```

Empty state: centered icon + message inside a single full-span cell.

### Toast Notification

Fixed top-right success/error strip:

```
fixed top-4 right-4 z-[100] flex items-center gap-2
p-4 rounded-lg border animate-slide-up
```

### Form Fields

```
Input:    w-full px-4 py-3 rounded-xl border border-white/10
          bg-muted/50 focus:ring-2 focus:ring-blue-500/20 outline-none
Select:   same as input + appearance-none cursor-pointer
Textarea: same as input + resize-none
Label:    text-sm font-semibold text-muted-foreground ml-1
```

---

## 6. Modal System

All modals use **Radix UI `Dialog`** with custom animations.

### Modal Shell

```
Dialog.Overlay: fixed inset-0 bg-black/60 backdrop-blur-sm z-50 animate-fadeIn
Dialog.Content: fixed left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2
                max-w-{size} bg-card rounded-3xl shadow-2xl border border-white/10
                animate-modalSlideIn
```

### Modal Gradient Header

All modal headers use a blue/indigo gradient:

```css
bg-gradient-to-r from-blue-600 to-indigo-600
```

Contains: icon (`bg-white/20 rounded-xl`), title (`text-white bold`), subtitle (`blue-100/80`), close button (X).

### Active Modals

| Modal | Trigger | Width | Content |
|---|---|---|---|
| `OrganizationActionModal` | New Org / Edit button | `max-w-2xl` | 2-step form with animated step transitions |
| `OrganizationDetailsModal` | Eye icon in Org list | `max-w-2xl` | Org profile, checklist progress, campus info |
| `OrganizationStatisticsModal` | Org card on Statistics page | `max-w-4xl` | Academic years, quick links, 4 data tables |

### Multi-Step Form (OrganizationActionModal)

Step transitions use CSS `translate-x` + `opacity` with `pointer-events-none` on hidden step:

```
Step 1  →  Step 2
slide-left  slide-in
```

Progress bar: 2 `flex-1 h-1.5 rounded-full` bars — active = `bg-white`, inactive = `bg-white/20`

### Success Overlay

Appears on successful form submit:

```
absolute inset-0 bg-gradient-to-br from-blue-600 to-indigo-700
flex items-center justify-center z-50 animate-fadeIn
  └── icon circle: w-20 h-20 bg-white rounded-full animate-scaleIn
      └── SVG checkmark (blue-600)
  └── h3: "Saving Changes..." text-white
```

---

## 7. Animation Keyframes

| Name | Effect | Easing |
|---|---|---|
| `modalSlideIn` | opacity 0→1, scale 0.95→1, translate-y -48%→-50% | `cubic-bezier(0.34, 1.56, 0.64, 1)` (spring) |
| `fadeIn` | opacity 0→1 | `ease-out` |
| `scaleIn` | scale 0→1.1→1 | `cubic-bezier(0.34, 1.56, 0.64, 1)` (bounce) |
| `animate-slide-up` | translateY + opacity entrance | Staggered with `animation-delay-{n}00ms` |
| `animate-fade-in` | opacity entrance | Used for table rows, staggered |

---

## 8. Screen Inventory — Admin Portal

### 8.1 Login Page (`/login`)

- Centered auth card on dark background
- Logo + system name at top
- Email + Password fields
- Primary "Login" button
- Error flash message area

---

### 8.2 Admin Dashboard (`/admin/dashboard`)

**Layout**: 4-column stat cards → chart → recent submissions table

**Stat Cards** (animated entrance, staggered):

| Card | Color Accent | Data |
|---|---|---|
| Total Organizations | Blue | Count of active orgs |
| Total Documents | Indigo | All submissions |
| Pending Review | Yellow | Pending count |
| Approved | Green | Approved count |

**Chart**: Bar chart via Chart.js (document submissions over time) — `canvas#submissionsChart`

**Recent Submissions Table**: Last 10 submissions — columns: Organization, Document Type, Status badge, Submitted Date, Actions (View link)

---

### 8.3 Manage Organizations (`/admin/organizations`)

**Page Header**: "Manage Organizations" sub-title, Campus filter dropdown, Print List button, **+ New Organization** (blue CTA)

**Organizations Table** (React: `OrganizationsList.jsx`):

| Column | Notes |
|---|---|
| `#ID` | Muted foreground |
| Organization Details | Name (bold) + acronym (muted) |
| Campus | Blue accent text |
| Status | Colored badge |
| Created Date | Hidden on mobile |
| Actions | 👁 View Details, ✏️ Edit, 🗑 Delete |

Empty state: Building icon + "No organizations found" message

**Modals on this page**: `OrganizationActionModal` (Create/Edit), `OrganizationDetailsModal` (View)

---

### 8.4 Organization Details Modal

Triggered by Eye icon. Shows:
- **Header**: Gradient blue/indigo, org name + acronym
- **Profile Tab**: Full org info, campus, status, description, date
- **Accreditation Tab**: Checklist progress bar (9 required documents), each item with ✓ or ✗

---

### 8.5 Document Submissions (`/admin/documents`)

**Filters Bar** (6 columns on desktop):
Organization | Campus | Status | Document Type | Academic Year | [Apply] button

**Documents Table**:

| Column |
|---|
| Organization |
| Campus |
| Document Type |
| Academic Year |
| Document Title |
| Status badge |
| Submitted Date |
| Actions: View button |

**Print Reports** button → opens print-friendly table in new tab

---

### 8.6 Document Review (`/admin/documents/view/{id}`)

Full-page dual-pane layout:

```
┌──────────────────────┬───────────────────────────────┐
│  LEFT PANE           │  RIGHT SIDEBAR                │
│  File Viewer         │  Organization Info card        │
│  (PDF/image embed)   │  Document Metadata card        │
│                      │  Review Status Actions card    │
│                      │  [ Pending / Reviewed /        │
│                      │    Approved / Rejected ]       │
│                      │  Discussion / Comments card    │
│                      │  Review History Timeline       │
└──────────────────────┴───────────────────────────────┘
```

Status action buttons update via AJAX. Comments POST via AJAX with CSRF token.

---

### 8.7 Statistics (`/admin/statistics`)

**Summary Cards Row**:
- Uploaded Docs (This Year) — `text-blue-400`
- Total Organizations — `text-green-400`
- Total Activities — `text-orange-400`

**Bar Chart**: Chart.js bar chart of "Activities per Organization"

**Organization Cards Grid** (React: `StatisticsOrgCards`):
- 3-column responsive grid
- Each card: org name, acronym, campus, chevron arrow
- On click → opens `OrganizationStatisticsModal` (no page redirect)

**Comparison Tool**:
- Multi-select checkboxes for organizations and years
- Metric dropdown (Total Collection / Total Expenses / Remaining Fund)
- "Generate Comparison" → renders Chart.js line chart + data table

---

### 8.8 Organization Statistics Modal

Large scrollable modal (`max-w-4xl`):

| Section | Details |
|---|---|
| Academic Years | List of years from financial reports |
| Quick Links | View Documents, Calendar download/print, Expenditure download/print |
| Commitment Forms | Table: officer, position, year, status, Download/Print |
| Approved Financial Reports | Table: year, title, submitter, date, View/Download |
| Financial Reports (Yearly) | Year, ₱ Collection, ₱ Expenses, ₱ Remaining, Status, Download/Print |
| Program Expenditure Summary | Year, ₱ Grand Total, Download/Print |

---

## 9. Screen Inventory — Organization Portal

### 9.1 Org Dashboard (`/organization/dashboard`)

Stats cards (green accent):
- Accreditation Progress (bar/circular)
- Documents Submitted count
- Pending Documents count
- Notifications (unread count)

Recent notifications list below.

---

### 9.2 Accreditation Checklist (`/organization/accreditation`)

**Progress Header**: Org name + progress indicator showing % toward full accreditation

**9-Item Checklist** (mandatory requirements):

| # | Requirement | Status |
|---|---|---|
| 1 | Application Letter Form | ✓ / ✗ |
| 2 | Lists of Officers | ✓ / ✗ |
| 3 | Commitment Forms of Officers and Advisers | ✓ / ✗ |
| 4 | Constitution and By-Laws | ✓ / ✗ |
| 5 | Organizational Structure | ✓ / ✗ |
| 6 | Plan and Calendar of Activities | ✓ / ✗ |
| 7 | Audited Financial Report | ✓ / ✗ |
| 8 | Program of Expenditures | ✓ / ✗ |
| 9 | Accomplishment Reports | ✓ / ✗ |

> **Note**: The `other` document type is **not** counted toward progress. 100% requires all 9 items verified.

**Certificate Download** button — enabled only when all 9 items are verified.

---

### 9.3 Document Submissions (`/organization/submissions`)

**Upload Button** → opens `UploadModal` (React)

**Submissions Table**: Document Type, Academic Year, Title, File, Status badge, Submitted Date, Actions (View, Download)

**Upload Modal** (React: `UploadModal.jsx`):
- Document type selector (9 types + Other)
- Academic year input
- Title input
- File upload drag-and-drop area
- AJAX submit with loading state

---

### 9.4 Forms — Calendar of Activities (`/organization/calendar-activities`)

- Add activity rows (month, activity name, venue)
- Table of activities by academic year
- Download PDF / Print buttons

---

### 9.5 Forms — Program of Expenditures (`/organization/program-expenditure`)

- CRUD table: Fee Type, Amount, Frequency, No. of Students, Total
- Auto-calculates grand total
- Download / Print at the bottom

---

### 9.6 Forms — Financial Reports (`/organization/financial-report`)

- Form with financial data entry for an academic year
- Fields: Total Collection, Total Expenses, Remaining Fund
- Tracking view: compares multiple years

---

### 9.7 Forms — Accomplishment Reports (`/organization/accomplishment-report`)

- List of accomplishment report entries
- Create / Edit form
- Download / Print actions

---

### 9.8 Forms — Commitment Forms (`/organization/commitment-form`)

- List of commitment forms by officer
- Create form: Officer Name, Position, Academic Year, Signature date
- Download / Print per form

---

### 9.9 Notifications (`/organization/notifications`)

- Newest-first notification cards
- Each card: title, message, timestamp, unread indicator (blue dot)
- Mark as Read / Mark All as Read via AJAX

---

## 10. Print / PDF Views

All print views use minimal, print-safe layouts:
- No dark backgrounds or sidebars
- `@media print` hides interactive elements
- Formal header: org name, academic year, date generated
- Table-based data layout
- Footer with signature lines where applicable

**Available for**: Organization list, Document submissions, Calendar of Activities, Program of Expenditures, Financial Report, Accomplishment Reports, Commitment Forms, Accreditation Certificate

---

## 11. Responsive Behavior

| Breakpoint | Behavior |
|---|---|
| `sm` (≥640px) | Single-column grids expand to 2-column |
| `md` (≥768px) | Filter bars go from 2-col to 3-col |
| `lg` (≥1024px) | Sidebar fully visible, content at full width |
| Mobile | Sidebar collapses; `hidden md:table-cell` columns disappear |

---

## 12. Dark / Light Mode Toggle

- Toggle button in top-right navbar (sun/moon icon)
- Toggles `dark` class on `<html>` element
- Persists selection in `localStorage` as `"theme"`
- CSS variables update instantly across all tokens
- **Default**: Dark mode
