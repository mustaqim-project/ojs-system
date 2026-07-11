# Chapter 27 — REST API Documentation

## Introduction
API-first REST interface secured by **Laravel Sanctum** (token-based, suitable for SPA/mobile/third-party). Documented in OpenAPI 3.0 via `darkaonline/l5-swagger`; this chapter is the human-readable companion.

## Objectives
Enable independent frontend/mobile teams and external integrators to consume the platform without touching backend code.

## Conventions
- Base URL: `https://{domain}/api/v1`
- Auth: `Authorization: Bearer {sanctum_token}`
- Content-Type: `application/json` (file uploads: `multipart/form-data`)
- All list endpoints support `?page=&per_page=&sort=&filter[field]=`
- Standard envelope:
```json
{
  "success": true,
  "data": { },
  "meta": { "page": 1, "per_page": 15, "total": 120 },
  "message": null
}
```
- Error envelope:
```json
{
  "success": false,
  "message": "The given data was invalid.",
  "errors": { "email": ["The email field is required."] }
}
```
- HTTP status codes: `200` OK, `201` Created, `204` No Content, `400` Bad Request, `401` Unauthenticated, `403` Forbidden, `404` Not Found, `422` Validation Error, `429` Too Many Requests, `500` Server Error.
- Rate limiting: default `60 requests/minute/token`, configurable per token ability (Sanctum `abilities`).

---

## Authentication

### POST `/api/v1/auth/register`
Body: `{ name, email, password, password_confirmation }`
Response `201`: `{ user, token }`

### POST `/api/v1/auth/login`
Body: `{ email, password }`
Response `200`: `{ user, token }`
Errors: `422` invalid credentials, `429` rate-limited after threshold failures.

### POST `/api/v1/auth/oauth/{provider}` (`google`|`orcid`)
Body: `{ access_token }` (obtained from client-side OAuth flow)
Response `200`: `{ user, token }`

### POST `/api/v1/auth/logout`
Header: Bearer token. Response `204`.

### GET `/api/v1/auth/me`
Response `200`: current authenticated user profile + roles per journal.

---

## Journals

### GET `/api/v1/journals`
Public. Query: `?status=active&search=`
Response `200`: paginated list `{ id, uuid, name, slug, issn, eissn, status }`

### GET `/api/v1/journals/{slug}`
Public. Full journal profile incl. focus/scope, board, APC info.

### POST `/api/v1/journals` *(auth: Super Admin/System Admin)*
Body: `{ name, slug, issn, eissn, doi_prefix, apc_default_amount, ... }`
Response `201`.

### PUT `/api/v1/journals/{id}` *(auth: scoped Journal Manager or above)*
### DELETE `/api/v1/journals/{id}` *(auth: Super Admin, soft delete)*

---

## Submissions

### GET `/api/v1/submissions` *(auth)*
Scoped to caller's role: Author sees own; Editor sees journal queue.
Query: `?journal_id=&status=&stage=&search=`

### POST `/api/v1/submissions` *(auth: Author)*
Body (multipart): metadata fields (see Validation Rules D.2) + files.
Response `201`: `{ id, uuid, tracking_code, status }`

### GET `/api/v1/submissions/{uuid}` *(auth: owner/co-author/assigned staff)*
Full submission detail incl. versions, files, review summary (role-filtered — reviewer identity hidden per blind mode).

### PUT `/api/v1/submissions/{uuid}` *(auth: Author, only while `status=draft`)*

### POST `/api/v1/submissions/{uuid}/submit` *(auth: Author)*
Transitions draft → submitted. Full validation enforced.

### POST `/api/v1/submissions/{uuid}/withdraw` *(auth: Author)*
Body: `{ reason }`

### POST `/api/v1/submissions/{uuid}/versions` *(auth: Author — revision upload)*
Body (multipart): revised files + `response_to_reviewers_file`.

---

## Editorial

### POST `/api/v1/submissions/{uuid}/screening` *(auth: Editor)*
Body: `{ outcome: pass_to_review|desk_reject|revision_required, comment }`

### POST `/api/v1/submissions/{uuid}/reviewers` *(auth: Editor)*
Body: `{ reviewer_ids: [], due_date, blind_mode }`

### POST `/api/v1/submissions/{uuid}/decision` *(auth: Editor)*
Body: `{ decision, comment_to_author, internal_note }`

