# Review — Versions & API reality-check (Architecture Spine, IPCN site público)

- **Subject:** `../ARCHITECTURE-SPINE.md` (created 2026-09-24) and its companion `.memlog.md`
- **Method:** Web verification against `developer.wordpress.org`, `wordpress.org`, `make.wordpress.org`, `api.wordpress.org`, `github.com/WordPress/wordpress-develop` (tag `7.1`), plus one non-core industry source clearly labelled as such. Local theme header read from `wp-content/themes/ipcn-fse/style.css`.
- **Date of review:** 2026 (latest WordPress = **7.1.2**).
- **Rule honored:** the spine was **not** edited. This file is advisory only.

## Verdict

**Largely sound, with 2 stale/incorrect claims and 1 incomplete rule.**

Of the six items requested:

| # | Claim in spine | Verdict |
| --- | --- | --- |
| 1 | WP floor `>= 6.4`; "7.1 corrente à data da escrita" | ✅ **Confirmed** |
| 2 | `do_blocks()` / `render_block()` current and behave as described | ✅ **Confirmed** |
| 3 | `/patterns` registered **and PHP-compiled on `init`**; PHP cannot see loop; `Inserter: false` header real | ⚠️ **Partly confirmed** — `Inserter: false` real; "registers on init" real; "compiled on init" is **imprecise for WP ≥ 6.5** (content is lazy-loaded) |
| 4 | `core/query` cannot express `meta_query` / `date_query` | ✅ **Confirmed** (with a caveat about a PHP filter) |
| 5 | Server-registered dynamic block + `render_callback` is current and supported; behaviour in Site Editor without an editor script | ✅ Supported — but the spine's **accepted limitation is out of date**: WP ≥ 7.0 `supports.autoRegister` gives editor presence with no JS |
| 6 | `register_post_meta` gives the editor a date field (AD-10); `Y-m-d` storage | ⚠️ **Incomplete** — `register_post_meta` yields **no editor UI** (no native date picker); `Y-m-d` storage itself is sound |

### Findings by severity

