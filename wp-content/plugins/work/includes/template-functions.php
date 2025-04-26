<?php
/**
 * Template Functions
 *
 * @package Work
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

/**
 * Output the breadcrumb for Work post types and taxonomies
 *
 * @return void
 */
function work_breadcrumb() {
    $delimiter = '<span class="breadcrumb-separator"> / </span>';
    echo '<nav class="breadcrumb-global work-breadcrumb" aria-label="' . esc_attr__('breadcrumbs', 'work') . '" itemscope itemtype="http://schema.org/BreadcrumbList">';
    
    // Home link (position 1)
    echo '<span itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">';
    echo '<a class="crumb-link" itemprop="item" href="' . home_url() . '"><span itemprop="name">' . __('Home', 'work') . '</span></a>';
    echo '<meta itemprop="position" content="1" />';
    echo '</span>';
    
    echo $delimiter; 
    
    // Work archive link or text (position 2)
    echo '<span itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">';
    // Check if we're on the work archive page
    if (is_post_type_archive('work')) {
        // If we're on the work archive, don't make it a link
        echo '<span itemprop="name">' . __('Work', 'work') . '</span>';
    } else {
        // Otherwise, make it a link
        echo '<a class="crumb-link" itemprop="item" href="' . get_post_type_archive_link('work') . '"><span itemprop="name">' . __('Work', 'work') . '</span></a>';
    }
    echo '<meta itemprop="position" content="2" />';
    echo '</span>';
    
    // Position counter for schema markup
    $position = 3;
    
    if (is_tax('work_category')) {
        // Get the term and its ancestors
        $term = get_queried_object();
        $ancestors = get_ancestors($term->term_id, 'work_category', 'taxonomy');
        
        // Display ancestors in order from highest to lowest (reversed array)
        $ancestors = array_reverse($ancestors);
        foreach ($ancestors as $ancestor_id) {
            $ancestor = get_term($ancestor_id, 'work_category');
            
            echo $delimiter;
            echo '<span itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">';
            echo '<a class="crumb-link" itemprop="item" href="' . get_term_link($ancestor) . '"><span itemprop="name">' . esc_html($ancestor->name) . '</span></a>';
            echo '<meta itemprop="position" content="' . $position . '" />';
            echo '</span>';
            
            $position++;
        }
        
        // Current category
        echo $delimiter;
        echo '<span itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">';
        echo '<span itemprop="name">' . esc_html($term->name) . '</span>';
        echo '<meta itemprop="position" content="' . $position . '" />';
        echo '</span>';
        
    } elseif (is_singular('work')) {
        // Get categories for this post
        $terms = get_the_terms(get_the_ID(), 'work_category');
        
        if ($terms && !is_wp_error($terms)) {
            // Get the first category
            $term = reset($terms);
            
            // Get ancestors for this category
            $ancestors = get_ancestors($term->term_id, 'work_category', 'taxonomy');
            
            // Display ancestors in order from highest to lowest (reversed array)
            $ancestors = array_reverse($ancestors);
            foreach ($ancestors as $ancestor_id) {
                $ancestor = get_term($ancestor_id, 'work_category');
                
                echo $delimiter;
                echo '<span itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">';
                echo '<a class="crumb-link" itemprop="item" href="' . get_term_link($ancestor) . '"><span itemprop="name">' . esc_html($ancestor->name) . '</span></a>';
                echo '<meta itemprop="position" content="' . $position . '" />';
                echo '</span>';
                
                $position++;
            }
            
            // Direct parent category
            echo $delimiter;
            echo '<span itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">';
            echo '<a class="crumb-link" itemprop="item" href="' . get_term_link($term) . '"><span itemprop="name">' . esc_html($term->name) . '</span></a>';
            echo '<meta itemprop="position" content="' . $position . '" />';
            echo '</span>';
            
            $position++;
        }
        
        // Current post
        echo $delimiter;
        echo '<span itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">';
        echo '<span itemprop="name">' . get_the_title() . '</span>';
        echo '<meta itemprop="position" content="' . $position . '" />';
        echo '</span>';
    }
    
    echo '</nav>';
}

