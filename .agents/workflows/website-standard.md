---
description: Website Standard -> Google Antigravity Workflow
---

# Website Standard -> Google Antigravity Workflow

Use this as a **Workflow** in Google Antigravity (`/website-standard-audit`) and optionally split parts into always-on Rules.

## Title
Website Standard Audit (SEO + Performance + Compliance)

## Description
Run a full website quality audit based on Google Search Essentials, Core Web Vitals, legal/trust requirements, technical SEO, and ad-landing readiness. Return prioritized fixes with evidence.

## Workflow Steps

### Step 1: Intake
1. Ask for:
   - Primary domain
   - Target country/language
   - Whether site runs ads (Google/Meta)
   - CMS/stack (if known)
2. Define scope:
   - Homepage + top conversion pages + legal pages + blog/product templates.

### Step 2: Crawlability & Indexing Checks
1. Verify `https` is used everywhere.
2. Check `robots.txt` exists and allows main content crawling.
3. Check `sitemap.xml` exists and includes important indexable URLs.
4. Confirm key pages return HTTP 200.
5. Detect redirect loops/chains (>1 hop).
6. Confirm important pages are within 3 clicks from homepage.

### Step 3: Required Pages & Trust Signals
1. Check existence and quality of:
   - About
   - Contact
   - Privacy Policy
   - Terms of Service
   - Cookie Policy (if cookies/analytics used)
   - Custom 404 page
2. For ad-ready sites, confirm Contact and policy pages are visible in nav/footer.

### Step 4: On-Page SEO Validation (per sampled page)
1. Validate:
   - Unique `<title>` (50-60 chars)
   - Unique meta description (120-160 chars)
   - Canonical tag
   - Robots meta is correct (`index,follow` unless intentional noindex)
2. Check heading structure:
   - Single H1
   - Logical H2/H3 hierarchy
3. Check image quality:
   - Descriptive `alt`
   - Compression and modern formats
4. Check URL hygiene:
   - Lowercase, hyphenated, short, descriptive

### Step 5: Performance & Core Web Vitals
1. Evaluate likely CWV risk points:
   - LCP target <= 2.5s
   - INP target <= 200ms
   - CLS target <= 0.1
2. Verify implementation basics:
   - Compression (Gzip/Brotli)
   - Static asset cache headers
   - `defer/async` for non-critical JS
   - Lazy loading below-the-fold images
3. Flag heavy scripts impacting ads landing performance.

### Step 6: Mobile UX & Accessibility
1. Mobile-first checks:
   - Correct viewport
   - No horizontal scroll
   - Readable text size
   - Touch targets roughly >= 48px
2. Accessibility checks (WCAG AA baseline):
   - Color contrast
   - Keyboard focus visibility
   - Form labels + clear errors
   - No harmful flashing content

### Step 7: Security & Technical Hygiene
1. Confirm HTTPS-only and no mixed content.
2. Check security headers where possible:
   - HSTS
   - CSP
   - X-Frame-Options
3. Ensure sensitive files are not publicly exposed (`.env`, `.git`, etc).

### Step 8: Structured Data & Analytics Readiness
1. Validate relevant schema usage (JSON-LD):
   - Organization/WebSite on homepage
   - BreadcrumbList on inner pages
   - Article/Product/FAQ where appropriate
2. Verify analytics/search setup:
   - Google Search Console verification + sitemap submission path
   - Analytics present (GA4 or alternative)
   - 404 tracking capability

### Step 9: Ad-Landing Readiness (Google/Meta)
1. Check conversion tracking basics:
   - Pixel/tag placement consistency
   - Event mapping readiness
   - UTM-friendly URL behavior
2. Check policy-risk elements:
   - Misleading claims
   - Broken destination pages
   - Thin or low-trust landing content

### Step 10: Output Format (Required)
Return exactly these sections:
1. **Executive Summary** (Pass/Needs Work/Critical)
2. **Critical Fixes (P0)** - blocking indexing, ad approval, or conversions
3. **High Priority (P1)** - strong SEO/CWV/trust improvements
4. **Medium Priority (P2)** - quality upgrades
5. **Per-Page Findings** - URL -> issues -> fix
6. **30/60/90 Day Plan**

For each finding include:
- Evidence
- Impact
- Recommended fix
- Effort (S/M/L)

## Optional companion Rule (Always On)

### Rule Title
Website QA Baseline Rule

### Rule Content
- Always evaluate pages against Search Essentials, CWV, legal/trust pages, mobile UX, accessibility, and security basics.
- Never mark a website as "ready" if `robots.txt`/`sitemap.xml`/policy pages are missing.
- Prioritize business impact: indexing blockers and conversion blockers first.
- Separate facts from assumptions.
- Provide fixes in implementation order (P0 -> P1 -> P2).
