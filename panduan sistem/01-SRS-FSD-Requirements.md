# Chapter 4–5, 23–26 — FSD / SRS / Functional & Non-Functional Requirements / Business & Validation Rules

## Introduction
This chapter defines *what* the system must do (functional), *how well* it must do it (non-functional), the invariant *business rules* governing state transitions, and field-level *validation rules* for forms/API payloads. It is written to Laravel 12 conventions (FormRequest validation, Policies, Events/Listeners, Jobs).

## Objectives
Provide developers a single, unambiguous reference so each Laravel module (Submission, Review, Editorial, Finance, Publication, CMS, Notification, Security) can be implemented without further clarification.

## Scope
Covers all modules listed in the project brief; excludes UI pixel-level detail (see `09-CMS-Reporting-Dashboard.md` for screen specs).

---

# A. Functional Requirements (FSD/SRS Core)

Numbering convention: `FR-<MODULE>-<seq>`.

## A.1 Identity & Access (IAM)

| ID | Requirement |
|---|---|
| FR-IAM-01 | System shall allow registration with email/password, Google OAuth, and ORCID OAuth. |
| FR-IAM-02 | System shall support multiple **guards**: `web` (backend staff), `author` (frontend users) — implemented via Laravel multi-guard config (`config/auth.php`) with separate `users` and `authors` tables OR a unified `users` table with `user_type` + Spatie Permission teams (recommended: unified table + role scoping per journal, see DB Dictionary). |
| FR-IAM-03 | System shall implement RBAC via roles & permissions (Spatie `laravel-permission` package), permissions scoped per journal (`journal_id` pivot on `model_has_roles`). |
| FR-IAM-04 | System shall support 2FA-ready (TOTP) via `pragmarx/google2fa` — toggle in Security Settings; not mandatory Phase 1. |
| FR-IAM-05 | System shall log every login attempt (success/fail), device, IP, and user agent → `login_histories` table. |
| FR-IAM-06 | System shall allow Super Admin/System Admin to force-logout any active session (`sessions` table row deletion + `activity_log`). |
| FR-IAM-07 | Password policy configurable per installation: min length 8, requires upper/lower/number/symbol (togglable), password history (last 5 not reusable), expiry optional. |
| FR-IAM-08 | Account lockout after N failed attempts (default 5) for 15 minutes, using Laravel `RateLimiter`. |

## A.2 Journal Management

| ID | Requirement |
|---|---|
| FR-JRN-01 | Super Admin / System Admin can create unlimited Journals, each with unique slug, ISSN, eISSN, DOI prefix. |
| FR-JRN-02 | Each Journal has independent: editorial board, reviewer pool, publication schedule, APC fee table, bank account(s), SMTP config (fallback to global SMTP if not set), theme (color/logo), SEO meta, About/Scope/Focus/Policies (CMS pages). |
| FR-JRN-03 | Journal Manager role is scoped strictly to their assigned journal(s) via `journal_user` pivot. |
| FR-JRN-04 | System shall support per-journal custom domain OR subpath routing (`/journal/{slug}`) — recommended for shared hosting: subpath routing (single Laravel app, no per-domain vhost needed). |
| FR-JRN-05 | Journal status: `draft`, `active`, `suspended`, `archived`. Suspended journals reject new submissions but keep published content visible. |

## A.3 Submission

| ID | Requirement |
|---|---|
| FR-SUB-01 | Author can create a submission draft with metadata: title, abstract, keywords, language, references, funding, conflict of interest, ethics statement, acknowledgement, license (CC BY, CC BY-NC, etc.). |
| FR-SUB-02 | Submission supports unlimited co-authors with role (corresponding/co-author) and order. |
| FR-SUB-03 | Submission supports unlimited file uploads categorized as: Manuscript, Supplementary File, Dataset, Ethical Clearance, Copyright Form, Revision File. |
| FR-SUB-04 | Every file re-upload creates a new **version** (immutable history; old versions retained, never overwritten on disk). |
| FR-SUB-05 | Author can Save Draft (no validation enforced) or Submit (full validation enforced — see Validation Rules §D). |
| FR-SUB-06 | Author can Withdraw a submission prior to Editor Assignment; after that, withdrawal requires Editor approval. |
| FR-SUB-07 | Author can Resubmit a withdrawn/rejected submission as a **new submission** referencing the original (`parent_submission_id`), not overwrite. |
| FR-SUB-08 | System auto-generates a unique Submission Tracking Code (e.g., `JRN-2026-000123`) at creation. |
| FR-SUB-09 | Duplicate-submission detection: warn (not block) if title similarity > 85% (Levenshtein/embedding stub) within same journal. |

