# Blocks

Tidbits uses a parent/child block architecture. The parent block controls layout, and each child block represents a single tidbit post.

## Parent Block: Tidbits (`tidbits/tidbit`)

The wrapper block that editors insert into pages and posts.

**Source:** `src/blocks/tidbits/`

### Attributes

| Attribute | Type | Default | Description |
|-----------|------|---------|-------------|
| `displayMode` | `string` | `"accordion"` | Layout mode: `accordion`, `stacked`, or `columns` |

### Supports

| Feature | Value |
|---------|-------|
| HTML editing | Disabled |
| Anchor | Enabled |
| Alignment | Wide, Full |
| Layout | Enabled |
| Spacing (margin) | Top, Bottom |
| Spacing (padding) | All sides |

### Context

The parent block provides context to its children:

```json
{
  "providesContext": {
    "tidbits/displayMode": "displayMode"
  }
}
```

This means child Morsel blocks automatically receive the current display mode without needing their own attribute for it.

### InnerBlocks

The parent uses `InnerBlocks` restricted to only `tidbits/morsel` children. It initializes with one empty Morsel block as a template.

### Rendering

- **Editor:** Renders as a `<dl>` element with class `tidbits tidbits--{mode}`
- **Frontend:** Server-rendered via `render.php`. Wraps child block output in a `<dl>`. Adds `data-wp-interactive="tidbits"` only in accordion mode
- **Save:** Serializes `InnerBlocks.Content` so child block data persists

### Assets

| File | Purpose |
|------|---------|
| `index.js` | Block registration |
| `edit.js` | Editor component with display mode selector |
| `save.js` | Serializes inner blocks |
| `render.php` | Frontend server-side rendering |
| `view.js` | Interactivity API store (accordion toggle) |
| `style.scss` | Frontend styles for all display modes |
| `editor.scss` | Editor-specific overrides |

---

## Child Block: Morsel (`tidbits/morsel`)

Represents a single tidbit post within the parent block.

**Source:** `src/blocks/morsel/`

### Attributes

| Attribute | Type | Default | Description |
|-----------|------|---------|-------------|
| `postId` | `number` | `0` | The ID of the selected tidbit post |

### Constraints

- **Parent:** Can only exist inside `tidbits/tidbit`
- **Reusable:** Disabled (each instance is unique to its parent)

### Context

Receives the display mode from its parent:

```json
{
  "usesContext": [ "tidbits/displayMode" ]
}
```

### Editor Behavior

**Empty state:** Shows a `Placeholder` component with a `ComboboxControl` to search and select a tidbit post.

**Selected state:** Shows a preview matching the current display mode:
- Accordion: Working toggle with chevron rotation and animated expand/collapse
- Stacked: Static term and content
- Columns: Two-column term/content layout

**Sidebar:** When a post is selected, the block sidebar shows a "Change Tidbit" combobox to swap the selected post.

**Duplicate prevention:** The editor queries sibling Morsel blocks and filters their `postId` values out of the combobox options, preventing the same tidbit from being selected twice within one parent block.

### Rendering

The Morsel block is fully dynamic -- `save()` returns `null` and all frontend output comes from `render.php`. The PHP template reads `$attributes['postId']` and `$block->context['tidbits/displayMode']` to fetch the post and render the appropriate markup.

### Assets

| File | Purpose |
|------|---------|
| `index.js` | Block registration |
| `edit.js` | Editor component with post picker and mode-aware preview |
| `render.php` | Frontend server-side rendering with three mode branches |
| `editor.scss` | Editor-specific placeholder styling |
