# Accessibility

Tidbits is built with accessibility as a core requirement, not an afterthought. Every display mode uses semantic HTML and appropriate ARIA attributes.

## Semantic HTML

### Definition list structure

All display modes use `<dl>`, `<dt>`, and `<dd>` elements. This is the semantically correct HTML for term/definition pairs and is recognized by screen readers as a description list.

```html
<dl>           <!-- Description list wrapper -->
  <div>        <!-- Grouping div (valid in HTML5 dl) -->
    <dt>...</dt> <!-- Description term -->
    <dd>...</dd> <!-- Description details -->
  </div>
</dl>
```

### Button trigger

The accordion mode uses a native `<button>` element for the toggle trigger, not a `<div>` with `role="button"`. This provides:

- Keyboard focusable by default (no `tabindex` needed)
- Activatable with Enter and Space keys
- Announced as "button" by screen readers
- Proper focus styling from the browser

## ARIA Attributes

### Accordion mode

| Attribute | Element | Value | Purpose |
|-----------|---------|-------|---------|
| `aria-expanded` | `<button>` | `true` / `false` | Indicates whether the controlled content is currently visible |
| `aria-controls` | `<button>` | `morsel-{postId}` | Points to the ID of the content region this button controls |
| `role="region"` | `<dd>` | -- | Marks the content as a landmark region |
| `aria-labelledby` | `<dd>` | `morsel-term-{postId}` | Associates the region with its heading (the button) |
| `aria-hidden` | `<dd>` | `true` / `false` | Hides collapsed content from the accessibility tree |
| `aria-hidden="true"` | `<svg>` | static | Hides the decorative chevron icon from screen readers |

### ID relationships

Each accordion item creates two IDs to establish relationships:

- `morsel-term-{postId}` on the `<button>` (referenced by `aria-labelledby`)
- `morsel-{postId}` on the `<dd>` (referenced by `aria-controls`)

### Stacked and columns modes

These modes use no ARIA attributes because all content is always visible and the HTML structure is self-describing.

## Keyboard Navigation

### Accordion

- **Tab:** Moves focus between accordion triggers (buttons)
- **Enter / Space:** Toggles the focused accordion item open/closed
- Focus remains on the trigger after toggling

### All modes

Standard browser keyboard navigation applies. All interactive elements are natively focusable.

## Animation and Motion

The accordion animation uses CSS `grid-template-rows` transitions. Users who prefer reduced motion can disable it:

```css
@media (prefers-reduced-motion: reduce) {
  .tidbits-morsel--accordion .tidbits-morsel__content,
  .tidbits-morsel__icon {
    transition: none;
  }
}
```

This is not included by default but can be added by the consuming theme. Consider adding it if your site needs to meet WCAG 2.3.3 (Animation from Interactions).

## Content Considerations

- Tidbit titles should be descriptive and unique within a block (they serve as headings for screen reader users)
- Tidbit content should make sense in the context of its title
- Avoid using tidbits for critical content that must be immediately visible -- use stacked or columns mode instead of accordion in those cases
