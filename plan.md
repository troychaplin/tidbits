# Tidbits: Block Styling System

## Current state (on `change/a11y-iapi`)

- A11y markup is implemented: accordion uses APG disclosure pattern
  (`<h3><button aria-expanded aria-controls>` + `<div inert aria-labelledby>`).
  Stacked/columns keep `<dl>/<dt>/<dd>`. `view.js` store, `wp_unique_id()` IDs,
  `:focus-visible`, `prefers-reduced-motion` — all in place.
- `class-styles.php` exists: a 22-token registry + `get_global_css()` + an
  `enqueue_block_assets` hook. **This is being removed** (see item 3 below).
- `style.scss` — ✅ refactored. Token defaults live in `:where(.tidbits)`.
  Visual properties are at zero specificity via `:where()`. Structural rules
  stay at natural specificity. Heading normalization inside content panels added.
- Parent `block.json` — ✅ `color`, `typography`, `__experimentalBorder` added
  (step 1 manually applied). **Needs revision** — border selector and typography
  default controls require updates per findings below (see item 1 below).
- Morsel `block.json` supports: `html`, `reusable`. **Stays support-less.**

---

## Goal

A three-layer styling cascade, with each layer overriding the previous:

```
token defaults  →  [theme.json + Global Styles]  →  per-block instance
  (zero spec)           (WordPress-generated)           (inline styles)
```

- **Token defaults** — the floor. CSS custom properties on `:where(.tidbits)`
  at zero specificity so any real rule wins without a fight.
- **theme.json + Global Styles** — one merged layer. A theme can set block
  styles in `theme.json`; a site admin can override those in the Site Editor.
  WordPress generates the CSS from this merge automatically.
- **Per-block instance** — inline `style=""` attributes (from block supports)
  and inline CSS custom properties (from custom attributes) both land here,
  overriding the global layer for that one block.

Custom attributes output their values as CSS custom properties inline on the
block wrapper (`style="--tidbits-*: value"`). Because inline styles are at
`1,0,0,0` in the cascade they beat the `:where()` token defaults automatically,
with no specificity tricks needed.

---

## Locked decisions

1. **`color.background` target** → the whole block wrapper (`.tidbits`).
2. **`border` (block support) target** → the whole block wrapper (`.tidbits`).
   This is the outer/box border around the widget. *(Revised from `.tidbits-morsel`.)*
3. **Morsel divider** → custom attributes (`dividerColor`, `dividerWidth`),
   NOT the `__experimentalBorder` block support. Attributes are output as
   `--tidbits-divider-color` and `--tidbits-divider-width` inline on the wrapper.
4. **Token defaults source** → CSS only (`:where()` in `style.scss`).
   `class-styles.php` is removed.
5. **Typography scope:**
   - **Header** (trigger + static term): `fontSize`, `fontFamily`, `fontWeight`.
     `fontFamily` and `fontWeight` shown by default in the Style Book panel
     (surfacing font family fixes the font weight issue — weights only work
     when the selected font has those weights loaded).
   - **Content** (panel inner): `fontSize` only. Content font-size also
     normalizes heading blocks inside the panel (see below). `fontFamily` and
     `fontWeight` are token-only for content — consistent body text is the
     right default.
6. **Morsel** stays support-less. Site Editor shows one clean "Tidbits" entry.
7. **Heading normalization inside content panels** — headings inside
   `.tidbits-morsel__inner` inherit the content font-size token. Zero-specificity
   reset so deliberate theme overrides still win.

---

## Implementation TODOs

### 1. ✅ `src/blocks/tidbits/block.json` — initial pass (manually applied)

Needs the following revisions before verification:

**Move `__experimentalBorder` selector from `.tidbits-morsel` to `.tidbits`:**

```json
"selectors": {
  "root": ".tidbits",
  "color": {
    "text": ".tidbits-morsel__trigger, .tidbits-morsel__term",
    "background": ".tidbits"
  },
  "typography": ".tidbits-morsel__trigger, .tidbits-morsel__term",
  "border": ".tidbits",
  "spacing": {
    "padding": ".tidbits",
    "margin": ".tidbits"
  }
}
```

**Surface font family and weight by default in the Style Book panel:**

```json
"typography": {
  "fontSize": true,
  "fontFamily": true,
  "__experimentalFontWeight": true,
  "__experimentalDefaultControls": {
    "fontSize": true,
    "fontFamily": true,
    "fontWeight": true
  }
}
```

**Add custom attributes** for controls not covered by block supports:

```json
"attributes": {
  "dividerColor": {
    "type": "string",
    "default": ""
  },
  "dividerWidth": {
    "type": "string",
    "default": ""
  },
  "itemPadding": {
    "type": "string",
    "default": ""
  },
  "iconColor": {
    "type": "string",
    "default": ""
  }
}
```

### 2. ✅ `src/blocks/tidbits/style.scss`

Done. Token defaults in `:where(.tidbits)`, visual rules at zero specificity,
heading normalization added.

