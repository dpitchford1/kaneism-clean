<?php
/**
 * Bits Page Metabox Functionality
 * 
 * Adds a metabox to the Bits page for uploading and managing photos
 *
 * @package kaneism
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

/**
 * Register metabox for the Bits page
 */
function kaneism_bits_register_meta_box() {
    // Add the metabox to pages using the "Gallery Grid" template or those with specific names
    global $post;
    
    if (is_admin() && $post && $post->post_type === 'page') {
        // Check if this page has the "Grid Gallery" template or has a specific post name
        $allowed_pages = array('bits', 'doodads'); // Add more page slugs as needed
        $show_metabox = false;
        
        // Check if this is one of our predefined pages
        if (in_array($post->post_name, $allowed_pages)) {
            $show_metabox = true;
        }
        
        // Check if the "show_photo_grid" option is enabled
        $show_photo_grid = get_post_meta($post->ID, '_kaneism_show_photo_grid', true);
        if ($show_photo_grid === 'yes') {
            $show_metabox = true;
        }
        
        if ($show_metabox) {
            add_meta_box(
                'kaneism_bits_gallery',
                __('Photo Collection Grid', 'kaneism'),
                'kaneism_bits_gallery_callback',
                'page',
                'normal',
                'high'
            );
        }
    }
}
add_action('add_meta_boxes', 'kaneism_bits_register_meta_box');

/**
 * Add a checkbox to enable the photo grid in the page attributes meta box
 */
function kaneism_add_photo_grid_option() {
    global $post;
    
    if (is_admin() && $post && $post->post_type === 'page') {
        // Get the current value
        $show_photo_grid = get_post_meta($post->ID, '_kaneism_show_photo_grid', true);
        ?>
        <p class="post-attributes-label-wrapper">
            <label class="post-attributes-label" for="kaneism_show_photo_grid"><?php _e('Photo Grid', 'kaneism'); ?></label>
        </p>
        <select name="kaneism_show_photo_grid" id="kaneism_show_photo_grid">
            <option value="no" <?php selected($show_photo_grid, 'no'); ?>><?php _e('No Photo Grid', 'kaneism'); ?></option>
            <option value="yes" <?php selected($show_photo_grid, 'yes'); ?>><?php _e('Enable Photo Grid', 'kaneism'); ?></option>
        </select>
        <p class="description">
            <?php _e('Enable to show the photo grid editor and display a gallery grid on this page.', 'kaneism'); ?>
        </p>
        <?php
    }
}
add_action('page_attributes_misc_attributes', 'kaneism_add_photo_grid_option');

/**
 * Save the photo grid option
 */
function kaneism_save_photo_grid_option($post_id) {
    // Check if this is an autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    // Check user permissions
    if (!current_user_can('edit_page', $post_id)) {
        return;
    }
    
    // Save the photo grid option
    if (isset($_POST['kaneism_show_photo_grid'])) {
        update_post_meta($post_id, '_kaneism_show_photo_grid', sanitize_text_field($_POST['kaneism_show_photo_grid']));
    }
}
add_action('save_post_page', 'kaneism_save_photo_grid_option');

/**
 * Bits gallery metabox callback
 *
 * @param WP_Post $post Current post object.
 */
function kaneism_bits_gallery_callback($post) {
    // Add nonce for security
    wp_nonce_field('kaneism_bits_gallery_nonce', 'kaneism_bits_gallery_nonce');
    
    // Get saved gallery images
    $gallery_images = get_post_meta($post->ID, '_kaneism_bits_images', true);
    
    ?>
    <div class="bits-gallery-container">
        <p class="description"><?php esc_html_e('Upload and manage photos for the photo grid. Drag images to reorder them.', 'kaneism'); ?></p>
        
        <div class="bits-gallery-images" id="bits-gallery-images">
            <?php
            if (!empty($gallery_images)) {
                foreach ($gallery_images as $image_id) {
                    $image = wp_get_attachment_image_src($image_id, 'thumbnail');
                    if ($image) {
                        ?>
                        <div class="bits-gallery-image" data-id="<?php echo esc_attr($image_id); ?>">
                            <img src="<?php echo esc_url($image[0]); ?>" alt="">
                            <input type="hidden" name="bits_gallery_images[]" value="<?php echo esc_attr($image_id); ?>">
                            <a href="#" class="bits-remove-image"><?php esc_html_e('Remove', 'kaneism'); ?></a>
                        </div>
                        <?php
                    }
                }
            }
            ?>
        </div>
        
        <div class="bits-gallery-actions">
            <input type="button" class="button" id="bits-add-images" value="<?php esc_attr_e('Add Photos', 'kaneism'); ?>">
        </div>
    </div>
    
    <style>
        .bits-gallery-container {
            margin: 15px 0;
        }
        .bits-gallery-images {
            display: flex;
            flex-wrap: wrap;
            margin: 0 -5px;
            min-height: 30px;
        }
        .bits-gallery-image {
            position: relative;
            width: 150px;
            height: 150px;
            margin: 5px;
            border: 1px solid #ddd;
            overflow: hidden;
            cursor: move;
        }
        .bits-gallery-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .bits-remove-image {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(0, 0, 0, 0.7);
            color: #fff;
            text-align: center;
            padding: 5px;
            text-decoration: none;
            display: none;
        }
        .bits-gallery-image:hover .bits-remove-image {
            display: block;
        }
        .ui-sortable-placeholder {
            border: 1px dashed #ccc;
            visibility: visible !important;
            background: #f7f7f7;
            height: 150px;
            width: 150px;
            margin: 5px;
        }
    </style>
    
    <script>
    jQuery(document).ready(function($) {
        // Make images sortable
        $('#bits-gallery-images').sortable({
            items: '.bits-gallery-image',
            cursor: 'move',
            placeholder: 'ui-sortable-placeholder',
            update: function() {
                // You can add logic here if needed on sort update
            }
        });
        
        // Media uploader
        var file_frame;
        
        $('#bits-add-images').on('click', function(e) {
            e.preventDefault();
            
            // If the media frame already exists, reopen it
            if (file_frame) {
                file_frame.open();
                return;
            }
            
            // Create the media frame
            file_frame = wp.media.frames.file_frame = wp.media({
                title: '<?php esc_html_e('Select Photos for Bits', 'kaneism'); ?>',
                button: {
                    text: '<?php esc_html_e('Add to Bits', 'kaneism'); ?>'
                },
                multiple: true
            });
            
            // When images are selected, add them to the gallery
            file_frame.on('select', function() {
                var attachments = file_frame.state().get('selection').toJSON();
                
                $.each(attachments, function(index, attachment) {
                    $('#bits-gallery-images').append(
                        '<div class="bits-gallery-image" data-id="' + attachment.id + '">' +
                        '<img src="' + (attachment.sizes.thumbnail ? attachment.sizes.thumbnail.url : attachment.url) + '" alt="">' +
                        '<input type="hidden" name="bits_gallery_images[]" value="' + attachment.id + '">' +
                        '<a href="#" class="bits-remove-image"><?php esc_html_e('Remove', 'kaneism'); ?></a>' +
                        '</div>'
                    );
                });
            });
            
            // Open the media uploader
            file_frame.open();
        });
        
        // Remove image
        $(document).on('click', '.bits-remove-image', function(e) {
            e.preventDefault();
            $(this).parent().remove();
        });
    });
    </script>
    <?php
}

