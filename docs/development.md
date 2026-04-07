# Development

## Prerequisites

- Node.js 20.10.0 (pinned in `.nvmrc`)
- pnpm 10.33.0 (via corepack)
- Composer
- PHP 7.0+

## Setup

```bash
# Use the correct Node version
nvm use

# Install dependencies
pnpm install
composer install

# Build for production
pnpm run build

# Or start dev server with hot reload
pnpm run start
```

## Commands

| Command | Description |
|---------|-------------|
| `pnpm run build` | Production build with blocks manifest |
| `pnpm run start` | Dev server with hot reload |
| `pnpm run lint` | Run all linters (CSS, JS, PHP) in parallel |
| `pnpm run format` | Auto-fix CSS and JS |
| `pnpm run lint:css` | Lint SCSS files |
| `pnpm run format:css` | Auto-fix SCSS |
| `pnpm run lint:js` | Lint JavaScript files |
| `pnpm run format:js` | Auto-fix JavaScript |
| `pnpm run lint:php` | Lint PHP via PHPCS |
| `pnpm run format:php` | Auto-fix PHP via PHPCBF |
| `composer dump-autoload` | Regenerate autoloader after adding/renaming classes |

## Build System

The plugin uses `@wordpress/scripts` with no custom webpack configuration. Blocks are discovered automatically from `block.json` files in `src/blocks/*/`.

Build flags:
- `--experimental-modules` -- Enables ES module output for Interactivity API `viewScriptModule`
- `--blocks-manifest` -- Generates `build/blocks-manifest.php` used by the block registration class

**Important:** Always `rm -rf build/` before building if block directories were renamed or removed. Stale manifests cause registration mismatches.

## Project Structure

```
tidbits/
├── classes/                        # PHP module classes
│   ├── class-plugin-module.php     # Abstract base class
│   ├── class-register-blocks.php   # Block registration from manifest
│   ├── class-register-tidbit-post-type.php
│   └── class-register-flavour-taxonomy.php
├── src/
│   └── blocks/
│       ├── tidbits/                # Parent block
│       │   ├── block.json          # Block metadata and supports
│       │   ├── index.js            # Block registration
│       │   ├── edit.js             # Editor component
│       │   ├── save.js             # Serializes InnerBlocks
│       │   ├── render.php          # Frontend rendering
│       │   ├── view.js             # Interactivity API store
│       │   ├── style.scss          # Frontend styles
│       │   └── editor.scss         # Editor overrides
│       └── morsel/                 # Child block
│           ├── block.json          # Block metadata
│           ├── index.js            # Block registration
│           ├── edit.js             # Editor component with post picker
│           ├── render.php          # Frontend rendering (3 mode branches)
│           └── editor.scss         # Editor placeholder styling
├── build/                          # Compiled output (git-ignored)
├── docs/                           # Documentation
├── vendor/                         # Composer autoload
├── plugin.php                      # Plugin entry point
├── package.json                    # Node dependencies and scripts
├── composer.json                   # PHP dependencies and autoload
├── CLAUDE.md                       # AI development context
└── README.md                       # Plugin overview
```

## PHP Architecture

All PHP classes extend `Tidbits\Plugin_Module`, an abstract class with a single `init()` method. Modules are instantiated in `plugin.php` and each hooks itself into WordPress via `init()`.

Composer classmap autoloading is used (`classes/` directory). After adding or renaming a class, run `composer dump-autoload`.

### Block Registration

`Register_Blocks` handles block registration using the generated `blocks-manifest.php`. It supports three WordPress versions:

1. **WP 6.7+:** `wp_register_block_types_from_metadata_collection()` (bulk registration)
2. **WP 6.5+:** `wp_register_block_metadata_collection()` + individual `register_block_type()` calls
3. **Older:** Individual `register_block_type()` from block directories

## Coding Standards

- **PHP:** WordPress Coding Standards via PHPCS (`phpcs.xml.dist`)
- **JavaScript:** WordPress ESLint configuration via `@wordpress/scripts`
- **CSS:** WordPress Stylelint configuration via `@wordpress/scripts`

## Dependencies

### JavaScript (devDependencies)

| Package | Purpose |
|---------|---------|
| `@wordpress/scripts` | Build tooling, linting, formatting |
| `@wordpress/blocks` | Block registration API |
| `@wordpress/block-editor` | Editor components (InnerBlocks, InspectorControls) |
| `@wordpress/components` | UI components (SelectControl, ComboboxControl, Placeholder) |
| `@wordpress/core-data` | Entity records API for fetching tidbit posts |
| `@wordpress/data` | State management (useSelect) |
| `@wordpress/element` | React utilities (useMemo, useState) |
| `@wordpress/html-entities` | Decode HTML entities in post titles |
| `@wordpress/i18n` | Internationalization |
| `@wordpress/interactivity` | Interactivity API store and directives |

### PHP

| Package | Purpose |
|---------|---------|
| `wp-coding-standards/wpcs` | PHP coding standards (dev only) |

## Adding a New Display Mode

1. Add the value to the `enum` array in `src/blocks/tidbits/block.json`
2. Add the option to `DISPLAY_MODE_OPTIONS` in `src/blocks/tidbits/edit.js`
3. Add a rendering branch in `src/blocks/morsel/render.php`
4. Add a preview component in `src/blocks/morsel/edit.js`
5. Add CSS rules in `src/blocks/tidbits/style.scss`
6. Rebuild
