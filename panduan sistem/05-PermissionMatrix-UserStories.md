# Chapter 20–22 — Permission Matrix, User Stories, Acceptance Criteria

## Introduction
Defines exact per-role access (implemented via `spatie/laravel-permission` with journal-scoped teams) and the backlog in agile user-story format with testable acceptance criteria.

---

## 20. Role Permission Matrix

Legend: **C**reate, **R**ead, **U**pdate, **D**elete, **A**pprove/Decide, **X**=no access. All permissions are additionally scoped by `journal_id` except Super Admin/System Admin (global).

| Module / Role | Super Admin | System Admin | Journal Manager | Managing Editor | Section Editor | Reviewer | Copy Editor | Layout Editor | Proofreader | Publisher | Finance | Marketing | Customer Service | Support | Author | Reader/Guest |
|---|---|---|---|---|---|---|---|---|---|---|---|---|---|---|---|---|
| System Settings | CRUD | CRUD | X | X | X | X | X | X | X | X | X | X | X | X | X | X |
| Journal Management | CRUD | CRUD | RU (own) | R | R | X | X | X | X | R | R | X | X | X | X | R (public) |
| User & Role Management | CRUD | CRUD | RU (own journal members) | X | X | X | X | X | X | X | X | X | R | X | RU (own profile) | X |
| Submission (own) | R | R | R | RUA | RU (assigned) | R (assigned) | R (assigned) | R (assigned) | R (assigned) | R | R | X | R | R | CRUD (own, pre-lock) | X |
| Initial Screening | X | X | A | A | A | X | X | X | X | X | X | X | X | X | X | X |
| Reviewer Assignment | X | X | CRU | CRU | CRU | X | X | X | X | X | X | X | X | X | X | X |
| Peer Review | X | R | R | R | R | CRU (own) | X | X | X | X | X | X | X | X | X | X |
| Editorial Decision | X | R | A | A | A (section) | X | X | X | X | X | X | X | X | X | X | X |
| Copyediting | X | R | R | R | R | X | CRU | X | X | X | X | X | X | X | R | X |
| Layout Editing | X | R | R | R | R | X | X | CRU | X | X | X | X | X | X | X | X |
| Proofreading | X | R | R | R | R | X | X | X | CRU | X | X | X | X | X | R | X |
| Publication Approval | X | A | A | A | X | X | X | X | X | A | X | X | X | X | X | X |
| Volume/Issue/DOI | X | CRUD | CRUD | CRU | X | X | X | X | X | CRU | X | X | X | X | X | X |
| Invoice | X | R | CRU | R | X | X | X | X | X | R | CRUD | X | R | X | R (own) | X |
| Payment Verification | X | R | R | X | X | X | X | X | X | R | CRUA | X | X | X | X | X |
| Waiver/Discount | X | A | A | X | X | X | X | X | X | X | CRU | X | X | X | X | X |
| Receipt | X | R | R | X | X | X | X | X | X | X | CR | X | R | X | R (own) | X |
| CMS Content | X | CRUD | CRUD (own) | RU | X | X | X | X | X | RU | X | CRU | X | X | X | R (public) |
| Announcements/News | X | CRUD | CRUD (own) | CRU | X | X | X | X | X | R | X | CRU | X | X | X | R (public) |
| Reports | X | R (all) | R (own journal) | R (own) | R (own) | X | X | X | X | R | R (finance) | R (marketing) | X | X | X | X |
| Audit Trail / Activity Log | R | R | R (own journal) | X | X | X | X | X | X | X | X | X | X | X | X | X |
| Notifications Config | CRUD | CRUD | RU (own) | X | X | X | X | X | X | X | X | X | X | X | RU (own prefs) | X |
| API Token Management | CRUD (all) | CRUD (all) | CRUD (own) | X | X | X | X | X | X | X | X | X | X | X | CRUD (own) | X |
| Support Ticket / CS | X | R | R | X | X | X | X | X | X | X | X | X | CRUD | CRUD | CR (own) | CR (guest) |

> Implementation note: encode as Spatie `permissions` seeded per module×action (e.g. `submission.create`, `payment.verify`), grouped into `roles` seeded per the matrix above, and attached per user **per journal** via `model_has_roles.team_id = journal_id` (Spatie teams feature, `teams` enabled in `config/permission.php`).

