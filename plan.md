# Tidbits Plan

## Part 1 — Accordion accessibility pass (IMPLEMENTED in this branch)

Status note: this was stashed/reverted twice during iteration, then re-applied.
As of now it IS in the working tree (verified against `morsel/render.php`,
`tidbits/render.php`, both `edit.js`). Not yet committed. Surgical a11y fixes to
the accordion, no feature changes:

- Accordion restructured to the WAI-ARIA APG disclosure pattern: `<div>` wrapper
  (not `<dl>`), each item is `<h3 class="tidbits-morsel__heading"><button></h3>`
  + `<div class="tidbits-morsel__panel">`. Stacked/columns still use `<dl>`.
- `inert` (static + `data-wp-bind--inert`) replaces `aria-hidden` on the panel —
  collapsed content leaves the tab order and the a11y tree.
- `role="region"` dropped (kept `aria-labelledby`).
- `wp_unique_id()` IDs to avoid cross-block duplicate IDs.
- `:focus-visible` ring; default `prefers-reduced-motion` block.
- `view.js` store unchanged (per-morsel `context.isOpen`). Heading level fixed
  at `h3` (deferred limitation).

---

## Part 2 — Configurable styling system (NEXT)

Goal: stop the accordion header (and morsels generally) from inheriting the
theme's heading styles, and give full control through a layered cascade.

### Decisions (locked)

- **Cascade:** `theme < base defaults < global settings < per-block overrides`.
- **Per-block scope:** the FULL token set, grouped into collapsible inspector
  panels.
- **Global settings UI:** a custom React settings panel (same components as the
  block editor), separate from the existing Settings-API form for CPT/taxonomy.

### Core technique — defeat theme inheritance

CSS variables alone don't win specificity battles. A property set *directly on an
element* always beats an *inherited* one, so:

1. **Reset the wrappers** (`.tidbits-morsel__heading`, `.tidbits-morsel__term`)
   to neutral with enough specificity to beat theme heading rules — e.g.
   `.tidbits .tidbits-morsel__heading { font: inherit; margin: 0 }` (`0,2,0`,
   beats a theme's `.entry-content h3` at `0,1,1`). **Verify in S1** against a
   theme that styles content headings.
2. **Apply tokenized properties on the leaf elements** — trigger button, title
   span, term, panel, content, icon, dividers — never relying on inheritance
   from the `<h3>`.
3. **Choose defaults that don't re-leak** — e.g. `term-font-size` defaults to a
   concrete `1rem`, not `inherit` (which would inherit the theme's `h3` size).

### The cascade, concretely (CSS custom properties)

- **Base + global** → one PHP-generated `<style>` scoped to `.tidbits`, holding
  every token: the registry default, replaced by the admin's global value when
  set. Enqueued via `wp_add_inline_style()` on the block style handle, so it only
  loads when the block is present.
- **Per-block** → sparse inline `style="--token: value"` on the block wrapper
  (via `get_block_wrapper_attributes`). Inline wins; only overridden tokens are
  emitted.
- **Consumption** → `style.scss` reads `var(--tidbits-…)` on the leaf elements.

### Single source of truth — PHP token registry

A new `Tidbits\Styles` class exposes `get_tokens()`: an array of token
definitions, each with `key` (CSS var suffix), `label`, `group`, `type`
(`color` | `length` | `number` | `select` | `font-family`), `default`, and
(for selects) `options`. This one registry drives:

- the global `<style>` generation,
- the settings React panel controls,
- the per-block inspector controls,
- sanitization (per `type`).

### Token set (~21)

| Group | Tokens (CSS var → default) |
|---|---|
| **Header typography** | `--tidbits-term-font-family` (inherit, after wrapper reset) · `--tidbits-term-font-size` (1rem) · `--tidbits-term-font-weight` (600) · `--tidbits-term-line-height` (1.4) |
| **Colors & states** | `--tidbits-term-color` (inherit) · `--tidbits-term-color-hover` (inherit) · `--tidbits-header-bg` (transparent) · `--tidbits-panel-bg` (transparent) · `--tidbits-content-color` (inherit) · `--tidbits-focus-color` (currentcolor) |
| **Spacing & dividers** | `--tidbits-padding-block` (0.75rem) · `--tidbits-divider-color` (#e5e5e5) · `--tidbits-divider-width` (1px) · `--tidbits-divider-style` (solid) · `--tidbits-panel-inset` (0) · `--tidbits-columns-term-width` (260px) · `--tidbits-columns-gap` (1.5rem) · `--tidbits-trigger-gap` (0.5rem) |
| **Icon** | `--tidbits-icon-color` (#808080) · `--tidbits-icon-size` (20px) · `--tidbits-accordion-speed` (0.3s) |

(`--tidbits-border-color` is renamed to `--tidbits-divider-color`.)

### Security

All token values are user-supplied and end up in CSS, so they MUST be sanitized
per type before output (both global option save and per-block render):
- `color` → `sanitize_hex_color()` / allow CSS color keywords + `currentcolor`,
  reject anything else.
- `length` / `number` → validate against a strict numeric+unit pattern.
- `select` → must match a registry option.
- `font-family` → strip `;{}()` and `url(`/`@import`/`expression`.
Apply `safecss_filter_attr()` as a backstop on the final declaration string.

### Phasing

- **S1 — Token foundation (no UI).** Add `Styles` registry; refactor
  `style.scss` to consume tokens on leaf elements with wrapper resets; emit the
  global default `<style>`. **Fixes the original complaint on its own.**
- **S2 — Global React settings panel.** New `tidbits_styles` option exposed via
  REST (registered option with schema, or a small controller). Settings page
  gains an "Appearance" tab mounting a React app built from the registry; live
  preview optional. Sanitize on save.
- **S3 — Per-block overrides.** `styles` object attribute on `tidbits/tidbit`;
  grouped collapsible inspector panels with native components
  (`ColorPalette`, `FontSizePicker`, `UnitControl`, `SelectControl`,
  `FontFamilyControl`) writing into `styles`; each control shows the global
  value as its placeholder/fallback (registry + global values localized to the
  editor). Render emits sparse inline vars; editor preview enqueues the same
  global `<style>` so WYSIWYG holds.

### Open implementation notes

- Settings page layout: tabbed single page — keep the existing CPT/taxonomy
  form as the "General" tab (PHP/Settings API), add "Appearance" as the React
  mount. (Alternative: a second submenu. Confirm during S2.)
- Tokens apply across all display modes where relevant (typography/colors/
  dividers are shared; icon/animation are accordion-only).
- Editor preview must apply per-block inline vars to the wrapper and load the
  global `<style>` in the editor canvas.

### Verify (per phase)

- S1: header no longer inherits theme `h3` styling; all leaf styling driven by
  `--tidbits-*`; lint/build clean; a11y from Part 1 intact.
- S2: global values override base defaults site-wide; values sanitized; only set
  tokens emitted.
- S3: per-block overrides beat global; unset controls fall back to global
  (shown as placeholder); editor preview matches front end; sparse output.
