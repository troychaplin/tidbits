# Custom Post Type and Taxonomy

## Tidbit Post Type

**Slug:** `tidbit`

Tidbit posts are the content units that the Tidbits block pulls from. Each post has a title (the term) and content (the definition/answer/description).

### Configuration

| Setting | Value | Notes |
|---------|-------|-------|
| Public | `false` | No frontend URLs, archives, or search results |
| Show UI | `true` | Visible in the admin dashboard |
| Show in REST | `true` | Required for the block editor and `@wordpress/core-data` |
| Supports | `title`, `editor`, `revisions` | No featured image, excerpt, or custom fields by default |
| Menu icon | `dashicons-image-filter` | |
| Menu position | `25` | Below Comments in the admin sidebar |
| Has archive | `false` | No archive page |
| Capability type | `post` | Uses standard post capabilities |

### REST API

Tidbit posts are accessible via the WordPress REST API at:

```
GET /wp-json/wp/v2/tidbit
GET /wp-json/wp/v2/tidbit/{id}
```

The Morsel block's editor uses this endpoint through `@wordpress/core-data` to search and fetch posts.

### Why Not Public?

Tidbit posts are designed as content fragments, not standalone pages. They don't have their own URLs because they're always displayed within the context of a Tidbits block on a page. This keeps the site's URL structure clean and avoids thin content pages.

---

## Flavour Taxonomy

**Slug:** `flavour`

An optional hierarchical taxonomy for organizing tidbit posts into groups.

### Configuration

| Setting | Value | Notes |
|---------|-------|-------|
| Hierarchical | `true` | Supports parent/child relationships (like categories) |
| Public | `false` | No frontend archive pages |
| Show UI | `true` | Manageable in the admin |
| Show in REST | `true` | Available in the block editor sidebar |
| Show admin column | `true` | Appears as a column on the Tidbits list table |

### REST API

```
GET /wp-json/wp/v2/flavour
```

### Usage

Flavours are purely an organizational tool for editors. They help manage a large number of tidbits by grouping them (e.g., "Authentication", "General", "Billing"). They do not affect frontend display -- the Tidbits block selects individual posts regardless of their flavour.

Potential future uses:

- Filtering the Morsel combobox by flavour
- Auto-populating a Tidbits block with all posts from a specific flavour
- Admin list table filtering

### PHP Registration

Both the post type and taxonomy are registered by dedicated PHP module classes that extend `Tidbits\Plugin_Module`:

- `classes/class-register-tidbit-post-type.php`
- `classes/class-register-flavour-taxonomy.php`

Each class hooks into `init` via its `init()` method and calls the appropriate WordPress registration function.