## A.4 Peer Review

| ID | Requirement |
|---|---|
| FR-REV-01 | System supports Single Blind, Double Blind, Open Review — configurable per Journal, overridable per Submission. |
| FR-REV-02 | Editor can assign N reviewers per submission per round; system prevents assigning an author as reviewer of their own submission (conflict-of-interest guard). |
| FR-REV-03 | Reviewer receives assignment with deadline (configurable, default 14 days); reminder sent at -3 days and on due date; escalation notification to Editor at +2 days overdue. |
| FR-REV-04 | Review form supports custom rubric (journal-configurable: weighted criteria + free-text) and numeric score (e.g., 1–5 or 1–100). |
| FR-REV-05 | Reviewer submits: Recommendation (`accept`, `minor_revision`, `major_revision`, `reject`), Private Comments (editor-only), Public Comments (visible to author per blind-mode rules). |
| FR-REV-06 | Review rounds are unlimited; each round is a child of the submission version under review. |
| FR-REV-07 | Reviewer can Decline an assignment with reason; Editor is notified and can reassign. |
| FR-REV-08 | Full Review History is retained and visible to Editor (never deleted, only soft-deleted if a review is retracted). |

## A.5 Editorial Workflow

| ID | Requirement |
|---|---|
| FR-EDT-01 | Managing/Section Editor performs Initial Screening (desk review) with outcome: `pass_to_review`, `desk_reject`, `revision_required_before_review`. |
| FR-EDT-02 | Editor assigns Section Editor / Reviewers. |
| FR-EDT-03 | Editorial Decision types: `minor_revision`, `major_revision`, `reject`, `accept`. Decision triggers notification + state transition. |
| FR-EDT-04 | Revision Management: author uploads revised files + point-by-point response-to-reviewers document; new round created automatically if editor requests re-review. |
| FR-EDT-05 | On `accept`, submission moves to Production (Copy Editing). |

## A.6 Production (Copyediting / Layout / Proofreading)

| ID | Requirement |
|---|---|
| FR-PRD-01 | Copy Editor uploads copyedited file; Author can respond/approve. |
| FR-PRD-02 | Layout Editor produces galley files (PDF/HTML/XML/EPUB) linked to the article. |
| FR-PRD-03 | Proofreader reviews galleys, logs corrections, approves final galley set. |
| FR-PRD-04 | Publication Approval requires sign-off from Managing Editor / Publisher role before it can be scheduled into an Issue. |

## A.7 Publication

| ID | Requirement |
|---|---|
| FR-PUB-01 | Articles are grouped into Volume → Issue; Issue has publication date (can be future-dated/scheduled). |
| FR-PUB-02 | Each published article gets: DOI (via DataCite/Crossref API), page range, Crossmark metadata, downloadable formats (PDF mandatory; HTML/XML/EPUB optional per journal config). |
| FR-PUB-03 | Public site exposes Current Issue, Archive (Past Issues), search (title/author/keyword/full-text if enabled), citation export (BibTeX/RIS/EndNote), view/download counters. |
| FR-PUB-04 | DOI assignment is idempotent — one DOI per article version; re-assignment blocked once `registered_at` is set. |

## A.8 Finance / Payment (Manual Only)

| ID | Requirement |
|---|---|
| FR-FIN-01 | System generates Invoice automatically when submission passes Initial Screening AND journal APC > 0 AND no waiver applies. |
| FR-FIN-02 | Invoice displays journal bank account(s), amount, due date, unique payment reference code. |
| FR-FIN-03 | Author uploads Proof of Payment (image/PDF) against an Invoice; status → `waiting_verification`. |
| FR-FIN-04 | Finance role verifies: Approve (→ `paid`, Receipt auto-generated PDF), Reject (→ `rejected`, reason required), Need Reupload (→ `waiting_payment`, note to author). |
| FR-FIN-05 | Finance can apply Waiver (100%) or Discount (%/fixed) with justification note; requires Journal Manager or higher approval per business rule BR-011. |
| FR-FIN-06 | Refund workflow: Finance records refund (amount, method, reference); does not integrate a gateway — manual bookkeeping entry only. |
| FR-FIN-07 | Submission cannot proceed to Reviewer Assignment while invoice status is `waiting_payment` or `waiting_verification` and APC is mandatory (BR-001). |

## A.9 CMS

