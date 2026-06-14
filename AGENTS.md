# AGENTS.md

## Project

BarPro Premium

Premium WordPress theme for a bar catering agency.

Purpose:

* Generate leads
* Showcase catering services
* Showcase portfolio and cases
* Present team and cocktail catalog
* Convert visitors into inquiries through the calculator and contact forms

Primary business goal:
Lead generation.

Any code changes must preserve lead generation functionality.

---

## Technology Stack

Backend:

* WordPress
* PHP 8.1+
* Custom Theme

Frontend:

* Vite
* JavaScript
* CSS

Compatibility:

* WordPress 6.8+
* PHP 8.1+

---

## Critical Business Features

The following functionality is business-critical and must never be broken:

1. Catering Calculator
2. Lead Forms
3. Email Submission
4. Cases Portfolio
5. Mobile Version
6. SEO Metadata

If uncertain, preserve existing behavior.

---

## Important Pages

### /

Homepage

Purpose:

* Present company
* Showcase services
* Drive calculator usage
* Generate inquiries

---

### /calculator

Most important page in the project.

Purpose:

* Calculate event cost
* Generate leads

Requirements:

* All calculations must work
* AJAX submission must work
* Validation must work
* Mobile version must work
* Email delivery must work

After any calculator change:

1. Open calculator page
2. Complete calculation
3. Submit form
4. Verify successful submission
5. Verify no JavaScript errors

---

### /cases

Portfolio page.

Purpose:

* Show completed events
* Increase trust

Requirements:

* Cases load correctly
* Images load correctly
* Filters work correctly
* Pagination works correctly

---

### /cocktails

Cocktail catalog.

Purpose:

* Showcase available cocktails

Requirements:

* Taxonomies work
* Filtering works
* Mobile layout works

---

### /team

Team page.

Purpose:

* Present bartenders and management

Requirements:

* Images optimized
* Responsive layout

---

### /catering

Service page.

Purpose:

* SEO landing page
* Service presentation

---

### /bar-catering

Service page.

Purpose:

* SEO landing page
* Lead generation

---

## Custom Post Types

### cases

Portfolio projects.

Possible fields:

* Event Type
* Guest Count
* Budget
* Gallery
* Description

Requirements:

* Admin editing works
* Frontend rendering works

---

### cocktails

Cocktail catalog.

Possible fields:

* Ingredients
* Category
* Description
* Image

Requirements:

* Archive pages work
* Single pages work

---

## AJAX Requirements

All AJAX handlers must:

* Validate nonce
* Sanitize input
* Escape output
* Use rate limiting
* Return JSON responses

Never remove security checks.

---

## Security Requirements

Mandatory:

* sanitize_text_field()
* sanitize_email()
* wp_verify_nonce()
* esc_html()
* esc_attr()
* esc_url()

Avoid:

* raw SQL
* direct $_POST output
* direct $_GET output
* unsafe file operations

All user input must be validated and sanitized.

---

## SEO Requirements

Maintain:

* Unique title tags
* Meta descriptions
* OpenGraph tags
* Structured data
* Internal linking
* Breadcrumbs where applicable

Goals:

* Lighthouse SEO 95+
* Mobile-friendly pages
* Fast page load

---

## Performance Requirements

Targets:

* Lighthouse Performance 90+
* CLS < 0.1
* LCP < 2.5s

Avoid:

* unnecessary queries
* duplicate requests
* large JavaScript bundles
* blocking assets

Prefer:

* lazy loading
* caching
* optimized images

---

## Accessibility

Maintain:

* semantic HTML
* alt attributes
* keyboard navigation
* visible focus states

Target:
WCAG AA where possible.

---

## Mobile Requirements

Mobile traffic is priority.

Every change must be tested for:

* 320px width
* 375px width
* 768px width

Do not introduce horizontal scrolling.

---

## Before Any Commit

Verify:

1. Homepage loads
2. Calculator loads
3. Calculator submission works
4. Cases archive works
5. Cocktails archive works
6. Mobile menu works
7. No PHP warnings
8. No JavaScript errors

---

## Audit Checklist

When auditing:

Review:

* Security
* Architecture
* WordPress standards
* Performance
* SEO
* Accessibility
* AJAX handlers
* CPT registration
* Mobile UX
* Build process
* Dependencies

Provide:

* Critical Issues
* High Priority Issues
* Medium Priority Issues
* Nice-to-have Improvements

---

## Coding Style

Prefer:

* WordPress Coding Standards
* Small reusable functions
* Clear naming
* Defensive programming

Avoid:

* duplicated code
* global state
* hidden side effects

---

## Deployment Requirements

Before release:

* Build assets
* Verify production bundle
* Verify no missing assets
* Verify calculator
* Verify forms
* Verify CPT pages
* Verify SEO metadata

Project is considered broken if lead generation no longer works.
