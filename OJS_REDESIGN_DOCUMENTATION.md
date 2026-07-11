# 🚀 OJS Enterprise Design System — Complete Redesign Documentation

## Executive Summary

This document provides a comprehensive redesign of the Open Journal System (OJS) based on Laravel, following enterprise-grade design principles inspired by leading platforms like Stripe, Vercel, GitHub, and academic publishers like Elsevier, Springer Nature, and IEEE Xplore.

---

## 1. DESIGN FOUNDATION

### 1.1 Color Palette

#### Primary Colors
```
--primary: #0F4C81       /* Classic Blue - Trust, Professionalism */
--primary-hover: #0d4372
--primary-light: #EFF6FF

--secondary: #6B7280     /* Gray - Neutral, Balanced */
--secondary-hover: #5f6775
```

#### Semantic Colors
```
--success: #16A34A       /* Emerald - Acceptance, Completion */
--success-bg: #F0FDF4

--warning: #D97706       /* Amber - Review, Attention */
--warning-bg: #FFFBEB

--danger: #DC2626        /* Red - Rejection, Error */
--danger-bg: #FEF2F2

--info: #2563EB          /* Blue - Information */
--info-bg: #EFF6FF
```

#### Neutral Colors
```
--gray-50: #F9FAFB
--gray-100: #F3F4F6
--gray-200: #E5E7EB
--gray-300: #D1D5DB
--gray-400: #9CA3AF
--gray-500: #6B7280
--gray-600: #4B5563
--gray-700: #374151
--gray-800: #1F2937
--gray-900: #111827
```

### 1.2 Typography

#### Font Family
```css
font-family: 'Inter', system-ui, -apple-system, sans-serif;
```

#### Scale
```
--text-xs: 12px    --line-xs: 16px
--text-sm: 14px    --line-sm: 20px
--text-base: 16px  --line-base: 24px
--text-lg: 18px    --line-lg: 28px
--text-xl: 20px    --line-xl: 30px
--text-2xl: 24px   --line-2xl: 32px
--text-3xl: 30px   --line-3xl: 38px
--text-4xl: 36px   --line-4xl: 44px
```

#### Weights
```
--font-normal: 400
--font-medium: 500
--font-semibold: 600
--font-bold: 700
--font-extrabold: 800
```

### 1.3 Spacing System

```
--space-1: 4px
--space-2: 8px
--space-3: 12px
--space-4: 16px
--space-5: 20px
--space-6: 24px
--space-8: 32px
--space-10: 40px
--space-12: 48px
--space-16: 64px
--space-20: 80px
--space-24: 96px
```

### 1.4 Border Radius

```
--radius-sm: 6px
--radius-md: 8px
--radius-lg: 12px
--radius-xl: 16px
--radius-2xl: 20px
--radius-full: 9999px
```

### 1.5 Shadows

```
--shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05)
--shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06)
--shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05)
--shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04)
```

---

## 2. COMPONENT LIBRARY

### 2.1 Buttons

#### Variants
- **Primary**: Solid blue background, white text
- **Secondary**: Solid gray background, white text
- **Outline**: Transparent with border
- **Ghost**: Transparent, no border
- **Danger**: Red background for destructive actions
- **Success**: Green background for confirmations

#### Sizes
- **XS**: 28px height, 12px font
- **SM**: 32px height, 13px font
- **MD**: 40px height, 14px font (default)
- **LG**: 48px height, 16px font

### 2.2 Cards

#### Types
- **Stat Card**: For KPIs and metrics
- **Content Card**: For articles, journals
- **Action Card**: For quick actions
- **Form Card**: For grouped form sections

### 2.3 Forms

#### Input Fields
- Text input
- Email input
- Password input
- Number input
- Search input
- Date picker
- File upload

#### Selection Controls
- Select dropdown
- Multi-select
- Radio buttons
- Checkboxes
- Toggle switch

#### Rich Input
- WYSIWYG editor
- Code editor
- Markdown editor

### 2.4 Data Display

#### Tables
- Sortable columns
- Filterable rows
- Pagination
- Bulk actions
- Inline editing
- Export options

#### Lists
- Simple list
- Action list
- Timeline
- Activity feed

#### Badges
- Status badges (submitted, under_review, accepted, rejected, published)
- Role badges (admin, editor, reviewer, author)
- Category badges

### 2.5 Navigation

#### Types
- Sidebar navigation (dashboard)
- Top navigation (public)
- Breadcrumb
- Tabs
- Pagination

### 2.6 Feedback

#### Alerts
- Success
- Error
- Warning
- Info

