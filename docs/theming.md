# Theming

Tidbits uses CSS custom properties (tokens) for all visual styling. Themes can override any of these values to match their design system.

## CSS Custom Properties

All tokens are defined on the `.tidbits` wrapper element and can be overridden at any level of the CSS cascade.

| Token | Default | Description |
|-------|---------|-------------|
| `--tidbits-border-color` | `#e5e5e5` | Border color between items |
| `--tidbits-padding-block` | `0.75rem` | Vertical padding on each item |
| `--tidbits-term-color` | `inherit` | Text color for the term/title |
| `--tidbits-term-font-size` | `inherit` | Font size for the term/title |
| `--tidbits-term-font-weight` | `600` | Font weight for the term/title |
| `--tidbits-content-font-size` | `inherit` | Font size for the content/definition area |
| `--tidbits-columns-term-width` | `260px` | Fixed width of the term column in columns mode |
| `--tidbits-columns-gap` | `1.5rem` | Gap between term and content in columns mode |
| `--tidbits-accordion-speed` | `0.3s` | Duration of the accordion expand/collapse animation |

## Overriding Tokens in a Theme

### Global override

Apply to all Tidbits blocks site-wide:

```css
.tidbits {
  --tidbits-border-color: #d0d0d0;
  --tidbits-padding-block: 1rem;
  --tidbits-term-font-weight: 700;
  --tidbits-accordion-speed: 0.2s;
}
```

### Per-mode override

Target a specific display mode:

```css
.tidbits--accordion {
  --tidbits-border-color: #ccc;
  --tidbits-padding-block: 1.25rem;
}

.tidbits--columns {
  --tidbits-columns-term-width: 200px;
  --tidbits-columns-gap: 2rem;
}
```

### Using theme.json

If your theme uses `theme.json`, you can target the block with custom CSS in the `styles.blocks` section:

```json
{
  "styles": {
    "blocks": {
      "tidbits/tidbit": {
        "css": ".tidbits { --tidbits-border-color: var(--wp--preset--color--contrast-3); }"
      }
    }
  }
}
```

## CSS Classes Reference

### Parent wrapper

| Class | Description |
|-------|-------------|
| `.tidbits` | Base class on the `<dl>` wrapper |
| `.tidbits--accordion` | Accordion display mode |
| `.tidbits--stacked` | Stacked display mode |
| `.tidbits--columns` | Columns display mode |

### Morsel (individual item)

| Class | Description |
|-------|-------------|
| `.tidbits-morsel` | Base class on each item wrapper |
| `.tidbits-morsel--accordion` | Accordion mode modifier |
| `.tidbits-morsel--stacked` | Stacked mode modifier |
| `.tidbits-morsel--columns` | Columns mode modifier |
| `.tidbits-morsel__term` | The `<dt>` element |
| `.tidbits-morsel__content` | The `<dd>` element |
| `.tidbits-morsel__inner` | Inner wrapper inside `<dd>` (used for accordion overflow) |
| `.tidbits-morsel__trigger` | The `<button>` element in accordion mode |
| `.tidbits-morsel__title` | The title `<span>` inside the trigger |
| `.tidbits-morsel__icon` | The chevron SVG in accordion mode |

### State classes

| Class | Description |
|-------|-------------|
| `.is-open` | Applied to `.tidbits-morsel__content` and `.tidbits-morsel__icon` when an accordion item is expanded |

## Responsive Behavior

### Columns mode

The columns layout collapses to a single-column stack below 600px viewport width:

```css
@media (max-width: 599px) {
  .tidbits-morsel--columns {
    grid-template-columns: 1fr;
    gap: 0.5rem;
  }
}
```

To change the breakpoint, override the rule in your theme stylesheet.

## Accordion Animation

The accordion uses a `grid-template-rows: 0fr / 1fr` technique with a CSS transition. This provides smooth height animation without JavaScript measurement. The `.tidbits-morsel__inner` div has `overflow: hidden` to clip content during the transition.

To disable animation:

```css
.tidbits-morsel--accordion .tidbits-morsel__content {
  transition: none;
}
```