| ID | Requirement |
|---|---|
| FR-CMS-01 | Journal Manager can manage: Landing Page blocks, About, Editorial Team, Reviewer list, Focus & Scope, Announcements, News, Contact, FAQ, Publication Ethics, Author Guideline, Reviewer Guideline, Publication Fee page, Template Downloads, Editorial Policies, Privacy Policy, Terms, Banner, Menu, Footer. |
| FR-CMS-02 | All CMS content supports SEO fields (meta title/description/OG image) and is version-tracked (audit trail). |
| FR-CMS-03 | Announcements/News support scheduled publish & expiry dates. |

## A.10 Reporting

| ID | Requirement |
|---|---|
| FR-RPT-01 | System provides: Submission, Publication, Acceptance Rate, Reviewer Performance, Editor Performance, Revenue, Outstanding Payment, Payment, Invoice, Author, Institution, Journal, Audit, Activity reports. |
| FR-RPT-02 | All reports filterable by date range, journal, and exportable to Excel, CSV, PDF (queued job for large exports to avoid shared-hosting timeout — see NFR-PERF-03). |

## A.11 Notification

| ID | Requirement |
|---|---|
| FR-NTF-01 | Channels: Email (SMTP, per-journal override), WhatsApp (Fonnte API), In-App (bell + polling). |
| FR-NTF-02 | Notification events (non-exhaustive — full matrix in `10-Security-Audit-Notification.md`): registration, submission received, decision made, reviewer assigned/reminder/escalation, invoice issued, payment verified/rejected, article published. |
| FR-NTF-03 | Users can manage channel preferences per notification category (opt-out of WhatsApp, keep Email, etc.). |

---

# B. Non-Functional Requirements