/**
 * Display category navigation for work items
 */
function work_category_navigation() {
    // Get all work categories
    $categories = get_terms(array(
        'taxonomy' => 'work_category',
        'hide_empty' => true,
        'parent' => 0, // Only get top-level categories first
    ));
    
    if (empty($categories) || is_wp_error($categories)) {
        return;
    }
    
    // Get the current category if we're on a category archive
    $current_term_id = 0;
    $current_parents = array();
    if (is_tax('work_category')) {
        $current_term = get_queried_object();
        $current_term_id = $current_term->term_id;
        
        // Get parent categories if this is a subcategory
        if ($current_term->parent != 0) {
            $current_parents = get_ancestors($current_term_id, 'work_category', 'taxonomy');
        }
    } elseif (is_singular('work')) {
        // If we're on a single work post, get its category
        $post_terms = get_the_terms(get_the_ID(), 'work_category');
        if ($post_terms && !is_wp_error($post_terms)) {
            $current_term = reset($post_terms); // Get the first category
            $current_term_id = $current_term->term_id;
            
            // Get parent categories if this is in a subcategory
            if ($current_term->parent != 0) {
                $current_parents = get_ancestors($current_term_id, 'work_category', 'taxonomy');
            }
        }
    }
    
    // Start the category navigation
    echo '<nav class="subnav--global" aria-label="' . esc_attr__('Work Categories', 'work') . '">';
    echo '<ul class="subnav--menu inline-list">';
    
    // Add "All" link
    $all_class = (!is_tax('work_category') && !is_singular('work')) ? ' class="current-cat"' : '';
    $all_a_class = $all_class ? ' class="nav--selected"' : '';
    echo '<li' . $all_class . '>';
    echo '<a href="' . esc_url(get_post_type_archive_link('work')) . '"' . $all_a_class . '>' . __('All Work', 'work') . '</a>';
    echo '</li>';
    
    // Add category links (hierarchical)
    foreach ($categories as $category) {
        // Check if this category is the current one or a parent of the current one
        $is_current = ($current_term_id === $category->term_id);
        $is_parent = in_array($category->term_id, $current_parents);
        $class = '';
        $a_class = '';
        
        if ($is_current) {
            $class = ' class="current-cat"';
            $a_class = ' class="nav--selected"';
        } elseif ($is_parent) {
            $class = ' class="current-parent-cat"';
            $a_class = ' class="current-parent-page"';
        }
        
        echo '<li' . $class . '>';
        
        // Build the category URL with correct structure
        $category_url = home_url('/work/' . $category->slug . '/');
        echo '<a href="' . esc_url($category_url) . '"' . $a_class . '>' . esc_html($category->name) . '</a>';
        
        // Get child categories
        $children = get_terms(array(
            'taxonomy' => 'work_category',
            'hide_empty' => true,
            'parent' => $category->term_id,
        ));
        
        // Only display child categories if this is the current category, a parent of the current,
        // or we're on a child page
        if (!empty($children) && !is_wp_error($children) && ($is_current || $is_parent || in_array($current_term_id, wp_list_pluck($children, 'term_id')))) {
            echo '<ul class="subnav--menu inline-list">';
            foreach ($children as $child) {
                $child_class = ($current_term_id === $child->term_id) ? ' class="current-cat"' : '';
                $child_a_class = $child_class ? ' class="nav--selected"' : '';
                echo '<li' . $child_class . '>';
                // Build the correct URL for subcategories
                $child_url = home_url('/work/' . $category->slug . '/' . $child->slug . '/');
                echo '<a href="' . esc_url($child_url) . '"' . $child_a_class . '>' . esc_html($child->name) . '</a>';
                
                // Get grandchild categories (if needed - uncomment for deeper hierarchy)
                $grandchildren = get_terms(array(
                    'taxonomy' => 'work_category',
                    'hide_empty' => true,
                    'parent' => $child->term_id,
                ));
                
                if (!empty($grandchildren) && !is_wp_error($grandchildren) && 
                    ($current_term_id === $child->term_id || in_array($current_term_id, wp_list_pluck($grandchildren, 'term_id')))) {
                    echo '<ul class="work-grandchild-categories">';
                    foreach ($grandchildren as $grandchild) {
                        $grandchild_class = ($current_term_id === $grandchild->term_id) ? ' class="current-cat"' : '';
                        $grandchild_a_class = $grandchild_class ? ' class="nav--selected"' : '';
                        echo '<li' . $grandchild_class . '>';
                        // Build the correct URL for deeper subcategories
                        $grandchild_url = home_url('/work/' . $category->slug . '/' . $child->slug . '/' . $grandchild->slug . '/');
                        echo '<a href="' . esc_url($grandchild_url) . '"' . $grandchild_a_class . '>' . esc_html($grandchild->name) . '</a>';
                        echo '</li>';
                    }
                    echo '</ul>';
                }
                echo '</li>';
            }
            echo '</ul>';
        }
        echo '</li>';
    }
    
    echo '</ul>';
    echo '</nav>';
    
    // Add enhanced styling
    ?>
    <?php
}