#### Notifications
- Toast notifications
- Banner notifications
- In-app notifications

#### Loading States
- Spinner
- Skeleton loader
- Progress bar

### 2.7 Overlays

- Modal dialog
- Drawer/slideout
- Tooltip
- Popover
- Dropdown menu

---

## 3. ROUTE ANALYSIS & REDESIGN PLAN

### 3.1 Public Routes (web.php)

| Route | Controller | Purpose | Priority |
|-------|-----------|---------|----------|
| `/` | HomeController@index | Landing page | P0 |
| `/search` | HomeController@search | Global search | P0 |
| `/journals` | JournalController@index | Journal listing | P0 |
| `/journals/{slug}` | JournalController@show | Journal detail | P0 |
| `/articles` | ArticleController@index | Article listing | P0 |
| `/articles/{slug}` | ArticleController@show | Article reading view | P0 |
| `/oai` | OaiController | OAI-PMH endpoint | P2 |

### 3.2 Auth Routes (auth.php)

| Route | Controller | Purpose | Priority |
|-------|-----------|---------|----------|
| `/login` | LoginController@show | Sign in page | P0 |
| `/register` | RegisterController@show | Registration | P0 |
| `/logout` | LogoutController@destroy | Sign out | P0 |
| `/auth/orcid/*` | OrcidController | ORCID OAuth | P1 |
| `/auth/google/*` | GoogleController | Google OAuth | P1 |

### 3.3 Admin Routes (admin.php)

| Route | Controller | Purpose | Priority |
|-------|-----------|---------|----------|
| `/admin/dashboard` | DashboardController@index | Admin dashboard | P0 |
| `/admin/users` | UserController CRUD | User management | P0 |
| `/admin/journals` | JournalController CRUD | Journal management | P0 |
| `/admin/issues` | IssueController CRUD | Issue management | P0 |
| `/admin/articles` | ArticleController CRUD | Article management | P0 |
| `/admin/payments` | PaymentController CRUD | Payment verification | P0 |
| `/admin/settings` | SettingController CRUD | System settings | P0 |
| `/admin/integrations` | ApiIntegrationController | API integrations | P1 |
| `/admin/export/*` | ExportController | XML exports | P2 |

### 3.4 Author Routes (author.php)

| Route | Controller | Purpose | Priority |
|-------|-----------|---------|----------|
| `/author/dashboard` | DashboardController@index | Author dashboard | P0 |
| `/author/articles` | ArticleController CRUD | Submission management | P0 |
| `/author/articles/{id}/revision` | ArticleController@uploadRevision | Upload revision | P0 |
| `/author/articles/{id}/payment` | PaymentController@show | APC payment | P0 |
| `/author/orcid/sync` | OrcidProfileController@sync | ORCID sync | P1 |

### 3.5 Editor Routes (editor.php)

| Route | Controller | Purpose | Priority |
|-------|-----------|---------|----------|
| `/editor/dashboard` | DashboardController@index | Editor dashboard | P0 |
| `/editor/articles` | ArticleController@index | Manuscript queue | P0 |
| `/editor/articles/{id}` | ArticleController@show | Manuscript detail | P0 |
| `/editor/articles/{id}/assign-reviewer` | ArticleController@assignReviewer | Assign reviewer | P0 |
| `/editor/articles/{id}/decision` | ArticleController@makeDecision | Editorial decision | P0 |

### 3.6 Reviewer Routes (reviewer.php)

| Route | Controller | Purpose | Priority |
|-------|-----------|---------|----------|
| `/reviewer/dashboard` | DashboardController@index | Reviewer dashboard | P0 |
| `/reviewer/reviews` | ReviewController@index | Review assignments | P0 |
| `/reviewer/reviews/{id}` | ReviewController@show | Review detail | P0 |
| `/reviewer/reviews/{id}/accept` | ReviewController@accept | Accept invitation | P0 |
| `/reviewer/reviews/{id}/decline` | ReviewController@decline | Decline invitation | P0 |
| `/reviewer/reviews/{id}/submit` | ReviewController@submit | Submit review | P0 |

### 3.7 API Routes (api.php)

| Route | Controller | Purpose | Priority |
|-------|-----------|---------|----------|
| `/api/v1/journals` | JournalApiController@index | Journal API | P1 |
| `/api/v1/journals/{id}` | JournalApiController@show | Journal detail API | P1 |
| `/api/v1/submissions` | ArticleApiController@index | Submissions API | P1 |
| `/api/v1/submissions/{id}` | ArticleApiController@show | Submission detail API | P1 |

---

