<?php
/**
 * Template Name: Bits Test Page
 * The template for testing custom Bits gallery layouts
 *
 * This template provides examples of accessing individual images
 * from the Bits metabox for complex custom layouts.
 *
 * @package kaneism
 */

get_header(); ?>

<main id="main" class="site-main bits-test-page" role="main">
    <?php the_title( '<h2 class="sizes-XLG">', '</h2>' ); ?>
    
    <?php
    while ( have_posts() ) :
        the_post();

        do_action( 'kaneism_page_before' );
        ?>
        
        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
            <div class="entry-content">
                <?php the_content(); ?>
                
                <h2>Standard Photo Grid</h2>
                <p>This is the standard photo grid using the template part:</p>
                
                <?php 
                // Display the photo grid using our helper function
                kaneism_display_photo_grid();
                ?>
                
                <h2>Example 1: Accessing Individual Images</h2>
                
                <?php
                // Get the bits gallery images
                $gallery_images = get_post_meta(get_the_ID(), '_kaneism_bits_images', true);

                if (!empty($gallery_images)) {
                    // Access a specific image by index (0 is the first image, 1 is the second, etc.)
                    $first_image_id = $gallery_images[0]; // First image
                    $second_image_id = isset($gallery_images[1]) ? $gallery_images[1] : false; // Second image (if exists)
                    
                    // Display a specific image (the first one in this example)
                    if ($first_image_id) {
                        $full_image_url = wp_get_attachment_image_url($first_image_id, 'full');
                        $large_image_url = wp_get_attachment_image_url($first_image_id, 'large');
                        $medium_image_url = wp_get_attachment_image_url($first_image_id, 'medium');
                        $image_alt = get_post_meta($first_image_id, '_wp_attachment_image_alt', true);
                        if (empty($image_alt)) {
                            $image_alt = get_the_title();
                        }
                        
                        // Get image metadata
                        $image_meta = wp_get_attachment_metadata($first_image_id);
                        $image_width = isset($image_meta['width']) ? $image_meta['width'] : '';
                        $image_height = isset($image_meta['height']) ? $image_meta['height'] : '';
                        
                        // Get image caption
                        $image_post = get_post($first_image_id);
                        $image_caption = $image_post->post_excerpt;
                        
                        // Display the image in any custom HTML structure you need
                        echo '<div class="featured-bit">';
                        echo '<h3>Featured Image (First Image)</h3>';
                        echo '<img src="' . esc_url($large_image_url) . '" alt="' . esc_attr($image_alt) . '" class="custom-bit-image">';
                        if (!empty($image_caption)) {
                            echo '<div class="bit-caption">' . esc_html($image_caption) . '</div>';
                        }
                        echo '<p class="image-meta">Image dimensions: ' . esc_html($image_width) . 'x' . esc_html($image_height) . '</p>';
                        echo '</div>';
                    }
                    
                    // Show a section with the second image if available
                    if ($second_image_id) {
                        echo '<div class="secondary-bit">';
                        echo '<h3>Secondary Image (Second Image)</h3>';
                        echo wp_get_attachment_image($second_image_id, 'medium', false, array('class' => 'secondary-bit-image'));
                        echo '</div>';
                    }
                }
                ?>
                
                <h2>Example 2: Displaying Specific Range of Images</h2>
                
                <?php
                if (!empty($gallery_images) && count($gallery_images) > 2) {
                    // Display images 3 through 6 (if they exist)
                    echo '<div class="special-bits-section">';
                    echo '<h3>Images 3-6</h3>';
                    echo '<div class="bits-range">';
                    for ($i = 2; $i <= 5; $i++) { // Remember arrays start at 0, so 2 is the 3rd image
                        if (isset($gallery_images[$i])) {
                            $img_id = $gallery_images[$i];
                            
                            echo '<div class="special-bit-item">';
                            echo wp_get_attachment_image($img_id, 'medium', false, array('class' => 'special-bit-image'));
                            echo '</div>';
                        }
                    }
                    echo '</div>';
                    echo '</div>';
                }
                ?>
                
                <h2>Example 3: Complex Layout</h2>
                
                <?php if (!empty($gallery_images)): ?>
                <div class="complex-ui-layout">
                    <!-- Hero image area -->
                    <div class="hero-area">
                        <h3>Hero Area (First Image)</h3>
                        <?php 
                        if (isset($gallery_images[0])) {
                            echo wp_get_attachment_image($gallery_images[0], 'full', false, array('class' => 'hero-image'));
                        }
                        ?>
                    </div>
                    
                    <!-- Sidebar images -->
                    <div class="sidebar-images">
                        <h3>Sidebar Images (2-4)</h3>
                        <?php
                        for ($i = 1; $i <= 3; $i++) {
                            if (isset($gallery_images[$i])) {
                                echo '<div class="sidebar-image">';
                                echo wp_get_attachment_image($gallery_images[$i], 'medium', false, array('class' => 'sidebar-img'));
                                echo '</div>';
                            }
                        }
                        ?>
                    </div>
                    
                    <!-- Bottom strip -->
                    <div class="bottom-strip">
                        <h3>Bottom Strip (Images 5-8)</h3>
                        <div class="strip-container">
                        <?php
                        for ($i = 4; $i <= 7; $i++) {
                            if (isset($gallery_images[$i])) {
                                echo '<div class="strip-image">';
                                echo wp_get_attachment_image($gallery_images[$i], 'thumbnail', false, array('class' => 'strip-img'));
                                echo '</div>';
                            }
                        }
                        ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                
                <h2>Example 4: Accessing Image Metadata & Different Sizes</h2>
                
                <?php
                if (!empty($gallery_images) && isset($gallery_images[0])) {
                    $image_id = $gallery_images[0];
                    
                    echo '<div class="image-metadata-demo">';
                    echo '<h3>Image Metadata and Size Variations</h3>';
                    
                    // Get attachment data
                    $attachment = get_post($image_id);
                    
                    // Get different size URLs
                    $full_url = wp_get_attachment_image_url($image_id, 'full');
                    $large_url = wp_get_attachment_image_url($image_id, 'large');
                    $medium_url = wp_get_attachment_image_url($image_id, 'medium');
                    $thumbnail_url = wp_get_attachment_image_url($image_id, 'thumbnail');
                    
                    // Get metadata
                    $alt_text = get_post_meta($image_id, '_wp_attachment_image_alt', true);
                    $caption = $attachment->post_excerpt;
                    $description = $attachment->post_content;
                    
                    // Display metadata
                    echo '<div class="metadata-section">';
                    echo '<p><strong>Title:</strong> ' . esc_html($attachment->post_title) . '</p>';
                    echo '<p><strong>Alt Text:</strong> ' . esc_html($alt_text) . '</p>';
                    echo '<p><strong>Caption:</strong> ' . esc_html($caption) . '</p>';
                    echo '<p><strong>Description:</strong> ' . esc_html($description) . '</p>';
                    echo '</div>';
                    
                    // Display different sizes
                    echo '<div class="image-sizes-section">';
                    echo '<h4>Available Image Sizes:</h4>';
                    
                    echo '<div class="size-example">';
                    echo '<h5>Thumbnail</h5>';
                    echo '<img src="' . esc_url($thumbnail_url) . '" alt="Thumbnail" />';
                    echo '</div>';
                    
                    echo '<div class="size-example">';
                    echo '<h5>Medium</h5>';
                    echo '<img src="' . esc_url($medium_url) . '" alt="Medium" />';
                    echo '</div>';
                    
                    echo '</div>';
                    
                    echo '</div>';
                }
                ?>
            </div><!-- .entry-content -->
        </article><!-- #post-## -->
        
        <?php
        /**
         * Functions hooked in to kaneism_page_after action
         *
         * @hooked kaneism_display_comments - 10
         */
        do_action( 'kaneism_page_after' );

    endwhile; // End of the loop.
    ?>