---

## Peer Review

### GET `/api/v1/review-assignments` *(auth: Reviewer)*
List of my assignments across journals: `?status=`

### POST `/api/v1/review-assignments/{id}/respond` *(auth: Reviewer)*
Body: `{ action: accept|decline, decline_reason? }`

### POST `/api/v1/review-assignments/{id}/review` *(auth: Reviewer)*
Body: `{ recommendation, score, rubric_scores, private_comment, public_comment }`
Response `201`.

---

## Finance

### GET `/api/v1/invoices` *(auth)*
Scoped: Author sees own; Finance sees journal's.

### GET `/api/v1/invoices/{uuid}`

### POST `/api/v1/invoices/{uuid}/payments` *(auth: Author, multipart)*
Body: `{ amount_transferred, bank_name, transfer_date, proof_file }`
Response `201`.

### POST `/api/v1/payments/{id}/verify` *(auth: Finance)*
Body: `{ action: approve|reject|need_reupload, reason? }`

### GET `/api/v1/invoices/{uuid}/receipt` *(auth: Author/Finance)*
Returns signed URL to receipt PDF.

---

## Publication

### GET `/api/v1/journals/{slug}/issues` — Public
### GET `/api/v1/journals/{slug}/issues/{id}/articles` — Public
### GET `/api/v1/articles/{uuid}` — Public (full metadata + galley links)
### GET `/api/v1/articles/{uuid}/download/{galley_id}` — Public, signed URL, increments download counter async
### GET `/api/v1/articles/search?q=&journal_id=&author=&year=` — Public

---

## Reports *(auth: role-scoped)*

### GET `/api/v1/reports/submissions?journal_id=&from=&to=`
### GET `/api/v1/reports/revenue?journal_id=&from=&to=`
### POST `/api/v1/reports/export` — Body: `{ report_type, format: excel|csv|pdf, filters }` → Response `202 Accepted` `{ job_id }` (queued; poll `/api/v1/reports/export/{job_id}` for `download_url` once ready — required pattern on shared hosting to avoid timeouts, see NFR-PERF-03).

---

## Notifications

### GET `/api/v1/notifications?unread=true` *(auth)*
### POST `/api/v1/notifications/{id}/read` *(auth)*
### GET `/api/v1/notification-preferences` / `PUT /api/v1/notification-preferences`

---

## Sample Full Request/Response — Submit Article

**Request**
```
POST /api/v1/submissions
Authorization: Bearer 12|abcdef...
Content-Type: multipart/form-data

title=Machine Learning for Crop Yield Prediction
abstract=This study explores...
keywords[]=machine learning
keywords[]=agriculture
keywords[]=yield prediction
language=en
license=cc-by
conflict_of_interest=None declared.
authors[0][name]=Dr. Siti Rahma
authors[0][email]=siti@univ.ac.id
authors[0][is_corresponding]=true
manuscript_file=@manuscript.pdf
```

**Response `201`**
```json
{
  "success": true,
  "data": {
    "id": "9f1c2e2a-...-uuid",
    "tracking_code": "AGRI-2026-000045",
    "status": "draft",
    "current_stage": "draft"
  },
  "message": "Submission draft created."
}
```

**Error `422`**
```json
{
  "success": false,
  "message": "The given data was invalid.",
  "errors": {
    "keywords": ["The keywords field must have at least 3 items."],
    "manuscript_file": ["The manuscript file must be a file of type: doc, docx, pdf."]
  }
}
```

---

## Swagger / OpenAPI
- Generate via annotations (`@OA\Get`, `@OA\Post`, etc.) directly above each controller method.
- Served at `/api/documentation` (l5-swagger default) — restrict access in production via Basic Auth or IP allowlist in `.htaccess` since it's a documentation surface, not meant to be fully public.
- CI step (or manual `php artisan l5-swagger:generate`) regenerates `storage/api-docs/api-docs.json` — must run on deploy (see Ch.12 CI/CD).

## Pagination / Filtering / Sorting Standard
```
GET /api/v1/submissions?page=2&per_page=20&sort=-submitted_at&filter[status]=under_review&filter[journal_id]=3
```
`sort=-field` = descending, `sort=field` = ascending. Multiple filters combine with AND.

---
*Continue to `07-Editorial-Review-Publication-Workflow.md`.*