| Sev | Finding | Where in spine |
| --- | --- | --- |
| **High** | The deferred "no editor script → block shows only in a limited way in the Site Editor" limitation is **avoidable on the running version (7.1)** via `'supports' => [ 'autoRegister' => true ]` (added in WP 7.0). | Deferred, item 2 |
| **High** | AD-10 asserts the theme "regista um campo de data … no editor de `post`". `register_post_meta()` **only** exposes the meta to the REST API/Block Bindings; it creates **no field, and WordPress has no native date-picker control**. A JS panel/block (or a legacy meta box) is still required. | AD-10 |
| **Medium** | "os patterns são compilados no `init`" is not literally true on WP ≥ 6.5: only the **metadata/headers** are registered at `init`; the file body is **lazily `include`d on first retrieval**. The *conclusion* (don't rely on the loop) still matches current docs and stays the safe rule. | Paradigm, line 47 |
| **Low** | `core/query` truly has no `meta_query`/`date_query` attributes, but a developer **can** inject them server-side via the `query_loop_block_query_vars` filter (front-end only). The spine picked a dynamic block instead — valid, just not the only option. | AD-9 |
| **Low** | Stack lists **PHP `>= 8.1`**; PHP 8.1 reached **end of life 2025-12-31**. WP 7.1 still runs on it, but recommends PHP 8.3+. | Stack |

---

## 1. WordPress version, floor and "7.1 current"

**Confirmed.**

- Current release channel: **WordPress 7.1.x**, latest point release **7.1.2** — from the official update API: `.../release/wordpress-7.1.2.zip`.
- 7.1 "Mary Lou" was **released 2026-08-19** ("On August 19, 2026, WordPress 7.1 'Mary Lou' was released to the public"; "the second major release of 2026").
- The spine is dated **2026-09-24**, i.e. *after* 7.1 shipped, so **"7.1 corrente à data da escrita" is correct**. No 7.2 exists yet.
- The memlog line ("7.0 Armstrong saiu em maio 2026; a documentação serve o 7.1") is also accurate: 7.0 "Armstrong" released **2026-05-20**; the developer docs render source links against tag `7.1`.
- Floor: the local theme header **does** declare it — `wp-content/themes/ipcn-fse/style.css`: `Requires at least: 6.4`, `Tested up to: 6.6`, `Requires PHP: 8.1`, `Version: 0.2.0`. So ">= 6.4 declarado no tema" is factually right, and the spine's own Deferred note that `Tested up to: 6.6` is stale against 7.x is correct.
- The **6.4 floor is safe for the feature set the spine actually uses** (patterns `/patterns` folder auto-registration, `do_blocks`, `render_block`, `register_block_type`+`render_callback`, `register_post_meta` all predate 6.4). Caveat: it will become wrong if the theme adopts WP 7.0's `autoRegister` (see §5) — that requires 7.0+.

Notes / could drift:
- `PHP >= 8.1`: as of this review PHP 8.1 is EOL (security support ended 2025-12-31); WP 7.1's download page "Recommend[s] PHP 8.3 or greater". Consider bumping the declared PHP floor or at least noting the EOL.

Sources: <https://api.wordpress.org/core/version-check/1.7/>, <https://wordpress.org/documentation/wordpress-version/version-7-1/>, <https://make.wordpress.org/core/7-1/>, <https://wordpress.org/documentation/wordpress-version/version-7-0/>, <https://wordpress.org/download/>, <https://www.php.net/supported-versions.php>.

## 2. `do_blocks()` and `render_block()`

**Confirmed — both current, described correctly.**

- `do_blocks( string $content ): string` — "Parses dynamic blocks out of `post_content` and re-renders them." Source shown against tag **7.1** (`wp-includes/blocks.php#L2572`). Introduced 5.0.0.
- `render_block( array $parsed_block ): string` — "Renders a single block into a HTML string." Source against tag **7.1** (`#L2412`). Introduced 5.0.0. `do_blocks()` is literally implemented by looping `render_block()` over `parse_blocks()` output.
- AD-1's rule ("when PHP must emit markup at render time, go through the block API, or use a dynamic block") describes the supported mechanism accurately. `do_blocks()` is the documented equivalent of `do_shortcode()` and is a valid way to render block markup (including `<!-- wp:pattern {"slug":"…"} /-->`) from PHP.

Caveat (not an error): `do_blocks()` takes **block markup strings**, not a pattern slug. The memlog decision "shortcodes render patterns via `do_blocks()`" is implementable, but the concrete call is `do_blocks( '<!-- wp:pattern {"slug":"ipcn/card"} /-->' )` (or `render_block()` on a `WP_Block`), not `do_blocks( 'ipcn/card' )`.

Sources: <https://developer.wordpress.org/reference/functions/do_blocks/>, <https://developer.wordpress.org/reference/functions/render_block/>.

## 3. `/patterns` folder registration, timing, and the `Inserter` header

**`Inserter: false` — confirmed real.** The current Theme Handbook lists the pattern file headers, including **`Inserter`** ("Whether to show the pattern in the inserter. Defaults to `true`."). The parser (`WP_Theme::get_block_patterns()`) maps `'inserter' => 'Inserter'` and treats any value other than `yes`/`true` as `false`, so `Inserter: false` works as intended (pattern hidden from the inserter, still usable programmatically / via `wp:pattern`). AD-3 is correct.

**"Registered on `init`" — confirmed.** Theme patterns are auto-registered; `register_block_pattern()` is documented to run on the `init` hook, and `_register_theme_block_patterns()` (core, `@since 6.0`) is the auto-registration path for `./patterns/`.

**"Compiled on `init` … PHP inside a pattern does not see the loop" — partly confirmed / imprecise.**

- The currently published Theme Handbook states, verbatim, what the spine assumes: *"Pattern registration happens on the `init` hook. At this point, WordPress compiles the content of the pattern and saves a copy of it as HTML-based block markup. … Your patterns cannot access things like the global query or post. … `is_home()`, `is_single()`, `get_post()`, and others are simply not ready yet."* So the spine is aligned with **documented** behaviour, and the downstream rule (listings go through `core/query`, not pattern PHP) is the documented pattern.
- **However, at the implementation level on WP ≥ 6.5 this is no longer literal.** `_register_theme_block_patterns()` only calls `WP_Theme::get_block_patterns()`, which reads the **file headers** (Title, Slug, Inserter, …) — it does **not** execute the file body. The body is read lazily by `WP_Block_Patterns_Registry::get_content()` (`@since 6.5.0`, `filePath` support added in 6.5.0) via `ob_start(); include …; ob_get_clean();`, on **first retrieval** of the pattern (e.g. the REST request that feeds the inserter, or a `wp:pattern` render). In other words: *metadata at `init`, content lazily.*
- Consequence for the review: "compiled on `init`" is a **stale/oversimplified description** for 7.1, and "cannot see the loop" is best treated as a **constraint you must respect by design**, not an absolute runtime guarantee (in the front-end template path the main query is already set up when the `wp:pattern` block renders, and the content is then cached for the rest of the request). The spine's operational rule survives; only the stated mechanism is wrong. Recommend rephrasing to "patterns are *registered* on `init`; their PHP is evaluated in the pattern registry and must not rely on the main query/post".

Sources: <https://developer.wordpress.org/themes/patterns/registering-patterns/> (file headers incl. `Inserter`), <https://developer.wordpress.org/themes/patterns/using-php-in-patterns/> ("Patterns are registered on init"), <https://developer.wordpress.org/reference/functions/_register_theme_block_patterns/>, <https://developer.wordpress.org/reference/classes/wp_theme/get_block_patterns/>, <https://developer.wordpress.org/reference/classes/wp_block_patterns_registry/register/> (`filePath`, lazy `content`, changelog 6.5.0).

## 4. `core/query` and `meta_query` / `date_query`

**Confirmed.** The `core/query` block's `query` attribute (from `blocks/query/block.json`, tag 7.1) is a fixed object: `perPage, pages, offset, postType, order, orderBy, author, search, exclude, sticky, inherit, taxQuery, parents, format, excludeCurrent`. There is **no `metaQuery` and no `dateQuery`** attribute, and the block editor exposes none. AD-9's premise — "que não tem `meta_query` nem `date_query`" — is correct.

Caveat (alternative exists): the `query_loop_block_query_vars` filter (`@since 6.1`) "…can help, for example, to include … meta queries not directly supported by the core Query Loop Block". So a time-window *front-end* query is technically achievable by filtering the core query with PHP — but the filter does **not** affect the editor preview (the REST-rendered preview ignores it), which is exactly why a self-contained dynamic block is the more coherent choice. The spine's decision stands; it is simply not the only route.

Sources: <https://developer.wordpress.org/block-editor/reference-guides/core-blocks/core-blocks-theme/core-block-query/>, <https://raw.githubusercontent.com/WordPress/wordpress-develop/7.1/src/wp-includes/blocks/query/block.json>, <https://developer.wordpress.org/reference/hooks/query_loop_block_query_vars/>.

## 5. Server-registered dynamic block with `render_callback`; Site Editor without an editor script

**Supported and current — confirmed.** `register_block_type( $name, [ 'render_callback' => … ] )` is the documented, current way to define dynamic/server rendering; a dynamic block's `save` may return `null`, and the front end is produced by the callback. AD-9's mechanism is valid.

**But the spine's deferred limitation is out of date (High).** Without a client-side script the block is *not* known to the editor and the Site Editor shows: *"Your site doesn't include support for the '<name>' block."* That matches the spine's "mostra o bloco de forma limitada" — **for a bare server-only registration**. However, **WordPress 7.0 added the `autoRegister` block support** (`wp-includes/block-supports/auto-register.php`, `@since 7.0.0`): with `'supports' => [ 'autoRegister' => true ]`, a PHP-only block with a `render_callback` "will automatically be registered in the editor and use `ServerSideRender`", gets an editor preview from the block-renderer REST endpoint, and gets auto-generated inspector controls from its `attributes`. Since the site runs **7.1**, adding that one flag removes the need for an editor script for the agenda block — the Deferred item ("reabrir se a contratante precisar de o mover sozinha") can likely be closed now.

Trade-offs of `autoRegister` worth recording (core + a June-2026 industry walkthrough): no in-canvas editing, editor preview served via REST (no access to the edited post's client-side data / `$_GET['post']` workaround needed), and the auto-generated controls cover only string/number/integer/boolean (text/number/checkbox/dropdown) — **no date pickers**. For the agenda block (a query + card list, no per-instance attributes) these limits are largely irrelevant, so `autoRegister` is a good fit.

Sources: <https://developer.wordpress.org/block-editor/getting-started/fundamentals/registration-of-a-block/>, <https://developer.wordpress.org/block-editor/reference-guides/block-api/block-supports/#autoregister>, <https://raw.githubusercontent.com/WordPress/wordpress-develop/7.1/src/wp-includes/block-supports/auto-register.php>, <https://github.com/WordPress/gutenberg/discussions/55884>, <https://woracious.com/%E2%9D%97-your-site-doesnt-include-support-for-the-block-what-it-means-how-to-fix-it/> (non-core), <https://css-tricks.com/wordpress-php-block-registration/> (non-core, WordPress 7.0 walkthrough).

## 6. AD-10 — `register_post_meta` and a date field; `Y-m-d`

**`register_post_meta()` is current and is the right low-level registration call — but it does not give the editor a date field (High).**

- `register_post_meta( $post_type, $meta_key, $args )` registers a meta key (delegating to `register_meta()`), and with `'show_in_rest' => true` the value becomes available over the REST API — which is what the block editor reads/writes. Introduced 4.9.8; current on 7.1.
- What it does **not** do: create any UI. There is **no automatic editor field**. To edit it a user needs one of:
  - a JS UI — a `PluginDocumentSettingPanel` + `useEntityProp` (the pattern in the Block Editor Handbook "Meta Boxes" guide and in the Seravo tutorial), or a custom block that stores meta; or
  - a legacy PHP meta box rendered by the block editor; or
  - the raw **Custom Fields** panel (name/value pairs) — which is exactly the route AD-10 calls "não é o caminho documentado".
- There is **no built-in date picker**. Even the WP 7.0 `autoRegister` auto-controls explicitly do not include date pickers. So AD-10's phrasing — "O tema regista um campo de data para `data_evento` no editor de `post`" — over-reads what `register_post_meta` provides. As written, following AD-10 literally leaves the client with no field. AD-10 needs to name the UI mechanism (JS panel/block, or meta box) in addition to the meta registration.
- Prerequisite to remember: the **post type must support `custom-fields`** for registered meta to work in the block editor; built-in `post` does.

**`Y-m-d` storage — sound (Low).** Storing a date as the ISO-8601 string `YYYY-MM-DD` is the standard, lexicographically-sortable representation: string ordering equals chronological ordering, so `meta_query` comparisons (`>=`, `<=`, `BETWEEN`) and `ORDER BY meta_value` behave correctly, and `'type' => 'DATE'` in `meta_query` parses it cleanly. Two caveats to encode in the stories: (a) **validate/normalise on save** (a malformed string silently breaks comparisons), and (b) keep the REST schema consistent (`type => 'string'`), since a `Y-m-d` value must not be registered as `integer`/`datetime`. The spine's display format `j \d\e M \d\e Y` is unrelated to storage and fine.

Sources: <https://developer.wordpress.org/reference/functions/register_post_meta/>, <https://developer.wordpress.org/block-editor/how-to-guides/metabox/>, <https://seravo.com/en/native-meta-fields-ui-in-the-block-editor/> (non-core), <https://css-tricks.com/wordpress-php-block-registration/> (non-core), <https://developer.wordpress.org/reference/classes/wp_meta_query/>.

---

## Could not confirm / uncertainty

- **Exact execution moment of pattern PHP on a front-end template render.** The current Handbook says "compiled on `init`"; the 7.1 source shows lazy `include` on first registry access. Both are load-bearing for how strongly "PHP cannot see the loop" can be stated. The spine's *rule* is safe either way; the *justification wording* is the only thing in doubt. Not resolvable from docs alone without a runtime test on the staging site (recommended: `var_dump( is_singular() )` inside `patterns/ipcn-card.php` and observe).
- **Whether the agenda block will be authored with `autoRegister`.** Foundational evidence (`autoRegister` @since 7.0.0, doc + source) is solid, but the spine predates the decision; not something the spine currently states, so no contradiction — just a missing opportunity.
- **`_register_theme_block_patterns` hook registration line** appears in `wp-settings.php` (not in `default-filters.php`); the hook itself (`init`) is confirmed indirectly via the docs and the `register()` "registered outside `init`" guard, not by reading the exact `add_action` line. Low risk.

## Things that could be out of date (watch list)

- **`Tested up to: 6.6`** in `style.css` — already flagged in the spine's Deferred; still stale (7.1 is current). No change needed here beyond the note.
- **PHP floor 8.1** — EOL since 2025-12-31; WP 7.1 recommends PHP 8.3+.
- **`supports.autoRegister`** is new (7.0) and evolving; the CSS-Tricks walkthrough warns that PHP-only auto-registered blocks cannot access fresh post data or add in-canvas controls, and that WP 7.1 enforces the iframed editor. Pin behaviour when the block is built.

## Recommendation summary (no spine edits made)

1. Keep the Stack version lines as-is: they are correct for the writing date and the running version. Optionally append a PHP-EOL note.
2. AD-10: add the missing UI mechanism (JS `PluginDocumentSettingPanel`/block, or meta box); `register_post_meta` alone is not a field. Keep `Y-m-d`; add save-time validation.
3. Deferred item 2: on WP 7.1, `'supports' => [ 'autoRegister' => true ]` removes the "no editor script" limitation — revisit before accepting it.
4. Paradigm line 47: soften "compiled on `init`" to "registered on `init`; content evaluated by the pattern registry — do not rely on the main query/post". The rule (listings via `core/query`) is otherwise verified.
5. AD-9/AD-2: optionally note the `query_loop_block_query_vars` filter as the alternative you deliberately declined (front-end-only, editor preview unaffected).

## Sources

Core / official:
- <https://api.wordpress.org/core/version-check/1.7/>
- <https://wordpress.org/documentation/wordpress-version/version-7-1/>
- <https://wordpress.org/documentation/wordpress-version/version-7-0/>
- <https://make.wordpress.org/core/7-1/>
- <https://wordpress.org/download/>
- <https://developer.wordpress.org/reference/functions/do_blocks/>
- <https://developer.wordpress.org/reference/functions/render_block/>
- <https://developer.wordpress.org/reference/functions/register_block_type/>
- <https://developer.wordpress.org/reference/functions/register_post_meta/>
- <https://developer.wordpress.org/reference/functions/register_block_pattern/>
- <https://developer.wordpress.org/reference/functions/_register_theme_block_patterns/>
- <https://developer.wordpress.org/reference/classes/wp_block_patterns_registry/register/>
- <https://developer.wordpress.org/reference/classes/wp_theme/get_block_patterns/>
- <https://developer.wordpress.org/reference/hooks/query_loop_block_query_vars/>
- <https://developer.wordpress.org/reference/classes/wp_meta_query/>
- <https://developer.wordpress.org/themes/patterns/registering-patterns/>
- <https://developer.wordpress.org/themes/patterns/using-php-in-patterns/>
- <https://developer.wordpress.org/block-editor/getting-started/fundamentals/registration-of-a-block/>
- <https://developer.wordpress.org/block-editor/getting-started/fundamentals/static-dynamic-rendering/>
- <https://developer.wordpress.org/block-editor/reference-guides/block-api/block-supports/#autoregister>
- <https://developer.wordpress.org/block-editor/reference-guides/core-blocks/core-blocks-theme/core-block-query/>
- <https://developer.wordpress.org/block-editor/how-to-guides/metabox/>
- <https://raw.githubusercontent.com/WordPress/wordpress-develop/7.1/src/wp-includes/blocks/query/block.json>
- <https://raw.githubusercontent.com/WordPress/wordpress-develop/7.1/src/wp-includes/block-supports/auto-register.php>
- <https://raw.githubusercontent.com/WordPress/wordpress-develop/7.1/src/wp-includes/class-wp-block-patterns-registry.php>
- <https://github.com/WordPress/gutenberg/discussions/55884>
- <https://www.php.net/supported-versions.php>

Non-core (labelled, used only to corroborate editor behaviour):
- <https://css-tricks.com/wordpress-php-block-registration/> (WP 7.0 PHP-only blocks)
- <https://seravo.com/en/native-meta-fields-ui-in-the-block-editor/>
- <https://woracious.com/%E2%9D%97-your-site-doesnt-include-support-for-the-block-what-it-means-how-to-fix-it/>
