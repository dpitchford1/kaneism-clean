# Work Plugin Template Customization

## Template System Overview

The Work plugin follows WordPress's template hierarchy but provides enhanced customization options via action hooks and filters. This allows themes to modify plugin templates without overriding entire files.

### Template Ownership

The template system is designed with clear ownership boundaries:

1. **Plugin Templates** (`wp-content/plugins/work/templates/`):
   - Primary templates for displaying Work post types
   - Provide a consistent base experience
   - Include numerous hooks for theme customization

2. **Theme Templates** (Optional overrides in your theme):
   - Can override plugin templates completely by creating matching files in the theme
   - Can use hooks to modify parts of plugin templates without overriding them

## Template Loading Priority

Templates are loaded in this order:

1. Check for a matching template in the theme directory
2. If not found, use the plugin's template

## Available Templates

The Work plugin includes these templates:

- `single-work.php` - For displaying individual work items
- `archive-work.php` - For displaying the main work archive page
- `taxonomy-work_category.php` - For displaying work category archives

## Customization Methods

### Method 1: Using Action Hooks (Recommended)

The Work plugin templates include numerous action hooks that allow for targeted customization without overriding entire templates.

#### Single Work Template Hooks

```php
// Before header
do_action('work_before_single_work_header');

// Main content area
do_action('work_before_single_work_content');
do_action('work_before_breadcrumb');
do_action('work_after_breadcrumb');
do_action('work_before_category_navigation');
do_action('work_after_category_navigation');
do_action('work_before_entry_title');
do_action('work_after_entry_title');
do_action('work_before_gallery');
do_action('work_after_gallery');
do_action('work_before_description');
do_action('work_after_description');
do_action('work_before_project_details');
do_action('work_after_project_details');
do_action('work_after_entry_content');
do_action('work_after_single_work_content');

// Footer area
do_action('work_before_single_work_footer');
do_action('work_after_single_work_footer');
```

#### Archive Work Template Hooks

```php
// Before header
do_action('work_before_archive_header');

// Main content area
do_action('work_before_archive_content');
do_action('work_before_archive_title');
do_action('work_after_archive_title');
do_action('work_before_category_filter');
do_action('work_after_category_filter');
do_action('work_before_loop');
do_action('work_before_grid_item');
do_action('work_before_thumbnail');
do_action('work_after_thumbnail');
do_action('work_before_item_title');
do_action('work_after_item_title');
do_action('work_before_item_excerpt');
do_action('work_after_item_excerpt');
do_action('work_item_footer');
do_action('work_after_grid_item');
do_action('work_after_loop');
do_action('work_before_pagination');
do_action('work_after_pagination');
do_action('work_after_archive_content');

// Footer area
do_action('work_before_archive_footer');
do_action('work_after_archive_footer');
```

#### Work Category Template Hooks

```php
// Before header
do_action('work_before_category_header');

// Main content area
do_action('work_before_category_content');
do_action('work_before_category_title');
do_action('work_after_category_title');
do_action('work_before_category_filter');
do_action('work_after_category_filter');
do_action('work_before_category_loop');
do_action('work_before_category_grid_item');
do_action('work_before_category_thumbnail');
do_action('work_after_category_thumbnail');
do_action('work_before_category_item_title');
do_action('work_after_category_item_title');
do_action('work_before_category_item_excerpt');
do_action('work_after_category_item_excerpt');
do_action('work_category_item_footer');
do_action('work_after_category_grid_item');
do_action('work_after_category_loop');
do_action('work_before_category_pagination');
do_action('work_after_category_pagination');
do_action('work_after_category_content');

// Footer area
do_action('work_before_category_footer');
do_action('work_after_category_footer');
```

### Method 2: Using Filters

The plugin also includes filters for modifying specific outputs:

- `work_gallery_output` - Filter the gallery HTML output
- `work_description_output` - Filter the work description
- `work_project_details_output` - Filter the project details output
- `work_no_results_text` - Filter the "no results" text on archive pages
- `work_category_description` - Filter the category description
- `work_category_no_results_text` - Filter the "no results" text on category pages

### Method 3: Complete Template Override

If needed, you can override the entire plugin template by creating matching files in your theme:

- `single-work.php`
- `archive-work.php`
- `taxonomy-work_category.php`

## Examples

### Example 1: Adding Content Before the Work Title

```php
function mytheme_add_content_before_work_title() {
    echo '<div class="custom-banner">Featured Work</div>';
}
add_action('work_before_entry_title', 'mytheme_add_content_before_work_title');
```

### Example 2: Modifying the Gallery Output

```php
function mytheme_modify_gallery_output($gallery_html) {
    // Add a class to the gallery
    $gallery_html = str_replace('work-gallery', 'work-gallery custom-gallery', $gallery_html);
    return $gallery_html;
}
add_filter('work_gallery_output', 'mytheme_modify_gallery_output');
```

### Example 3: Adding Related Posts After Work Content

```php
function mytheme_add_related_works() {
    // Your code to display related works
    get_template_part('template-parts/related-works');
}
add_action('work_after_entry_content', 'mytheme_add_related_works');
```

## Best Practices

1. Use hooks and filters whenever possible instead of overriding templates
2. If overriding templates, copy the plugin template as a starting point
3. Keep customizations organized in a theme functions file or separate class
4. Use the theme prefix for all custom functions to avoid conflicts
5. Only use the templates, hooks, and filters documented here to ensure compatibility 