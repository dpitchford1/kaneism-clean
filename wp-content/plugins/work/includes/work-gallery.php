<?php
/**
 * Work Gallery Metabox
 * 
 * Simple metabox for managing work gallery images
 * Based on the bits metabox pattern
 *
 * @package Work
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

/**
 * Register gallery metabox for Work post type
 */
function work_register_gallery_meta_box() {
    add_meta_box(
        'work_gallery',
        __('Gallery Images', 'work'),
        'work_gallery_callback',
        'work',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'work_register_gallery_meta_box');

/**
 * Work gallery metabox callback
 * Based on the bits metabox pattern
 *
 * @param WP_Post $post Current post object.
 */
function work_gallery_callback($post) {
    // Add nonce for security
    wp_nonce_field('work_gallery_nonce', 'work_gallery_nonce');
    
    // Get saved gallery images
    $gallery_images = get_post_meta($post->ID, '_work_gallery_images', true);
    
    if (!is_array($gallery_images)) {
        $gallery_images = array();
    }
    
    ?>
    <div class="work-gallery-container">
        <p class="description"><?php esc_html_e('Upload and manage photos for the project gallery. Drag images to reorder them.', 'work'); ?></p>
        
        <div class="work-gallery-images" id="work-gallery-images">
            <?php
            if (!empty($gallery_images)) {
                foreach ($gallery_images as $image_id) {
                    $image = wp_get_attachment_image_src($image_id, 'thumbnail');
                    if ($image) {
                        ?>
                        <div class="work-gallery-image" data-id="<?php echo esc_attr($image_id); ?>">
                            <img src="<?php echo esc_url($image[0]); ?>" alt="">
                            <input type="hidden" name="work_gallery_images[]" value="<?php echo esc_attr($image_id); ?>">
                            <a href="#" class="work-remove-image"><?php esc_html_e('Remove', 'work'); ?></a>
                        </div>
                        <?php
                    }
                }
            }
            ?>
        </div>
        
        <div class="work-gallery-actions">
            <input type="button" class="button" id="work-add-images" value="<?php esc_attr_e('Add Photos', 'work'); ?>">
        </div>
    </div>
    
    <style>
        .work-gallery-container {
            margin: 15px 0;
        }
        .work-gallery-images {
            display: flex;
            flex-wrap: wrap;
            margin: 0 -5px;
            min-height: 30px;
        }
        .work-gallery-image {
            position: relative;
            width: 150px;
            height: 150px;
            margin: 5px;
            border: 1px solid #ddd;
            overflow: hidden;
            cursor: move;
        }
        .work-gallery-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .work-remove-image {
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
        .work-gallery-image:hover .work-remove-image {
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
        $('#work-gallery-images').sortable({
            items: '.work-gallery-image',
            cursor: 'move',
            placeholder: 'ui-sortable-placeholder',
            update: function() {
                // You can add logic here if needed on sort update
            }
        });
        
        // Media uploader
        var file_frame;
        
        $('#work-add-images').on('click', function(e) {
            e.preventDefault();
            
            // If the media frame already exists, reopen it
            if (file_frame) {
                file_frame.open();
                return;
            }
            
            // Create the media frame
            file_frame = wp.media.frames.file_frame = wp.media({
                title: '<?php esc_html_e('Select Photos for Gallery', 'work'); ?>',
                button: {
                    text: '<?php esc_html_e('Add to Gallery', 'work'); ?>'
                },
                multiple: true
            });
            
            // When images are selected, add them to the gallery
            file_frame.on('select', function() {
                var attachments = file_frame.state().get('selection').toJSON();
                
                $.each(attachments, function(index, attachment) {
                    $('#work-gallery-images').append(
                        '<div class="work-gallery-image" data-id="' + attachment.id + '">' +
                        '<img src="' + (attachment.sizes.thumbnail ? attachment.sizes.thumbnail.url : attachment.url) + '" alt="">' +
                        '<input type="hidden" name="work_gallery_images[]" value="' + attachment.id + '">' +
                        '<a href="#" class="work-remove-image"><?php esc_html_e('Remove', 'work'); ?></a>' +
                        '</div>'
                    );
                });
            });
            
            // Open the media uploader
            file_frame.open();
        });
        
        // Remove image
        $(document).on('click', '.work-remove-image', function(e) {
            e.preventDefault();
            $(this).parent().remove();
        });
    });
    </script>
    <?php
}

/**
 * Save work gallery data
 *
 * @param int $post_id Post ID.
 */
function work_save_gallery_meta_box($post_id) {
    // Check if our nonce is set
    if (!isset($_POST['work_gallery_nonce'])) {
        return;
    }
    
    // Verify that the nonce is valid
    if (!wp_verify_nonce($_POST['work_gallery_nonce'], 'work_gallery_nonce')) {
        return;
    }
    
    // If this is an autosave, our form has not been submitted, so we don't want to do anything
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    // Check the user's permissions
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    
    // Save gallery images
    if (isset($_POST['work_gallery_images']) && is_array($_POST['work_gallery_images'])) {
        $gallery_images = array_map('intval', $_POST['work_gallery_images']);
        update_post_meta($post_id, '_work_gallery_images', $gallery_images);
        
        // Regenerate attachment metadata to ensure proper srcset data
        foreach ($gallery_images as $image_id) {
            if ($image_id > 0) {
                // Get the attachment file
                $file = get_attached_file($image_id);
                if ($file && file_exists($file)) {
                    // Regenerate attachment metadata to ensure proper srcset data
                    require_once(ABSPATH . 'wp-admin/includes/image.php');
                    $metadata = wp_generate_attachment_metadata($image_id, $file);
                    wp_update_attachment_metadata($image_id, $metadata);
                }
            }
        }
    } else {
        // No images selected, so clear the meta
        delete_post_meta($post_id, '_work_gallery_images');
    }
}
add_action('save_post_work', 'work_save_gallery_meta_box');

/**
 * Enqueue the required admin scripts for the gallery
 */
function work_admin_enqueue_scripts($hook) {
    global $post;
    
    // Only enqueue on post edit screens for the 'work' post type
    if (($hook == 'post.php' || $hook == 'post-new.php') && $post && $post->post_type === 'work') {
        wp_enqueue_media();
        wp_enqueue_script('jquery-ui-sortable');
    }
}
add_action('admin_enqueue_scripts', 'work_admin_enqueue_scripts'); 