/**
 * Add CSS for work templates
 */
function work_template_styles() {
    if (is_singular('work') || is_post_type_archive('work') || is_tax('work_category')) {
        ?>
        <?php
    }
}
//add_action('wp_head', 'work_template_styles');

/**
 * Get the proper permalink for a work item, including its category in the URL
 *
 * @param int|WP_Post $post The post object or ID.
 * @return string The permalink URL.
 */
function work_get_proper_permalink($post) {
    $post = get_post($post);
    if (!$post || $post->post_type !== 'work') {
        return '';
    }
    
    // Get the post's categories
    $categories = get_the_terms($post->ID, 'work_category');
    if (empty($categories) || is_wp_error($categories)) {
        // Fallback to regular permalink if no categories
        return get_permalink($post);
    }
    
    // Get the first category (could be enhanced to select a primary category)
    $category = reset($categories);
    
    // Build the URL manually to ensure correct format
    $base_url = home_url('/');
    $permalink = $base_url . 'work/' . $category->slug . '/' . $post->post_name . '/';
    
    return $permalink;
}

/**
 * Update links in the content to use the proper permalinks with categories
 */
function work_fix_content_links($content) {
    if (!is_singular('work') && !is_post_type_archive('work') && !is_tax('work_category')) {
        return $content;
    }
    
    // Get all work posts
    $work_posts = get_posts(array(
        'post_type' => 'work',
        'posts_per_page' => -1,
        'fields' => 'ids',
    ));
    
    if (empty($work_posts)) {
        return $content;
    }
    
    // For each work post, replace any links to it with the proper permalink
    foreach ($work_posts as $post_id) {
        $old_permalink = get_permalink($post_id);
        $new_permalink = work_get_proper_permalink($post_id);
        
        if ($old_permalink !== $new_permalink) {
            $content = str_replace($old_permalink, $new_permalink, $content);
        }
    }
    
    return $content;
}
add_filter('the_content', 'work_fix_content_links', 20);

/**
 * Output structured data for Work post types
 * Should be hooked to wp_footer
 * 
 * @return void
 */
function work_structured_data() {
    // Disabled to avoid duplicate schema with work_output_schema_data
    return;

    /*
    if (!is_singular('work')) {
        return;
    }

    global $post;
    
    // Get project details for additional structured data
    $project_details = array();
    if (function_exists('work_get_project_details')) {
        $project_details = work_get_project_details();
    }
    
    // Convert project details to structured data properties
    $structured_data_props = array(
        '@context' => 'http://schema.org',
        '@type' => 'VisualArtwork',
        'name' => get_the_title(),
        'description' => get_the_excerpt(),
        'image' => get_the_post_thumbnail_url(get_the_ID(), 'full')
    );
    
    // Add additional properties from project details
    foreach ($project_details as $detail) {
        if ($detail['label'] === 'Date') {
            $structured_data_props['dateCreated'] = $detail['value'];
        }
        if ($detail['label'] === 'Materials') {
            $structured_data_props['artMedium'] = $detail['value'];
        }
        if ($detail['label'] === 'Size' || $detail['label'] === 'Dimensions') {
            $structured_data_props['size'] = $detail['value'];
        }
    }
    
    // Output the structured data JSON-LD
    echo '<script type="application/ld+json">';
    echo wp_json_encode($structured_data_props);
    echo '</script>';
    */
}