</main><!-- #main -->

<style>
    /* Styling for Example 1 */
    .featured-bit, .secondary-bit {
        margin-bottom: 40px;
        border: 1px solid #eee;
        padding: 20px;
        background: #f9f9f9;
    }
    
    .featured-bit img, .secondary-bit img {
        max-width: 100%;
        height: auto;
        display: block;
    }
    
    .bit-caption {
        margin-top: 10px;
        font-style: italic;
    }
    
    /* Styling for Example 2 */
    .special-bits-section {
        margin-bottom: 40px;
    }
    
    .bits-range {
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
    }
    
    .special-bit-item {
        flex: 0 0 calc(50% - 15px);
    }
    
    .special-bit-item img {
        max-width: 100%;
        height: auto;
        display: block;
    }
    
    /* Styling for Example 3 */
    .complex-ui-layout {
        display: grid;
        grid-template-columns: 2fr 1fr;
        grid-template-areas:
            "hero sidebar"
            "strip strip";
        gap: 20px;
        margin-bottom: 40px;
        border: 1px solid #ddd;
        padding: 20px;
        background: #f5f5f5;
    }
    
    .hero-area {
        grid-area: hero;
    }
    
    .sidebar-images {
        grid-area: sidebar;
    }
    
    .bottom-strip {
        grid-area: strip;
    }
    
    .hero-image {
        width: 100%;
        height: auto;
        display: block;
    }
    
    .sidebar-images .sidebar-image {
        margin-bottom: 15px;
    }
    
    .sidebar-img {
        width: 100%;
        height: auto;
        display: block;
    }
    
    .strip-container {
        display: flex;
        justify-content: space-between;
    }
    
    .strip-image {
        flex: 0 0 calc(25% - 10px);
    }
    
    .strip-img {
        width: 100%;
        height: auto;
        display: block;
    }
    
    /* Styling for Example 4 */
    .image-metadata-demo {
        border: 1px solid #ddd;
        padding: 20px;
        margin-bottom: 40px;
        background: #f9f9f9;
    }
    
    .metadata-section {
        margin-bottom: 20px;
    }
    
    .image-sizes-section {
        display: flex;
        gap: 20px;
    }
    
    .size-example {
        text-align: center;
    }
    
    @media (max-width: 767px) {
        .complex-ui-layout {
            grid-template-columns: 1fr;
            grid-template-areas:
                "hero"
                "sidebar"
                "strip";
        }
        
        .image-sizes-section {
            flex-direction: column;
        }
        
        .special-bit-item {
            flex: 0 0 100%;
        }
        
        .strip-container {
            flex-wrap: wrap;
            gap: 10px;
        }
        
        .strip-image {
            flex: 0 0 calc(50% - 5px);
            margin-bottom: 10px;
        }
    }
</style>

<?php
get_footer(); 