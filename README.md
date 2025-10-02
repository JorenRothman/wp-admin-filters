# WP Admin Filters

A modern, Composer-ready WordPress plugin for **contextual admin filters** on
custom post types. Easily add dropdown filters for **meta fields**,
**taxonomies**, and **custom callbacks** in the admin post list. Includes
Select2 for a better UI.

---

## ✨ Features

- Add filters to the admin list table for any (custom) post type
- Filter by **meta fields** (auto-discover distinct values or provide options)
- Filter by **taxonomies**
- Add **custom callback filters** with your own query logic
- Select2 dropdowns for better usability

---

## 📦 Installation

### With Composer (recommended)

```bash
composer require jorenrothman/wp-admin-filters
```

Then activate include the mu-plugin some how.

### Manual

1. Download or clone this repository into
   `wp-content/mu-plugins/wp-admin-filters`
2. Run `composer install` inside the plugin folder
3. Activate **WP Admin Filters** from the WordPress admin

---

## 🚀 Usage

Register filters inside a hook (e.g. `plugins_loaded`):

```php
use JorenRothman\WpAdminFilters\FilterManager;

add_action('plugins_loaded', static function () {
    $filters = new FilterManager('book'); // post type slug

    // Meta filter: ISBN
    $filters->addMetaFilter('isbn', [
        'label' => 'ISBN',
        'key'   => 'isbn', // meta_key
    ]);

    // Meta filter: Post Object/Relationship Field
    $filters->addMetaFilter('items', [
        'label' => 'Items',
        'key'   => 'items', // meta_key
        'resolve_posts' => true,
    ]);

    // Taxonomy filter: Genre
    $filters->addTaxonomyFilter('genre', [
        'label'    => 'Genre',
        'taxonomy' => 'genre',
    ]);

    // Callback filter: Has Reviews
    $filters->addCallbackFilter('has_reviews', [
        'label' => 'Has Reviews',
        'options' => [
            'yes' => 'Yes',
            'no'  => 'No',
        ],
        'apply' => function (\WP_Query $query, $value) {
            if ($value === 'yes') {
                $query->set('meta_query', [[
                    'key'     => 'review_count',
                    'value'   => 0,
                    'compare' => '>',
                ]]);
            }
            if ($value === 'no') {
                $query->set('meta_query', [[
                    'key'     => 'review_count',
                    'value'   => 0,
                    'compare' => '=',
                ]]);
            }
        },
    ]);

    $filters->register();
});
```

Now when you visit the **Books** admin screen, you’ll see the filters above the
post list.

---

## 🔧 API Reference

### `FilterManager::__construct(string $postType)`

Create a filter manager for a custom post type.

### `FilterManager::addMetaFilter(string $key, array $args = [])`

Add a meta field filter. Args:

- `label` (string) – Dropdown label
- `key` (string) – Meta key (defaults to `$key`)
- `options` (array) – Optional predefined key → label pairs. If omitted,
  distinct values are auto-discovered.
- `resolve_posts` (bool) - Transform ids into post titles handy for
  relationship/post object fields.

### `FilterManager::addTaxonomyFilter(string $key, array $args = [])`

Add a taxonomy filter. Args:

- `label` (string) – Dropdown label
- `taxonomy` (string) – Taxonomy name

### `FilterManager::addCallbackFilter(string $key, array $args = [])`

Add a custom callback filter. Args:

- `label` (string) – Dropdown label
- `options` (array) – Key → label pairs for dropdown options
- `apply` (callable) – Function `fn(WP_Query $query, string $value)` that
  applies custom query logic

### `FilterManager::register()`

Registers the filters (renders the dropdowns and modifies the query).

---

## 🛡 Security

- All incoming request data is sanitized (`sanitize_text_field`, `wp_unslash`)
- All HTML output is escaped (`esc_attr`, `esc_html`)
- No unsafe direct database queries (only safe `$wpdb->prepare` when
  auto-discovering meta values)

---

## 📄 License

MIT License. See `LICENSE` file for details.

---
