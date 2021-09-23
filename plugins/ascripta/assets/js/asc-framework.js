/**
 * Ascripta: Framework
 * 
 * The following scripts apply to the site front-end.
 *
 * @version     1.4.11
 * @package     AE/Assets/JS
 * @author      Ascripta
 */

(function($) {
    'use strict';

    /* ========================================================================
     * Core
     * ======================================================================== */

    var Ascripta = class {

        constructor(options) {

            // Declare the default settings.
            this.settings = $.extend({
                header: {
                    element: $('.header'),
                    fixed: {
                        enabled: false,
                        front: true
                    },
                    fold: {
                        enabled: false,
                        front: true
                    },
                    dropdowns: {
                        enabled: true,
                        front: true,
                        ghost: false
                    }
                },
                footer: {
                    element: $('.footer'),
                    sticky: {
                        enabled: true
                    }
                },
                modules: {
                    carousels: true,
                    animations: true,
                    jumpers: {
                        enabled: true,
                        offset: 0
                    },
                    svg: {
                        enabled: true,
                        inline: true
                    }
                }
            }, options);

            // Initialize the class.
            this.Initialize();

        }

    };

    /* ========================================================================
     * Header
     * 
     * Enhance the header functionality.
     * ======================================================================== */

    Ascripta.prototype.Header = function() {

        // Fetch the header settings.
        var header = this.settings.header;

        // Provide support for disabling the header extras on the front page.
        if ($('body').hasClass('home') && header.front === false) {
            header.enabled = false;
        }

        // Run the header extras.
        if (header.enabled !== false && header.element !== null) {

            /*
             * Dropdowns
             *
             * Implement extra dropdown functionality.
             */

            // Provide support for disabling the header fixed functionality on the front page.
            if ($('body').hasClass('home') && header.dropdowns.front === false) {
                header.dropdowns.enabled = false;
            }

            // Run the dropdown functionality.
            if (header.dropdowns === true || header.dropdowns.enabled === true) {

                if (header.dropdowns.ghost === true) {

                    // Click through the items.
                    $(window).on('load resize', function() {
                        header.element.find('[data-toggle="dropdown"]').click(function() {
                            if ($(window).width() >= 768) {
                                location.href = $(this).attr('href');
                                return false;
                            }
                        });
                    });

                } else {

                    // Depth behaviour for dropdown menus on click.
                    header.element.find('.dropdown > .dropdown-toggle').on('click', function() {

                        // Close the sibling dropdowns.
                        $(this).parent().siblings('.dropdown').removeClass('open');

                        // Toggle the parent dropdown if closed.
                        $(this).parent().parents('.dropdown:not(".open")').addClass('open');

                        // Toggle the clicked dropdown.
                        $(this).parent().toggleClass('open');

                        return false;

                    });

                    // Remove the dropdown menu siblings on hover.
                    header.element.find('.dropdown').on('mouseenter', function() {
                        $(this).siblings('.dropdown').removeClass('open');
                    });

                }

            }

            /*
             * Fixed
             *
             * Make the header fixed to the top of the window.
             */

            // Provide support for disabling the header fixed functionality on the front page.
            if ($('body').hasClass('home') && header.fixed.front === false) {
                header.fixed.enabled = false;
            }

            // Run the header fixed functionality.
            if (header.fixed === true || header.fixed.enabled === true) {

                // Add the element class.
                header.element.addClass('fixed');

                // Adjust the DOM accordingly.
                $(window).resize($.fn.throttle(function() {
                    $('body').css('padding-top', header.element.css('top', $('#wpadminbar').height()).height());
                }));

                /*
                 * Folding
                 *
                 * Make the header fold when scrolling down and
                 * return when scrolling up.
                 */

                // Provide support for disabling the header fixed functionality on the front page.
                if ($('body').hasClass('home') && header.fold.front === false) {
                    header.fold.enabled = false;
                }

                // Run the header fold functionality.
                if (header.fold === true || header.fold.enabled === true) {

                    // Add the element class.
                    header.element.attr('data-folding', 'in');

                    // Handle the scroll.
                    var scroll, previous = 0,
                        offset;

                    $(window).scroll($.fn.throttle(function() {

                        if (!$('.navbar-collapse').hasClass('in')) {

                            scroll = $(window).scrollTop();

                            if ($(window).width >= 768) {
                                offset = header.element.height() * 2;
                            } else {
                                offset = header.element.height();
                            }

                            if (scroll > offset) {
                                if (scroll < previous) {
                                    header.element.attr('data-folding', 'in');
                                } else {
                                    header.element.attr('data-folding', 'out');
                                }
                            } else {
                                header.element.attr('data-folding', 'in');
                            }

                            previous = scroll;

                        }

                    }, 75));

                }

            }

        }

    };

    /* ========================================================================
     * Footer
     * 
     * Enhance the footer functionality.
     * ======================================================================== */

    Ascripta.prototype.Footer = function() {

        // Fetch the header settings.
        var footer = this.settings.footer;

        // Check if the element exists.
        if (footer.element !== null) {

            /**
             * Sticky
             *
             * Put the footer at the bottom of the viewport on
             * windows larger than the document content.
             */
            if (footer.sticky === true || footer.sticky.enabled === true) {

                // Add the sticky class.
                footer.element.addClass('sticky');

                // Adjust the html element.
                $('html').css({
                    'position': 'relative',
                    'min-height': '100%'
                });

                // Set the body to min-width: 100%.
                $('body').css('min-height', '100%');

                // Adjust the body bottom margin.
                $(window).resize($.fn.throttle(function() {
                    $('body').css('margin-bottom', footer.element.outerHeight(true));
                }));

            }

        }

    };

    /* ========================================================================
     * Backgrounds
     * 
     * Create the background image and color wrappers.
     * ======================================================================== */

    Ascripta.prototype.Backgrounds = function() {

        /**
         * Background Image
         *
         * Set the background image for the element.
         */
        if ($('[data-background-image]').length) {
            $('[data-background-image]').each(function() {
                $(this).css('background-image', 'url(' + $(this).data('background-image') + ')');
            });
        }

        /**
         * Background Color
         *
         * Set the background color for the element.
         */
        if ($('[data-background-color]').length) {
            $('[data-background-color]').each(function() {
                $(this).css('background-color', $(this).data('background-color'));
            });
        }

    };

    /* ========================================================================
     * Jumpers
     * 
     * Allow smooth scroll on the page or on external pages.
     * ======================================================================== */

    Ascripta.prototype.Jumpers = function() {

        // Fetch the module settings.
        var modules = this.settings.modules;

        // Check if the module is enabled.
        if (modules.jumpers.enabled === true && ($('[data-jumper]').length || $('.jumper').length)) {

            // Make the links scroll.
            $('[data-jumper], a.jumper, li.jumper > a').on('click', function() {
                if (location.pathname.replace(/^\//, '') === this.pathname.replace(/^\//, '') && location.hostname === this.hostname) {
                    var target = $(this.hash);
                    target = target.length ? target : $('[name=' + this.hash.slice(1) + ']');
                    if (target.length) {
                        $.fn.jump(target, modules.jumpers.offset);
                        return false;
                    }
                }
            });

            // Make the document scroll if accessed using hash.
            if (location.hash) {
                setTimeout(function() {
                    window.scrollTo(0, 0);
                }, 0);
                $.fn.jump($(location.hash), modules.jumpers.offset);
            }

        }

    };

    /* ========================================================================
     * Vectors
     * 
     * Add support for SVG assets.
     * ======================================================================== */

    Ascripta.prototype.Vectors = function() {

        // Fetch the module settings.
        var modules = this.settings.modules;

        // Check if the module is enabled.
        if (typeof modules.svg !== 'undefined' && modules.svg.enabled === true && modules.svg.inline === true && $('img.svg').length) {

            // Process the SVG assets.
            $('img.svg').each(function(index) {

                // Store the element object.
                var element = {
                    obj: $(this)
                };

                // Declare the element attributes.
                element.id = element.obj.attr('id');
                element.url = element.obj.attr('src');
                element.className = element.obj.attr('class');

                // Check if the element is a SVG.
                if (!element.url.endsWith('svg')) {
                    return;
                }

                // Get the asset and manipulate its data.
                $.get(element.url, function(data) {

                    // Get the SVG tag, ignore the rest
                    element.svg = $(data).find('svg');
                    element.svgID = element.svg.attr('id');

                    // Add replaced image's ID to the new SVG if necessary
                    if (typeof element.id === 'undefined') {
                        if (typeof element.svgID === 'undefined') {
                            element.id = 'svg-replaced-' + index;
                            element.svg = element.svg.attr('id', element.id);
                        } else {
                            element.id = element.svgID;
                        }
                    } else {
                        element.svg = element.svg.attr('id', element.id);
                    }

                    // Enhance the SVG for accessibility.
                    element.svg.attr('role', 'image');
                    element.svg.attr('aria-label', element.obj.attr('alt'));

                    // Add replaced image's classes to the new SVG
                    if (typeof element.className !== 'undefined') {
                        element.svg.attr('class', element.className);
                    }

                    // Replace image with new SVG
                    element.obj.replaceWith(element.svg.removeAttr('xmlns:a'));

                    $(document).trigger('svg.loaded', [element.id]);

                }, 'xml');
            });

        }

    };

    /* ========================================================================
     * Enhancements
     * 
     * Perform various operations to enhance the website.
     * ======================================================================== */

    Ascripta.prototype.Enhancements = function() {

        // Fetch the module settings.
        var modules = this.settings.modules;

        // Instantiate and fade in the carousel components.
        if (modules.carousels === true && $('[data-slick]').length) {
            $('[data-slick]').slick();
        }

        // Prevent empty search form submission.
        $('.search-form').each(function() {
            $(this).submit(function(e) {
                var s = $(this).find("#s");
                if (!s.val()) {
                    e.preventDefault();
                    s.focus();
                }
            });
        });

    };

    /* ========================================================================
     * Animations
     * 
     * Handle animations when the element is in view.
     * ======================================================================== */

    Ascripta.prototype.Animations = class {

        constructor(modules) {

            // Check if the module is enabled.
            if (modules.animations === true && $('[data-animation]').length) {

                // Prepare elements for animation.
                $('[data-animation]').each((index, element) => {
                    $(element).addClass('invisible animated');
                });

                // Add support for slider animations.
                if ($('.slick-slider').length) {
                    this.sliders('.slick-slider');
                }

                // Add support for scroll animations.
                this.scroller();

            }

        }

        /**
         * Trigger the element animation.
         */
        trigger(element) {
            if (element.is('[data-animation-delay]')) {
                setTimeout(() => {
                    element.removeClass('invisible').addClass(element.data('animation'));
                }, Number.parseInt($(this).data('animation-delay')));
            } else {
                element.removeClass('invisible').addClass(element.data('animation'));
            }
        }

        /**
         * Reset the animation state for an element.
         */
        revert(element) {
            element.addClass('invisible').removeClass(element.data('animation'));
        }

        /**
         * Add support for Slick.js animations.
         */
        sliders(slider) {

            // Setup the slider animations.
            $(slider).each((index, slide) => {

                // Replace the default data attribute.
                $(slide).find('[data-animation]').each((index, element) => {
                    $(element).attr('data-slick-animation', $(element).data('animation')).removeAttr('data-animation');
                });

                // Trigger the animations on slide change.
                $(slide).on('afterChange', (event, slider, index) => {

                    // Animate out the previous slide elements.
                    $(slide).find('[data-slick-animation]').each((index, element) => {
                        this.revert($(element));
                    });

                    // Animate in the current slide elements.
                    $(slide).find('[data-slick-index="' + index + '"] [data-slick-animation]').each((index, element) => {
                        this.trigger($(element));
                    });

                });

            });

            // Bind the scroll actions.
            $(window).scroll($.fn.throttle(() => {
                $(slider).each((index, element) => {
                    if ($(element).visible()) {
                        $(element).find('.slick-current [data-slick-animation]').each((index, element) => {
                            this.trigger($(element));
                        });
                    }
                });
            }));

        }

        /**
         * Declare the scroll event.
         */
        scroller() {

            $(window).scroll($.fn.throttle(() => {
                $('[data-animation]').each((index, element) => {
                    if ($(element).visible()) {
                        this.trigger($(element));
                    } else {
                        if ($(element).data('animation-infinite') === true) {
                            this.revert($(element));
                        }
                    }
                });
            }));

        }

    };

    /* ========================================================================
     * Initialize
     * 
     * Bind together all the class methods.
     * ======================================================================== */

    Ascripta.prototype.Initialize = function() {

        // Call the methods.
        this.Header();
        this.Footer();
        this.Backgrounds();
        this.Jumpers();
        this.Vectors();
        this.Enhancements();

        // Call the subclasses.
        new this.Animations(this.settings.modules);

        // Force an instance of a resize and a scroll event on page load.
        $(window).trigger('resize').trigger('scroll');

    };

    /* ========================================================================
     * Functions
     * 
     * Various helper functions used across the framework.
     * ======================================================================== */

    /**
     * Throttle
     *
     * Used to add performance to continuous code executions.
     */
    $.fn.throttle = function(no_trailing, delay, callback, debounce_mode) {

        var timeout_id, last_exec = 0;

        if (typeof no_trailing !== 'boolean') {
            debounce_mode = callback;
            callback = no_trailing;
            no_trailing = undefined;
        }

        function wrapper() {
            var that = this,
                elapsed = +new Date() - last_exec,
                args = arguments;

            function exec() {
                last_exec = +new Date();
                callback.apply(that, args);
            };

            function clear() {
                timeout_id = undefined;
            };

            if (debounce_mode && !timeout_id) {
                exec();
            }

            timeout_id && clearTimeout(timeout_id);

            if (debounce_mode === undefined && elapsed > delay) {
                exec();
            } else if (no_trailing !== true) {
                timeout_id = setTimeout(debounce_mode ? clear : exec, debounce_mode === undefined ? delay - elapsed : delay);
            }
        };

        if ($.guid) {
            wrapper.guid = callback.guid = callback.guid || $.guid++;
        }

        return wrapper;
    };

    /**
     * Jump
     *
     * Helper function used to scroll to a DOM element.
     */
    $.fn.jump = function(target, offset) {
        $('html,body').animate({
            scrollTop: target.offset().top - offset - $('#wpadminbar').height()
        }, 400);
    };

    /**
     * Visible
     *
     * Helper function that checks if an element is in the viewport.
     */
    $.fn.visible = function() {
        var win = $(window);

        var viewport = {
            top: win.scrollTop(),
            left: win.scrollLeft()
        };
        viewport.right = viewport.left + win.width();
        viewport.bottom = viewport.top + win.height();

        var bounds = this.offset();
        bounds.right = bounds.left + this.outerWidth();
        bounds.bottom = bounds.top + this.outerHeight();

        return (!(viewport.right < bounds.left || viewport.left > bounds.right || viewport.bottom < bounds.top || viewport.top > bounds.bottom));
    };

    /* ========================================================================
     * Instance
     * 
     * Create the new class instance when the function is called.
     * ======================================================================== */

    $.fn.ascripta = function(options) {

        new Ascripta(options);

    };

}(jQuery));
