# Display Modes

The display mode is set on the parent Tidbits block and applies uniformly to all child Morsel blocks. There are three options.

## Accordion

Content is hidden by default. Each item has a clickable trigger that expands and collapses the content with a smooth animation.

**Best for:** FAQs, troubleshooting guides, content where users scan headings before reading.

### Behavior

- Each item starts collapsed
- Clicking the trigger toggles the item open/closed independently
- A chevron icon rotates 90 degrees when open
- The expand/collapse is animated using CSS `grid-template-rows`
- Multiple items can be open simultaneously

### Frontend Markup

```html
<dl class="tidbits tidbits--accordion" data-wp-interactive="tidbits">
  <div class="tidbits-morsel tidbits-morsel--accordion"
       data-wp-context='{"isOpen":false}'>
    <dt class="tidbits-morsel__term">
      <button type="button"
              class="tidbits-morsel__trigger"
              data-wp-on--click="actions.toggle"
              data-wp-bind--aria-expanded="context.isOpen"
              aria-controls="morsel-123">
        <span class="tidbits-morsel__title">Question text</span>
        <svg class="tidbits-morsel__icon"
             data-wp-class--is-open="context.isOpen">...</svg>
      </button>
    </dt>
    <dd class="tidbits-morsel__content"
        id="morsel-123"
        role="region"
        data-wp-class--is-open="context.isOpen"
        data-wp-bind--aria-hidden="!context.isOpen">
      <div class="tidbits-morsel__inner">
        <p>Answer content</p>
      </div>
    </dd>
  </div>
</dl>
```

---

## Stacked

A simple vertical definition list. Both the term and content are always visible.

**Best for:** Glossary terms, definitions, short reference content, simple key-value lists.

### Behavior

- All content is visible on page load
- No interactivity or animation
- Items are separated by a bottom border

### Frontend Markup

```html
<dl class="tidbits tidbits--stacked">
  <div class="tidbits-morsel tidbits-morsel--stacked">
    <dt class="tidbits-morsel__term">Term text</dt>
    <dd class="tidbits-morsel__content">
      <p>Definition content</p>
    </dd>
  </div>
</dl>
```

---

## Columns

A two-column grid layout with the term on the left and content on the right. Collapses to a single column on small screens.

**Best for:** Metadata, specifications, contact details, key-value content that benefits from horizontal alignment.

### Behavior

- All content is visible on page load
- No interactivity or animation
- Term column has a fixed width (configurable via CSS token)
- Responsive: collapses to stacked layout below 600px

### Frontend Markup

```html
<dl class="tidbits tidbits--columns">
  <div class="tidbits-morsel tidbits-morsel--columns">
    <dt class="tidbits-morsel__term">Label</dt>
    <dd class="tidbits-morsel__content">
      <p>Value content</p>
    </dd>
  </div>
</dl>
```

---

## Shared Markup Patterns

All three modes share:

- Outer `<dl>` wrapper with `tidbits` base class and mode modifier
- Each item wrapped in a `<div>` containing `<dt>` (term) and `<dd>` (content)
- BEM naming convention: `tidbits-morsel`, `tidbits-morsel__term`, `tidbits-morsel__content`
- Border separators between items (last item has no border)
- First item has no top padding
