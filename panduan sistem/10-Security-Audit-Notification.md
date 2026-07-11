# Chapter 28–30 — Notification Matrix, Audit Matrix, Security Design

## Introduction
Cross-cutting concerns applied across all modules: who gets notified when, what gets audited, and how the system is hardened — all adapted to run without Redis/Nginx-specific tooling.

---

## 28. Notification Matrix

| Event | Recipient(s) | Email | WhatsApp (Fonnte) | In-App |
|---|---|---|---|---|
| Registration Success | New user | ✓ | ✓ (if phone provided) | ✓ |
| Submission Received | Author, Journal Manager | ✓ | ✓ | ✓ |
| Desk Reject | Author | ✓ | ✓ | ✓ |
| Invoice Issued | Author | ✓ | ✓ | ✓ |
| Payment Uploaded | Finance | ✓ | ✓ | ✓ |
| Payment Approved | Author | ✓ | ✓ | ✓ |
| Payment Rejected/Reupload | Author | ✓ | ✓ | ✓ |
| Reviewer Invited | Reviewer | ✓ | ✓ | ✓ |
| Reviewer Reminder (-3d) | Reviewer | ✓ | ✓ | ✓ |
| Reviewer Escalation (+2d) | Assigning Editor | ✓ | ✗ | ✓ |
| Review Submitted | Editor | ✓ | ✗ | ✓ |
| Editorial Decision Made | Author, Co-Authors | ✓ | ✓ | ✓ |
| Revision Deadline Reminder (-7d) | Author | ✓ | ✓ | ✓ |
| Accepted → Production Start | Author, Copy Editor | ✓ | ✗ | ✓ |
| Article Published | Author, Co-Authors | ✓ | ✓ | ✓ |
| DOI Registration Failed | Publisher | ✓ | ✗ | ✓ |
| Announcement Published | Subscribers (opt-in) | ✓ | ✗ | ✓ |
| Password Changed / Suspicious Login | User | ✓ | ✓ | ✓ |
| Role Assigned/Changed | User | ✓ | ✗ | ✓ |

Delivery mechanics: each event fires a Laravel Notification class implementing `via()` returning `[MailChannel::class, FonnteChannel::class, DatabaseChannel::class]` filtered by the recipient's `notification_preferences` row. All non-Mail/Database channels (WhatsApp) go through the queue (`ShouldQueue`) since Fonnte is an external HTTP call — never synchronous.

---

## 29. Audit Matrix

| Entity | Audited Actions | Captured Fields |
|---|---|---|
| Submission | create, update, submit, withdraw, status_change | old/new status, editor/actor, IP |
| Review Assignment | assign, accept, decline, complete | reviewer, editor, timestamps |
| Editorial Decision | create | decision, actor, submission_id |
| Invoice | create, waive, discount | amount, approver, reason |
| Payment | create, approve, reject, need_reupload | amount, verifier, reason |
| Refund | create, approve | amount, approver |
| User | create, role_assigned, role_revoked, suspended, password_changed | actor, target user, role/journal |
| Journal | create, update, suspend, archive | actor, changed fields |
| CMS Page | create, update, publish | actor, page, version |
| API Token | created, revoked | actor, abilities, expiry |

**Retention:** Audit trail records are **never deleted** by application logic (BR-005, immutable/append-only); archival to a cold-storage export (CSV/S3) after N years is an operational task, not a code path exposed to any role including Super Admin (prevents tampering).

**Implementation:** Central `AuditService::log($module, $action, $model, $old, $new)` called from a `HasAuditTrail` trait's model observer hooks (`created`, `updated`, `deleted`) on all audited models, supplemented by explicit calls inside Services for business-meaning actions (e.g., "payment_verified" is more meaningful than a generic "updated").

---

## 30. Security Design

### 30.1 Authentication & Session
- Sanctum tokens for API; session-cookie auth (`database` session driver) for the Blade web app.
- Password hashing: `bcrypt` (Laravel default, cost factor 12).
- 2FA-ready: TOTP via `pragmarx/google2fa-laravel`, optional per user, enforceable per role by System Admin policy.
- Session/device management: `login_histories` (every attempt) + `device_histories` (fingerprint: user_agent+IP hash) surfaced in user's "Security" profile page with a "Log out other sessions" action (deletes matching `sessions` table rows).

### 30.2 Authorization
- RBAC via `spatie/laravel-permission` with **team** feature enabled = journal-scoped roles (`model_has_roles.team_id = journal_id`).
- Every Controller action guarded by a Laravel `Policy` (`SubmissionPolicy`, `PaymentPolicy`, etc.) — never inline `if ($user->role == ...)` checks, for testability and consistency.
- Middleware stack: `auth:sanctum` (API) / `auth` (web) → `EnsureJournalScope` custom middleware (injects/validates `journal_id` route param against the user's assigned journals) → Policy `authorize()` call inside controller/FormRequest.

### 30.3 Input & Transport Security
- CSRF: Laravel default `VerifyCsrfToken` on all web POST/PUT/DELETE.
- XSS: Blade's `{{ }}` auto-escaping; rich-text CMS fields sanitized server-side (`mews/purifier` / HTMLPurifier) before storage, not just on output.
- SQL Injection: Eloquent/Query Builder parameter binding exclusively; no raw string-concatenated queries.
- Rate Limiting: `throttle:login` (5/min), `throttle:api` (60/min default per Sanctum token, configurable per ability), `throttle:password-reset` (3/hour).
- File upload validation: MIME whitelist + magic-byte check (`finfo`, not trusting client-provided extension), max size, stored under UUID filenames outside any publicly-guessable enumeration, served via **signed URLs** (`URL::temporarySignedRoute`) with short expiry (e.g., 15 minutes) for sensitive documents (payment proofs, ethical clearance).
- Virus-scan-ready: `ScanUploadedFileJob` stub queued on every upload; no-op pass-through by default (logs "skipped — no scanner configured"), swappable to a real ClamAV HTTP-API-based scanner if the client upgrades hosting (shared hosting typically lacks a local ClamAV daemon).

### 30.4 HTTPS & Headers
- Force HTTPS via `.htaccess` redirect + `URL::forceScheme('https')`.
- Security headers middleware: `Strict-Transport-Security`, `X-Content-Type-Options: nosniff`, `X-Frame-Options: SAMEORIGIN`, `Content-Security-Policy` (scoped to allow CDN assets used: Bootstrap, AlpineJS, TinyMCE/Quill CDN).

### 30.5 Secrets Management
- `.env` file outside `public/` webroot (Laravel default) — verify Hostinger deployment places the Laravel root **above** `public_html`/document root, with only `public/` exposed (see Deployment Guide Ch.12 for the exact folder-mapping trick required on shared hosting where changing document root isn't always available).
- Per-journal SMTP credentials and other sensitive JSON config columns encrypted at rest via Laravel's `encrypted` Eloquent cast.

### 30.6 Backup, Monitoring readiness
- See `12-Deployment-DevOps-Hostinger.md` for backup/DR; Security chapter's concern here is ensuring backups themselves are access-controlled (not stored under public webroot, downloadable only via authenticated Super Admin action or cPanel).

---
*Continue to `11-Testing-Strategy.md`.*