**Follow-up:** Review `--tidbits-divider-color` and `--tidbits-icon-color` token
defaults. Both are hardcoded (`#e5e5e5`, `#808080`) and theme-blind. Consider
`currentColor` (icon) and `color-mix(in srgb, currentColor 15%, transparent)`
(divider) for theme-aware defaults.

### 3. `src/blocks/tidbits/edit.js` — InspectorControls for custom attributes

Add a dedicated sidebar panel for the controls that block supports can't reach.
Use WP's `PanelColorSettings` for colour pickers (respects theme palette) and
`__experimentalUnitControl` or a `RangeControl` for numeric values.

Suggested panel layout:

**Divider** panel:
- Colour (`dividerColor`) → `PanelColorSettings`
- Width (`dividerWidth`) → `UnitControl` (px, default placeholder "1px")

**Layout** panel (or fold into existing Spacing panel if possible):
- Item padding (`itemPadding`) → `UnitControl` (rem/px, default placeholder "0.75rem")

**Icon** panel (or fold into existing Colour panel):
- Icon colour (`iconColor`) → `PanelColorSettings`

### 4. `src/blocks/tidbits/render.php` — output custom attributes as CSS custom properties

Non-empty attribute values get written into the block wrapper's `style`
attribute alongside any block-support-generated styles:

```php
$custom_props = [];

if ( ! empty( $attributes['dividerColor'] ) ) {
    $custom_props[] = '--tidbits-divider-color: ' . esc_attr( $attributes['dividerColor'] );
}
if ( ! empty( $attributes['dividerWidth'] ) ) {
    $custom_props[] = '--tidbits-divider-width: ' . esc_attr( $attributes['dividerWidth'] );
}
if ( ! empty( $attributes['itemPadding'] ) ) {
    $custom_props[] = '--tidbits-padding-block: ' . esc_attr( $attributes['itemPadding'] );
}
if ( ! empty( $attributes['iconColor'] ) ) {
    $custom_props[] = '--tidbits-icon-color: ' . esc_attr( $attributes['iconColor'] );
}

$wrapper_attributes = get_block_wrapper_attributes( [
    'style' => implode( '; ', $custom_props ),
] );
```

Note: `get_block_wrapper_attributes` merges the `style` string with any
block-support-generated inline styles, so both coexist safely.

### 5. Remove `class-styles.php` and tidy up

- Delete `classes/class-styles.php`.
- Remove `new Tidbits\Styles()` from `plugin.php`.
- Remove the `TIDBITS_VERSION` constant from `plugin.php` if it was only added
  for the styles class (check whether anything else uses it).
- Run `composer dump-autoload` to regenerate the classmap.

### 6. Verify in the browser

**Site Editor → Styles → Blocks → Tidbits** should show:
- Typography: font size, font family, font weight (all visible by default).
- Colour: text colour, background.
- Border: outer box border (colour, width, style) — targets the whole widget.
- Dimensions: padding (outer), margin (already there).

**Inspector sidebar (per-block instance):**
- Divider panel: colour picker, width field.
- Layout panel: item padding field.
- Icon panel: colour picker.

**Cascade verification:**
- [ ] Default (no customisation): accordion header ~1rem, weight 600, body font.
- [ ] Set a Global Style (e.g. font size 1.25rem) → applies site-wide to all
  Tidbits blocks, targets trigger/term, not the wrapper.
- [ ] Add a per-block font size via the inspector → overrides global on that block only.
- [ ] Set divider colour via inspector → overrides token default on that block only.
- [ ] Set icon colour via inspector → overrides token default on that block only.
- [ ] Outer border set in Style Book → wraps the whole widget, not between items.
- [ ] Item padding set via inspector → adjusts spacing on each morsel row.

**Font weight verification:**
- [ ] Select a font family with multiple registered weights (e.g. a Google Font
  loaded by the theme) → weight picker produces visible change.
- [ ] No font family set → only 400/700 reliably render (expected behaviour).

**Content heading normalisation:**
- [ ] `h4` inside a tidbit panel renders at content font size, not theme `h4` size.

**A11y regression (must be intact):**
- [ ] Collapsed panels are not Tab-reachable (`inert`).
- [ ] `aria-expanded` toggles; heading navigation works.
- [ ] Focus-visible ring visible on keyboard focus.
- [ ] Reduced-motion disables transitions.
- [ ] Stacked/columns modes render `<dl>` unchanged.

### 7. `docs/theming.md`

Update to document:
- The three-layer cascade model.
- Block support properties (Site Editor / per-block): typography, colour, border, spacing.
- Custom attribute controls (per-block inspector): divider colour/width, item padding, icon colour.
- Token-only properties (dev/theme layer): animation speed, columns width/gap, line height,
  hover colour, focus-ring colour, trigger gap, icon size, panel inset.
- How to override tokens in a child theme or `theme.json`.
- The heading-normalisation behaviour and how to opt out.

---

## Out of scope (confirmed)

- Per-morsel instance controls (Morsel stays support-less).
- A React settings page or PHP settings UI for styles.
- `flavour` taxonomy surfacing (reserved for a future "all morsels" block).
- Deep-linking / open-on-URL-hash support in the accordion.
