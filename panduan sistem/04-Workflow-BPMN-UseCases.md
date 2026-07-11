# Chapter 10–17 — Workflow Diagram, BPMN, Use Case Diagram & Specs, Activity, Sequence, State, Class Diagrams

## Introduction
Visual and specification artifacts describing system behavior — used directly by developers to implement controllers/services and by QA to derive test cases.

---

## 10. End-to-End Workflow (BPMN-style)

```mermaid
flowchart TD
    Start([Author Registers]) --> Submit[Submit Article]
    Submit --> Screen{Initial Screening}
    Screen -- Desk Reject --> RejEnd([Rejected - Archived])
    Screen -- Pass --> APCCheck{APC Required?}
    APCCheck -- Yes --> Invoice[Generate Invoice]
    Invoice --> Transfer[Author Bank Transfer]
    Transfer --> UploadProof[Upload Payment Proof]
    UploadProof --> Verify{Finance Verification}
    Verify -- Rejected --> UploadProof
    Verify -- Need Reupload --> UploadProof
    Verify -- Approved --> Receipt[Receipt Generated]
    Receipt --> AssignEditor[Editor Assignment]
    APCCheck -- No --> AssignEditor
    AssignEditor --> AssignReviewer[Reviewer Assignment]
    AssignReviewer --> PeerReview[Peer Review Round]
    PeerReview --> Decision{Editorial Decision}
    Decision -- Minor/Major Revision --> Revise[Author Revision]
    Revise --> PeerReview
    Decision -- Reject --> RejEnd
    Decision -- Accept --> CopyEdit[Copy Editing]
    CopyEdit --> Layout[Layout Editing]
    Layout --> Proof[Proofreading]
    Proof --> DOI[DOI Assignment]
    DOI --> VolIssue[Volume and Issue Assignment]
    VolIssue --> Publish[Publication]
    Publish --> Index[Indexing - Crossref/DOAJ/Garuda/SINTA]
    Index --> Archive[Archiving]
    Archive --> Report[Reporting]
    Report --> End([Done])
```

## 11. BPMN — Swimlane (Actor Responsibility)

```mermaid
flowchart LR
    subgraph Author
        A1[Register/Login]
        A2[Submit Manuscript]
        A3[Upload Payment Proof]
        A4[Revise Manuscript]
    end
    subgraph Editor
        E1[Initial Screening]
        E2[Assign Reviewer]
        E3[Make Decision]
        E4[Approve Publication]
    end
    subgraph Reviewer
        R1[Accept/Decline Assignment]
        R2[Submit Review]
    end
    subgraph Finance
        F1[Verify Payment]
        F2[Issue Receipt]
    end
    subgraph Production
        P1[Copyedit]
        P2[Layout]
        P3[Proofread]
    end
    A2 --> E1 --> E2 --> R1 --> R2 --> E3
    E3 -->|revision| A4 --> R1
    A3 --> F1 --> F2 --> E2
    E3 -->|accept| P1 --> P2 --> P3 --> E4
```

## 12. Use Case Diagram

```mermaid
flowchart TB
    Author((Author))
    Reviewer((Reviewer))
    Editor((Editor))
    Finance((Finance))
    Admin((Super/System Admin))
    Publisher((Publisher))
    Reader((Reader/Guest))

    Author --> UC1[Submit Article]
    Author --> UC2[Track Submission]
    Author --> UC3[Upload Payment Proof]
    Author --> UC4[Revise Article]
    Author --> UC5[Download Certificate]

    Reviewer --> UC6[Accept/Decline Review]
    Reviewer --> UC7[Submit Review]

    Editor --> UC8[Screen Submission]
    Editor --> UC9[Assign Reviewer]
    Editor --> UC10[Make Editorial Decision]

    Finance --> UC11[Verify Payment]
    Finance --> UC12[Issue Receipt]
    Finance --> UC13[Apply Waiver/Discount]

    Admin --> UC14[Manage Journal]
    Admin --> UC15[Manage Users/Roles]
    Admin --> UC16[View Audit Trail]

    Publisher --> UC17[Approve Publication]

    Reader --> UC18[Browse/Search Published Article]
    Reader --> UC19[Download Article]
```

## 13. Use Case Specifications (representative set)

