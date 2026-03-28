## Key: Labels & Tags

| Tag | Meaning |
|-----|---------|
| 🆕 NEW | No GitHub issue; identified from codebase/schema gap |
| ✂️ SPLIT | Original issue split; original link preserved |
| ⚠️ BLOCKER | Blocks launch or a critical dependency |
| 💰 MONEY | Directly enables monetization |
| 🐛 BUG | Active bug on live site |
| 🔗 DEP: X | Depends on task X |
| 🟢 XS | < 2 hours, single file or function |
| 🟡 S | ~half day, few files |
| 🟠 M | 1–3 days, multiple files/systems |
| 🔴 L | 1–2 weeks, significant scope |
| ⬛ XL | Weeks–months, major architectural work |
| 🔧 | No-decision: pure mechanical execution, no design choices |
| 💬 CONSIDER | No action yet; revisit later |

---

## Phase 0 — Core Infrastructure & Foundations

*Do these first. Self-contained; unblock everything that follows.*

---

### 0.1 · Critical Live Bug Fixes 🐛 ⚠️

| Issue | URL | Note |
|-------|-----|------|
| [ffstatistics optimization](https://github.com/Simbiat/FFXIV-Tracker/issues/11) | FFXIV-Tracker | 20-min generation blocks resources 🔴 L |
| [ffAddServers on wrong days](https://github.com/Simbiat/FFXIV-Tracker/issues/31) | FFXIV-Tracker | Cron scheduling bug; possible Cron library bug 🟡 S |
| [multiple groups in "current" status](https://github.com/Simbiat/FFXIV-Tracker/issues/12) | FFXIV-Tracker | Duplicate FC "current" membership 🟡 S 🔴 L |
| [Bic update through UI waits](https://github.com/Simbiat/simbiat.ru/issues/349) | simbiat.ru | Should update one-at-a-time 🟢 XS 🔧 |
| [0 does not conform to required format](https://github.com/Simbiat/simbiat.ru/issues/103) | simbiat.ru | Timestamp input bug 🟢 XS |
| [index page says it's static when DB is down](https://github.com/Simbiat/simbiat.ru/issues/168) | simbiat.ru | Wrong error message 🟢 XS 🔧 |

---

### 0.2 · Static Analysis Setup ⚠️ 🟡 S 🔧

Set up PHP static analysis (PhpStan, Psalm), TypeScript/JS linting, and CSS linting **before** the cleanup pass in 0.3 — so 0.3 is clearing a known, finite list rather than guessing at what needs fixing.

**Issue:** [More SAT](https://github.com/Simbiat/simbiat.ru/issues/166)

---

### 0.3 · Security & Code Quality Quick Fixes ⚠️ 🔗 DEP: 0.2

| Issue | URL | Note |
|-------|-----|------|
| [SQL Concatenation](https://github.com/Simbiat/simbiat.ru/issues/110) | simbiat.ru | Audit all query construction 🟡 S 🔧 |
| [twig raw](https://github.com/Simbiat/simbiat.ru/issues/111) | simbiat.ru | Remove unsafe `\|raw` usages 🟡 S 🔧 |
| [strict_variables in twig](https://github.com/Simbiat/simbiat.ru/issues/7) | simbiat.ru | Enable strict mode 🟢 XS 🔧 |
| [eval, setTimeOut, setInterval, Function](https://github.com/Simbiat/simbiat.ru/issues/50) | simbiat.ru | Remove/replace unsafe JS patterns 🟡 S |
| [TS clean-up](https://github.com/Simbiat/simbiat.ru/issues/368) | simbiat.ru | Resolve IDE/SAT warnings in TS/JS; also covers some unsafe JS patterns 🟡 S |
| [resolve EA inspections](https://github.com/Simbiat/simbiat.ru/issues/15) | simbiat.ru | Resolve Qodana/IDE highlights — do early so development only generates new items 🟠 M |
| [allow media only from same host](https://github.com/Simbiat/simbiat.ru/issues/55) | simbiat.ru | Verify sanitizer rule (may already be done) 🟢 XS 🔧 |
| [don't tell if account exists](https://github.com/Simbiat/simbiat.ru/issues/102) | simbiat.ru | Login/reset response normalization 🟢 XS |
| [DMARC scan](https://github.com/Simbiat/simbiat.ru/issues/237) | simbiat.ru | Add easydmarc scan link to the security/about page as a trust signal 🟢 XS 🔧 |
| [Pure JSON shown on form submit](https://github.com/Simbiat/simbiat.ru/issues/365) | simbiat.ru | Fix AJAX handler to never render raw JSON 🟡 S |
| [limit emails with `+`](https://github.com/Simbiat/simbiat.ru/issues/53) | simbiat.ru | Reject/normalize `+` addressing on registration 🟢 XS 🔧 |
| [anonymized IPs](https://github.com/Simbiat/simbiat.ru/issues/59) | simbiat.ru | Implement per IP storage policy decision 🟡 S |
| [DNT and GPC](https://github.com/Simbiat/simbiat.ru/issues/360) | simbiat.ru | Honor DNT/GPC headers in session/SEO storage 🟡 S |
| [Client Hints](https://github.com/Simbiat/simbiat.ru/issues/359) | simbiat.ru | Accept-CH for Matomo bot detection; update Privacy Policy 🟡 S |

---

### 0.4 · Remove Foreign Key Constraints ⚠️ 🔗 DEP: 0.1 🔴 L
**Issue:** [No foreign key constraints?](https://github.com/Simbiat/simbiat.ru/issues/339)

---

### 0.5 · Shared Library Finalization & Maintenance

Stabilize all libraries used throughout the project. Includes ongoing maintenance tasks.

**array2table:**

| Issue | URL |
|-------|-----|
| [adjust to new cute-bytes](https://github.com/Simbiat/array2table/issues/2) | array2table 🟢 XS 🔧 |
| [Ordinal numbers support](https://github.com/Simbiat/array2table/issues/3) | array2table 🟡 S |
| [better currency support](https://github.com/Simbiat/array2table/issues/4) | array2table 🟡 S |
| [more html types support](https://github.com/Simbiat/array2table/issues/5) | array2table 🟡 S |
| [aria to inputs](https://github.com/Simbiat/array2table/issues/6) | array2table 🟢 XS 🔧 |
| [Support IRI](https://github.com/Simbiat/array2table/issues/7) | array2table 🟡 S |
| [timezone in array2table](https://github.com/Simbiat/array2table/issues/8) | array2table 🟡 S |
| [extensibility](https://github.com/Simbiat/array2table/issues/9) | array2table 🟠 M |

**translit:**

| Issue | URL |
|-------|-----|
| [complete transliterations](https://github.com/Simbiat/translit/issues/2) | translit 🟡 S 🔧 |
| [ASCII only](https://github.com/Simbiat/translit/issues/3) | translit 🟡 S |
| [update documentation](https://github.com/Simbiat/translit/issues/4) | translit 🟢 XS 🔧 |
| [path from hash](https://github.com/Simbiat/translit/issues/5) | translit 🟡 S |
| [potential extra functions](https://github.com/Simbiat/translit/issues/6) | translit 🟡 S |
| [password generator](https://github.com/Simbiat/translit/issues/7) | translit 🟡 S |
| [letters to numbers](https://github.com/Simbiat/translit/issues/8) | translit 🟡 S |

**Other libraries and ongoing maintenance:**

| Issue | URL | Note |
|-------|-----|------|
| [languages to separate files](https://github.com/Simbiat/sand-clock/issues/1) | sand-clock | Extensibility 🟢 XS 🔧 |
| [[php8.4] Use HTMLDocument](https://github.com/Simbiat/HTMLCut/issues/2) | HTMLCut | PHP 8.4 upgrade 🟢 XS 🔧 |
| [[php8.4] Use HTMLDocument](https://github.com/Simbiat/nl2tag/issues/2) | nl2tag | PHP 8.4 upgrade 🟢 XS 🔧 |
| [refactor](https://github.com/Simbiat/HTMLCache/issues/1) | HTMLCache | RFC 7234 compliance, cache invalidation 🟠 M |
| [DOMDocument usage](https://github.com/Simbiat/simbiat.ru/issues/378) | simbiat.ru | Replace DOMDocument with `\Dom\HTMLDocument` (PHP 8.4) in OG description and sitemap generation 🟡 S 🔧 |
| [Generate list of missing icons](https://github.com/Simbiat/simbiat.ru/issues/379) | simbiat.ru | DeviceDetectorIcons library — generate list of missing icons to update README; ongoing maintenance 🟡 S |
| [integrate array2table](https://github.com/Simbiat/simbiat.ru/issues/141) | simbiat.ru | Use array2table in templates where appropriate — note: requires `\|raw` in Twig; only apply where output is provably server-generated, not user-supplied. Revisit with Twig restructure in Phase 1.2 🟡 S |

---

### 0.6 · HTTP20 Library Split & Completion ✂️
**Issue:** [Split into smaller libraries](https://github.com/Simbiat/HTTP20/issues/2) — do this first, then work remaining HTTP20 issues.

| Issue | URL | Priority |
|-------|-----|----------|
| [disable header rewrite](https://github.com/Simbiat/HTTP20/issues/3) | HTTP20 | High 🟢 XS 🔧 |
| [cachecontrol updates](https://github.com/Simbiat/HTTP20/issues/7) | HTTP20 | High 🟡 S |
| [Early hints](https://github.com/Simbiat/HTTP20/issues/4) | HTTP20 | Medium 🟡 S |
| [zstd](https://github.com/Simbiat/HTTP20/issues/6) | HTTP20 | Medium 🟡 S |
| [Document-Policy](https://github.com/Simbiat/HTTP20/issues/8) | HTTP20 | Medium 🟡 S |
| [OpenGraph](https://github.com/Simbiat/HTTP20/issues/12) | HTTP20 | Medium 🟡 S |
| [RDFa prefixes](https://github.com/Simbiat/HTTP20/issues/13) | HTTP20 | Medium 🟡 S |
| [Image sitemaps](https://github.com/Simbiat/HTTP20/issues/1) | HTTP20 | Medium 🟡 S |
| [media sitemaps](https://github.com/Simbiat/HTTP20/issues/14) | HTTP20 | Medium 🟡 S |
| [Server-Timing](https://github.com/Simbiat/HTTP20/issues/16) | HTTP20 | Medium 🟡 S |
| [hash in HTTP header](https://github.com/Simbiat/HTTP20/issues/17) | HTTP20 | Medium (enables Ajax integrity) 🟡 S 🟠 M |
| [replace trigger_error with exceptions](https://github.com/Simbiat/HTTP20/issues/18) | HTTP20 | Medium — 🔗 DEP: Phase 1.4 (Custom Error class must exist first) 🟡 S |
| [roadmap library](https://github.com/Simbiat/HTTP20/issues/19) | HTTP20 | Medium 💰 (needed for public roadmap page in Phase 1.11) 🟡 S 🟠 M |
| [force left/right for timeline](https://github.com/Simbiat/HTTP20/issues/22) | HTTP20 | Low 🔴 L 🟢 XS |
| [sitemap enhancements](https://github.com/Simbiat/HTTP20/issues/24) | HTTP20 | Low 🔴 L 🟡 S |
| [[php8.4] Use HTMLDocument](https://github.com/Simbiat/HTTP20/issues/25) | HTTP20 | Low 🔴 L 🟢 XS 🔧 |
| [callback in clientReturn](https://github.com/Simbiat/HTTP20/issues/11) | HTTP20 | Low 🟡 S |
| [HTTP authentication](https://github.com/Simbiat/HTTP20/issues/15) | HTTP20 | Low 🟡 S 🟠 M |
| [Remove Feature-Policy](https://github.com/Simbiat/HTTP20/issues/5) | HTTP20 | 💬 DEFER — cannot remove until Document-Policy and Permission-Policy have broader adoption 🟢 XS 🔧 |
| [HAL and Siren formats](https://github.com/Simbiat/HTTP20/issues/9) | HTTP20 | 💬 CONSIDER — niche 🟠 M |
| [Resumable PUT](https://github.com/Simbiat/HTTP20/issues/10) | HTTP20 | 💬 CONSIDER — only if large file uploads are needed 🟡 S 🟠 M |

**After hash-in-header (#17) is done:**
- [Ajax integrity validation](https://github.com/Simbiat/simbiat.ru/issues/260) — validate AJAX responses using header hash

---

## Phase 1 — Page Class Cleanup, Then Symfony Migration

### 1.0 · Abstract Page Class Cleanup ⚠️ 🟠 M
**Issues:** [Common webpage class](https://github.com/Simbiat/simbiat.ru/issues/358), [Pages' logic to classes](https://github.com/Simbiat/simbiat.ru/issues/356)

---

### 1.1 · Symfony Migration Plan & Scoping 🆕 ⚠️ 🔗 DEP: 1.0 🟠 M
**Issue:** [migrate to symfony](https://github.com/Simbiat/simbiat.ru/issues/12)

Possible component adoption order (each step independently releasable):

1. **Symfony Logger** — first; immediate audit trail value; DB action logging
2. **Symfony Forms + Validator** — replaces current input sanitization; adopt Symfony Translation ([multilanguage](https://github.com/Simbiat/simbiat.ru/issues/63)) here
3. **Symfony Security** — replaces/wraps current auth; includes CSRF component; Symfony Security bundle used for OAuth2 where needed (social media integration later)
4. **Symfony HTTP Foundation + Controllers** — standardizes request/response; page classes (already cleaned up in 1.0) conform to Symfony Controller paradigm here (see 1.2)
5. **Symfony Messenger** — async delivery for notifications, IART, social posts
6. **Worker mode** — FrankenPHP worker mode via Symfony HTTP Foundation ([worker approach](https://github.com/Simbiat/simbiat.ru/issues/13) — now unblocked)

---

### 1.2 · Symfony Controller Migration 🔗 DEP: 1.0, 1.1 (step 4) ⬛ XL
**Issues:** [file based routing](https://github.com/Simbiat/simbiat.ru/issues/9), [POST to method arguments](https://github.com/Simbiat/simbiat.ru/issues/329), [reverse twig](https://github.com/Simbiat/simbiat.ru/issues/343), [twig components](https://github.com/Simbiat/simbiat.ru/issues/342), [twig check to PHP](https://github.com/Simbiat/simbiat.ru/issues/367)

Migrate existing page classes to Symfony Controllers. File-based routing via Symfony. POST as method arguments. Reverse Twig structure (per-page Twigs with shared includes, Symfony UX Twig Components). Boolean-only checks in templates.

**Also here (natural fits during this restructure):**
- 🟢 XS [customize favicon](https://github.com/Simbiat/simbiat.ru/issues/375) — depends on Controller type
- 🟡 S 🔧 [Move non-search functions](https://github.com/Simbiat/simbiat.ru/issues/323) — move non-search logic from tracker search pages to main pages
- 🟡 S [http20 headers](https://github.com/Simbiat/simbiat.ru/issues/30) — apply HTTP20 header classes to pages; 🔗 DEP: 0.6 (HTTP20 split)
- 🟡 S [Early Hints](https://github.com/Simbiat/simbiat.ru/issues/31) — consider Early Hints instead of HTTP/2 push; 🔗 DEP: 0.6 (HTTP20/issues/4)
- 🟢 XS 🔧 [co-cache or private](https://github.com/Simbiat/simbiat.ru/issues/97) — verify no-cache/private on editor/user pages once Controller types are codified
- [opening from other sites does not authenticate](https://github.com/Simbiat/simbiat.ru/issues/341) — likely resolved by Symfony request handling; verify here
- 🟡 S [integrate array2table](https://github.com/Simbiat/simbiat.ru/issues/141) — use array2table in Twig where safe (server-generated output only; `\|raw` permitted in those cases)
- 🟢 XS 🔧 [game controls array](https://github.com/Simbiat/simbiat.ru/issues/234) — standardize controls display for 3 existing game pages; quick kill once Controllers exist

---

### 1.3 · DB Action Logging via Symfony Logger 🔗 DEP: 1.1, 1.2 🟡 S
**Issue:** [logging](https://github.com/Simbiat/simbiat.ru/issues/134)

Structured DB action logging using Symfony Logger, placed strategically now that Page class structure is in place. Required for audit trails on payments, moderation, and bans.

**Also:** [API request IDs](https://github.com/Simbiat/simbiat.ru/issues/160) — add request IDs to API logs here.

---

### 1.4 · Error Handling Overhaul 🔗 DEP: 1.2 🟠 M
**Issues:** [Custom Error library](https://github.com/Simbiat/simbiat.ru/issues/372), [Error pages to separate PHP entities](https://github.com/Simbiat/simbiat.ru/issues/373), [throw HTTP errors instead of returning](https://github.com/Simbiat/simbiat.ru/issues/324), [consider custom exceptions](https://github.com/Simbiat/simbiat.ru/issues/11), [403 vs 401](https://github.com/Simbiat/simbiat.ru/issues/248)

Custom exception per HTTP error type. Throw instead of return. Error pages as separate classes. Cron fails loudly when job class not found. Correct 401 vs 403 semantics.

**Unblocks:** HTTP20 `replace trigger_error with exceptions` (0.5) — implement here.

---

### 1.5 · Symfony Forms & Input Handling 🔗 DEP: 1.1 🔴 L
**Issues:** [global user strings sanitization](https://github.com/Simbiat/simbiat.ru/issues/46), [UTF normalization](https://github.com/Simbiat/simbiat.ru/issues/49), [trim input fields](https://github.com/Simbiat/simbiat.ru/issues/380), [multilanguage](https://github.com/Simbiat/simbiat.ru/issues/63), [default language setting](https://github.com/Simbiat/simbiat.ru/issues/106), [No Content-Language header](https://github.com/Simbiat/simbiat.ru/issues/107)

Symfony Forms + Validator replaces current input handling. Symfony Translation for multilanguage. NFC normalization, trim, Content-Language header, default language setting.

---

### 1.6 · DB Optimization Pass 🔗 DEP: 1.3 🔴 L
**Issues:** [deferred joins](https://github.com/Simbiat/simbiat.ru/issues/122), [UNION vs UNION ALL](https://github.com/Simbiat/simbiat.ru/issues/240), [joins to subqueries](https://github.com/Simbiat/simbiat.ru/issues/183), [Query efficiency](https://github.com/Simbiat/simbiat.ru/issues/241), [check for long queries](https://github.com/Simbiat/simbiat.ru/issues/297), [index on composite keys](https://github.com/Simbiat/simbiat.ru/issues/165), [consider JSON from DB](https://github.com/Simbiat/simbiat.ru/issues/331), [time series in SQL](https://github.com/Simbiat/simbiat.ru/issues/257), [Potential performance improvements](https://github.com/Simbiat/simbiat.ru/issues/281), [check all insert ignore](https://github.com/Simbiat/simbiat.ru/issues/164), [int instead of varchar in KF](https://github.com/Simbiat/simbiat.ru/issues/118), [Show page load time](https://github.com/Simbiat/simbiat.ru/issues/169)

Full DB/query optimization pass while the codebase is already being restructured. Use long-query logging (1.3) to find real bottlenecks. Prefetch-on-hover and critical CSS applied during Twig restructure (1.2). Also: Server-Timing integration (0.5).

---

### 1.7 · Symfony Security (Auth Overhaul) 🔗 DEP: 1.1 ⬛ XL
**Issues:** [up-to-date CSRF](https://github.com/Simbiat/simbiat.ru/issues/124), [session revocation](https://github.com/Simbiat/simbiat.ru/issues/245), [remember me needs to expire](https://github.com/Simbiat/simbiat.ru/issues/117), [ed25519](https://github.com/Simbiat/simbiat.ru/issues/40), [2FA, passkeys and yubikey](https://github.com/Simbiat/simbiat.ru/issues/192), [password entropy](https://github.com/Simbiat/simbiat.ru/issues/74)

Replace/wrap current auth with Symfony Security bundle. CSRF via Symfony component. Session expiry and revocation. ed25519, 2FA/passkeys (WebAuthn). Password entropy enforcement.

---

### 1.8 · API Action Nodes & OpenAPI 🔗 DEP: 1.2 🟠 M
**Issues:** [api action nodes](https://github.com/Simbiat/simbiat.ru/issues/352), [GraphQL or OpenAPI](https://github.com/Simbiat/simbiat.ru/issues/232), [Access-Control-Allow-Origin for API](https://github.com/Simbiat/simbiat.ru/issues/181), [Pragmatic RESTful](https://github.com/Simbiat/simbiat.ru/issues/182), [Origin header](https://github.com/Simbiat/simbiat.ru/issues/227)

Permission-check routing layer in API. OpenAPI spec (start with most-used endpoints). CORS header. RESTful semantics cleanup. Origin header in cURL.

---

### 1.9 · Sitemap Overhaul 🔗 DEP: 1.2 🟠 M
**Issues:** [sitemap generation + restructure](https://github.com/Simbiat/simbiat.ru/issues/318), [IndexNow support](https://github.com/Simbiat/simbiat.ru/issues/347), [human-readable portions of URLs](https://github.com/Simbiat/simbiat.ru/issues/161), [Use XML sitemap for lists without search](https://github.com/Simbiat/simbiat.ru/issues/314), [sitemap enhancements](https://github.com/Simbiat/HTTP20/issues/24)

---

### 1.10 · One Search Page 🔗 DEP: 1.2 🟠 M
**Issues:** [one search page](https://github.com/Simbiat/simbiat.ru/issues/18), [filter threads by creator id](https://github.com/Simbiat/simbiat.ru/issues/19), [search history](https://github.com/Simbiat/simbiat.ru/issues/354), [search on 404](https://github.com/Simbiat/simbiat.ru/issues/307)

---

### 1.11 · PHPUnit Test Coverage 🔗 DEP: 1.1 🔴 L
**Issue:** [phpunit](https://github.com/Simbiat/simbiat.ru/issues/236)

Add PHPUnit during migration, starting with: Talks entity classes (once Phase 2 defines them), authentication flow, core utility classes. Add InventoryService tests when Phase 6 is implemented.

---

### 1.12 · Public Roadmap + Patreon Soft-Launch + Intentionality Pitch 🆕 💰 🔗 DEP: 0.6 (roadmap library) 🟡 S
**Issues:** [pages for timeline and roadmap](https://github.com/Simbiat/private-issues/issues/46) 🔒, [intentionality](https://github.com/Simbiat/private-issues/issues/61) 🔒, [public monetization goal(s)](https://github.com/Simbiat/private-issues/issues/66) 🔒

Patreon stub + public roadmap

**Also:** [GitHub to Changelog](https://github.com/Simbiat/simbiat.ru/issues/253), [Changelog](https://github.com/Simbiat/talks/issues/12) — link git commits to the changelog section.

---

## Phase 2 — Talks Architecture Refactor

*Symfony is now in place. All Talks pages and admin forms will use it. The architecture decision precedes all forum work.*

---

### 2.1 · Architecture Decision: Thread Subclass Pattern ⚠️ 🟢 XS
**Issue:** [threads into subclasses](https://github.com/Simbiat/talks/issues/21)

Write an Architecture Decision Record before coding. Pattern: shared base tables (`talks__sections`, `talks__threads`, `talks__posts`) plus 1:1 type-specific extension tables (`talks__forum__threads`, `talks__ticket__threads`, `talks__blog__threads`, `talks__changelog__threads`, `talks__kb__threads`). PHP class hierarchy mirrors DB. Type-dispatch on load.

---

### 2.2 · DDL: Soft Delete + States + Count Columns ✂️ ⚠️ 🔗 DEP: 0.4, 2.1 🟡 S 🔧
**Issues:** [soft delete for talks](https://github.com/Simbiat/talks/issues/10), [states improvements](https://github.com/Simbiat/talks/issues/19), [threads/posts counts](https://github.com/Simbiat/simbiat.ru/issues/306)

---

### 2.3 · DDL: Type-Specific Extension Tables ⚠️ 🔗 DEP: 2.1, 2.2 🟡 S 🔧
**Issue:** [threads into subclasses](https://github.com/Simbiat/talks/issues/21)

---

### 2.4 · PHP Class Hierarchy for Talks ✂️ ⚠️ 🔗 DEP: 2.3 ⬛ XL
**Issues:** [threads into subclasses](https://github.com/Simbiat/talks/issues/21), [Changes to Talks types](https://github.com/Simbiat/talks/issues/11)

---

### 2.7 · Entities Decoupling (Entity Interface) 🔗 DEP: 2.4 🟠 M
**Issue:** [entities decoupling](https://github.com/Simbiat/simbiat.ru/issues/363)

PHP Entity interface (`setId`, `setAttribute`, `select`, `check`, `add`, `delete`, `update`). Start with Talks classes.

---

### 2.8 · PHP-Level Type Enums 🔗 DEP: 2.4 🟡 S 🔧
**Issues:** [types to PHP](https://github.com/Simbiat/simbiat.ru/issues/361), [constant attributes to constants](https://github.com/Simbiat/simbiat.ru/issues/362)

Move static types from DB to PHP Enums with metadata. Start with `talks__types`.

---

### 2.9 · PII Separation in User Tables 🆕 🔗 DEP: 0.4 🟠 M
**Issue:** [separate sensitive data](https://github.com/Simbiat/simbiat.ru/issues/172)

Move PI/PII from `uc__users` into satellite tables so a breach of the main user table does not compile a full profile. Emails already separate. Extend to other fields (first/last name, birthday, etc.). New tables join on `user_id`; orphan cleanup via cron.

---

### 2.9 · Additional Talks Library Features ✂️ 🔗 DEP: 2.4

| Issue | URL | Note |
|-------|-----|------|
| [colored names](https://github.com/Simbiat/talks/issues/1) | talks | Group-based colored usernames 🟡 S |
| [change post time](https://github.com/Simbiat/talks/issues/3) | talks | Admin: adjust post timestamp 🟢 XS 🔧 |
| [thread time change](https://github.com/Simbiat/talks/issues/4) | talks | Admin: adjust thread timestamp 🟢 XS 🔧 |
| [copying banner from thread to thread](https://github.com/Simbiat/talks/issues/5) | talks | During create/edit 🟢 XS |
| [tag cloud](https://github.com/Simbiat/talks/issues/8) | talks | Tag visualization 🟡 S |
| [reply to](https://github.com/Simbiat/talks/issues/7) | talks | Threaded replies 🟠 M |
| [comment resolution](https://github.com/Simbiat/talks/issues/17) | talks | Ticket + KB types 🟡 S |
| ["wall" feed for blog](https://github.com/Simbiat/talks/issues/14) | talks | Blog section feed 🟡 S |
| [visible thread history](https://github.com/Simbiat/talks/issues/22) | talks | Opt-in public post history 🟡 S |
| [sorting arrows](https://github.com/Simbiat/talks/issues/15) | talks | Thread list sort UI 🟡 S |
| [backposting](https://github.com/Simbiat/talks/issues/18) | talks | Post with historical timestamp — needed for Phase 4.12 content import 🟡 S |
| [entity ID change](https://github.com/Simbiat/talks/issues/26) | talks | ID migration logic 🟠 M |
| [og images for sections](https://github.com/Simbiat/talks/issues/16) | talks | OG image — base Section class 🟡 S 🟢 XS |
| [Changelog](https://github.com/Simbiat/talks/issues/12) | talks | Changelog section type 🟡 S |

---

## Phase 3 — Forum Admin, Moderation & Feature Completion

*Talks architecture and Symfony are in place. Now build the moderation and admin tools that block public launch.*

---

### 3.1 · Admin UI: User Groups & Permissions ⚠️ 🔗 DEP: 2.5, 1.7 🔴 L
**Issue:** [page to edit user's permissions](https://github.com/Simbiat/simbiat.ru/issues/128)

Built with Symfony Forms. Assign users to groups; per-user overrides; group-level permissions. Schema exists.

---

### 3.2 · Admin UI: User Management ⚠️ 🔗 DEP: 3.1 🟠 M
**Issues:** [tab to edit users](https://github.com/Simbiat/simbiat.ru/issues/127), [prohibit modification of system users](https://github.com/Simbiat/simbiat.ru/issues/114)

---

### 3.3 · User Banning ✂️ ⚠️ 🔗 DEP: 3.2 🟠 M
**Issues:** [ban name/email when banning user](https://github.com/Simbiat/simbiat.ru/issues/75), [UI to ban/unban IPs](https://github.com/Simbiat/simbiat.ru/issues/69), [check for banned ip return details](https://github.com/Simbiat/simbiat.ru/issues/175), [ban for accessing pages](https://github.com/Simbiat/simbiat.ru/issues/319)

Cascade ban to `uc__bad_names`/`uc__bad_mails`. Rename to `User #ID`. Ban reason on profile. Reverse on unban. IP ban UI (also removes from SEO tables). Return ban reason details in IP check response. Admin UI to extend CrowdSec rules for page-level bans (`.env`, `.htaccess`, etc.) — potentially replaces `ban__ips` table.

---

### 3.4 · Soft Delete Admin UI + Cron ✂️ ⚠️ 🔗 DEP: 2.2 🟡 S 🔧
**Issue:** [soft delete for talks](https://github.com/Simbiat/talks/issues/10) — admin side

View/restore/hard-delete deleted entities. Cron hard-deletes after 30 days.

**Also:** [potential post history improvements](https://github.com/Simbiat/simbiat.ru/issues/328), [flag for public post history](https://github.com/Simbiat/talks/issues/6) — store only changed text; purge after configurable period; selective admin removal; opt-in public history.

---

### 3.5 · Ticketing System (Full) 🔗 DEP: 2.3, 2.4 🔴 L
**Issues:** [ticketing system](https://github.com/Simbiat/simbiat.ru/issues/21), [link support tickets](https://github.com/Simbiat/simbiat.ru/issues/357), [multi-page support threads](https://github.com/Simbiat/talks/issues/20)

Full ticketing UI on `Talks\Ticket\Thread`: statuses, priority, linking, optional public disclosure, pagination.

---
### 3.6 · Reporting System ✂️ ⚠️ 🔗 DEP: 3.4, 3.5 🟠 M
**Issues:** [Reporting system](https://github.com/Simbiat/simbiat.ru/issues/138), [StopForumSpam](https://github.com/Simbiat/simbiat.ru/issues/313)

Report button per entity → creates support ticket. Categories: spam, violence, NSFW, spoiler. Admin queue. StopForumSpam API check on ban; submit confirmed spammers.

---

### 3.7 · Forum Feature Bundle ✂️ 🔗 DEP: 2.4, 3.1

| Issue | URL | Note |
|-------|-----|------|
| [hiding NSFW and spoiler globally](https://github.com/Simbiat/simbiat.ru/issues/44) | simbiat.ru | Global user preference 🟡 S |
| [Buttons for spoiler and NSFW](https://github.com/Simbiat/simbiat.ru/issues/125) | simbiat.ru | TinyMCE buttons 🟢 XS 🔧 |
| [reactions redesign](https://github.com/Simbiat/simbiat.ru/issues/140) | simbiat.ru | Likes → reactions; notifications per post 🟠 M |
| [Entity tagging](https://github.com/Simbiat/simbiat.ru/issues/191) | simbiat.ru | @mention users; # tag entities 🟠 M |
| [embed youtube](https://github.com/Simbiat/talks/issues/13) | talks | YouTube embed in TinyMCE 🟡 S |
| [TinyMCE URL insert](https://github.com/Simbiat/talks/issues/25) | talks | URL insert helper 🟢 XS |
| [style articles and section in TinyMCE](https://github.com/Simbiat/simbiat.ru/issues/170) | simbiat.ru | Article/section styling 🟡 S |
| [TinyMCE context menu on mobile](https://github.com/Simbiat/simbiat.ru/issues/121) | simbiat.ru | Mobile UX fix 🟡 S |
| [Way to update images in posts](https://github.com/Simbiat/simbiat.ru/issues/57) | simbiat.ru | Image update flow 🟡 S |

---

### 3.8 · Subscriptions & Notifications ✂️ 🔗 DEP: 3.5, 1.6 🟠 M
**Issues:** [subscriptions](https://github.com/Simbiat/simbiat.ru/issues/131), [atom feeds for subforums and threads](https://github.com/Simbiat/simbiat.ru/issues/64), [atom feed for main page](https://github.com/Simbiat/simbiat.ru/issues/167)

Subscribe/unsubscribe UI. Notification generation. Force-subscribe to own tickets. Atom feeds for sections, threads, main page. Notification management UI (view, mark-read, email preferences). Failure-to-write handled via Symfony Messenger (available from Phase 1).

**Snackbar (separate from sys__notifications):** [notifications countdown](https://github.com/Simbiat/simbiat.ru/issues/47) — JS dismiss timer counts only while page is active (Page Visibility API); this is the snackbar UI component only.

---

### 3.9 · Knowledge Base Section ✂️ 🔗 DEP: 2.3, 2.4 🟠 M
**Issues:** [permission to create KBs in owned sections](https://github.com/Simbiat/talks/issues/9), [Changes to Talks types](https://github.com/Simbiat/talks/issues/11)

Multi-level hierarchy, variable sort, section-owner KB creation permission. [Basic help articles in knowledgebase](https://github.com/Simbiat/private-issues/issues/4) 🔒 — write after KB is live.

---

### 3.10 · Cron & Admin Tooling ✂️ 🔗 DEP: 1.3 🟡 S
**Issues:** [Cron job management](https://github.com/Simbiat/simbiat.ru/issues/70), [Manual Cron trigger](https://github.com/Simbiat/simbiat.ru/issues/67), [Admin links in admin panel](https://github.com/Simbiat/simbiat.ru/issues/68), [maintenance toggle](https://github.com/Simbiat/simbiat.ru/issues/71), [maintenance instead of redirect](https://github.com/Simbiat/simbiat.ru/issues/6), [registered/active user count](https://github.com/Simbiat/simbiat.ru/issues/66), [alert for too many cron tasks](https://github.com/Simbiat/simbiat.ru/issues/371)

Admin: view/trigger/enable/disable cron jobs. Maintenance toggle. Webmaster links. **Public-facing** active user count. Alert at ~10k cron tasks.

Per-issue sizes: [Cron job management](https://github.com/Simbiat/simbiat.ru/issues/70) 🟠 M · [Manual Cron trigger](https://github.com/Simbiat/simbiat.ru/issues/67) 🟡 S · [Admin links](https://github.com/Simbiat/simbiat.ru/issues/68) 🟢 XS 🔧 · [maintenance toggle](https://github.com/Simbiat/simbiat.ru/issues/71) 🟡 S · [maintenance instead of redirect](https://github.com/Simbiat/simbiat.ru/issues/6) 🟢 XS 🔧 · [registered/active user count](https://github.com/Simbiat/simbiat.ru/issues/66) 🟡 S · [alert for too many cron tasks](https://github.com/Simbiat/simbiat.ru/issues/371) 🟢 XS 🔧

---


## Phase 4 — Pre-Registration Hardening, MVP Builds, Patron Early Access & Open Registration

### 4.1 · File Attachment & Upload Hardening ✂️ ⚠️ 🔗 DEP: 1.5 🟠 M
**Issues:** [attach files to posts](https://github.com/Simbiat/talks/issues/2), [filenames sanitization](https://github.com/Simbiat/simbiat.ru/issues/32), [handle single quotes in filenames](https://github.com/Simbiat/simbiat.ru/issues/266), [TinyMCE images alt text](https://github.com/Simbiat/simbiat.ru/issues/33), [Merge upload folder](https://github.com/Simbiat/simbiat.ru/issues/130), [use TinyPNG for image processing](https://github.com/Simbiat/simbiat.ru/issues/332), [avif](https://github.com/Simbiat/simbiat.ru/issues/238), [temporary image upload](https://github.com/Simbiat/private-issues/issues/50) 🔒

Per-issue sizes: [attach files to posts](https://github.com/Simbiat/talks/issues/2) 🟠 M · [filenames sanitization](https://github.com/Simbiat/simbiat.ru/issues/32) 🟡 S 🔧 · [handle single quotes](https://github.com/Simbiat/simbiat.ru/issues/266) 🟢 XS 🔧 · [TinyMCE images alt text](https://github.com/Simbiat/simbiat.ru/issues/33) 🟢 XS · [Merge upload folder](https://github.com/Simbiat/simbiat.ru/issues/130) 🟡 S 🔧 · [TinyPNG processing](https://github.com/Simbiat/simbiat.ru/issues/332) 🟡 S · [avif](https://github.com/Simbiat/simbiat.ru/issues/238) 🟢 XS 🔧 · [temporary image upload](https://github.com/Simbiat/private-issues/issues/50) 🟡 S · [files meta to DB](https://github.com/Simbiat/simbiat.ru/issues/246) 🟡 S

Filename sanitization. TinyPNG integration (avif → webp fallback, silent failure). Temporary image upload for drafts.

**Also:** [files' meta to database](https://github.com/Simbiat/simbiat.ru/issues/246) — add cron job to scan static image library folders and populate DB, replacing per-request filesystem scans; simplifies image sitemap generation.

**💬 CONSIDER:**
- [uploaded files through PHP](https://github.com/Simbiat/simbiat.ru/issues/90) — streaming uploads via PHP for integrity validation; unclear security benefit; evaluate if actually needed
- [X-Sendfile](https://github.com/Simbiat/simbiat.ru/issues/364) — send processed files via X-Sendfile header; related to uploaded-files-through-PHP; evaluate together
- [image edit before upload](https://github.com/Simbiat/simbiat.ru/issues/254) — client-side image editing; unclear demand; also a potential separate monetizable service; defer

---

### 4.2 · Link Sanitization ⚠️ 🟡 S 🔧
**Issue:** [sanitize links in posts](https://github.com/Simbiat/simbiat.ru/issues/305)

Sanitize outbound links in post HTML. Block forbidden GET parameters; forbid user@pass in URLs. Potentially contribute to Symfony's sanitizer.

---

### 4.3 · Track Links Per Post ⚠️ 🟡 S 🔧
**Issues:** [track links per post](https://github.com/Simbiat/talks/issues/24), [cron to check alt links](https://github.com/Simbiat/simbiat.ru/issues/96)

Store outbound links per post. Enables moderation, spam detection, and dead-link cleanup via cron.

---

### 4.4 · Password Reset & Account Security ⚠️ 🔗 DEP: 1.7 🟡 S
**Issues:** [Password reset function](https://github.com/Simbiat/simbiat.ru/issues/41), [JS password check](https://github.com/Simbiat/simbiat.ru/issues/73), [Password date in user table](https://github.com/Simbiat/simbiat.ru/issues/326)

Complete forgot-password flow. Fix JS validation console error. Add `password_changed` column.

---

### 4.5 · Registration: ToS/PP Acceptance ⚠️ 🟢 XS 🔧
**Issue:** [confirming ToS and PP](https://github.com/Simbiat/simbiat.ru/issues/56)

Checkbox + server-side validation. GDPR consent record.

---

### 4.6 · Anti-Phishing in Emails 🟢 XS 🔧
**Issue:** [anti-phishing](https://github.com/Simbiat/simbiat.ru/issues/370)

Add notification ID to email footers. Show in user UI for validation.

---

### 4.7 · API Rate Limiter ⚠️ 🟠 M
**Issue:** [API Rate limiter](https://github.com/Simbiat/simbiat.ru/issues/43)

Rate limiting by IP and API key. Higher limits for authenticated users.

---

### 4.8a · MVP Inventory System 🆕 💰 ⚠️ 🔗 DEP: 4.1–4.7 🟠 M
**Issue:** [monetization system](https://github.com/Simbiat/private-issues/issues/47) 🔒

---

### 4.8b · MVP MediaTracker 🆕 💰 🔗 DEP: 4.8a 🔴 L
**Issues:** [mediatracker](https://github.com/Simbiat/private-issues/issues/25) 🔒 — Phase 7.1 + 7.2 minimum

---

### 4.8c · **PATRON EARLY ACCESS** 🎯 💰 🔗 DEP: 4.8a, 4.8b 🟡 S

---

### 4.9 · UI Polish Bundle (Pre-Registration) ✂️ 🔴 L

| Issue | URL | Note |
|-------|-----|------|
| [columns to center](https://github.com/Simbiat/simbiat.ru/issues/93) | simbiat.ru | Layout fix 🟢 XS 🔧 |
| [sidebar and footer](https://github.com/Simbiat/simbiat.ru/issues/85) | simbiat.ru | Visual consistency 🟡 S |
| [trackers section](https://github.com/Simbiat/simbiat.ru/issues/88) | simbiat.ru | Move trackers to /trackers 🟢 XS 🔧 |
| [tox-statusbar height](https://github.com/Simbiat/simbiat.ru/issues/62) | simbiat.ru | UI size fix 🟢 XS 🔧 |
| [Notifications are behind dialog](https://github.com/Simbiat/simbiat.ru/issues/52) | simbiat.ru | z-index bug 🟢 XS |
| [tooltips and dialogs](https://github.com/Simbiat/simbiat.ru/issues/369) | simbiat.ru | Tooltips visible inside dialogs 🟢 XS |
| [sticky tab-names](https://github.com/Simbiat/simbiat.ru/issues/100) | simbiat.ru | Sticky on scroll 🟢 XS 🔧 |
| [tables are too small](https://github.com/Simbiat/simbiat.ru/issues/144) | simbiat.ru | Layout 🟢 XS 🔧 |
| [Small menu button](https://github.com/Simbiat/simbiat.ru/issues/145) | simbiat.ru | Touch target 🟢 XS 🔧 |
| [Gallery arrows too wide](https://github.com/Simbiat/simbiat.ru/issues/147) | simbiat.ru | UI fix 🟢 XS 🔧 |
| [Image gallery improvements](https://github.com/Simbiat/simbiat.ru/issues/187) | simbiat.ru | Gallery UX 🟡 S |
| [Check for capslock](https://github.com/Simbiat/simbiat.ru/issues/189) | simbiat.ru | Password field 🟢 XS |
| [Dynamic hamburger placement](https://github.com/Simbiat/simbiat.ru/issues/190) | simbiat.ru | Mobile nav 🟡 S |
| [dialog elements with tabindex](https://github.com/Simbiat/simbiat.ru/issues/193) | simbiat.ru | Accessibility 🟢 XS 🔧 |
| [Open sidepanel with swipe](https://github.com/Simbiat/simbiat.ru/issues/194) | simbiat.ru | Mobile UX 🟡 S |
| [cursor: wait](https://github.com/Simbiat/simbiat.ru/issues/200) | simbiat.ru | Loading indicator 🟢 XS 🔧 |
| [more aria roles](https://github.com/Simbiat/simbiat.ru/issues/251) | simbiat.ru | Accessibility 🟡 S |
| [input to button](https://github.com/Simbiat/simbiat.ru/issues/377) | simbiat.ru | Semantic HTML 🟡 S 🔧 |
| [page number for breadcrumbs](https://github.com/Simbiat/simbiat.ru/issues/301) | simbiat.ru | Navigation 🟡 S |
| [animation for hiding elements](https://github.com/Simbiat/simbiat.ru/issues/198) | simbiat.ru | UX polish 🟢 XS |
| [Zoom for tables](https://github.com/Simbiat/simbiat.ru/issues/199) | simbiat.ru | Mobile 🟡 S |
| [table sorting JS](https://github.com/Simbiat/simbiat.ru/issues/374) | simbiat.ru | Client-side sort 🟡 S |
| [highlight table rows](https://github.com/Simbiat/simbiat.ru/issues/376) | simbiat.ru | UX hover 🟢 XS 🔧 |
| [og:description loses spaces around links](https://github.com/Simbiat/simbiat.ru/issues/333) | simbiat.ru | SEO bug 🟢 XS |
| [force refresh page](https://github.com/Simbiat/simbiat.ru/issues/185) | simbiat.ru | Pull-to-refresh 🟡 S |
| [removed characters get doubled](https://github.com/Simbiat/simbiat.ru/issues/91) | simbiat.ru | UX: typed chars duplicating in inputs 🟡 S |
| [browser cache is used for updated pages](https://github.com/Simbiat/simbiat.ru/issues/29) | simbiat.ru | New post loads stale cached page — 🔗 DEP: 0.3 (HTMLCache) 🟡 S |
| [popover](https://github.com/Simbiat/simbiat.ru/issues/334) | simbiat.ru | Evaluate HTML `popover` attribute for tabs/dialogs 🟡 S |
| [ToC generation](https://github.com/Simbiat/simbiat.ru/issues/196) | simbiat.ru | JS-inject table of contents on long pages; threshold to be determined 🟡 S |

---

### 4.10 · SEO, Infrastructure & Misc Pre-Registration ✂️

| Issue | URL | Note |
|-------|-----|------|
| [meta keywords](https://github.com/Simbiat/simbiat.ru/issues/109) | simbiat.ru | Meta keywords tag 🟢 XS 🔧 |
| [feed country flags based on name](https://github.com/Simbiat/simbiat.ru/issues/173) | simbiat.ru | Flag API/service 🟡 S |
| [content discoverability](https://github.com/Simbiat/private-issues/issues/42) 🔒 | private-issues | Tag/language-based suggestions 🟡 S |
| [SEO Stats update](https://github.com/Simbiat/simbiat.ru/issues/142) | simbiat.ru | SEO stats refresh logic 🟡 S |
| [user agents in DB](https://github.com/Simbiat/simbiat.ru/issues/243) | simbiat.ru | Separate table for user agents 🟡 S |
| [Multiple-value attributes to separate tables](https://github.com/Simbiat/simbiat.ru/issues/60) | simbiat.ru | Normalize multi-value attrs (old/alt names, emails) 🟠 M |
| [webmention](https://github.com/Simbiat/simbiat.ru/issues/252) | simbiat.ru | Webmention support 🟠 M |
| [initial DB setup for mariadb container](https://github.com/Simbiat/simbiat.ru/issues/325) | simbiat.ru | DevOps: container init script 🟡 S 🔧 |

---

### 4.11 · API Knowledgebase ⚠️ 🟡 S
**Issue:** [API Knowledgebase](https://github.com/Simbiat/simbiat.ru/issues/83)

---

### 4.12 · OWASP Top 10 Audit ⚠️ 🔗 DEP: all above 🟠 M
**Issues:** [OWASP Top 10](https://github.com/Simbiat/simbiat.ru/issues/135), [DAST](https://github.com/Simbiat/simbiat.ru/issues/119), [openappsec integration](https://github.com/Simbiat/simbiat.ru/issues/298)

---

### 4.13 · Final ToS/Policy Review + GDPR Form ⚠️ 🟢 XS 🔧
**Issues:** [ToS and Policies](https://github.com/Simbiat/simbiat.ru/issues/137) — final pass, [GDPR](https://github.com/Simbiat/private-issues/issues/63) 🔒

---

### 4.14 · Content Backposting & ID Cleanup ✂️ ⚠️ 🔗 DEP: 2.9 (backposting), 4.8b (MVP MediaTracker) 🔴 L
**Issues:** [facebook and vk posts](https://github.com/Simbiat/private-issues/issues/64) 🔒, [old game ideas](https://github.com/Simbiat/private-issues/issues/16) 🔒

1. Backpost

2. Migrate reviews

3. Renumber IDs

---

### 4.15 · **OPEN REGISTRATION** 🎯 🟢 XS 🔧

---

## Phase 5 — Monetization Infrastructure (Full)

### 5.1 · Inventory / Monetization Core (Full Build) 💰 ⚠️ 🔗 DEP: 4.8a 🟠 M
**Issue:** [monetization system](https://github.com/Simbiat/private-issues/issues/47) 🔒

---

### 5.2 · Patreon Integration ✂️ 💰 🔗 DEP: 5.1 🟠 M

**Issue:** [donations](https://github.com/Simbiat/private-issues/issues/54) 🔒

---

### 5.3 · Subscriber Benefits Page 💰 🔗 DEP: 5.1 🟡 S

**Issue:** [subscriber page](https://github.com/Simbiat/private-issues/issues/62) 🔒

---

### 5.5 · Scheduled Publishing for Subscribers 💰 🔗 DEP: 5.1 🟡 S

**Issue:** [scheduled publishing to subscriber](https://github.com/Simbiat/simbiat.ru/issues/3)

---

## Phase 6 — MediaTracker (Full Build)

### 6.1 · MediaTracker DB Schema 🔗 DEP: 4.8b 🟡 S 🔧

**Issue:** [mediatracker](https://github.com/Simbiat/private-issues/issues/25) 🔒

---

### 6.2 · MediaTracker Core Library (Complete) 🔗 DEP: 6.1 🟠 M

Complete remaining operations: private library gate, full change-log, re-watch reset, NSFW gate via inventory.

---

### 6.3 · MediaTracker Submission & Moderation 🔗 DEP: 6.2, 3.1 🟠 M

Submission queue, moderator action log.

---

### 6.4 · MediaTracker: Comments & Reactions via Talks 🔗 DEP: 6.2, 2.4 🟡 S

**Issues:** [Comment section utilizing forum](https://github.com/Simbiat/FFXIV-Tracker/issues/23), [reaction/stickers for media characters](https://github.com/Simbiat/private-issues/issues/60) 🔒

---

### 6.5 · MediaTracker UI & Pages 🆕 🔗 DEP: 6.2 🔴 L
Catalog browse, individual media page, user tracking, lists, activity feed. Spoiler suppression for unseen episodes/chapters.

---

## Phase 6a — FFXIV Tracker Improvements

*Work on these opportunistically after registration opens, while monitoring MediaTracker feedback — before social media integration (Phase 7).*

---

### 6a.1 · FFXIV Tracker: DB Refactoring ✂️ 🔗 DEP: 0.4 🔴 L
**Issues:** [Splitting character table](https://github.com/Simbiat/FFXIV-Tracker/issues/1), [freecompanyid to BIGINT](https://github.com/Simbiat/FFXIV-Tracker/issues/8), [Separate crossworld linkshells](https://github.com/Simbiat/FFXIV-Tracker/issues/9), [move images to library](https://github.com/Simbiat/FFXIV-Tracker/issues/14), [multiple groups in "current" status](https://github.com/Simbiat/FFXIV-Tracker/issues/12)

**Also:** [DOM instead of Regex](https://github.com/Simbiat/lodestone-parser/issues/2) — replace Lodestone parser regex with DOM/CSS-to-XPath (e.g. Symfony DomCrawler); do here alongside other FFXIV refactoring.

---

### 6a.2 · FFXIV Tracker: Features & UX ✂️

| Issue | URL | Note |
|-------|-----|------|
| [`updated` field to `checked`](https://github.com/Simbiat/FFXIV-Tracker/issues/6) | FFXIV-Tracker | Rename field 🟢 XS 🔧 |
| [Track results from ranking pages](https://github.com/Simbiat/FFXIV-Tracker/issues/7) | FFXIV-Tracker | Store ranking data 🟠 M |
| [lodestone alt link based on IP](https://github.com/Simbiat/FFXIV-Tracker/issues/13) | FFXIV-Tracker | Regional Lodestone link 🔴 L 🟡 S |
| [Shared name tabs](https://github.com/Simbiat/FFXIV-Tracker/issues/15) | FFXIV-Tracker | Tab for same-name entities 🔴 L 🟡 S |
| [FF friend list](https://github.com/Simbiat/FFXIV-Tracker/issues/16) | FFXIV-Tracker | Friend list, mutual detection 🔴 L 🟠 M |
| [ff-track dynamic tip](https://github.com/Simbiat/FFXIV-Tracker/issues/17) | FFXIV-Tracker | Tooltip for character ID location 🔴 L 🟢 XS |
| [ff entities atom feed](https://github.com/Simbiat/FFXIV-Tracker/issues/18) | FFXIV-Tracker | Atom feeds for entity updates 🔴 L 🟡 S |
| [dynamically add linked character](https://github.com/Simbiat/FFXIV-Tracker/issues/24) | FFXIV-Tracker | Dynamic linking UI 🟡 S |
| [dynamic avatar](https://github.com/Simbiat/FFXIV-Tracker/issues/25) | FFXIV-Tracker | Avatar from linked FF character 🟡 S |
| [search in old names](https://github.com/Simbiat/FFXIV-Tracker/issues/26) | FFXIV-Tracker | Search historical names 🟡 S |
| [FF key regeneration](https://github.com/Simbiat/FFXIV-Tracker/issues/27) | FFXIV-Tracker | Regenerate FF linking token 🟢 XS 🔧 |
| [API Key regeneration](https://github.com/Simbiat/simbiat.ru/issues/80) | simbiat.ru | Regenerate API key 🟢 XS 🔧 |
| [link to json](https://github.com/Simbiat/FFXIV-Tracker/issues/28) | FFXIV-Tracker | JSON representation link 🟢 XS 🔧 |
| [affiliation blocks too small](https://github.com/Simbiat/FFXIV-Tracker/issues/29) | FFXIV-Tracker | UI size fix 🟢 XS 🔧 |
| [avoid duplicate FF avatars](https://github.com/Simbiat/FFXIV-Tracker/issues/30) | FFXIV-Tracker | Deduplication 🟡 S |
| [Entities with same name](https://github.com/Simbiat/simbiat.ru/issues/82) | simbiat.ru | FFXIV-specific: show same-name entities on entity page 🟡 S |
| [Finalize repo as a library](https://github.com/Simbiat/FFXIV-Tracker/issues/33) | FFXIV-Tracker | Library cleanup 🟡 S |

**Lower priority — complex or niche; do after monetization stabilizes:**

| Issue | URL |
|-------|-----|
| [Character cards](https://github.com/Simbiat/FFXIV-Tracker/issues/19) | FFXIV-Tracker 🔴 L 🟠 M |
| [Recruitment logic](https://github.com/Simbiat/FFXIV-Tracker/issues/20) | FFXIV-Tracker 🟠 M |
| [categories for minions and mounts](https://github.com/Simbiat/FFXIV-Tracker/issues/4) | FFXIV-Tracker 🟡 S |
| [List of orchestrion rolls and triple triad cards](https://github.com/Simbiat/FFXIV-Tracker/issues/5) | FFXIV-Tracker 🟡 S |
| [ffxivcollect](https://github.com/Simbiat/FFXIV-Tracker/issues/22) | FFXIV-Tracker 🟠 M |

---

## Phase 6b — BIC Tracker Maintenance

### 6b.1 · BIC Tracker Tasks ✂️

| Issue | URL | Note |
|-------|-----|------|
| [bic errors log](https://github.com/Simbiat/BIC-Tracker/issues/3) | BIC-Tracker | Verify errors are logged 🟢 XS 🔧 |
| [Split BIC to library](https://github.com/Simbiat/BIC-Tracker/issues/4) | BIC-Tracker | Extract library 🟠 M |
| [BIC changes tracking](https://github.com/Simbiat/BIC-Tracker/issues/5) | BIC-Tracker | Track changes in separate table 🟠 M |
| [[php8.4] Use HTMLDocument](https://github.com/Simbiat/BIC-Tracker/issues/6) | BIC-Tracker | PHP 8.4 upgrade 🟢 XS 🔧 |
| [BIC Statistics](https://github.com/Simbiat/BIC-Tracker/issues/1) | BIC-Tracker | Stats page 🟠 M |
| [BIC Atom](https://github.com/Simbiat/simbiat.ru/issues/247) | simbiat.ru | Restore BIC RSS/Atom feeds 🟡 S 🔧 |
| [BIC number to title of BICs](https://github.com/Simbiat/simbiat.ru/issues/162) | simbiat.ru | BIC number in titles/cards 🟢 XS 🔧 |

---

## Phase 7 — Social Media Integration

---

### 7.1 · Post to Social (Core API) ✂️ 💰 🔗 DEP: 5.1 🔴 L
**Issues:** [Post to social core](https://github.com/Simbiat/private-issues/issues/53) 🔴 L · [Facebook permissions](https://github.com/Simbiat/simbiat.ru/issues/202) 🟡 S · [upload video to multiple services](https://github.com/Simbiat/private-issues/issues/24) 🟠 M · [activitypub](https://github.com/Simbiat/simbiat.ru/issues/258) 🔴 L

---

### 7.2 · FF Achievements → Social 🔗 DEP: 7.1 🟡 S
**Issue:** [FF Achievements to social](https://github.com/Simbiat/FFXIV-Tracker/issues/21)

---

## Phase 8 — IfYouAreReadingThis 💰 🔗 DEP: 5.1, 5.5 🔴 L

**Issue:** [IfYouAreReadingThis](https://github.com/Simbiat/private-issues/issues/37) 🔒

---

## Phase 9 — Smaller Tools

### 9.1 · Private Messages 🔒 💰 🔗 DEP: 5.1 🟠 M
**Issue:** [private messages](https://github.com/Simbiat/private-issues/issues/59) 🔒 — DMs as hidden Talks threads

### 9.2 · Personal Notes 🔒 🔗 DEP: 2.4 🟡 S
**Issue:** [notes based on hidden forum per user](https://github.com/Simbiat/private-issues/issues/26) 🔒

### 9.3 · Utility Tools Bundle

| Issue | URL | Note |
|-------|-----|------|
| [unix time converter](https://github.com/Simbiat/simbiat.ru/issues/152) | simbiat.ru | 🟠 M 🟡 S |
| [string comparison](https://github.com/Simbiat/simbiat.ru/issues/218) | simbiat.ru | 🟡 S |
| [image to base64](https://github.com/Simbiat/simbiat.ru/issues/221) | simbiat.ru | 🟢 XS 🔧 |
| [numbers conversion](https://github.com/Simbiat/simbiat.ru/issues/216) | simbiat.ru | 🟡 S |
| [random time and date](https://github.com/Simbiat/simbiat.ru/issues/208) | simbiat.ru | 🟡 S |
| [lucky draw page](https://github.com/Simbiat/simbiat.ru/issues/213) | simbiat.ru | 🟢 XS |
| [Color page](https://github.com/Simbiat/simbiat.ru/issues/150) | simbiat.ru | Color filter, inverter, hex/rgba/CMYK converter, named color list 🟠 M |
| [Letter registry changer page](https://github.com/Simbiat/simbiat.ru/issues/211) | simbiat.ru | 🟡 S |
| [pt to px converter](https://github.com/Simbiat/simbiat.ru/issues/212) | simbiat.ru | 🟢 XS 🔧 |
| [String repeat](https://github.com/Simbiat/simbiat.ru/issues/214) | simbiat.ru | 🟢 XS 🔧 |
| [HTTP20 generators to pages](https://github.com/Simbiat/simbiat.ru/issues/244) | simbiat.ru | Online forms for HTTP20 generators 🟡 S |
| [characters replacements to database](https://github.com/Simbiat/simbiat.ru/issues/151) | simbiat.ru | Potential character-conversion library/service — merges PrettyURL, filename sanitization, text-to-special-chars; possible standalone API 🟠 M |

**JS Games (work opportunistically in slow periods):**

Jiangshi: [secondary windows cover hints](https://github.com/Simbiat/Jiangshi/issues/1), [jumping sprites](https://github.com/Simbiat/Jiangshi/issues/2), [death and transition for civilians](https://github.com/Simbiat/Jiangshi/issues/3), [Better fire](https://github.com/Simbiat/Jiangshi/issues/4), [responsive scaling](https://github.com/Simbiat/Jiangshi/issues/5), [Gamepad support](https://github.com/Simbiat/Jiangshi/issues/6), [highscore board](https://github.com/Simbiat/Jiangshi/issues/7), [update UI elements](https://github.com/Simbiat/Jiangshi/issues/8), [Moving fire](https://github.com/Simbiat/Jiangshi/issues/9), [sound settings](https://github.com/Simbiat/Jiangshi/issues/10)

Dangerous Dave: [adjust scaling](https://github.com/Simbiat/Dangerous-Dave-Endless-Nightmare/issues/1), [add gamepad support](https://github.com/Simbiat/Dangerous-Dave-Endless-Nightmare/issues/2), [do not slow down putin](https://github.com/Simbiat/Dangerous-Dave-Endless-Nightmare/issues/3), [light sources](https://github.com/Simbiat/Dangerous-Dave-Endless-Nightmare/issues/4), [1up shader](https://github.com/Simbiat/Dangerous-Dave-Endless-Nightmare/issues/5), [glitter shader](https://github.com/Simbiat/Dangerous-Dave-Endless-Nightmare/issues/6), [items flash](https://github.com/Simbiat/Dangerous-Dave-Endless-Nightmare/issues/7), [Enemies logic](https://github.com/Simbiat/Dangerous-Dave-Endless-Nightmare/issues/8), [spawning logic](https://github.com/Simbiat/Dangerous-Dave-Endless-Nightmare/issues/9), [platforms and ladders](https://github.com/Simbiat/Dangerous-Dave-Endless-Nightmare/issues/10), [starting menu](https://github.com/Simbiat/Dangerous-Dave-Endless-Nightmare/issues/11), [difficulty escalation](https://github.com/Simbiat/Dangerous-Dave-Endless-Nightmare/issues/12), [achievements](https://github.com/Simbiat/Dangerous-Dave-Endless-Nightmare/issues/13), [game over screen](https://github.com/Simbiat/Dangerous-Dave-Endless-Nightmare/issues/14), [cheats](https://github.com/Simbiat/Dangerous-Dave-Endless-Nightmare/issues/15), [highscore board](https://github.com/Simbiat/Dangerous-Dave-Endless-Nightmare/issues/16), [Music](https://github.com/Simbiat/Dangerous-Dave-Endless-Nightmare/issues/17)

Radical Resonance: [difficulty settings](https://github.com/Simbiat/Radical-Resonance/issues/1), [tutorial](https://github.com/Simbiat/Radical-Resonance/issues/2), [highscore board](https://github.com/Simbiat/Radical-Resonance/issues/3), [closer to Hellsinger](https://github.com/Simbiat/Radical-Resonance/issues/4)
