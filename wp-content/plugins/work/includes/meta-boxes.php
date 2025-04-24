<?php
/**
 * Custom Meta Boxes
 *
 * @package Work
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

/**
 * Register meta boxes for the Work post type
 */
function work_register_meta_boxes() {
    // Gallery meta box
    add_meta_box(
        'work_gallery',
        __('Project Gallery', 'work'),
        'work_gallery_meta_box_callback',
        'work',
        'normal',
        'high'
    );
    
    // Featured meta box
    add_meta_box(
        'work_featured',
        __('Featured Status', 'work'),
        'work_featured_meta_box_callback',
        'work',
        'side',
        'high'
    );
    
    // Project Details meta box
    add_meta_box(
        'work_project_details',
        __('Project Details', 'work'),
        'work_project_details_meta_box_callback',
        'work',
        'normal',
        'high'
    );
    
    // Schema.org meta box
    add_meta_box(
        'work_schema',
        __('SEO Schema Data', 'work'),
        'work_schema_meta_box_callback',
        'work',
        'normal',
        'default'
    );
}
add_action('add_meta_boxes', 'work_register_meta_boxes');

/**
 * Gallery meta box callback
 *
 * @param WP_Post $post Current post object.
 */
function work_gallery_meta_box_callback($post) {
    // Add nonce for security
    wp_nonce_field('work_gallery_nonce', 'work_gallery_nonce');
    
    // Get saved gallery images
    $gallery_images = get_post_meta($post->ID, '_work_gallery_images', true);
    
    ?>
    <div class="work-gallery-container">
        <div class="work-gallery-images" id="work-gallery-images">
            <?php
            if (!empty($gallery_images)) {
                foreach ($gallery_images as $image_id) {
                    $image = wp_get_attachment_image_src($image_id, 'thumbnail');
                    if ($image) {
                        ?>
                        <div class="work-gallery-image">
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
            <input type="button" class="button" id="work-add-images" value="<?php esc_attr_e('Add Images', 'work'); ?>">
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
        }
        .work-gallery-image {
            position: relative;
            width: 150px;
            height: 150px;
            margin: 5px;
            border: 1px solid #ddd;
            overflow: hidden;
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
    </style>
    
    <script>
    jQuery(document).ready(function($) {
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
                title: '<?php esc_html_e('Select Images', 'work'); ?>',
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
                        '<div class="work-gallery-image">' +
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
 * Featured meta box callback
 *
 * @param WP_Post $post Current post object.
 */
function work_featured_meta_box_callback($post) {
    // Add nonce for security
    wp_nonce_field('work_featured_nonce', 'work_featured_nonce');
    
    // Get saved value
    $is_featured = get_post_meta($post->ID, '_work_is_featured', true);
    
    ?>
    <div class="work-featured-container">
        <label for="work_is_featured">
            <input type="checkbox" id="work_is_featured" name="work_is_featured" value="1" <?php checked($is_featured, '1'); ?> />
            <?php esc_html_e('Feature this work item on archive pages', 'work'); ?>
        </label>
        <p class="description"><?php esc_html_e('Featured items will be displayed prominently at the top of archive pages.', 'work'); ?></p>
    </div>
    <?php
}

/**
 * Project Details meta box callback
 *
 * @param WP_Post $post Current post object.
 */
function work_project_details_meta_box_callback($post) {
    // Add nonce for security
    wp_nonce_field('work_project_details_nonce', 'work_project_details_nonce');
    
    // Get saved details
    $project_details = get_post_meta($post->ID, '_work_project_details', true);
    if (!is_array($project_details)) {
        $project_details = array();
    }
    
    // Define default fields (can be extended)
    $default_fields = array(
        'type' => __('Type', 'work'),
        'location' => __('Location', 'work'),
        'materials' => __('Materials', 'work'),
        'size' => __('Size', 'work'),
        'date' => __('Date', 'work'),
        'schedule' => __('Schedule', 'work')
    );
    
    // Filter for adding custom fields
    $fields = apply_filters('work_project_detail_fields', $default_fields);
    
    ?>
    <div class="work-details-container">
        <p class="description"><?php esc_html_e('Enter project details to be displayed in the details table.', 'work'); ?></p>
        
        <table class="form-table">
            <tbody>
                <?php foreach ($fields as $field_id => $field_label) : ?>
                    <tr>
                        <th scope="row">
                            <label for="work_detail_<?php echo esc_attr($field_id); ?>"><?php echo esc_html($field_label); ?></label>
                        </th>
                        <td>
                            <input 
                                type="text" 
                                id="work_detail_<?php echo esc_attr($field_id); ?>" 
                                name="work_project_details[<?php echo esc_attr($field_id); ?>]" 
                                value="<?php echo esc_attr(isset($project_details[$field_id]) ? $project_details[$field_id] : ''); ?>" 
                                class="regular-text"
                            />
                        </td>
                    </tr>
                <?php endforeach; ?>
                
                <!-- Dynamic Field Section -->
                <tr class="work-dynamic-fields">
                    <th colspan="2">
                        <h3><?php esc_html_e('Additional Details', 'work'); ?></h3>
                        <p class="description"><?php esc_html_e('Add custom detail fields for this project.', 'work'); ?></p>
                    </th>
                </tr>
                
                <!-- Container for dynamic fields -->
                <tr>
                    <td colspan="2">
                        <div id="work-custom-fields">
                            <?php 
                            // Display existing custom fields
                            if (isset($project_details['custom_fields']) && is_array($project_details['custom_fields'])) {
                                foreach ($project_details['custom_fields'] as $index => $custom_field) {
                                    if (isset($custom_field['label']) && isset($custom_field['value'])) {
                                        ?>
                                        <div class="work-custom-field">
                                            <input 
                                                type="text" 
                                                name="work_project_details[custom_fields][<?php echo $index; ?>][label]" 
                                                value="<?php echo esc_attr($custom_field['label']); ?>" 
                                                placeholder="<?php esc_attr_e('Label', 'work'); ?>" 
                                                class="medium-text"
                                            />
                                            <input 
                                                type="text" 
                                                name="work_project_details[custom_fields][<?php echo $index; ?>][value]" 
                                                value="<?php echo esc_attr($custom_field['value']); ?>" 
                                                placeholder="<?php esc_attr_e('Value', 'work'); ?>" 
                                                class="medium-text"
                                            />
                                            <button type="button" class="button work-remove-field"><?php esc_html_e('Remove', 'work'); ?></button>
                                        </div>
                                        <?php
                                    }
                                }
                            }
                            ?>
                        </div>
                        <button type="button" class="button" id="work-add-field"><?php esc_html_e('Add Field', 'work'); ?></button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    
    <script>
    jQuery(document).ready(function($) {
        // Add new field
        $('#work-add-field').on('click', function() {
            var index = $('#work-custom-fields .work-custom-field').length;
            var newField = $(
                '<div class="work-custom-field">' +
                '<input type="text" name="work_project_details[custom_fields][' + index + '][label]" placeholder="<?php esc_attr_e('Label', 'work'); ?>" class="medium-text" />' +
                '<input type="text" name="work_project_details[custom_fields][' + index + '][value]" placeholder="<?php esc_attr_e('Value', 'work'); ?>" class="medium-text" />' +
                '<button type="button" class="button work-remove-field"><?php esc_html_e('Remove', 'work'); ?></button>' +
                '</div>'
            );
            $('#work-custom-fields').append(newField);
        });
        
        // Remove field
        $(document).on('click', '.work-remove-field', function() {
            $(this).parent().remove();
        });
    });
    </script>
    
    <style>
        .work-custom-field {
            margin-bottom: 10px;
        }
        .work-custom-field input {
            margin-right: 10px;
        }
    </style>
    <?php
}

/**
 * Schema.org meta box callback
 *
 * @param WP_Post $post Current post object.
 */
function work_schema_meta_box_callback($post) {
    // Add nonce for security
    wp_nonce_field('work_schema_nonce', 'work_schema_nonce');
    
    // Get saved schema data
    $schema_data = get_post_meta($post->ID, '_work_schema_data', true);
    if (!is_array($schema_data)) {
        $schema_data = array();
    }
    
    // Define schema fields
    $schema_fields = array(
        'type' => array(
            'label' => __('Type', 'work'),
            'default' => 'VisualArtwork',
            'description' => __('The type of artwork (usually VisualArtwork)', 'work')
        ),
        'desc' => array(
            'label' => __('Description', 'work'),
            'default' => '',
            'description' => __('Detailed description for SEO (defaults to excerpt if empty)', 'work')
        ),
        'location' => array(
            'label' => __('Location', 'work'),
            'default' => '',
            'description' => __('Where the artwork is located', 'work')
        ),
        'medium' => array(
            'label' => __('Medium', 'work'),
            'default' => '',
            'description' => __('The materials used in creating the artwork', 'work')
        ),
        'surface' => array(
            'label' => __('Surface', 'work'),
            'default' => '',
            'description' => __('The surface the artwork appears on', 'work')
        ),
        'width' => array(
            'label' => __('Width', 'work'),
            'default' => '',
            'description' => __('Width with unit (e.g., 90 ft)', 'work')
        ),
        'height' => array(
            'label' => __('Height', 'work'),
            'default' => '',
            'description' => __('Height with unit (e.g., 38 ft)', 'work')
        ),
        'depth' => array(
            'label' => __('Depth', 'work'),
            'default' => '0',
            'description' => __('Depth with unit (defaults to 0)', 'work')
        ),
        'date' => array(
            'label' => __('Date Created', 'work'),
            'default' => '',
            'description' => __('Year or date the artwork was created', 'work')
        )
    );
    
    ?>
    <div class="work-schema-container">
        <p class="description"><?php esc_html_e('Enter schema.org structured data for SEO. These values may be different from the display values in the Project Details section.', 'work'); ?></p>
        
        <table class="form-table">
            <tbody>
                <?php foreach ($schema_fields as $field_id => $field) : ?>
                    <tr>
                        <th scope="row">
                            <label for="work_schema_<?php echo esc_attr($field_id); ?>"><?php echo esc_html($field['label']); ?></label>
                        </th>
                        <td>
                            <input 
                                type="text" 
                                id="work_schema_<?php echo esc_attr($field_id); ?>" 
                                name="work_schema_data[<?php echo esc_attr($field_id); ?>]" 
                                value="<?php echo esc_attr(isset($schema_data[$field_id]) ? $schema_data[$field_id] : $field['default']); ?>" 
                                class="regular-text"
                            />
                            <p class="description"><?php echo esc_html($field['description']); ?></p>
                        </td>
                    </tr>
                <?php endforeach; ?>
                
                <tr>
                    <th scope="row">
                        <?php esc_html_e('Auto-populate from details', 'work'); ?>
                    </th>
                    <td>
                        <button type="button" class="button" id="work-populate-schema"><?php esc_html_e('Populate from Project Details', 'work'); ?></button>
                        <p class="description"><?php esc_html_e('Fill in schema fields based on the project details you entered above.', 'work'); ?></p>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    
    <script>
    jQuery(document).ready(function($) {
        // Populate schema fields from project details
        $('#work-populate-schema').on('click', function() {
            var mappings = {
                'work_detail_type': 'work_schema_type',
                'work_detail_location': 'work_schema_location',
                'work_detail_materials': 'work_schema_medium',
                'work_detail_size': '',  // Handle specially
                'work_detail_date': 'work_schema_date'
            };
            
            // Copy values
            $.each(mappings, function(sourceId, targetId) {
                if (targetId && $('#' + sourceId).length) {
                    $('#' + targetId).val($('#' + sourceId).val());
                }
            });
            
            // Handle size specially to break into dimensions
            var size = $('#work_detail_size').val();
            if (size) {
                // Try to parse width and height
                var match = size.match(/(\d+[.\d]*\s*(?:ft|in|cm|m)?)(?:\s*x\s*)(\d+[.\d]*\s*(?:ft|in|cm|m)?)(?:\s*x\s*(\d+[.\d]*\s*(?:ft|in|cm|m)?))?/i);
                if (match) {
                    if (match[1]) $('#work_schema_width').val(match[1]);
                    if (match[2]) $('#work_schema_height').val(match[2]);
                    if (match[3]) $('#work_schema_depth').val(match[3]);
                } else {
                    // Just use the whole value as surface
                    $('#work_schema_surface').val(size);
                }
            }
            
            // Use excerpt for description if empty
            if (!$('#work_schema_desc').val()) {
                var excerpt = $('textarea[id^="excerpt"]').val();
                if (excerpt) {
                    $('#work_schema_desc').val(excerpt);
                }
            }
        });
    });
    </script>
    <?php
}

/**
 * Save meta box data
 *
 * @param int $post_id Post ID.
 */
function work_save_meta_boxes($post_id) {
    // Save gallery images
    if (isset($_POST['work_gallery_nonce']) && wp_verify_nonce($_POST['work_gallery_nonce'], 'work_gallery_nonce')) {
        // If this is an autosave, don't do anything
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        
        // Check user permissions
        if (isset($_POST['post_type']) && 'work' === $_POST['post_type']) {
            if (!current_user_can('edit_page', $post_id)) {
                return;
            }
        } else {
            if (!current_user_can('edit_post', $post_id)) {
                return;
            }
        }
        
        // Save gallery images
        if (isset($_POST['work_gallery_images'])) {
            $gallery_images = array_map('intval', $_POST['work_gallery_images']);
            update_post_meta($post_id, '_work_gallery_images', $gallery_images);
        } else {
            delete_post_meta($post_id, '_work_gallery_images');
        }
    }
    
    // Save featured status
    if (isset($_POST['work_featured_nonce']) && wp_verify_nonce($_POST['work_featured_nonce'], 'work_featured_nonce')) {
        // If this is an autosave, don't do anything
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        
        // Check user permissions
        if (isset($_POST['post_type']) && 'work' === $_POST['post_type']) {
            if (!current_user_can('edit_page', $post_id)) {
                return;
            }
        } else {
            if (!current_user_can('edit_post', $post_id)) {
                return;
            }
        }
        
        // Save featured status
        $is_featured = isset($_POST['work_is_featured']) ? '1' : '0';
        update_post_meta($post_id, '_work_is_featured', $is_featured);
    }
    
    // Save project details
    if (isset($_POST['work_project_details_nonce']) && wp_verify_nonce($_POST['work_project_details_nonce'], 'work_project_details_nonce')) {
        // If this is an autosave, don't do anything
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        
        // Check user permissions
        if (isset($_POST['post_type']) && 'work' === $_POST['post_type']) {
            if (!current_user_can('edit_page', $post_id)) {
                return;
            }
        } else {
            if (!current_user_can('edit_post', $post_id)) {
                return;
            }
        }
        
        // Sanitize and save project details
        if (isset($_POST['work_project_details']) && is_array($_POST['work_project_details'])) {
            $project_details = array();
            
            // Sanitize standard fields
            foreach ($_POST['work_project_details'] as $key => $value) {
                if ($key === 'custom_fields') {
                    // Handle custom fields separately
                    continue;
                }
                $project_details[$key] = sanitize_text_field($value);
            }
            
            // Sanitize custom fields
            if (isset($_POST['work_project_details']['custom_fields']) && is_array($_POST['work_project_details']['custom_fields'])) {
                $project_details['custom_fields'] = array();
                foreach ($_POST['work_project_details']['custom_fields'] as $index => $field) {
                    if (!empty($field['label']) || !empty($field['value'])) {
                        $project_details['custom_fields'][] = array(
                            'label' => sanitize_text_field($field['label']),
                            'value' => sanitize_text_field($field['value'])
                        );
                    }
                }
            }
            
            update_post_meta($post_id, '_work_project_details', $project_details);
        }
    }
    
    // Save schema data
    if (isset($_POST['work_schema_nonce']) && wp_verify_nonce($_POST['work_schema_nonce'], 'work_schema_nonce')) {
        // If this is an autosave, don't do anything
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        
        // Check user permissions
        if (isset($_POST['post_type']) && 'work' === $_POST['post_type']) {
            if (!current_user_can('edit_page', $post_id)) {
                return;
            }
        } else {
            if (!current_user_can('edit_post', $post_id)) {
                return;
            }
        }
        
        // Sanitize and save schema data
        if (isset($_POST['work_schema_data']) && is_array($_POST['work_schema_data'])) {
            $schema_data = array();
            
            foreach ($_POST['work_schema_data'] as $key => $value) {
                $schema_data[$key] = sanitize_text_field($value);
            }
            
            update_post_meta($post_id, '_work_schema_data', $schema_data);
        }
    }
}
add_action('save_post', 'work_save_meta_boxes'); 