### UC-01: Submit Article
- **Actor:** Author
- **Precondition:** Logged in, journal is `active`, no prior mandatory unpaid invoice blocking new submissions (if journal policy requires).
- **Main Flow:**
  1. Author opens "New Submission" wizard.
  2. Step 1: Fill metadata (title, abstract, keywords, language, section).
  3. Step 2: Add authors (self + co-authors), mark corresponding author.
  4. Step 3: Upload manuscript file + optional supplementary/dataset/ethics files.
  5. Step 4: Declare conflict of interest, funding, license.
  6. Step 5: Review & Submit.
  7. System validates (see `01-SRS-FSD-Requirements.md` §D.2), generates `tracking_code`, sets `status=submitted`, `current_stage=screening`.
  8. System fires `SubmissionCreated` event → notifies Author (confirmation) and Journal Manager (new submission alert).
- **Alternate Flow:** Author selects "Save Draft" at any step — validation relaxed, `status=draft`.
- **Postcondition:** Submission visible in Editor's screening queue.
- **Exceptions:** File exceeds size limit → inline error; duplicate title warning → non-blocking confirmation modal.

### UC-03: Upload Payment Proof
- **Actor:** Author
- **Precondition:** Invoice exists with status `waiting_payment`.
- **Main Flow:** Author opens Invoice detail → fills transfer details (bank, date, amount) → uploads proof file → system creates `payments` row (`status=waiting_verification`) and `payment_proofs` row → notifies Finance.
- **Postcondition:** Invoice status stays `waiting_payment` until Finance acts on the `payments` record (design choice: invoice reflects overall billing state, payment reflects a specific transfer attempt — supports partial/multiple transfer attempts).

### UC-07: Submit Review
- **Actor:** Reviewer
- **Precondition:** `review_assignments.status = accepted`.
- **Main Flow:** Reviewer opens review form (rubric per journal config) → scores criteria → writes public/private comments → selects recommendation → submits.
- **Postcondition:** `review_responses` row created, `review_assignments.status = completed`, Editor notified once **all** assignments in the round are completed (or editor manually closes the round early).

### UC-10: Make Editorial Decision
- **Actor:** Managing/Section Editor
- **Precondition:** At least one completed review (or desk-review-only path), round is closed or editor force-closes it.
- **Main Flow:** Editor reviews aggregated recommendations → selects decision → writes comment to author → submits.
- **Postcondition:** `editorial_decisions` row created; state machine (§16) transitions `submissions.current_stage` accordingly; notification sent to Author (and Co-Authors).

### UC-11: Verify Payment
- **Actor:** Finance
- **Precondition:** `payments.status = waiting_verification`.
- **Main Flow:** Finance opens payment queue → reviews proof file + entered details against bank mutation → Approve / Reject / Request Reupload.
  - Approve → `payments.status=approved`, `invoices.status=paid`, `receipts` row auto-generated (PDF via dompdf), Author notified, Submission unblocked to Reviewer Assignment stage.
  - Reject → `payments.status=rejected`, reason required, Author notified, may re-upload (new `payments` row).
  - Need Reupload → `payments.status=need_reupload`, note to Author.
- **Postcondition:** Audit trail entry with Finance user identity.

## 14. Activity Diagram — Peer Review Round

```mermaid
flowchart TD
    S([Editor Opens Round]) --> A[Select Reviewers]
    A --> B[Send Invitation]
    B --> C{Reviewer Response}
    C -- Decline --> A
    C -- Accept --> D[Reviewer Works on Manuscript]
    D --> E{Deadline Approaching?}
    E -- Yes, -3 days --> F[Send Reminder]
    F --> D
    E -- Overdue +2 days --> G[Escalate to Editor]
    G --> H{Editor Reassigns?}
    H -- Yes --> A
    H -- No, waits --> D
    D --> I[Submit Review]
    I --> J{All Assignments Complete?}
    J -- No --> D
    J -- Yes --> K[Editor Notified - Round Ready for Decision]
    K --> End([Round Closed])
```

## 15. Sequence Diagram — APC Payment Flow

