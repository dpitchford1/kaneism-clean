<?php
/**
 * Data Migration Functions
 *
 * Functions to handle migrations and backward compatibility for data access.
 *
 * @package Work
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

/**
 * Setup data migration hooks
 */
function work_setup_data_migration() {
    // Filter to maintain backward compatibility with themes directly accessing post meta
    add_filter('get_post_metadata', 'work_filter_post_meta_access', 10, 4);
    
    // Log deprecated direct meta access
    if (defined('WP_DEBUG') && WP_DEBUG) {
        add_action('admin_notices', 'work_show_meta_access_warnings');
    }
}
add_action('init', 'work_setup_data_migration', 20);

/**
 * Track deprecated meta key access
 *
 * @var array
 */
global $work_deprecated_meta_access;
$work_deprecated_meta_access = array();

/**
 * Filter post meta access to track deprecated usage
 *
 * @param mixed  $value     The value to return.
 * @param int    $object_id Object ID.
 * @param string $meta_key  Meta key.
 * @param bool   $single    Whether to return a single value.
 * @return mixed Original meta value.
 */
function work_filter_post_meta_access($value, $object_id, $meta_key, $single) {
    // Check if this is a Work plugin meta key
    if (strpos($meta_key, '_work_') === 0) {
        // Don't track access from within our own plugin functions
        $debug_backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 10);
        $from_plugin = false;
        
        foreach ($debug_backtrace as $trace) {
            if (isset($trace['file']) && strpos($trace['file'], plugin_dir_path(dirname(__FILE__))) !== false) {
                // This access is from within our plugin, so it's fine
                $from_plugin = true;
                break;
            }
        }
        
        if (!$from_plugin) {
            // This is direct access from outside the plugin - track it
            global $work_deprecated_meta_access;
            
            if (!isset($work_deprecated_meta_access[$meta_key])) {
                $work_deprecated_meta_access[$meta_key] = array();
            }
            
            $caller = '';
            foreach ($debug_backtrace as $trace) {
                if (isset($trace['file']) && strpos($trace['file'], plugin_dir_path(dirname(__FILE__))) === false) {
                    $caller = $trace['file'] . ':' . (isset($trace['line']) ? $trace['line'] : '?');
                    break;
                }
            }
            
            if (!in_array($caller, $work_deprecated_meta_access[$meta_key])) {
                $work_deprecated_meta_access[$meta_key][] = $caller;
                
                // Log this deprecated usage
                if (function_exists('error_log')) {
                    error_log(sprintf(
                        'Deprecated: Direct access to Work plugin meta key "%s" detected in %s. Use Work plugin API functions instead.',
                        $meta_key,
                        $caller
                    ));
                }
            }
        }
    }
    
    // Always return the original value
    return $value;
}

/**
 * Show admin notices about deprecated meta access
 */
function work_show_meta_access_warnings() {
    // Only show to admins
    if (!current_user_can('manage_options')) {
        return;
    }
    
    global $work_deprecated_meta_access;
    
    if (!empty($work_deprecated_meta_access)) {
        $meta_keys = array_keys($work_deprecated_meta_access);
        sort($meta_keys);
        
        echo '<div class="notice notice-warning is-dismissible">';
        echo '<p><strong>Work Plugin:</strong> Deprecated direct meta access detected.</p>';
        echo '<p>The following meta keys are being accessed directly:</p>';
        echo '<ul>';
        
        foreach ($meta_keys as $meta_key) {
            $recommended = str_replace('_work_', 'work_', $meta_key);
            $recommended = preg_replace_callback('/_([a-z])/', function($matches) {
                return '_' . strtoupper($matches[1]);
            }, $recommended);
            $recommended = str_replace('_', '', $recommended);
            
            echo '<li>';
            echo esc_html($meta_key) . ' - Please use <code>' . esc_html($recommended) . '()</code> instead';
            echo '</li>';
        }
        
        echo '</ul>';
        echo '<p>See the <a href="' . esc_url(admin_url('admin.php?page=work-docs')) . '">Work Plugin documentation</a> for more information on the data API.</p>';
        echo '</div>';
    }
}

