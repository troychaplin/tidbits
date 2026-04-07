# Interactivity API

The accordion display mode uses the WordPress Interactivity API for its toggle behavior. This is WordPress's standard framework for adding client-side interactivity to blocks.

## Why the Interactivity API?

The Interactivity API was chosen over vanilla JavaScript because:

- **Declarative state:** State lives in the markup via `data-wp-context` and is bound to attributes via `data-wp-bind--*` and `data-wp-class--*`. No manual DOM queries or event listener setup.
- **Automatic hydration:** If the block appears inside other interactive blocks (e.g., a Query Loop with pagination), state survives DOM updates. Vanilla JS event listeners would be lost.
- **Lifecycle management:** WordPress handles attaching and detaching behavior. No cleanup code needed.
- **Standard approach:** Core WordPress blocks (Search, Navigation, Image lightbox) use the same API.

## The Store

The entire Interactivity API implementation is in `src/blocks/tidbits/view.js`:

```js
import { store, getContext } from '@wordpress/interactivity';

store( 'tidbits', {
  actions: {
    toggle() {
      const ctx = getContext();
      ctx.isOpen = !ctx.isOpen;
    },
  },
} );
```

That's the complete file. The store is registered under the `tidbits` namespace with a single action.

## How It Works

### 1. Namespace

The parent block's `render.php` adds `data-wp-interactive="tidbits"` to the `<dl>` wrapper. This tells WordPress to load and connect the `tidbits` store to this subtree.

### 2. Per-item context

Each Morsel's `render.php` adds `data-wp-context` to the item wrapper:

```php
$context = wp_interactivity_data_wp_context( array( 'isOpen' => false ) );
```

This gives each accordion item its own isolated `isOpen` state. Opening one item does not affect others.

### 3. Directives

The Morsel template uses these directives:

| Directive | Element | Purpose |
|-----------|---------|---------|
| `data-wp-on--click="actions.toggle"` | `<button>` | Calls `toggle()` when clicked |
| `data-wp-bind--aria-expanded="context.isOpen"` | `<button>` | Syncs `aria-expanded` attribute with state |
| `data-wp-class--is-open="context.isOpen"` | `<dd>`, `<svg>` | Adds/removes `is-open` CSS class based on state |
| `data-wp-bind--aria-hidden="!context.isOpen"` | `<dd>` | Syncs `aria-hidden` attribute with state |

### 4. CSS drives the animation

The `is-open` class toggles `grid-template-rows` between `0fr` (collapsed) and `1fr` (expanded). The CSS transition handles the animation. No JavaScript measurement or style manipulation is needed.

## When It Loads

The Interactivity API store is registered as a `viewScriptModule` in `block.json`:

```json
{
  "viewScriptModule": "file:./view.js"
}
```

WordPress only loads this module on pages that contain the Tidbits block. It is an ES module (not a classic script), which is required by the Interactivity API.

## Stacked and Columns Modes

The Interactivity API is only active in accordion mode. The parent `render.php` conditionally adds `data-wp-interactive` only when `displayMode` is `accordion`. In stacked and columns modes, no JavaScript is loaded or executed.
