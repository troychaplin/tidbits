# Getting Started

## Installation

1. Download or clone the plugin into your WordPress installation's `wp-content/plugins/` directory
2. Run `composer install` to generate the autoloader
3. Run `pnpm install` and `pnpm run build` to compile block assets (see [Development](development.md) for build details)
4. Activate the plugin from the WordPress admin under **Plugins**

Once activated, two things appear:

- A **Tidbits** menu item in the admin sidebar (post type)
- A **Tidbits** block in the block editor inserter

## Creating Tidbit Posts

1. Navigate to **Tidbits** in the admin sidebar
2. Click **Add New Tidbit**
3. Enter a **title** -- this becomes the visible term (the question, the glossary word, the label)
4. Enter **content** in the block editor -- this becomes the definition, answer, or description
5. Optionally assign a **Flavour** taxonomy term to categorize the tidbit
6. Publish the post

Tidbit posts are not publicly accessible on the frontend. They exist solely as content to be pulled into the Tidbits block. There are no single post pages or archive pages for tidbits.

## Adding the Block

1. Open any page or post in the block editor
2. Click the block inserter (+) and search for **Tidbits**
3. Insert the block -- it creates a parent wrapper with one empty Morsel child block

## Selecting a Display Mode

With the Tidbits parent block selected, open the **block sidebar** (Settings panel) and find **Display Settings**. Choose from:

- **Accordion** -- Collapsible items, content hidden by default
- **Stacked** -- Simple vertical list, all content visible
- **Columns** -- Two-column grid with term on the left

The display mode applies to all child Morsel blocks uniformly.

## Picking Tidbit Posts

Each Morsel child block shows a search combobox. Start typing to search tidbit posts by title, then select one. The block immediately shows a preview of the selected post's title and content in the chosen display mode.

To change the selected post, use the **Tidbit Settings** panel in the block sidebar.

## Adding More Items

Click the block appender at the bottom of the Tidbits block to add additional Morsel blocks. Each one can be assigned a different tidbit post. Posts that are already selected by sibling Morsels are automatically filtered out of the search results.