| ID | Category | Requirement |
|---|---|---|
| NFR-PERF-01 | Performance | Page response time ≤ 2s under normal load (shared hosting baseline, cached responses). |
| NFR-PERF-02 | Performance | Use `cache` table (database driver) for config/route/view cache (`php artisan config:cache`, `route:cache`, `view:cache` at deploy time). |
| NFR-PERF-03 | Performance | Long-running tasks (bulk export, DOI batch registration, email blast) MUST be queued (`database` queue driver) and processed via cron-triggered worker, never executed synchronously in an HTTP request, to avoid shared-hosting PHP execution time limits (typically 30–60s on Hostinger). |
| NFR-SCAL-01 | Scalability | Application must support horizontal growth path: swap `database` cache/queue driver for Redis with a one-line `.env` change if client migrates to VPS (no code changes — always code against Laravel's Cache/Queue facades, never Redis-specific APIs). |
| NFR-AVAIL-01 | Availability | Target uptime 99.5%; graceful maintenance mode (`php artisan down --secret=`) for deployments. |
| NFR-SEC-01 | Security | All traffic HTTPS-only (Hostinger free SSL / Let's Encrypt via cPanel AutoSSL); HSTS header enabled. |
| NFR-SEC-02 | Security | Passwords hashed with bcrypt/argon2id (Laravel default). |
| NFR-SEC-03 | Security | CSRF protection on all state-changing web routes (Laravel default `VerifyCsrfToken`); API uses Sanctum tokens, not cookies, for third-party clients. |
| NFR-SEC-04 | Security | Rate limiting on auth endpoints (login, register, password reset) and API (per-token throttle, e.g., 60 req/min default, configurable per token ability). |
| NFR-SEC-05 | Security | File upload validation: MIME-type whitelist, max size (configurable per file category), filename sanitization, stored outside guessable paths, served via signed URLs (`Storage::temporaryUrl` for S3, or signed route + streamed response for local disk). |
| NFR-USAB-01 | Usability | Responsive design (Bootstrap 5 grid) — supports desktop, tablet, mobile breakpoints. |
| NFR-USAB-02 | Usability | WCAG 2.1 AA color contrast target for public-facing pages. |
| NFR-MAINT-01 | Maintainability | Modular Laravel structure (see Architecture doc): domain-based module folders, Form Requests for validation, Policies for authorization, Actions/Services for business logic, Events/Listeners for cross-module side effects. |
| NFR-COMPAT-01 | Compatibility | PHP 8.4, Laravel 12, MySQL 8.0 (Hostinger-provided), LiteSpeed/Apache — no server extensions unavailable on standard Hostinger shared hosting (no `pcntl`, no `Redis` extension assumed absent — code defensively). |
| NFR-AUDIT-01 | Auditability | Every create/update/delete on: Submission, Review, Editorial Decision, Invoice, Payment, User, Role assignment, Journal setting MUST produce an immutable Audit Trail record (see `10-Security-Audit-Notification.md`). |
| NFR-I18N-01 | Localization | UI text externalized via Laravel `lang` files; minimum English + Bahasa Indonesia at launch. |
| NFR-BACKUP-01 | Backup | Daily automated DB + storage backup via Hostinger's built-in backup tool / cron `mysqldump` + `tar` to a secondary storage location (see Deployment doc). |

---

# C. Business Rules (BR)

| ID | Rule |
|---|---|
| BR-001 | Submission cannot enter Reviewer Assignment while a mandatory APC invoice is `waiting_payment`/`waiting_verification`, unless waived/discounted to zero and approved. |
| BR-002 | Revision cycles are unlimited; each revision = new submission **version** (immutable). |
| BR-003 | Reviewer identity hidden from Author (Single/Double Blind); Author identity hidden from Reviewer (Double Blind only). |
| BR-004 | DOI assignment is a one-time, irreversible action per article version once registered externally. |
| BR-005 | Every state-changing action on Submission/Payment/User writes to `audit_trails`. |
| BR-006 | A user may hold different roles on different journals simultaneously (e.g., Editor on Journal A, Reviewer on Journal B). |
| BR-007 | An Author cannot review their own submission (system-enforced at assignment time). |
| BR-008 | A rejected submission may be resubmitted only as a new Submission record referencing the original via `parent_submission_id`; history of the original is preserved. |
| BR-009 | Publication requires: Accept decision + Copyediting complete + Layout complete + Proofreading approved + Publication Approval sign-off, in that order (state machine enforced, see `04-Workflow-BPMN-UseCases.md`). |
| BR-010 | Invoice due date default = journal-configurable (default 14 days); overdue invoices trigger reminder notifications but do NOT auto-cancel the submission. |
| BR-011 | Waiver/Discount above a configurable threshold (default: any waiver, or discount > 50%) requires approval from Journal Manager or higher, recorded with approver identity in audit trail. |
| BR-012 | Only Super Admin/System Admin can hard-delete records; all other roles perform soft-delete (`deleted_at`), and hard-delete requires a secondary confirmation + reason logged. |
| BR-013 | A Journal in `suspended` status blocks new Submissions but keeps all published Articles publicly accessible (archival integrity). |

---

# D. Validation Rules (representative — Laravel FormRequest rule sets)

## D.1 User Registration
```
name:        required|string|max:150
email:       required|email:rfc,dns|unique:users,email
password:    required|string|min:8|confirmed|regex:/[A-Z]/|regex:/[0-9]/
affiliation: nullable|string|max:255
orcid:       nullable|regex:/^\d{4}-\d{4}-\d{4}-\d{3}[0-9X]$/|unique:users,orcid
```

## D.2 Submission Metadata
```
title:              required|string|max:500
abstract:           required|string|min:150|max:5000
keywords:           required|array|min:3|max:10
keywords.*:         string|max:50
language:           required|in:en,id
authors:            required|array|min:1
authors.*.name:     required|string|max:150
authors.*.email:    required|email
authors.*.is_corresponding: required|boolean
manuscript_file:    required|file|mimes:doc,docx,pdf|max:10240   // 10MB, shared-hosting friendly
supplementary_file: nullable|file|mimes:pdf,doc,docx,xlsx,csv,zip|max:20480
license:            required|in:cc-by,cc-by-nc,cc-by-nc-nd,cc-by-sa,all-rights-reserved
conflict_of_interest: required|string|max:1000
```

## D.3 Invoice / Payment Proof
```
invoice_id:      required|exists:invoices,id
amount_transferred: required|numeric|min:0
transfer_date:   required|date|before_or_equal:today
bank_name:       required|string|max:100
proof_file:      required|file|mimes:jpg,jpeg,png,pdf|max:5120   // 5MB
```

## D.4 Review Submission
```
recommendation:   required|in:accept,minor_revision,major_revision,reject
score:             nullable|numeric|min:0|max:100
private_comment:   nullable|string|max:5000
public_comment:    required|string|max:5000
rubric_scores:     required_if:journal.rubric_enabled,true|array
```

## D.5 File Upload (global policy)
- Allowed MIME per category (manuscript: pdf/doc/docx; dataset: csv/xlsx/zip; proof: jpg/png/pdf).
- Max size configurable per category in `system_settings` (defaults chosen conservatively for shared-hosting PHP limits: raise `upload_max_filesize`/`post_max_size` via `.htaccess` or cPanel MultiPHP INI Editor up to the plan's ceiling, typically 128–512MB on Hostinger Business/Cloud).
- Filenames stored as UUID + original-extension; original filename kept in DB column, never used as the stored path.
- Virus-scan-ready: file upload triggers a queued `ScanUploadedFileJob` with a no-op/ClamAV-stub implementation swappable later.

---
*Continue to `02-System-Architecture-Hostinger.md`.*
