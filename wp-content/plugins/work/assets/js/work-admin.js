/**
 * Work Admin JavaScript
 * 
 * Handles admin-specific functionality for the Work plugin
 */
(function($) {
    'use strict';
    
    /**
     * Initialize admin functionality
     */
    function initWorkAdmin() {
        // Toggle featured status
        $('.work-toggle-featured').on('click', function(e) {
            e.preventDefault();
            
            const $this = $(this);
            const postId = $this.data('post-id');
            const nonce = $this.data('nonce');
            const $icon = $this.find('.dashicons');
            
            // Show loading state
            $icon.css('opacity', '0.5');
            
            // Send AJAX request
            $.ajax({
                url: workAdminVars.ajaxurl,
                type: 'POST',
                data: {
                    action: 'work_toggle_featured',
                    post_id: postId,
                    nonce: workAdminVars.toggleFeaturedNonce
                },
                success: function(response) {
                    if (response.success) {
                        // Update icon based on new status
                        if (response.data.new_status === '1') {
                            $icon.removeClass('dashicons-star-empty').addClass('dashicons-star-filled');
                            $icon.css('color', '#ffb900');
                            $icon.attr('title', 'Featured - Click to remove');
                        } else {
                            $icon.removeClass('dashicons-star-filled').addClass('dashicons-star-empty');
                            $icon.css('color', '#ccc');
                            $icon.attr('title', 'Not Featured - Click to feature');
                        }
                        
                        // Show success message
                        showNotice(workAdminVars.toggleFeaturedSuccess, 'success');
                    } else {
                        // Show error message
                        showNotice(workAdminVars.toggleFeaturedError, 'error');
                    }
                },
                error: function() {
                    // Show error message
                    showNotice(workAdminVars.toggleFeaturedError, 'error');
                },
                complete: function() {
                    // Reset loading state
                    $icon.css('opacity', '1');
                }
            });
        });
    }
    
    /**
     * Show admin notice
     * 
     * @param {string} message The message to display
     * @param {string} type The notice type (success, error)
     */
    function showNotice(message, type) {
        // Remove any existing notices
        $('.work-admin-notice').remove();
        
        // Create notice
        const $notice = $('<div class="work-admin-notice notice notice-' + type + ' is-dismissible"><p>' + message + '</p></div>');
        
        // Add notice to the top of the page
        $('#wpbody-content').prepend($notice);
        
        // Add dismiss button
        $notice.append('<button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>');
        
        // Handle dismiss
        $notice.find('.notice-dismiss').on('click', function() {
            $notice.fadeOut(300, function() {
                $(this).remove();
            });
        });
        
        // Auto dismiss after 3 seconds
        setTimeout(function() {
            $notice.fadeOut(300, function() {
                $(this).remove();
            });
        }, 3000);
    }
    
    // Initialize when DOM is ready
    $(document).ready(function() {
        initWorkAdmin();
    });
    
})(jQuery); 