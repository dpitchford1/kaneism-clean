# Work Plugin - Developer Documentation

## Plugin Architecture

The Work plugin follows a modular architecture with separate files for different functionality:

```
work/
├── assets/
│   ├── css/
│   │   └── work-gallery.css
│   │   └── work-public.css
│   └── js/
│       └── work-admin.js
│       └── work-gallery.js
├── includes/
│   ├── admin.php
│   ├── frontend.php
│   ├── meta-boxes.php
│   ├── post-types.php
│   ├── taxonomies.php
│   ├── template-functions.php
│   └── template-loader.php
├── languages/
│   └── (translation files)
├── templates/
│   ├── archive-work.php
│   └── single-work.php
├── README.md
├── DEVELOPER.md
├── index.php
├── uninstall.php
└── work.php (main plugin file)
```

## Core Components

### 1. Post Type Registration (`post-types.php`)

The Work custom post type is registered with the following key features:
- Hierarchical structure
- Support for featured images, excerpts, and page attributes
- Custom rewrite rules
- REST API support

### 2. Taxonomy Management (`taxonomies.php`)

- Registers the 'work_category' taxonomy
- Handles migration from legacy taxonomies
- Sets up default categories

### 3. Meta Boxes (`meta-boxes.php`)

- Gallery meta box for adding multiple images
- Featured status meta box for marking items as featured
- Handles saving and retrieving meta data

### 4. Admin Interface (`admin.php`)

- Custom columns in the admin list view
- AJAX functionality for toggling featured status
- Bulk actions for featured items
- Admin filtering options
- Custom styling for admin elements

### 5. Frontend Display (`frontend.php`)

- Script and style enqueuing
- Gallery display functionality
- Shortcode registration
- Content filtering

### 6. Template System (`template-loader.php` & `template-functions.php`)

- Custom template loading logic
- Breadcrumb navigation with schema.org markup
- Category navigation
- Template styling

## Key Features Implementation

### Gallery System

The gallery system uses Swiper.js and consists of:

1. **Admin Interface**: A meta box that allows adding multiple images to a work item
2. **Storage**: Images are stored as an array of attachment IDs in post meta
3. **Display**: The gallery is rendered using Swiper.js on the frontend

```php
// Example: Getting gallery images
$gallery_images = get_post_meta($post_id, '_work_gallery_images', true);
```

### Featured Items System

The featured items functionality includes:

1. **Meta Storage**: Featured status is stored in '_work_is_featured' post meta
2. **Admin UI**: Toggle in post edit screen and clickable star in admin columns
3. **AJAX Handling**: JavaScript for toggling status without page reload
4. **Display Logic**: Special layout for featured items on archive pages

```php
// Example: Checking if an item is featured
$is_featured = get_post_meta($post_id, '_work_is_featured', true);
if ($is_featured) {
    // Item is featured
}
```

### Template Hierarchy

The plugin respects WordPress template hierarchy while providing custom templates:

1. First checks if the theme has a custom template
2. Falls back to plugin templates if not found
3. Uses default WordPress templates as a last resort

## Hooks and Filters

### Actions

- `work_before_gallery`: Fires before the gallery is displayed
- `work_after_gallery`: Fires after the gallery is displayed

### Filters

- `work_gallery_options`: Modify Swiper gallery options
- `work_breadcrumb_separator`: Customize breadcrumb separator
- `work_featured_heading`: Customize the featured section heading

## JavaScript Components

### Admin JavaScript (`work-admin.js`)

Handles:
- AJAX requests for toggling featured status
- Admin notifications
- UI interactions

### Gallery JavaScript (`work-gallery.js`)

Handles:
- Swiper initialization
- Responsive behavior
- Error handling and retries

## CSS Structure

The plugin includes several CSS files:

- `work-gallery.css`: Styles for the Swiper gallery
- `work-public.css`: Styles for archive and single templates

## Adding Custom Functionality

### Custom Meta Fields

To add custom meta fields:

1. Add a new meta box in `meta-boxes.php`
2. Create the callback function to display the field
3. Update the save function to handle the new field

### Custom Templates

To override templates:

1. Create a file in your theme with the same name as the plugin template
2. The template loader will automatically use your theme's version

### Custom Shortcodes

To add new shortcodes:

1. Register the shortcode in `frontend.php`
2. Create a callback function that returns the HTML

## Best Practices

1. **Prefix**: All functions, hooks, and meta keys use the `work_` prefix
2. **Escaping**: Always escape output with appropriate functions
3. **Nonces**: Security checks are implemented for all form submissions
4. **Capabilities**: User capability checks before performing actions
5. **Internationalization**: All strings are translatable

## Troubleshooting

### Common Issues

1. **Gallery not displaying**: Check if Swiper.js is loading correctly
2. **Featured items not showing**: Verify meta values in the database
3. **Templates not working**: Check template hierarchy and file paths

### Debugging

Enable WordPress debugging in `wp-config.php`:

```php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
```

## Future Development

When extending the plugin, consider:

1. Maintaining backward compatibility
2. Following WordPress coding standards
3. Adding appropriate documentation
4. Writing unit tests for new functionality

---

*This documentation is intended for developers working with the Work plugin. For user documentation, please refer to README.md.* 