## 4. PAGE-BY-PAGE REDESIGN SPECIFICATIONS

### 4.1 Public Home Page (Landing)

**Purpose**: First impression, showcase journals, encourage submissions

**Target Users**: Researchers, authors, readers

**Key Elements**:
- Hero section with value proposition
- Statistics bar (articles, journals, acceptance rate)
- Featured journals grid
- Latest articles list
- How it works section (4-step workflow)
- Features/benefits section
- CTA for registration

**UX Improvements**:
- Clear visual hierarchy
- Prominent "Submit Manuscript" CTA
- Social proof (statistics, featured journals)
- Educational content (workflow visualization)

**Educational Content**:
- Submission process timeline
- Benefits of publishing OA
- Peer review explanation

---

### 4.2 Login Page

**Purpose**: Secure authentication

**Target Users**: All registered users

**Key Elements**:
- Email/password form
- Remember me checkbox
- SSO options (ORCID, Google)
- Link to registration
- Demo credentials (for development)

**UX Improvements**:
- Clean, focused design
- Clear error messages
- Password visibility toggle
- Quick access to demo accounts

---

### 4.3 Admin Dashboard

**Purpose**: System overview, quick access to key functions

**Target Users**: Administrators

**Key Elements**:
- KPI cards (users, journals, articles, revenue)
- Recent submissions table
- Pending payments list
- Quick action buttons
- System alerts

**UX Improvements**:
- At-a-glance metrics
- Contextual quick actions
- Priority-based alerts
- Activity timeline

---

### 4.4 Author Dashboard

**Purpose**: Track submissions, start new submission

**Target Users**: Authors

**Key Elements**:
- Submission statistics
- Active submissions list
- Submission wizard entry point
- Revision notifications
- Payment status

**UX Improvements**:
- Clear submission progress indicators
- One-click new submission
- Revision deadline reminders
- Payment tracking

**Educational Content**:
- Submission guidelines quick reference
- Tips for successful publication
- Common rejection reasons

---

### 4.5 Editor Dashboard

**Purpose**: Manage editorial workflow

**Target Users**: Editors

**Key Elements**:
- Queue statistics (pending, in review, decisions needed)
- Submissions requiring action
- Reviewer availability
- Decision deadlines
- Editorial activity metrics

**UX Improvements**:
- Prioritized action items
- Reviewer matching suggestions
- Batch operations
- Decision templates

**Educational Content**:
- Editorial best practices
- COPE guidelines reference
- Decision criteria checklist

---

### 4.6 Reviewer Dashboard

**Purpose**: Manage review assignments

**Target Users**: Reviewers

**Key Elements**:
- Pending invitations
- Active reviews with deadlines
- Completed reviews history
- Performance metrics
- Expertise areas

**UX Improvements**:
- Clear deadline visualization
- One-click accept/decline
- Review form templates
- Time estimates

**Educational Content**:
- Review guidelines
- Ethical considerations
- Constructive feedback tips

---

## 5. IMPLEMENTATION GUIDE

### 5.1 File Structure

```
resources/views/
├── layouts/
│   ├── app.blade.php         # Public layout
│   ├── guest.blade.php       # Auth layout
│   └── dashboard.blade.php   # Dashboard layout (all roles)
├── components/ui/
│   ├── button.blade.php
│   ├── card.blade.php
│   ├── input.blade.php
│   ├── select.blade.php
│   ├── textarea.blade.php
│   ├── checkbox.blade.php
│   ├── radio.blade.php
│   ├── badge.blade.php
│   ├── avatar.blade.php
│   ├── breadcrumb.blade.php
│   ├── empty-state.blade.php
│   ├── skeleton.blade.php
│   ├── modal.blade.php
│   ├── tooltip.blade.php
│   └── ...
├── partials/
│   ├── _flash-messages.blade.php
│   ├── _pagination.blade.php
│   ├── _search-bar.blade.php
│   └── ...
├── public/
│   ├── home.blade.php
│   ├── journals/
│   │   ├── index.blade.php
│   │   └── show.blade.php
│   ├── articles/
│   │   ├── index.blade.php
│   │   └── show.blade.php
│   └── search.blade.php
├── auth/
│   ├── login.blade.php
│   └── register.blade.php
├── admin/
│   ├── dashboard.blade.php
│   ├── users/
│   ├── journals/
│   ├── issues/
│   ├── articles/
│   ├── payments/
│   ├── settings/
│   └── integrations/
├── author/
│   ├── dashboard.blade.php
│   └── articles/
├── editor/
│   ├── dashboard.blade.php
│   └── articles/
└── reviewer/
    ├── dashboard.blade.php
    └── reviews/
```