// Hook the structured data function to wp_footer
add_action('wp_footer', 'work_structured_data');

/**
 * Output breadcrumb structured data as JSON-LD
 * This complements the inline microdata already in the breadcrumb HTML
 *
 * @return void
 */
function work_breadcrumb_schema() {
    // Only add breadcrumb schema on work-related pages
    if (!is_singular('work') && !is_post_type_archive('work') && !is_tax('work_category')) {
        return;
    }

    // Initialize breadcrumb items array
    $breadcrumb_items = [];
    
    // Add Home item
    $breadcrumb_items[] = [
        '@type' => 'ListItem',
        'position' => 1,
        'name' => __('Home', 'work'),
        'item' => home_url()
    ];
    
    // Add Work archive item
    $breadcrumb_items[] = [
        '@type' => 'ListItem',
        'position' => 2,
        'name' => __('Work', 'work'),
        'item' => get_post_type_archive_link('work')
    ];
    
    // Position counter for additional items
    $position = 3;
    
    if (is_tax('work_category')) {
        // Get the term and its ancestors
        $term = get_queried_object();
        $ancestors = get_ancestors($term->term_id, 'work_category', 'taxonomy');
        
        // Display ancestors in order from highest to lowest (reversed array)
        $ancestors = array_reverse($ancestors);
        foreach ($ancestors as $ancestor_id) {
            $ancestor = get_term($ancestor_id, 'work_category');
            
            $breadcrumb_items[] = [
                '@type' => 'ListItem',
                'position' => $position,
                'name' => $ancestor->name,
                'item' => get_term_link($ancestor)
            ];
            
            $position++;
        }
        
        // Current category
        $breadcrumb_items[] = [
            '@type' => 'ListItem',
            'position' => $position,
            'name' => $term->name,
            'item' => get_term_link($term)
        ];
        
    } elseif (is_singular('work')) {
        // Get categories for this post
        $terms = get_the_terms(get_the_ID(), 'work_category');
        
        if ($terms && !is_wp_error($terms)) {
            // Get the first category
            $term = reset($terms);
            
            // Get ancestors for this category
            $ancestors = get_ancestors($term->term_id, 'work_category', 'taxonomy');
            
            // Display ancestors in order from highest to lowest (reversed array)
            $ancestors = array_reverse($ancestors);
            foreach ($ancestors as $ancestor_id) {
                $ancestor = get_term($ancestor_id, 'work_category');
                
                $breadcrumb_items[] = [
                    '@type' => 'ListItem',
                    'position' => $position,
                    'name' => $ancestor->name,
                    'item' => get_term_link($ancestor)
                ];
                
                $position++;
            }
            
            // Direct parent category
            $breadcrumb_items[] = [
                '@type' => 'ListItem',
                'position' => $position,
                'name' => $term->name,
                'item' => get_term_link($term)
            ];
            
            $position++;
        }
        
        // Current post - no "item" property because it's the current page
        $breadcrumb_items[] = [
            '@type' => 'ListItem',
            'position' => $position,
            'name' => get_the_title()
        ];
    }
    
    // Complete schema data
    $schema = [
        '@context' => 'https://schema.org',
        '@type' => 'BreadcrumbList',
        'itemListElement' => $breadcrumb_items
    ];
    
    // Output JSON-LD
    echo '<script type="application/ld+json">';
    echo wp_json_encode($schema);
    echo '</script>';
}

// Hook the breadcrumb schema function to wp_footer
add_action('wp_footer', 'work_breadcrumb_schema');