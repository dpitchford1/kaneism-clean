<?php
/**
 * Template part for displaying photo grid content
 *
 * @package Kaneism
 */

// Get the post ID to display grid for, or use current post
global $kaneism_photo_grid_post_id;
$post_id = $kaneism_photo_grid_post_id ? $kaneism_photo_grid_post_id : get_the_ID();

// Get the photo grid images using our helper function
$images = kaneism_get_photo_grid_images($post_id);

// Only display the grid if we have images
if (!empty($images)) : ?>
    <div class="photo-grid">
        <?php foreach ($images as $index => $image) : ?>
            <div class="photo-grid-item">
                <?php 
                // Use wp_get_attachment_image to get srcset with all default WordPress classes
                // The key is to call this function directly, not wrap it in other functions
                echo wp_get_attachment_image(
                    $image['id'],
                    'large',
                    false,
                    array(
                        'class' => 'photo-grid-image',
                        'alt' => $image['alt'],
                        'loading' => ($index === 0) ? 'eager' : 'lazy'
                    )
                );
                ?>
                <?php if (!empty($image['caption'])) : ?>
                <div class="photo-grid-caption"><?php echo esc_html($image['caption']); ?></div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?> 