---

## 21–22. User Stories & Acceptance Criteria (Backlog Sample — organized by Epic)

### EPIC-01 Identity & Access

**US-001** — As an Author, I want to register using Google or ORCID so that I don't need to remember another password.
- **AC1:** Given I click "Sign in with Google", when I authorize, then a `users` row is created/matched by `google_id` and I am logged in.
- **AC2:** Given I click "Sign in with ORCID", when I authorize, then `orcid_id` is stored and pre-fills my academic profile.
- **AC3:** Given an email already exists from manual registration, when I OAuth-login with the same email, then the accounts are merged (not duplicated), confirmed via email-match + explicit consent step.

**US-002** — As a Journal Manager, I want to assign roles scoped to my journal only, so that I cannot accidentally affect other journals.
- **AC1:** Role assignment UI only lists journals I manage.
- **AC2:** API rejects role assignment requests for journals outside my scope with `403`.

### EPIC-03 Submission

**US-010** — As an Author, I want to save a submission as a draft so I can finish it later.
- **AC1:** Draft submissions bypass full-field validation except a minimum: title present.
- **AC2:** Draft submissions are only visible to the author and co-authors, not to Editors.

**US-011** — As an Author, I want to track my submission status in real time.
- **AC1:** Status page shows current stage from the state diagram (§16 in Ch.04) with a visual timeline.
- **AC2:** Status updates reflect within one page refresh (no stale cache beyond 60s given DB-cache TTL).

### EPIC-04 Peer Review

**US-020** — As a Reviewer, I want to accept or decline an assignment with a reason.
- **AC1:** Decline requires a reason (min 10 characters).
- **AC2:** On decline, Editor receives an immediate in-app + email notification and the slot reopens for reassignment.

**US-021** — As an Editor, I want reviewers to be automatically reminded and escalated so reviews aren't stuck.
- **AC1:** Reminder sent exactly 3 days before `due_date` (scheduled job, idempotent — `reminded_at` guard prevents duplicate sends).
- **AC2:** Escalation to Editor sent exactly 2 days after `due_date` if still `accepted` (not `completed`).

### EPIC-08 Finance

**US-030** — As Finance, I want to verify a payment against uploaded proof so that revenue is accurately recorded.
- **AC1:** Verification screen shows invoice amount, transferred amount, and proof image/PDF side-by-side.
- **AC2:** Approve action is blocked (validation error) if `amount_transferred < invoice.amount - discount_amount` unless Finance explicitly checks "Accept Partial/Overpayment" with a note.
- **AC3:** Every verification action writes an `audit_trails` row with `old_values`/`new_values` and `user_id`.

**US-031** — As Finance, I want to issue a waiver so eligible authors (e.g., invited/indexed-partner) don't pay APC.
- **AC1:** Waiver requires a reason and, if journal policy demands, secondary approval from Journal Manager.
- **AC2:** Waived invoices show `status=waived` and unblock the submission workflow identically to `paid`.

### EPIC-07 Publication

**US-040** — As a Publisher, I want to approve final galleys before publication so quality is assured.
- **AC1:** "Publish" action is disabled in UI until `production_tasks` for copyediting, layout, and proofreading are all `status=completed`.
- **AC2:** Approving triggers DOI registration job (queued) and Volume/Issue assignment form.

**US-041** — As a Reader, I want to download the published PDF and cite the article easily.
- **AC1:** Article page exposes BibTeX/RIS/EndNote export buttons.
- **AC2:** Every download increments `article_downloads` log (async, queued, does not block file response).

### EPIC-11 Notification

**US-050** — As an Author, I want to choose whether I receive WhatsApp notifications.
- **AC1:** Notification Preferences page lists each event category with Email/WhatsApp/In-App toggles.
- **AC2:** Disabling WhatsApp for "Reminder" category suppresses only that category via Fonnte, other categories unaffected.

*(Full backlog for the software house should extend this pattern to every FR-xxx item in `01-SRS-FSD-Requirements.md`; the structure above is the required template: Story → AC1..ACn, each AC independently testable and traceable to an FR/BR ID.)*

---
*Continue to `06-API-Documentation.md`.*