/**
 * Register a custom admin page to view the documentation
 */
function work_register_docs_page() {
    add_submenu_page(
        'edit.php?post_type=work',
        __('Work Plugin Documentation', 'work'),
        __('Documentation', 'work'),
        'manage_options',
        'work-docs',
        'work_docs_page_callback'
    );
}
add_action('admin_menu', 'work_register_docs_page');

/**
 * Callback for the docs page
 */
function work_docs_page_callback() {
    echo '<div class="wrap">';
    echo '<h1>' . esc_html__('Work Plugin Documentation', 'work') . '</h1>';
    
    // Check if we have markdown files in the docs directory
    $docs_dir = plugin_dir_path(dirname(__FILE__)) . 'docs/';
    $docs = array(
        'data-api.md' => __('Data API', 'work'),
        'template-customization.md' => __('Template Customization', 'work'),
    );
    
    echo '<div class="nav-tab-wrapper">';
    $first_tab = true;
    foreach ($docs as $file => $title) {
        $class = $first_tab ? 'nav-tab nav-tab-active' : 'nav-tab';
        $first_tab = false;
        echo '<a href="#' . esc_attr(sanitize_title($title)) . '" class="' . esc_attr($class) . '">' . esc_html($title) . '</a>';
    }
    echo '</div>';
    
    $first_tab = true;
    foreach ($docs as $file => $title) {
        $style = $first_tab ? '' : 'display: none;';
        $first_tab = false;
        
        echo '<div id="' . esc_attr(sanitize_title($title)) . '" class="work-doc-section" style="' . esc_attr($style) . '">';
        
        if (file_exists($docs_dir . $file)) {
            // We'll use a simple markdown parser
            $content = file_get_contents($docs_dir . $file);
            
            // Super simple markdown to HTML conversion - this should be enhanced in production
            $content = preg_replace('/^# (.*?)$/m', '<h1>$1</h1>', $content);
            $content = preg_replace('/^## (.*?)$/m', '<h2>$1</h2>', $content);
            $content = preg_replace('/^### (.*?)$/m', '<h3>$1</h3>', $content);
            $content = preg_replace('/^#### (.*?)$/m', '<h4>$1</h4>', $content);
            $content = preg_replace('/^- (.*?)$/m', '<li>$1</li>', $content);
            $content = preg_replace('/\*\*(.*?)\*\*/m', '<strong>$1</strong>', $content);
            $content = preg_replace('/\*(.*?)\*/m', '<em>$1</em>', $content);
            
            // Replace lists
            $content = preg_replace('/(<li>.*?<\/li>\n)+/s', '<ul>$0</ul>', $content);
            
            // Replace code blocks
            $content = preg_replace('/```php\n(.*?)```/s', '<pre><code class="language-php">$1</code></pre>', $content);
            $content = preg_replace('/```(.*?)```/s', '<pre><code>$1</code></pre>', $content);
            $content = preg_replace('/`(.*?)`/s', '<code>$1</code>', $content);
            
            echo $content;
        } else {
            echo '<p>' . esc_html__('Documentation file not found.', 'work') . '</p>';
        }
        
        echo '</div>';
    }
    
    // Add simple tab switching script
    ?>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var tabs = document.querySelectorAll('.nav-tab');
        tabs.forEach(function(tab) {
            tab.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Remove active class from all tabs
                tabs.forEach(function(t) {
                    t.classList.remove('nav-tab-active');
                });
                
                // Add active class to clicked tab
                this.classList.add('nav-tab-active');
                
                // Hide all sections
                document.querySelectorAll('.work-doc-section').forEach(function(section) {
                    section.style.display = 'none';
                });
                
                // Show the clicked section
                var target = this.getAttribute('href').substring(1);
                document.getElementById(target).style.display = 'block';
            });
        });
    });
    </script>
    <?php
    
    echo '</div>';
}

/**
 * Add the data-migration.php file to plugin's list of included files
 */
function work_update_main_plugin_file() {
    // This is already included, so just add a note about it here
    // Make sure to update the work.php file to include this file
} 