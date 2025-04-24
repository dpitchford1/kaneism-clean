# Work Plugin Migration Guide

This guide is intended for theme developers who need to update their code to work with the latest version of the Work plugin. It outlines the changes in the API and provides code examples for upgrading your theme.

## Overview of Changes

The Work plugin has undergone significant improvements to create clearer boundaries between plugin and theme functionality. Key improvements include:

1. **Data API Functions**: All data access is now handled through standardized functions rather than direct post meta access
2. **Template Customization Hooks**: A comprehensive set of action hooks and filters for template customization
3. **Improved Asset Management**: Better control over script and style loading
4. **Backward Compatibility Layer**: Support for themes using older methods during transition

## Migrating Theme Code

### 1. Replace Direct Meta Access with API Functions

#### Before:
```php
$is_featured = get_post_meta(get_the_ID(), '_work_is_featured', true);
$gallery_images = get_post_meta(get_the_ID(), '_work_gallery_images', true);
$project_details = get_post_meta(get_the_ID(), '_work_project_details', true);
```

#### After:
```php
// Always check if functions exist first
if (function_exists('work_is_featured')) {
    $is_featured = work_is_featured(get_the_ID());
}

if (function_exists('work_get_gallery_images')) {
    $gallery_images = work_get_gallery_images(get_the_ID());
}

if (function_exists('work_get_project_details_data')) {
    $project_details = work_get_project_details_data(get_the_ID());
}
```

### 2. Use Template Hooks Instead of Overriding Files

#### Before:
Overriding entire plugin templates:
```php
// In your theme directory
// /your-theme/single-work.php
// /your-theme/archive-work.php
// /your-theme/taxonomy-work_category.php
```

#### After:
Use action hooks to inject custom content:
```php
// In your theme's functions.php
function mytheme_add_before_work_title() {
    echo '<div class="mytheme-custom-element">Featured Work</div>';
}
add_action('work_before_entry_title', 'mytheme_add_before_work_title');

function mytheme_modify_work_content($content) {
    // Modify the content
    return $content;
}
add_filter('work_description_output', 'mytheme_modify_work_content');
```

### 3. Create a Template Customizer Class

For a more organized approach, consider creating a template customizer class similar to the one in the Kane theme:

```php
class MyTheme_Work_Template_Customizer {
    public function __construct() {
        // Register hooks only if Work plugin is active
        if ($this->is_work_plugin_active()) {
            $this->register_hooks();
        }
    }
    
    private function is_work_plugin_active() {
        return function_exists('work_is_featured');
    }
    
    public function register_hooks() {
        // Single work customizations
        add_action('work_after_entry_content', [$this, 'add_related_works']);
        add_filter('work_gallery_output', [$this, 'enhance_gallery']);
        
        // Archive customizations
        add_action('work_before_archive_title', [$this, 'add_archive_intro']);
    }
    
    public function add_related_works() {
        // Your custom related works code
    }
    
    public function enhance_gallery($gallery_html) {
        // Modify gallery HTML
        return $gallery_html;
    }
    
    public function add_archive_intro() {
        // Add custom intro
    }
}

// Initialize the class
new MyTheme_Work_Template_Customizer();
```

## Handling the Transition Period

The Work plugin includes a backward compatibility layer that will log deprecated usage of direct meta access. If you enable `WP_DEBUG` in your development environment, you'll see admin notices for any deprecated code.

### Example Log Message:
```
Deprecated: Direct access to Work plugin meta key "_work_is_featured" detected in /path/to/your/theme/file.php:123. Use Work plugin API functions instead.
```

### Graceful Fallbacks

For maximum compatibility, you should implement graceful fallbacks that work with both old and new versions of the plugin:

```php
// Check for new function first, fall back to direct meta access if needed
if (function_exists('work_is_featured')) {
    $is_featured = work_is_featured(get_the_ID());
} else {
    $is_featured = get_post_meta(get_the_ID(), '_work_is_featured', true);
}
```

## Testing Your Migration

After updating your code, test thoroughly in these scenarios:

1. With the latest version of the Work plugin active
2. With the Work plugin deactivated
3. With an older version of the Work plugin

## Best Practices Going Forward

1. **Never access post meta directly**: Always use the provided API functions
2. **Check if functions exist**: Always wrap Work API functions in `function_exists()` checks
3. **Use hooks for customization**: Prefer using hooks rather than overriding template files
4. **Implement graceful degradation**: Ensure your theme works even when the plugin is inactive
5. **Reference the documentation**: Refer to the Data API and Template Customization documentation

## Additional Resources

- [Work Plugin Data API Documentation](admin.php?page=work-docs)
- [Work Plugin Template Customization Documentation](admin.php?page=work-docs)

By following this migration guide, you'll ensure that your theme remains compatible with current and future versions of the Work plugin. 