### 5.2 Component Usage Examples

#### Button Component
```blade
<x-ui.button variant="primary" size="md" icon="plus">
    New Submission
</x-ui.button>

<x-ui.button variant="outline" href="{{ route('...') }}">
    View Details
</x-ui.button>
```

#### Form Field Component
```blade
<x-ui.form-field 
    label="Email Address" 
    for="email" 
    required 
    :error="$errors->first('email')"
    help-text="We'll never share your email."
>
    <x-ui.input 
        type="email" 
        name="email" 
        id="email"
        placeholder="researcher@institution.edu"
        icon="envelope"
    />
</x-ui.form-field>
```

#### Stat Card Component
```blade
<x-ui.stat-card 
    label="Total Submissions"
    value="1,234"
    icon="document-text"
    trend="+12%"
    trend-direction="up"
/>
```

### 5.3 Tailwind Configuration

```javascript
// tailwind.config.js
module.exports = {
  theme: {
    extend: {
      colors: {
        primary: {
          DEFAULT: '#0F4C81',
          hover: '#0d4372',
          light: '#EFF6FF',
        },
        // ... semantic colors
      },
      fontFamily: {
        sans: ['Inter', 'system-ui', 'sans-serif'],
      },
      borderRadius: {
        sm: '6px',
        md: '8px',
        lg: '12px',
        xl: '16px',
      },
      boxShadow: {
        sm: '0 1px 2px 0 rgba(0, 0, 0, 0.05)',
        md: '0 4px 6px -1px rgba(0, 0, 0, 0.1)',
        lg: '0 10px 15px -3px rgba(0, 0, 0, 0.1)',
      },
    },
  },
}
```

---

## 6. ACCESSIBILITY CHECKLIST

### 6.1 Keyboard Navigation
- [ ] All interactive elements focusable
- [ ] Visible focus indicators
- [ ] Logical tab order
- [ ] Skip links for main content

### 6.2 Screen Readers
- [ ] Semantic HTML structure
- [ ] ARIA labels where needed
- [ ] Alt text for images
- [ ] Form labels associated with inputs

### 6.3 Visual
- [ ] Color contrast ratio ≥ 4.5:1
- [ ] Don't rely on color alone
- [ ] Resizable text (up to 200%)
- [ ] High contrast mode support

### 6.4 Cognitive
- [ ] Clear, simple language
- [ ] Consistent navigation
- [ ] Error prevention and recovery
- [ ] Progress indicators for multi-step processes

---

## 7. PERFORMANCE OPTIMIZATION

### 7.1 Frontend
- Lazy load images and components
- Code splitting by route
- Tree shaking for unused CSS
- Optimize asset delivery (WebP, compression)

### 7.2 Backend
- Eager loading for N+1 queries
- Query caching
- Database indexing
- Queue heavy operations

### 7.3 Caching
- View caching in production
- Route caching
- Config caching
- Redis for sessions/cache

---

## 8. NEXT STEPS

1. **Phase 1**: Core Components
   - Implement all UI components
   - Create base layouts
   - Set up Tailwind config

2. **Phase 2**: Public Pages
   - Redesign landing page
   - Journal listing/detail
   - Article listing/detail
   - Search functionality

3. **Phase 3**: Authentication
   - Login/register pages
   - Password reset flow
   - Profile management

4. **Phase 4**: Dashboards
   - Admin dashboard + all admin pages
   - Author dashboard + submission flow
   - Editor dashboard + editorial workflow
   - Reviewer dashboard + review forms

5. **Phase 5**: Polish
   - Animations and transitions
   - Empty states
   - Error handling
   - Mobile responsiveness

6. **Phase 6**: Testing
   - Accessibility audit
   - Performance testing
   - Cross-browser testing
   - User testing

---

## 9. DESIGN PRINCIPLES

### 9.1 Clarity Over Cleverness
- Use familiar patterns
- Clear labels and instructions
- Obvious interactive elements

### 9.2 Progressive Disclosure
- Show essential information first
- Provide details on demand
- Avoid overwhelming users

### 9.3 Consistency
- Same patterns across all pages
- Predictable behavior
- Unified visual language

### 9.4 Efficiency
- Minimize clicks for common tasks
- Keyboard shortcuts for power users
- Batch operations where possible

### 9.5 Trust
- Professional appearance
- Clear privacy policies
- Transparent processes

### 9.6 Education
- Contextual help
- Best practice guidance
- Workflow visualization

---

*Document Version: 1.0*
*Last Updated: 2024*
*Status: Ready for Implementation*
