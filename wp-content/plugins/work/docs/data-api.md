# Work Plugin Data API

This document outlines the proper way for themes and other plugins to access data from the Work plugin. Using these standardized API functions ensures that your theme will continue to work even if the underlying data structure changes in future updates.

## General Guidelines

1. **Never access post meta directly:** Instead of using `get_post_meta($post_id, '_work_*', true)`, use the provided API functions.
2. **Check if functions exist:** When using Work API functions, always check if they exist first to handle cases where the plugin might be deactivated.
3. **Use filters:** Many API functions provide filters that allow for customization without modifying the core code.
4. **Respect data boundaries:** The theme should display data but not modify it directly unless using provided functions.

## Available API Functions

### Featured Status

```php
// Check if a work item is featured
if (function_exists('work_is_featured')) {
    $is_featured = work_is_featured($post_id);
    if ($is_featured) {
        // Do something with featured works
    }
}
```

### Gallery Images

```php
// Get gallery images for a work item
if (function_exists('work_get_gallery_images')) {
    $gallery_images = work_get_gallery_images($post_id);
    foreach ($gallery_images as $image_id) {
        echo wp_get_attachment_image($image_id, 'large');
    }
}

// Get the number of gallery images
if (function_exists('work_get_gallery_image_count')) {
    $count = work_get_gallery_image_count($post_id);
    echo "This work has {$count} images.";
}
```

### Project Details

```php
// Get all project details
if (function_exists('work_get_project_details_data')) {
    $details = work_get_project_details_data($post_id);
    // Process details array
}

// Get a specific detail
if (function_exists('work_get_project_detail')) {
    $location = work_get_project_detail('location', $post_id);
    echo "Location: {$location}";
}
```

### Schema Data

```php
// Get schema data for SEO
if (function_exists('work_get_schema_data')) {
    $schema = work_get_schema_data($post_id);
    // Use schema data for SEO purposes
}
```

### Queries

```php
// Get featured works
if (function_exists('work_get_featured_works')) {
    $featured_query = work_get_featured_works(3);
    while ($featured_query->have_posts()) {
        $featured_query->the_post();
        // Display featured work
    }
    wp_reset_postdata();
}

// Get works by category
if (function_exists('work_get_category_works')) {
    $category_slug = 'murals';
    $category_query = work_get_category_works($category_slug, 5);
    while ($category_query->have_posts()) {
        $category_query->the_post();
        // Display category work
    }
    wp_reset_postdata();
}
```

### Categories

```php
// Get categories for a work item
if (function_exists('work_get_categories')) {
    $categories = work_get_categories($post_id);
    if (!is_wp_error($categories) && !empty($categories)) {
        foreach ($categories as $category) {
            echo $category->name;
        }
    }
}

// Get primary category
if (function_exists('work_get_primary_category')) {
    $primary = work_get_primary_category($post_id);
    if ($primary) {
        echo "Primary category: {$primary->name}";
    }
}

// Get category options (for dropdowns, etc.)
if (function_exists('work_get_category_options')) {
    $options = work_get_category_options();
    // Use for select fields, etc.
}
```

## Filters

The Work plugin provides several filters that allow themes to modify data:

### Query Filters

```php
// Modify featured works query
add_filter('work_featured_works_query_args', function($args, $count) {
    // Modify query arguments
    $args['orderby'] = 'title';
    $args['order'] = 'ASC';
    return $args;
}, 10, 2);

// Modify category works query
add_filter('work_category_works_query_args', function($args, $category, $count) {
    // Modify query arguments
    return $args;
}, 10, 3);
```

### Field Filters

```php
// Add custom project detail fields
add_filter('work_project_detail_fields', function($fields) {
    // Add a custom field
    $fields['client'] = __('Client', 'my-theme');
    return $fields;
});
```

## Best Practices

1. **Always check if functions exist:** Use `function_exists()` to check if Work API functions are available.
2. **Provide fallbacks:** Have fallback behavior for when the plugin is not active.
3. **Use theme prefixes:** When creating theme-specific functions that interact with Work data, use your theme's prefix.
4. **Reset post data:** After custom queries, always use `wp_reset_postdata()`.
5. **Cache results:** For performance, consider caching results from these API functions, especially for repeated queries.

## Example Integration

Here's a complete example of a theme function that displays featured works with proper error handling:

```php
function mytheme_display_featured_works() {
    // Check if the Work plugin is active
    if (!function_exists('work_get_featured_works')) {
        return '<p>Please activate the Work plugin to display featured works.</p>';
    }
    
    $output = '<div class="mytheme-featured-works">';
    
    // Get featured works using the plugin's API
    $featured_query = work_get_featured_works(3);
    
    if ($featured_query->have_posts()) {
        while ($featured_query->have_posts()) {
            $featured_query->the_post();
            
            $output .= '<article class="mytheme-work-item">';
            $output .= '<h3>' . get_the_title() . '</h3>';
            
            if (has_post_thumbnail()) {
                $output .= get_the_post_thumbnail(get_the_ID(), 'medium');
            }
            
            $output .= '<div class="mytheme-work-excerpt">' . get_the_excerpt() . '</div>';
            $output .= '<a href="' . get_permalink() . '" class="mytheme-work-link">View Project</a>';
            $output .= '</article>';
        }
        wp_reset_postdata();
    } else {
        $output .= '<p>No featured works found.</p>';
    }
    
    $output .= '</div>';
    
    return $output;
}
```

By following these guidelines, your theme will be more robust against future updates to the Work plugin and will gracefully handle cases where the plugin might be deactivated. 