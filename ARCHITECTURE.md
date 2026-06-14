# ARCHITECTURE.md

## BarPro Premium v10

## Business Goal

Primary goal:

Generate leads for bar catering services.

The project is not a content website.
The project is a lead-generation funnel.

Success metric:

Lead submissions from calculator and contact forms.

---

## Core Architecture

Theme Type:

Custom WordPress Theme

Stack:

* WordPress
* PHP 8.1+
* Vite
* JavaScript
* CSS
* ACF

---

## Critical Features

The following features are business-critical.

**Never break:**

1. Calculator
2. Lead submission
3. Email delivery
4. Cases portfolio
5. Mobile navigation
6. SEO metadata

Any regression in these areas is a release blocker.

---

## Main Files

### functions.php

Responsibilities:

* Theme bootstrap
* Security hardening
* Security headers
* Asset loading
* Theme setup
* Global initialization

Contains:

* CSP headers
* XSS protection
* Referrer policy
* XML-RPC disable
* Version hiding

**Modify carefully.**

---

### inc/ajax.php

Responsibilities:

* Calculator AJAX
* Lead saving
* Validation
* Rate limiting
* Spam protection

**Critical file.**

Contains:

* calculate_price
* save_lead

Must preserve:

* nonce validation
* rate limiting
* JSON responses
* input sanitization

---

### inc/class-cpt-manager.php

Responsibilities:

Registration of all CPT and taxonomies.

Registered CPT:

* cocktail
* package
* team_member
* case_study
* testimonial
* addon_service
* lead

**Do not change slugs without migration plan.**

---

### page-calculator.php

**Business-critical template.**

Purpose:

Event cost estimation.

Contains:

* Multi-step calculator
* Event type selection
* Guest count
* Service selection
* Lead capture

This file directly affects conversion rate.

---

### inc/seo.php

SEO functionality.

Maintain:

* metadata
* structured data
* indexing logic

---

### inc/tz-seo.php

Advanced SEO layer.

Modify carefully.

---

## CPT Architecture

### cocktail

Purpose: Cocktail catalog.
Frontend: `/cocktails`

Expected data:

* name
* description
* image
* category

---

### package

Purpose: Service packages.

Used by:

* calculator
* service pages

---

### team_member

Purpose: Company team.
Used by: `/team`

---

### case_study

Purpose: Portfolio.
Used by: `/cases`

**Business importance: HIGH**

---

### testimonial

Purpose: Trust building.
Used on landing pages.

---

### addon_service

Purpose: Additional services.
Used by calculator pricing.

---

### lead

Purpose: Lead storage.

**Business importance: CRITICAL**

Must remain accessible in admin panel.

---

## Calculator Architecture

### Step 1

Event type

Examples:

* Wedding
* Corporate
* Birthday
* Festival
* Private Event

### Step 2

Guest count — frontend range selector.

### Step 3+

Service selection and event parameters.

### Final Step

Lead collection.

Expected fields:

* Name
* Phone
* Contact data

---

## Pricing Architecture

Location: `inc/ajax.php`

Current logic:

```
Base Price
  (Guests × Multiplier × Price Per Guest)
+ (Hours × Price Per Hour)
+ Additional Services
− Discount
```

Values are managed through Customizer.

**Do not hardcode prices.**

---

## Security Architecture

All AJAX handlers must:

* verify nonce
* sanitize input
* validate data
* escape output

Required functions:

* sanitize_text_field
* sanitize_email
* wp_verify_nonce
* esc_html
* esc_attr
* esc_url

**Never trust:**

* $_POST
* $_GET
* $_REQUEST

---

## Anti-Spam

Current protections:

* Rate limiting
* Honeypot
* Validation

**Do not remove.**

---

## Lead Flow

```
User
  ↓
Calculator
  ↓
AJAX
  ↓
save_lead
  ↓
Lead CPT
  ↓
Email Notification
  ↓
Manager
```

---

## SEO Priorities

Highest priority pages:

1. Homepage
2. Calculator
3. Cases
4. Catering
5. Bar Catering

Maintain:

* OpenGraph
* Schema
* Meta Description
* Titles

---

## Performance

Target: Lighthouse > 90

Maintain:

* Vite bundles
* lazy loading
* optimized images

Avoid:

* duplicate queries
* large JS dependencies

---

## Mobile First

Required breakpoints:

* 320px
* 375px
* 768px
* 1024px+

Every change must be verified on mobile.

---

## Testing Checklist

Before release:

**Calculator**
* loads
* calculates
* submits

**Leads**
* saved
* emailed

**Cases**
* archive works
* single works

**Team**
* loads

**Cocktails**
* archive works

**SEO**
* meta tags present

**Performance**
* no major regressions

**JavaScript**
* no console errors

**PHP**
* no warnings
* no fatals

> Release is blocked if lead generation is broken.

---

## File Structure Reference

```
barpro-premium-v10/
├── AGENTS.md               ← AI dev rules
├── ARCHITECTURE.md         ← this file
├── BUSINESS_RULES.md       ← business logic
├── INSTALLATION.md         ← setup guide
├── README.md
├── functions.php           ← bootstrap, security, assets
├── style.css               ← theme header
├── header.php
├── footer.php
├── index.php
├── front-page.php
├── archive-case_study.php
├── archive-cocktail.php
├── single-case_study.php
├── page-calculator.php     ← CRITICAL
├── page-catering.php
├── page-bar-catering.php
├── page-cocktails.php
├── page-packages.php
├── page-team.php
├── inc/
│   ├── ajax.php            ← CRITICAL
│   ├── class-cpt-manager.php
│   ├── customizer.php
│   ├── meta.php
│   ├── seo.php
│   ├── template-functions.php
│   ├── tz-acf.php
│   ├── tz-bootstrap.php
│   └── tz-seo.php
├── template-parts/
│   ├── home/
│   │   ├── hero.php
│   │   ├── bento.php
│   │   ├── cocktails.php
│   │   ├── catering.php
│   │   ├── showcase.php
│   │   ├── pricing.php
│   │   ├── team.php
│   │   ├── testimonials.php
│   │   └── cta.php
│   ├── honeypot.php
│   ├── tz-breadcrumbs.php
│   ├── tz-cta-row.php
│   ├── tz-hero.php
│   └── tz-sticky-cta.php
├── assets/
│   ├── css/
│   │   ├── studio.css      ← master CSS (via @import)
│   │   ├── tokens.css
│   │   ├── design-system.css
│   │   ├── layout.css
│   │   ├── premium.css
│   │   ├── motion.css
│   │   ├── tz.css
│   │   └── components/
│   └── js/
│       ├── main.js         ← jQuery: popups, forms, scroll
│       ├── motion.js       ← motion coordinator
│       ├── tz.js           ← calculator + filters
│       ├── premium-interactions.js
│       └── modules/        ← individual motion modules
│           ├── drawer.js   ← mobile nav (CRITICAL)
│           ├── nav.js
│           ├── cursor.js
│           ├── hero.js
│           ├── lenis.js
│           ├── magnetic.js
│           ├── micro.js
│           ├── counters.js
│           ├── scroll-animations.js
│           ├── split-type.js
│           └── page-transitions.js
├── acf-json/               ← ACF Local JSON sync
├── vite.config.js
└── package.json
```
