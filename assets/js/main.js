/**
 * BarPro Premium - Main JavaScript
 * @package BarPro_Premium
 */

(function($) {
    'use strict';
    
    // DOM Ready
    $(document).ready(function() {
        
        // Mobile Menu — обрабатывается modules/drawer.js (vanilla JS, не требует jQuery)
        // initMobileMenu() удалён во избежание конфликта двух инициализаций
        
        // Exit Intent Popup
        initExitIntentPopup();
        
        // Timed Popup
        initTimedPopup();
        
        // Sticky Bar
        initStickyBar();
        
        // Smooth Scroll
        initSmoothScroll();
        
        // Animate on Scroll
        initScrollAnimations();
        
        // Form Submissions
        initFormHandlers();
    });
    
    /**
     * Exit Intent Popup
     */
    function initExitIntentPopup() {
        let popupShown = false;
        
        $(document).on('mouseleave', function(e) {
            if (e.clientY < 10 && !popupShown) {
                showPopup('exit-intent-popup');
                popupShown = true;
                
                // Don't show again for 24 hours
                localStorage.setItem('exitPopupShown', Date.now());
            }
        });
        
        // Check if already shown today
        const lastShown = localStorage.getItem('exitPopupShown');
        if (lastShown && (Date.now() - lastShown) < 86400000) {
            popupShown = true;
        }
    }
    
    /**
     * Timed Popup
     */
    function initTimedPopup() {
        const timedPopupDelay = 30000; // 30 seconds
        
        setTimeout(function() {
            const lastShown = localStorage.getItem('timedPopupShown');
            if (!lastShown || (Date.now() - lastShown) > 86400000) {
                showPopup('timed-popup');
                localStorage.setItem('timedPopupShown', Date.now());
            }
        }, timedPopupDelay);
    }
    
    /**
     * Sticky Bar
     */
    function initStickyBar() {
        const stickyBar = $('.sticky-bar');
        
        setTimeout(function() {
            const lastShown = localStorage.getItem('stickyBarShown');
            if (!lastShown || (Date.now() - lastShown) > 86400000) {
                stickyBar.addClass('active');
            }
        }, 3000);
        
        $('.sticky-bar-close').on('click', function() {
            stickyBar.removeClass('active');
            localStorage.setItem('stickyBarShown', Date.now());
        });
    }
    
    /**
     * Show Popup
     */
    function showPopup(popupId) {
        const popup = $('#' + popupId);
        if (popup.length) {
            popup.addClass('active');
        }
    }
    
    /**
     * Close Popup
     */
    $('.popup-close, .popup-overlay').on('click', function(e) {
        if (e.target === this) {
            $(this).closest('.popup-overlay').removeClass('active');
        }
    });
    
    /**
     * Smooth Scroll
     */
    function initSmoothScroll() {
        $('a[href^="#"]').on('click', function(e) {
            const target = $(this.getAttribute('href'));
            
            if (target.length) {
                e.preventDefault();
                $('html, body').stop().animate({
                    scrollTop: target.offset().top - 100
                }, 1000);
            }
        });
    }
    
    /**
     * Scroll Animations
     */
    function initScrollAnimations() {
        const animateElements = $('.animate-on-scroll');
        
        function checkScroll() {
            animateElements.each(function() {
                const elementTop = $(this).offset().top;
                const windowBottom = $(window).scrollTop() + $(window).height();
                
                if (elementTop < windowBottom - 100) {
                    $(this).addClass('animated');
                }
            });
        }
        
        $(window).on('scroll', checkScroll);
        checkScroll(); // Initial check
    }
    
    /**
     * Form Handlers
     */
    function initFormHandlers() {
        
        // Generic AJAX form submission
        $('.ajax-form').on('submit', function(e) {
            e.preventDefault();
            
            const form = $(this);
            const submitBtn = form.find('button[type="submit"]');
            const originalText = submitBtn.text();
            
            // Disable button
            submitBtn.prop('disabled', true).html('<span class="spinner"></span>');
            
            $.ajax({
                url: barproAjax.ajaxurl,
                type: 'POST',
                data: form.serialize() + '&nonce=' + barproAjax.nonce,
                success: function(response) {
                    if (response.success) {
                        showNotification('success', response.data.message || 'Успешно отправлено!');
                        form[0].reset();
                    } else {
                        showNotification('error', response.data.message || 'Ошибка отправки');
                    }
                },
                error: function() {
                    showNotification('error', 'Произошла ошибка. Попробуйте позже.');
                },
                complete: function() {
                    submitBtn.prop('disabled', false).text(originalText);
                }
            });
        });
        
        // Popup form submissions
        $('.popup-form').on('submit', function(e) {
            e.preventDefault();
            
            const form = $(this);
            const email = form.find('input[type="email"]').val();
            
            $.ajax({
                url: barproAjax.ajaxurl,
                type: 'POST',
                data: {
                    action: 'save_lead',
                    nonce: barproAjax.nonce,
                    email: email,
                    source: form.data('source') || 'popup'
                },
                success: function(response) {
                    if (response.success) {
                        form.closest('.popup-overlay').removeClass('active');
                        showNotification('success', 'Спасибо! Мы свяжемся с вами в ближайшее время.');
                    }
                }
            });
        });
    }
    
    /**
     * Show Notification
     */
    function showNotification(type, message) {
        const notification = $('<div class="notification notification-' + type + '">' + message + '</div>');
        
        $('body').append(notification);
        
        setTimeout(function() {
            notification.addClass('show');
        }, 100);
        
        setTimeout(function() {
            notification.removeClass('show');
            setTimeout(function() {
                notification.remove();
            }, 300);
        }, 3000);
    }
    
    /**
     * Initialize
     */
    if ( typeof WP_DEBUG !== 'undefined' && WP_DEBUG ) {
        console.log('BarPro Premium Theme Loaded');
    }

})(jQuery);

// Notification styles moved to premium.css