```mermaid
sequenceDiagram
    actor Author
    participant Web as Laravel App
    participant DB as MySQL
    participant Q as Queue (DB-driven)
    participant Finance
    actor FinanceUser as Finance User

    Author->>Web: Submit article (screening passed)
    Web->>DB: Create Invoice (status=waiting_payment)
    Web->>Q: Dispatch SendInvoiceNotification Job
    Q-->>Author: Email + WhatsApp (invoice details)

    Author->>Web: Upload payment proof
    Web->>DB: Create Payment (status=waiting_verification) + PaymentProof
    Web->>Q: Dispatch NotifyFinanceJob
    Q-->>FinanceUser: Email/In-app notification

    FinanceUser->>Web: Open verification queue
    Web->>DB: Fetch pending payments
    FinanceUser->>Web: Approve payment
    Web->>DB: Update Payment.status=approved, Invoice.status=paid
    Web->>DB: Insert Receipt (generate PDF via dompdf)
    Web->>DB: Insert AuditTrail
    Web->>Q: Dispatch NotifyAuthorPaymentApproved Job
    Q-->>Author: Email + WhatsApp (receipt attached)
    Web->>DB: Update Submission.current_stage=editor_assignment
```

## 16. State Diagram — Submission Lifecycle

```mermaid
stateDiagram-v2
    [*] --> Draft
    Draft --> Submitted: author submits
    Submitted --> Screening: auto
    Screening --> DeskRejected: editor desk-reject
    Screening --> WaitingPayment: APC required
    Screening --> ReviewerAssignment: no APC / paid
    WaitingPayment --> WaitingVerification: proof uploaded
    WaitingVerification --> WaitingPayment: rejected/need reupload
    WaitingVerification --> ReviewerAssignment: payment approved
    ReviewerAssignment --> UnderReview: reviewers assigned
    UnderReview --> RevisionRequired: minor/major revision
    RevisionRequired --> UnderReview: author resubmits revision
    UnderReview --> Rejected: reject decision
    UnderReview --> Accepted: accept decision
    Accepted --> CopyEditing
    CopyEditing --> LayoutEditing
    LayoutEditing --> Proofreading
    Proofreading --> ReadyToPublish
    ReadyToPublish --> Published: volume/issue + DOI assigned
    Published --> [*]
    DeskRejected --> [*]
    Rejected --> [*]
    Draft --> Withdrawn: author withdraws
    Submitted --> Withdrawn: author withdraws (pre-assignment)
    Withdrawn --> [*]
```

## 17. Class Diagram (core domain models)

```mermaid
classDiagram
    class User {
      +bigint id
      +string uuid
      +string name
      +string email
      +string status
      +assignRole(role, journal)
      +hasPermission(perm, journal)
    }
    class Journal {
      +bigint id
      +string name
      +string issn
      +string doi_prefix
      +string status
    }
    class Submission {
      +bigint id
      +string tracking_code
      +string status
      +string current_stage
      +submit()
      +withdraw()
      +createVersion()
    }
    class SubmissionVersion {
      +int version_number
    }
    class ReviewRound {
      +int round_number
      +string blind_mode
      +closeRound()
    }
    class ReviewAssignment {
      +string status
      +date due_date
      +accept()
      +decline()
    }
    class EditorialDecision {
      +string decision
      +apply()
    }
    class Invoice {
      +string invoice_number
      +decimal amount
      +string status
      +markPaid()
    }
    class Payment {
      +string status
      +verify()
      +reject()
    }
    class Article {
      +string status
      +publish()
    }
    class ArticleDoi {
      +string doi
      +register()
    }

    User "1" -- "many" Submission : authors
    Journal "1" -- "many" Submission
    Submission "1" -- "many" SubmissionVersion
    Submission "1" -- "many" ReviewRound
    ReviewRound "1" -- "many" ReviewAssignment
    ReviewAssignment "1" -- "1" User : reviewer
    Submission "1" -- "many" EditorialDecision
    Submission "1" -- "many" Invoice
    Invoice "1" -- "many" Payment
    Submission "1" -- "1" Article : becomes
    Article "1" -- "1" ArticleDoi
```

## 18–19. Deployment & Component Diagrams
See `02-System-Architecture-Hostinger.md` §6.3–6.4 (kept there to avoid duplication and stay consistent with the adapted Hostinger stack).

---
*Continue to `05-PermissionMatrix-UserStories.md`.*