/**
 * Save bits gallery data
 *
 * @param int $post_id Post ID.
 */
function kaneism_bits_save_meta_box($post_id) {
    // Check if our nonce is set
    if (!isset($_POST['kaneism_bits_gallery_nonce'])) {
        return;
    }
    
    // Verify that the nonce is valid
    if (!wp_verify_nonce($_POST['kaneism_bits_gallery_nonce'], 'kaneism_bits_gallery_nonce')) {
        return;
    }
    
    // If this is an autosave, our form has not been submitted, so we don't want to do anything
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    // Check the user's permissions
    if (!current_user_can('edit_page', $post_id)) {
        return;
    }
    
    // Save gallery images
    if (isset($_POST['bits_gallery_images'])) {
        $gallery_images = array_map('intval', $_POST['bits_gallery_images']);
        update_post_meta($post_id, '_kaneism_bits_images', $gallery_images);
    } else {
        // No images selected, so clear the meta
        delete_post_meta($post_id, '_kaneism_bits_images');
    }
}
add_action('save_post', 'kaneism_bits_save_meta_box'); 

/**
 * Get photo grid images for a page
 * 
 * Supports both the legacy meta approach (_kaneism_bits_images) and ACF repeater field (gallery_images)
 * 
 * @param int $post_id Optional. Post ID to get images for. Defaults to current post.
 * @return array Array of image data for the grid
 */
function kaneism_get_photo_grid_images($post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    
    $images = array();
    
    // First try ACF repeater field method
    if (function_exists('have_rows') && have_rows('gallery_images', $post_id)) {
        while (have_rows('gallery_images', $post_id)) {
            the_row();
            $image = get_sub_field('gallery_image');
            if ($image) {
                $images[] = array(
                    'id' => isset($image['ID']) ? $image['ID'] : 0,
                    'url' => $image['url'],
                    'alt' => $image['alt'],
                    'caption' => isset($image['caption']) ? $image['caption'] : '',
                    'type' => 'acf'
                );
            }
        }
    }
    
    // If no ACF images, try legacy meta approach
    if (empty($images)) {
        $meta_images = get_post_meta($post_id, '_kaneism_bits_images', true);
        if (!empty($meta_images) && is_array($meta_images)) {
            foreach ($meta_images as $image_id) {
                $image_url = wp_get_attachment_image_url($image_id, 'full');
                $image_alt = get_post_meta($image_id, '_wp_attachment_image_alt', true);
                $image_post = get_post($image_id);
                $image_caption = $image_post ? $image_post->post_excerpt : '';
                
                $images[] = array(
                    'id' => $image_id,
                    'url' => $image_url,
                    'alt' => $image_alt,
                    'caption' => $image_caption,
                    'type' => 'meta'
                );
            }
        }
    }
    
    return $images;
}

/**
 * Display the photo grid
 * 
 * This function can be used in theme templates to display the photo grid
 * without directly calling get_template_part
 * 
 * @param int $post_id Optional. Post ID to display grid for. Defaults to current post.
 * @return void
 */
function kaneism_display_photo_grid($post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    
    // Pass post ID via global variable so template part can access it
    global $kaneism_photo_grid_post_id;
    $kaneism_photo_grid_post_id = $post_id;
    
    // Include the template part
    get_template_part('template-parts/content', 'photo-grid');
    
    // Reset the global
    $kaneism_photo_grid_post_id = null;
}