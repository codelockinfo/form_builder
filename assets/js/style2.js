$(document).ready(function () {
    // code for create new form start
    $("#myBtn_new").click(function () {
        $("#myModal_new").css("display", "block");
    });
    $(".close_new").click(function () {
        $("#myModal_new").css("display", "none");
    });
    $(".close2_new").click(function () {
        $("#myModal_new").css("display", "none");
    });
    // code for create new form end
    $(".main_list_").click(function () {
        $(".first_txt_image").removeClass("first_txt_image");
        $(this).find(".text_image_list").addClass("first_txt_image");
        var getval = $(this).data("val");
        var formname = $(this).find(".text_image_list").html();
        $(".selectedType").val(getval);
        $(".formnamehide").val(formname);
    });
    // mobile desktop icon box-shadow
    $(".view_icon li ").click(function () {
        $(".view_icon li ").removeClass("active");
        $(this).addClass("active");
        $(".preview-box").removeClass("mobile");
        $(".preview-box").removeClass("desktop");
        $(".preview-box").addClass($(this).data("id"));
    });

    $(".settingselect li .settingsbtn ").click(function () {
        $(".settingsbtn").removeClass("Polaris-Tabs__Tab--selected");
        $(this).addClass("Polaris-Tabs__Tab--selected");
    });

    $(".settingselect li").click(function () {
        var tabID = $(this).attr("data-tab");
        $("#tab-" + tabID).addClass("active").siblings().removeClass("active");
    });

    $("#required_login").change(function () {
        if (this.checked) {
            $(".required_message").removeClass("hidden");
        } else {
            $(".required_message").addClass("hidden");
        }
    });
    $(document).on("change", ".confirmpass", function () {
        if (this.checked) {
            $(".conpass").removeClass("hidden");
        } else {
            $(".conpass").addClass("hidden");
        }
    });
    $('.selectval').hide();
    //show the first tab content
    $('#embedCode').show();

    var input = $('.quentity');
    $('.plus').on('click', function () {
        var inputValue = input.val();
        input.val(parseInt(inputValue) + 1);
    });

    $('.min').on('click', function () {
        var inputValue = input.val();
        input.val(parseInt(inputValue) - 1);
    });

    var input = $('.hoursadd');
    $('.hoursadd').on('click', function () {
        var inputValue = input.val();
        input.val(parseInt(inputValue) + 1);
    });
    $('.houminus').on('click', function () {
        var inputValue = input.val();
        input.val(parseInt(inputValue) - 1);

    });

    var input = $('.weekadd');
    $('.weekplus').on('click', function () {
        var inputValue = input.val();
        input.val(parseInt(inputValue) + 1);
    });

    $('.weekminus').on('click', function () {
        var inputValue = input.val();
        input.val(parseInt(inputValue) - 1);
    });

    // Function to initialize owl carousel (make it globally accessible)
    window.initOwlCarousel = function () {
        var $carousel = $('.owl-carousel');
        if ($carousel.length > 0) {

            // Clear slide mapping cache when reinitializing
            slideMappingCache = null;

            // Destroy existing instance completely and restore original structure
            var existingInstance = $carousel.data('owl.carousel');
            if (existingInstance) {
                try {
                    $carousel.trigger('destroy.owl.carousel');
                    $carousel.removeData('owl.carousel');
                } catch (e) {
                }

                // Manually unwrap owl carousel structure to restore original slides
                var $stageOuter = $carousel.find('.owl-stage-outer');
                if ($stageOuter.length > 0) {
                    var $stage = $stageOuter.find('.owl-stage');
                    if ($stage.length > 0) {
                        // Unwrap owl-items back to original divs
                        $stage.find('.owl-item').each(function () {
                            var $item = $(this);
                            var $content = $item.children();
                            if ($content.length > 0) {
                                $item.replaceWith($content);
                            } else {
                                $item.remove();
                            }
                        });
                        // Unwrap owl-stage - move its children to stage-outer
                        $stage.children().each(function () {
                            $(this).appendTo($stageOuter);
                        });
                        $stage.remove();
                    }
                    // Unwrap owl-stage-outer - move its children to carousel
                    $stageOuter.children().each(function () {
                        $(this).appendTo($carousel);
                    });
                    $stageOuter.remove();
                }

                // Remove owl carousel classes and elements
                $carousel.removeClass('owl-carousel owl-loaded owl-drag owl-grab owl-rtl');
                $carousel.find('.owl-nav, .owl-dots').remove();

                // Small delay to ensure DOM is updated
                setTimeout(function () {
                }, 50);
            }

            // Now count the ACTUAL slide divs (should be direct children)
            // After destroy, slides should be direct children again
            // First, let's check the raw HTML to see what's actually there
            var carouselHTML = $carousel[0].innerHTML;
            var polarisCount = (carouselHTML.match(/polarisformcontrol/g) || []).length;

            // Also check the actual DOM structure using native methods
            var nativeChildren = $carousel[0].children;
            for (var i = 0; i < nativeChildren.length; i++) {
                var child = nativeChildren[i];
                var classes = child.className || '(no class)';
            }

            var $actualSlides = $carousel.children().filter(function () {
                var $child = $(this);
                // Count divs that are actual slides (not owl carousel elements)
                return !$child.hasClass('owl-stage-outer') &&
                    !$child.hasClass('owl-nav') &&
                    !$child.hasClass('owl-dots') &&
                    !$child.hasClass('owl-stage') &&
                    !$child.hasClass('owl-item') &&
                    $child.is('div');
            });

            var slideCount = $actualSlides.length;

            // Log all actual slides with full details
            $actualSlides.each(function (index) {
                var $slide = $(this);
                var classes = $slide.attr('class') || '(no class)';
                var id = $slide.attr('id') || '';
                var isVisible = $slide.is(':visible');
                var display = $slide.css('display');
                var computedStyle = window.getComputedStyle($slide[0]);

                // Force visibility for hidden slides (owl carousel might skip them)
                if (!isVisible || display === 'none' || computedStyle.display === 'none') {
                    $slide.css('display', 'block');
                }
            });

            // Also log ALL children for debugging
            $carousel.children().each(function (index) {
                var $child = $(this);
                var tag = $child.prop('tagName');
                var classes = $child.attr('class') || '(no class)';
                var htmlPreview = $child.html().substring(0, 100).replace(/\s+/g, ' ');
            });

            // Check if slides exist elsewhere in the DOM (maybe they're siblings but not direct children yet)
            var $allPotentialSlides = $('.polarisformcontrol.headerData, .polarisformcontrol:has(.setvalue_element), .polarisformcontrol:has(.footerData), .polarisformcontrol');


            // Find slides that should be in the carousel but aren't direct children
            var $missingSlides = $allPotentialSlides.filter(function () {
                var $slide = $(this);
                var parent = $slide.parent();
                var isDirectChild = parent[0] === $carousel[0];
                if (!isDirectChild) {
                    var parentClass = parent.attr('class') || 'no-class';
                    return true;
                }
                return false;
            });

            if ($missingSlides.length > 0) {
            }

            if (slideCount === 0) {
                return;
            }

            // If we found less than 4 slides, try to find missing slides elsewhere and move them
            if (slideCount < 4) {
                // Get ALL polarisformcontrol divs in the entire document
                var $allPolarisSlides = $('.polarisformcontrol');

                // Check which ones are direct children vs not
                var movedCount = 0;
                $allPolarisSlides.each(function () {
                    var $slide = $(this);
                    var parent = $slide.parent();
                    var isDirectChild = parent[0] === $carousel[0];

                    if (!isDirectChild) {
                        var slideType = '';
                        if ($slide.hasClass('headerData')) slideType = 'Header';
                        else if ($slide.find('.footerData').length > 0) slideType = 'Footer';
                        else if ($slide.find('.setvalue_element').length > 0) slideType = 'Add element';
                        else slideType = 'Other';

                        $slide.appendTo($carousel);
                        movedCount++;
                        slideCount++;
                    }
                });

                // Also check for the first empty div (main panel) - it might be the only one found
                // Look for other empty divs or divs with specific content that should be slides
                var $allDivs = $carousel.parent().find('> .owl-carousel > div, > div').not('.owl-stage-outer, .owl-nav, .owl-dots');

                if (movedCount > 0) {
                    // Recount after moving
                    $actualSlides = $carousel.children().filter(function () {
                        var $child = $(this);
                        return !$child.hasClass('owl-stage-outer') &&
                            !$child.hasClass('owl-nav') &&
                            !$child.hasClass('owl-dots') &&
                            !$child.hasClass('owl-stage') &&
                            !$child.hasClass('owl-item') &&
                            $child.is('div');
                    });
                    slideCount = $actualSlides.length;
                } else {
                    // Check if slides are nested inside the first div
                    var $firstSlide = $carousel.children().first();
                    if ($firstSlide.length > 0) {
                        var $nestedSlides = $firstSlide.find('> .polarisformcontrol, > div.polarisformcontrol');
                        if ($nestedSlides.length > 0) {
                            $nestedSlides.each(function () {
                                $(this).appendTo($carousel);
                                slideCount++;
                            });
                        }
                    }
                }

                if (slideCount < 4) {
                }
            }

            // Ensure carousel has the owl-carousel class
            $carousel.addClass('owl-carousel');

            // Initialize carousel
            try {
                $carousel.owlCarousel({
                    items: 1,
                    loop: false,
                    margin: 10,
                    nav: false,
                    mouseDrag: false,
                    onInitialized: function (event) {
                        var instance = event.target;
                        var $instance = $(instance);
                        var itemCount = $instance.find('.owl-item').length;

                        // Detect slide mapping after initialization
                        slideMappingCache = detectSlideMapping($instance);

                        // Log all slides
                        $instance.find('.owl-item').each(function (index) {
                            var $item = $(this);
                            var $content = $item.children().first();
                            var classes = $content.attr('class') || '';
                            var title = $content.find('.title').text().trim();
                        });
                    }
                });

                // Verify initialization after a delay
                setTimeout(function () {
                    var instance = $carousel.data('owl.carousel');
                    if (instance) {
                        var itemCount = instance.items().length;
                        if (itemCount <= 1) {
                        } else {

                        }
                    } else {
                    }
                }, 200);
            } catch (e) {
            }

        } else {
        }
    }

    // Function to check if all slides are present before initializing
    function checkAndInitCarousel() {
        var $carousel = $('.owl-carousel');
        if ($carousel.length === 0) {

            return false;
        }

        // Check if carousel is already initialized
        var existingInstance = $carousel.data('owl.carousel');
        var slideCount = 0;
        var $actualSlides;

        if (existingInstance) {
            // Carousel already initialized - unwrap to check original slides
            var $stageOuter = $carousel.find('.owl-stage-outer');
            if ($stageOuter.length > 0) {
                // Get slides from owl-items
                $actualSlides = $stageOuter.find('.owl-item > div');
                slideCount = $actualSlides.length;
            } else {
                // No owl-stage-outer, count direct children
                $actualSlides = $carousel.children().filter(function () {
                    var $child = $(this);
                    return !$child.hasClass('owl-stage-outer') &&
                        !$child.hasClass('owl-nav') &&
                        !$child.hasClass('owl-dots') &&
                        $child.is('div');
                });
                slideCount = $actualSlides.length;
            }
        } else {
            // Carousel not initialized - count direct children
            $actualSlides = $carousel.children().filter(function () {
                var $child = $(this);
                return !$child.hasClass('owl-stage-outer') &&
                    !$child.hasClass('owl-nav') &&
                    !$child.hasClass('owl-dots') &&
                    $child.is('div');
            });
            slideCount = $actualSlides.length;
        }



        // We expect at least 4 slides: main tab panel, header, add element, footer
        if (slideCount >= 4) {
            // If already initialized but has wrong count, destroy first
            if (existingInstance && slideCount !== existingInstance.items().length) {
                try {
                    $carousel.trigger('destroy.owl.carousel');
                    // Unwrap structure
                    var $so = $carousel.find('.owl-stage-outer');
                    if ($so.length > 0) {
                        $so.find('.owl-item').each(function () {
                            $(this).children().unwrap();
                        });
                        $so.find('.owl-stage').children().unwrap();
                        $so.children().unwrap();
                    }
                    $carousel.removeClass('owl-carousel owl-loaded');
                } catch (e) {
                }
            }
            initOwlCarousel();
            return true;
        } else {
            return false;
        }
    }

    // Initialize owl carousel on page load - wait for DOM to be fully ready
    $(window).on('load', function () {
        setTimeout(function () {
            if (!checkAndInitCarousel()) {
                // Retry a few times if slides aren't ready
                var retries = 0;
                var checkInterval = setInterval(function () {
                    retries++;
                    if (checkAndInitCarousel() || retries >= 10) {
                        clearInterval(checkInterval);
                        if (retries >= 10) {
                            initOwlCarousel();
                        }
                    }
                }, 200);
            }
        }, 500);
    });

    // Also try to initialize after DOM ready (in case window.load already fired)
    setTimeout(function () {
        var $carousel = $('.owl-carousel');
        if ($carousel.length > 0) {
            var instance = $carousel.data('owl.carousel');
            if (!instance) {
                if (!checkAndInitCarousel()) {
                    // Retry
                    setTimeout(function () {
                        checkAndInitCarousel();
                    }, 500);
                }
            } else {
            }
        }
    }, 1000);

    $(document).on("click", ".settingselect .Polaris-Tabs__TabContainer,.Polaris-Tabs__Panel .list-item", function () {
        var slideTo = $(this).data("owl");
        if (slideTo !== undefined && slideTo !== null) {
            $('.owl-carousel').trigger('to.owl.carousel', [slideTo, 40, true]);
        }
    });

    // Function to detect slide type by content and create mapping
    function detectSlideMapping($carousel) {
        var mapping = {};

        // Get slides - try both ways (if carousel initialized or not)
        var $slides;
        var $stageOuter = $carousel.find('.owl-stage-outer');
        if ($stageOuter.length > 0) {
            // Carousel initialized - get from owl-items
            $slides = $stageOuter.find('.owl-item > div');
        } else {
            // Carousel not initialized - get direct children
            $slides = $carousel.children('div').not('.owl-stage-outer, .owl-nav, .owl-dots, .owl-stage');
        }


        $slides.each(function (index) {
            var $slide = $(this);
            var slideType = 'unknown';
            var title = $slide.find('.title').text().trim();
            var hasHeaderData = $slide.hasClass('headerData') || $slide.find('.headerData').length > 0;
            var hasFooterData = $slide.find('.footerData').length > 0;
            var hasSetValueElement = $slide.find('.setvalue_element').length > 0;
            // Check if this slide has elementAppend class (element edit panel)
            var hasElementAppend = $slide.hasClass('elementAppend') || $slide.find('.elementAppend').length > 0 || $slide.is('.elementAppend');

            // Detect slide type by content (check in order of specificity)
            if (hasHeaderData || title === 'Header') {
                slideType = 'header';
                mapping[2] = index; // data-owl="2" maps to Header
            } else if (hasElementAppend) {
                // This is the element edit panel (shows when clicking on an element)
                // It's the slide with class "elementAppend" - this is where element properties are edited
                slideType = 'element-edit';
                mapping[3] = index; // data-owl="3" maps to Element edit panel
            } else if (title === 'Add element') {
                // Check for the correct Add element slide - it should have:
                // 1. nested toggle class
                // 2. setvalue_element containers (even if empty)
                // 3. builder-item-wrapper with subheading "Input", "Selects", etc.
                var hasNestedToggle = $slide.find('.nested.toggle').length > 0;
                var hasSetValueContainers = $slide.find('.setvalue_element, .setvalue_element_select, .setvalue_element_static, .setvalue_element_structure, .setvalue_element_customization').length > 0;
                var hasBuilderItems = $slide.find('.builder-item-wrapper .subheading').length > 0;
                var hasInputSubheading = $slide.find('.subheading').text().indexOf('Input') !== -1;

                // The correct Add element panel MUST have nested toggle AND (setvalue containers OR builder items with Input subheading)
                if (hasNestedToggle && (hasSetValueContainers || (hasBuilderItems && hasInputSubheading))) {
                    // This is the correct Add element panel
                    slideType = 'add-element';
                    mapping[6] = index; // data-owl="6" maps to Add element
                } else {
                    // This is the other Add element panel (element edit panel) - don't map it
                    slideType = 'add-element-edit';

                }
            } else if (hasFooterData || title === 'Footer') {
                slideType = 'footer';
                mapping[7] = index; // data-owl="7" maps to Footer

            } else if (title === 'Other page') {
                slideType = 'other-page';

            } else if (title === 'Mail') {
                slideType = 'mail';
                mapping[8] = index;

            } else if (title === 'Appearance') {
                slideType = 'appearance';
                mapping[9] = index;

            } else if (title === 'Theme Settings' || $slide.find('.theme-settings-content').length > 0) {
                slideType = 'theme-settings';
                mapping[15] = index;

            } else if (index === 0 || title === '' || $slide.find('.Polaris-Tabs__Wrapper').length > 0) {
                slideType = 'main-panel';
                mapping[0] = index;
                mapping[1] = index;

            } else {

            }
        });


        return mapping;
    }

    // Mapping function to convert data-owl values to actual slide indices
    var slideMappingCache = null;
    function mapDataOwlToSlideIndex(dataOwl, $carousel) {
        // Detect mapping if not cached
        if (!slideMappingCache && $carousel && $carousel.length > 0) {
            slideMappingCache = detectSlideMapping($carousel);

        }

        // Use cached mapping or fallback
        if (slideMappingCache && slideMappingCache[dataOwl] !== undefined) {
            var mappedIndex = slideMappingCache[dataOwl];

            return mappedIndex;
        }


        // Fallback mapping based on expected structure
        var fallbackMapping = {
            0: 0, 1: 0, 2: 1, 3: 2, 4: 3, 5: 4, 6: 5, 7: 6,
            8: 7, 9: 8, 10: 9, 11: 10, 12: 11, 13: 12, 15: 14
        };
        var fallbackIndex = fallbackMapping[dataOwl] !== undefined ? fallbackMapping[dataOwl] : dataOwl;

        return fallbackIndex;
    }

    // Function to ensure carousel is initialized and navigate to a slide
    // Make it globally accessible
    window.navigateToSlide = function (slideTo) {
        if (slideTo === undefined || slideTo === null) {
            return false;
        }



        var $carousel = $('.owl-carousel');
        if ($carousel.length === 0) {

            return false;
        }

        // Map data-owl value to actual slide index
        var actualSlideIndex = mapDataOwlToSlideIndex(slideTo, $carousel);


        // Check carousel state
        var carouselInstance = $carousel.data('owl.carousel');

        // Always verify and correct mapping for Add element (data-owl="6") BEFORE navigation
        if (slideTo === 6 && carouselInstance) {


            // Get items - carouselInstance.items() returns an array, need to wrap in jQuery
            var items = carouselInstance.items();
            var $mappedSlide = $(items[actualSlideIndex]);
            var slideTitle = '';
            var slideHasNestedToggle = false;

            if ($mappedSlide.length > 0) {
                var $slideContent = $mappedSlide.children().first();
                slideTitle = $slideContent.find('.title').text().trim();
                slideHasNestedToggle = $slideContent.find('.nested.toggle').length > 0;

            }

            var isCorrectSlide = slideTitle === 'Add element' && slideHasNestedToggle;
            if (!isCorrectSlide) {

                var foundCorrectSlide = false;

                // Iterate through all items
                for (var idx = 0; idx < items.length; idx++) {
                    var $item = $(items[idx]);
                    var $content = $item.children().first();
                    var title = $content.find('.title').text().trim();
                    var hasToggle = $content.find('.nested.toggle').length > 0;
                    var hasSetValue = $content.find('.setvalue_element').length > 0;
                    var hasBuilderItems = $content.find('.builder-item-wrapper .subheading').length > 0;
                    var hasInputSubheading = $content.find('.subheading').text().indexOf('Input') !== -1;


                    // The correct Add element slide must have: title="Add element" AND nested toggle AND (setvalue OR builder items with Input)
                    if (title === 'Add element' && hasToggle && (hasSetValue || (hasBuilderItems && hasInputSubheading))) {

                        actualSlideIndex = idx;
                        foundCorrectSlide = true;
                        // Update cache immediately
                        if (slideMappingCache) {
                            slideMappingCache[6] = idx;

                        }
                        break; // Exit loop
                    }
                }

                if (!foundCorrectSlide) {

                } else {

                }
            } else {

            }

        }

        slideTo = actualSlideIndex;

        // $carousel and carouselInstance already defined above
        if ($carousel.length > 0) {
            // carouselInstance already checked above

            // Count actual slide divs (not owl carousel wrapper elements)
            var $actualSlides = $carousel.children().filter(function () {
                var $child = $(this);
                return !$child.hasClass('owl-stage-outer') &&
                    !$child.hasClass('owl-nav') &&
                    !$child.hasClass('owl-dots') &&
                    $child.is('div');
            });
            var actualSlideCount = $actualSlides.length;

            // If carousel is initialized, check owl-items
            var owlItemCount = 0;
            if (carouselInstance) {
                owlItemCount = carouselInstance.items().length;
            }

            // If carousel only has 1 slide but DOM has more actual slides, reinitialize
            if (carouselInstance && owlItemCount === 1 && actualSlideCount > 1) {
                initOwlCarousel();
                setTimeout(function () {
                    var newInstance = $carousel.data('owl.carousel');
                    if (newInstance) {
                        var newItemCount = newInstance.items().length;
                        if (newItemCount > 1) {
                            try {
                                $carousel.trigger('to.owl.carousel', [slideTo, 40, true]);
                            } catch (e) {

                            }
                        } else {
                        }
                    } else {
                    }
                }, 400);
                return true;
            }

            // Ensure carousel is initialized
            if (!carouselInstance) {
                initOwlCarousel();
                // Wait for carousel to be ready before navigating
                setTimeout(function () {
                    try {
                        var newInstance = $carousel.data('owl.carousel');
                        if (newInstance) {
                            $carousel.trigger('to.owl.carousel', [slideTo, 40, true]);
                        } else {
                        }
                    } catch (e) {
                    }
                }, 300);
            } else {
                // Carousel is already initialized, navigate immediately
                try {
                    $carousel.trigger('to.owl.carousel', [slideTo, 40, true]);
                } catch (e) {
                }
            }
            return true;
        } else {
            return false;
        }
    }

    // Handle clicks on list-item elements anywhere (for Header, Footer, etc.)
    $(document).on("click", ".list-item[data-owl]", function (e) {
        var slideTo = $(this).data("owl");
        if (navigateToSlide(slideTo)) {
            // Initialize CKEditor when header drawer is opened
            if (slideTo == 2) { // Header drawer
                setTimeout(function () {
                    var $headerEditor = $('.headerData textarea[name="contentheader"]');
                    if ($headerEditor.length > 0) {
                        if (CKEDITOR.instances['contentheader']) {
                            try {
                                CKEDITOR.instances['contentheader'].destroy();
                            } catch (e) {
                            }
                        }
                        initializeCKEditor('contentheader', '.boxed-layout .formHeader .description');
                    } else {

                    }
                }, 500);
            }
            // Initialize CKEditor when footer drawer is opened
            if (slideTo == 4) { // Footer drawer (adjust if needed)
                setTimeout(function () {
                    var $footerEditor = $('.footerData textarea[name="contentfooter"]');
                    if ($footerEditor.length > 0) {
                        if (CKEDITOR.instances['contentfooter']) {
                            try {
                                CKEDITOR.instances['contentfooter'].destroy();
                            } catch (e) {
                            }
                        }
                        initializeCKEditor('contentfooter', '.footer .footer-data__footerdescription');
                    } else {
                    }
                }, 500);
            }
            // Don't prevent default to allow other handlers to work
        }
    });

    // Also listen for carousel slide changes
    $(document).on('changed.owl.carousel', '.owl-carousel', function (event) {
        var currentSlide = event.relatedTarget.currentItem || event.item.index;

        // Check if we're on the header slide (usually index 2)
        setTimeout(function () {
            var $headerSlide = $('.headerData');
            if ($headerSlide.length > 0 && $headerSlide.is(':visible')) {
                var $headerEditor = $('.headerData textarea[name="contentheader"]');
                if ($headerEditor.length > 0 && !CKEDITOR.instances['contentheader']) {
                    initializeCKEditor('contentheader', '.boxed-layout .formHeader .description');
                }
            }

            // Check if we're on the footer slide
            var $footerSlide = $('.footerData');
            if ($footerSlide.length > 0 && $footerSlide.is(':visible')) {
                var $footerEditor = $('.footerData textarea[name="contentfooter"]');
                if ($footerEditor.length > 0 && !CKEDITOR.instances['contentfooter']) {
                    initializeCKEditor('contentfooter', '.footer .footer-data__footerdescription');
                }
            }
        }, 300);
    });

    // Also handle clicks on builder-item-wrapper that contains list-item (for better event capture)
    $(document).on("click", ".builder-item-wrapper", function (e) {
        // Skip if this is the btn_add_element wrapper (handled separately)
        if ($(this).hasClass('btn_add_element')) {
            return;
        }
        var $listItem = $(this).find('.list-item[data-owl]').first();
        if ($listItem.length > 0) {
            var slideTo = $listItem.data("owl");
            if (navigateToSlide(slideTo)) {
                // Don't prevent default to allow other handlers to work
            }
        }
    });

    $(document).on("click", ".backBtn", function () {
        $('.owl-carousel').trigger('to.owl.carousel', [0, 40, true]);
    });

    //footer submit button text change
    $(document).on('keydown, keyup', '#PolarisTextField17', function () {
        var addText = $(this).val();
        $(' .submit.classic-button').html(addText);
    });

    $(document).on("change ", " #PolarisCheckbox13 ", function () {
        if (this.checked) {
            $(".hideLabel").removeClass("hidden");
        } else {
            $(".hideLabel").addClass("hidden");
        }
    });

    $(document).on("change ", " #PolarisCheckbox15 ", function () {
        if (this.checked) {
            $(".required_Content").removeClass("hidden");
        } else {
            $(".required_Content").addClass("hidden");
        }

    });

    $(document).click(".select_ICon", function () {
        $(this).find(" .pickerList").addClass("show");
    });

    $(document).click(".close_icon", function () {
        $(this).find(".pickerList").removeClass("show");
    });

    $(document).on("change ", " #PolarisCheckbox3 ", function () {
        if (this.checked) {
            $(".hideLabel").removeClass("hidden");
        } else {
            $(".hideLabel").addClass("hidden");
        }
    });

    $(document).on("change ", " #PolarisCheckbox5 ", function () {
        if (this.checked) {
            $(".hideRequired").removeClass("hidden");
        } else {
            $(".hideRequired").addClass("hidden");
        }
    });

    $(document).on("click", ".Polaris-Tabs__Panel .list-item", function () {
        setTimeout(function () {
            $('.selectFile').select2();
        }, 100);
    });

    // dropdown select
    i = 1;
    $(document).on("click", "#add", function () {
        i++;
        var textarea_value = $(".mainskill").val();
        if (textarea_value.length >= 1) {
            var inputValue = $('.mainskill').val();
            $('#optionText').append('<div id="main' + i + '" class="addskildy"> <div style="display:flex;margin-bottom: 5px;" > <input type="text" class="mainskill" style="width:85%;" id="Skill' + i + '" name="Skill[' + inputValue + ']" ><button type="button" name="remove" id="Skillremove' + i + '" class="btn_add11" style="width:15%;padding: 10px 20px;">X</button></div>   </div>');
        } else {
        }
        var optionHtml = '';
        $(".mainskill").each(function (index) {
            var optionval = $(this).val();
            optionHtml += "<option>" + optionval + "</option>";
        });
        $('#optionSelect').html(optionHtml);
    });

    $(document).on('click', '.btn_add11', function () {
        var button_id = $(this).attr("id");
        $(this).closest(".addskildy").remove();
    });
});

// today

$(document).ready(function () {

    $(document).on("click", ".Polaris-Tabs__Panel .list-item", function () {
        var elementId = $(this).data("elementid");

        var newId = (elementId - 1);

        var elementId1 = "element" + newId;

        var elementWithDataId = $(".preview-box").find(".g-container .code-form-control[data-id='" + elementId1 + "']");
        elementWithDataId.css("display", "block");


        // Initialize CKEditor for specific fields as needed
        if (typeof initializeCKEditor === 'function') {
            setTimeout(function () {
                var $headerEditor = $('.headerData textarea[name="contentheader"], textarea[name="contentheader"]');
                if ($headerEditor.length > 0 && !CKEDITOR.instances['contentheader']) {
                    initializeCKEditor('contentheader', '.boxed-layout .formHeader .description');
                }

                var $footerEditor = $('.footerData textarea[name="contentfooter"], textarea[name="contentfooter"]');
                if ($footerEditor.length > 0 && !CKEDITOR.instances['contentfooter']) {
                    initializeCKEditor('contentfooter', '.footer  .footer-data__footerdescription');
                }

                // Initialize CKEditor for paragraph
                initializeCKEditor('contentparagraph', '.paragraph-container .paragraph-content');
            }, 500);
        }

    });
});
// width change
$(document).on("click", ".chooseItems .chooseItem-align", function () {
    // Remove active from all alignment buttons in the same container
    $(this).closest(".chooseItems").find('.chooseItem-align').removeClass("active");
    $(this).addClass("active");
    $dataValue = $(this).attr("data-value");

    // Check if this is footer alignment
    $inputFormate = $(this).closest(".form-control").find(".footer-button__alignment");
    if ($inputFormate.length > 0) {
        $inputFormate.val($dataValue);
        $(".forFooterAlign").removeClass("align-left align-center align-right").addClass($dataValue);
    }

    // Check if this is header alignment
    $headerInput = $(this).closest(".form-control").find(".header-text-align-input");
    if ($headerInput.length > 0) {
        $headerInput.val($dataValue);
        // Update preview header alignment
        $(".formHeader").removeClass("align-left align-center align-right").addClass($dataValue);
        $(".formHeader .title, .formHeader .description").css("text-align", $dataValue);
    }
});
$(document).on("click", ".chooseItems .chooseItem-noperline", function () {
    $('.chooseItem-noperline').removeClass("active");
    $(this).addClass("active");
    $dataValue = $(this).attr("data-value");
    $mainContainer = $(this).closest(".container").attr("class");
    var classArray = $mainContainer.split(" ");
    var containerClass = classArray.find(className => className.startsWith("container_"));
    $("." + containerClass).find(".input_no-perline").val($dataValue);
    $(".block-container").find("." + containerClass + " li").removeClass("option-1-column option-2-column option-3-column option-4-column option-5-column").addClass("option-" + $dataValue + "-column");
});
$(document).on("click", ".chooseItems .chooseItem-datetime", function () {
    $('.chooseItem-datetime').removeClass("active");
    $(this).addClass("active");
    $dataValue = $(this).attr("data-value");
    $mainContainer = $(this).closest(".container").attr("class");
    var classArray = $mainContainer.split(" ");
    var containerClass = classArray.find(className => className.startsWith("container_"));
    $("." + containerClass).find(".input_formate").val($dataValue);
    // $(".block-container").find("."+containerClass+" li").removeClass("option-1-column option-2-column option-3-column option-4-column option-5-column").addClass("option-"+$dataValue+"-column");
});
$(document).on("click", ".chooseItems .chooseItem ", function () {
    $('.chooseItem').removeClass("active");
    $(this).addClass("active");
    $dataValue = $(this).attr("data-value");
    $mainContainer = $(this).closest(".container").attr("class");
    var classArray = $mainContainer.split(" ");
    var containerClass = classArray.find(className => className.startsWith("container_"));

    $columnWidth = $(this).closest(".form-control").find("input");
    if ($columnWidth.length > 0) {
        $columnWidth.val($dataValue);
        $(".block-container ." + containerClass).removeClass("layout-1-column layout-2-column layout-3-column").addClass("layout-" + $dataValue + "-column");
    }
    $inputFormate = $(this).closest(".form-control").find(".input_formate");
    if ($inputFormate.length > 0) {
        $inputFormate.val($dataValue);
    }
});

$(document).on('keydown, keyup', '.Polaris-TextField__Input', function () {
    $mainContainerClass = $(this).closest(".container");
    if ($mainContainerClass.length > 0) {
        $classArray = $mainContainerClass.attr("class").split(" ");
        var containerClass = $classArray.find(className => className.startsWith("container_"));
    }
    $mainContainer = $(".block-container");

    $inputVal = $(this).val();
    $attrName = $(this).attr('name');
    $nameExlode = "";
    if ($attrName != undefined) {
        $nameExlode = $attrName.split("__");
    }
    if ($nameExlode[1] == "label") {
        $("." + $attrName).html($inputVal);
    } else if ($nameExlode[1] == "placeholder") {
        if ($nameExlode[0].includes("file")) {
            $("." + $attrName).html($inputVal);
        } else if ($nameExlode[0].includes("country")) {
            $mainContainer.find('.' + containerClass + ' option[value=""]').html($inputVal);
            $mainContainerClass.find('.selectDefaultCountry option[value=""]').html($inputVal);
        } else if ($nameExlode[0].includes("dropdown")) {
            $mainContainer.find('.' + containerClass + ' option[value=""]').html($inputVal);
            // $mainContainerClass.find('.selectDefaultCountry option[value=""]').html($inputVal);
        } else {
            $("." + $attrName).attr('placeholder', $inputVal);
        }
    } else if ($nameExlode[1] == "description") {
        $("." + $attrName).html($inputVal);
    } else if ($nameExlode[1] == "submittext") {
        $("." + $attrName).text($inputVal);
    } else if ($nameExlode[1] == "resetbuttontext") {
        $("." + $attrName).text($inputVal);
    } else if ($nameExlode[1] == "limitcharactervalue") {
        $("." + $nameExlode[0] + "__placeholder").attr("maxlength", $inputVal);
    } else if ($nameExlode[1] == "html-code") {
        $("." + $attrName).html($inputVal);
    } else if ($nameExlode[1] == "checkboxoption") {
        $preline = $mainContainerClass.find(".input_no-perline").val();
        $checkboxDefaultOption = $mainContainerClass.find(".checkboxDefaultOption").val();
        var checkbooxArray = $checkboxDefaultOption.split(',').map(function (checkboxoption) {
            return checkboxoption.trim();
        });
        var options = $inputVal.split(",");
        var htmlContent = "";
        options.forEach(function (option, index) {
            $value_checked = "";
            var optionValue = option.trim();
            if (optionValue !== "") {
                if (checkbooxArray.includes(optionValue)) {
                    $value_checked = 'checked';
                }
                htmlContent += `<li class="globo-list-control option-${$preline}-column">
                                <div class="checkbox-wrapper">
                                    <input class="checkbox-input ${$nameExlode[0]}__checkbox" id="false-checkbox-${index + 1}-${optionValue}-" type="checkbox" data-type="checkbox" name="checkbox-${index + 1}[]" value="${optionValue}" ${$value_checked}>
                                    <label class="checkbox-label globo-option ${$nameExlode[0]}__checkbox" for="false-checkbox-${index + 1}-${optionValue}-">${optionValue}</label>
                                </div>
                            </li>`;
            }
        });
        $("." + $attrName).html(htmlContent);
    } else if ($nameExlode[1] == "radiooption") {
        $preline = $mainContainerClass.find(".input_no-perline").val();
        $radioDefaultOption = $mainContainerClass.find(".checkboxDefaultOption").val();
        var radioArray = $radioDefaultOption.split(',').map(function (radiooption) {
            return radiooption.trim();
        });
        var options = $inputVal.split(",");
        var radioHtml = "";
        options.forEach(function (option, index) {
            $value_checked = "";
            var optionValue = option.trim();
            if (optionValue !== "") {
                if (radioArray.includes(optionValue)) {
                    $value_checked = 'checked';
                }
                radioHtml += `
                <li class="globo-list-control option-${$preline}-column">
                    <div class="radio-wrapper">
                        <input class="radio-input  ${$nameExlode[0]}__radio" id="false-radio-${index + 1}-${optionValue}-" type="radio" data-type="radio" name="radio-1" value="${optionValue}" ${$value_checked}>
                        <label class="radio-label globo-option ${$nameExlode[0]}__radio" for="false-radio-${index + 1}-${optionValue}-">${optionValue}</label>
                    </div>
                </li>`;
            }
        });
        $("." + $attrName).html(radioHtml);
    } else if ($nameExlode[1] == "title") {
        $(".formHeader .title").html($inputVal);
    } else if ($nameExlode[1] == "buttontext") {
        $("." + $attrName).html($inputVal);
        $("." + $attrName).removeClass("hidden");
        if ($inputVal == "") {
            $("." + $attrName).addClass("hidden");
        }
    } else if ($nameExlode[1] == "dropoption") {
        $dropdownDefaultOption = $mainContainerClass.find(".dropdownDefaultOption").val();
        var dropdownArray = $dropdownDefaultOption.split(',').map(function (dropdownoption) {
            return dropdownoption.trim();
        });
        var options = $inputVal.split(",");
        var dropdownHtml = `<option value="">Please select</option>`;
        options.forEach(function (option, index) {
            $value_checked = "";
            var optionValue = option.trim();
            if (dropdownArray.includes(optionValue)) {
                $value_checked = 'selected';
            }
            if (optionValue !== "") {
                dropdownHtml += `<option value="${optionValue}" ${$value_checked}>${optionValue}</option>`;
            }
        });
        $mainContainer.find('.' + containerClass + ' select').html(dropdownHtml);
    }
});

$(document).on("change ", ".showHeader", function () {
    $(".formHeader").addClass("hidden");
    if (this.checked) {
        $(".formHeader").removeClass("hidden");
    }
});

$(document).on("change ", ".resetButton", function () {
    if (this.checked) {
        $(".reset").removeClass("hidden");
        $(".reset.classic-button").removeClass("hidden");
    } else {
        $(".reset").addClass("hidden");
        $(".reset.classic-button").addClass("hidden");
    }
});

$(document).on('keydown, keyup', ".ck-content", function () {
    $inputVal = $(this).html();
    $footerData = $(this).closest(".tabContent").hasClass("footerData");

    if ($footerData) {
        $(".footer-data__footerdescription").html($inputVal);
    }
});

$(document).on("change", ".passLimitcar", function () {
    $mainContainerClass = $(this).closest(".container").attr("class");
    var classArray = $mainContainerClass.split(" ");
    var containerClass = classArray.find(className => className.startsWith("container_"));

    $mainContainer = $(this).closest(".container").find(".limitCaracters");
    $inputVal = $mainContainer.find(".Polaris-TextField__Input").val();
    if (this.checked) {
        $mainContainer.removeClass("hidden");
        $(".block-container").find("." + containerClass + " .classic-input").attr("maxlength", $inputVal);
    } else {
        $mainContainer.addClass("hidden");
        $(".block-container").find("." + containerClass + " .classic-input").attr("maxlength", '');
    }
});

$(document).on("change", ".hideLabel", function () {
    $mainContainerClass = $(this).closest(".container").attr("class");
    var classArray = $mainContainerClass.split(" ");
    var containerClass = classArray.find(className => className.startsWith("container_"));
    $classExlode = containerClass.split("_");

    $mainContainer = $(".block-container");
    $requiredCheckbox = $("input[name='" + $classExlode[1] + "__required']");
    $showRequiredLabel = $("input[name='" + $classExlode[1] + "__required-hidelabel']");
    if (this.checked) {
        $(".passhideLabel").removeClass("hidden");
        $mainContainer.find("." + containerClass + " .label-content").addClass("hidden");
        $mainContainer.find("." + containerClass + " .text-smaller").addClass("hidden");
        if ($showRequiredLabel.prop('checked') && $requiredCheckbox.prop('checked')) {
            $mainContainer.find("." + containerClass + " .text-smaller").removeClass("hidden");
        }
    } else {
        $(".passhideLabel").addClass("hidden");
        $mainContainer.find("." + containerClass + " .label-content").removeClass("hidden");
        if ($requiredCheckbox.prop('checked')) {
            $mainContainer.find("." + containerClass + " .text-smaller").removeClass("hidden");
        }
    }
});

$(document).on("change", ".keePositionLabel", function () {
    $mainContainerClass = $(this).closest(".container").attr("class");
    var classArray = $mainContainerClass.split(" ");
    var containerClass = classArray.find(className => className.startsWith("container_"));

    if (this.checked) {
        $(".block-container").find("." + containerClass + " .classic-label").addClass("position--label");
    } else {
        $(".block-container").find("." + containerClass + " .classic-label").removeClass("position--label");
    }
});

$(document).on("change", ".requiredCheck", function () {
    $mainContainerClass = $(this).closest(".container").attr("class");
    var classArray = $mainContainerClass.split(" ");
    var containerClass = classArray.find(className => className.startsWith("container_"));
    $classExlode = containerClass.split("_");

    $mainContainer = $(".block-container");
    $showRequiredLabel = $("input[name='" + $classExlode[1] + "__required-hidelabel']");
    $hideLabel = $("input[name='" + $classExlode[1] + "__hidelabel']");

    if (this.checked) {
        if ($hideLabel.prop('checked')) {
            if ($showRequiredLabel.prop('checked')) {
                $mainContainer.find("." + containerClass + " .text-smaller").removeClass("hidden");
            }
        } else {
            $mainContainer.find("." + containerClass + " .text-smaller").removeClass("hidden");
        }
        $(this).closest("." + containerClass).find(".Requiredpass").removeClass("hidden");
    } else {
        $mainContainer.find("." + containerClass + " .text-smaller").addClass("hidden");
        $(this).closest("." + containerClass).find(".Requiredpass").addClass("hidden");
    }
});

$(document).on("change", ".showRequireHideLabel", function () {
    $mainContainerClass = $(this).closest(".container").attr("class");
    var classArray = $mainContainerClass.split(" ");
    var containerClass = classArray.find(className => className.startsWith("container_"));
    $classExlode = containerClass.split("_");

    $mainContainer = $(".block-container");
    $requiredCheckbox = $("input[name='" + $classExlode[1] + "__required']");
    $hideLabel = $("input[name='" + $classExlode[1] + "__hidelabel']");
    if (this.checked) {
        if ($requiredCheckbox.prop('checked')) {
            $mainContainer.find("." + containerClass + " .text-smaller").removeClass("hidden");
        }
    } else {
        if ($hideLabel.prop('checked')) {
            $(".block-container").find("." + containerClass + " .text-smaller").addClass("hidden");
        }
    }
});

$(document).on("change", ".fullFooterButton", function () {
    if (this.checked) {
        $(".footer .classic-button").addClass("w100");
    } else {
        $(".footer .classic-button").removeClass("w100");
    }
});

$(document).on("change ", ".defaultSelectAcceptterms", function () {
    $mainContainerClass = $(this).closest(".container").attr("class");
    var classArray = $mainContainerClass.split(" ");
    var containerClass = classArray.find(className => className.startsWith("container_"));
    $classExlode = containerClass.split("_");

    $mainContainer = $(".block-container");
    $mainContainer.find("." + $classExlode[1] + "__acceptterms").prop("checked", false);
    if (this.checked) {
        $mainContainer.find("." + $classExlode[1] + "__acceptterms").prop("checked", true);
    }
});

$(document).on("change", ".selectDefaultCountry", function () {
    $selectVal = $(this).val();
    $mainContainerClass = $(this).closest(".container").attr("class");
    var classArray = $mainContainerClass.split(" ");
    var containerClass = classArray.find(className => className.startsWith("container_"));
    $mainContainer = $(".block-container");
    $mainContainer.find('.' + containerClass + ' select').val($selectVal).change();

});

$(document).on('keydown, keyup', ".dropdownDefaultOption", function () {
    $inputVal = $(this).val();
    $mainContainerClass = $(this).closest(".container").attr("class");
    var classArray = $mainContainerClass.split(" ");
    var containerClass = classArray.find(className => className.startsWith("container_"));
    $mainContainer = $(".block-container");
    if ($inputVal == "") {
        $mainContainer.find('.' + containerClass + ' select').val("").change();
    } else {
        $mainContainer.find('.' + containerClass + ' select').val($inputVal).change();
    }

});

$(document).on('keydown, keyup', ".checkboxDefaultOption", function () {
    $inputVal = $(this).val();
    var checkbooxArray = $inputVal.split(',').map(function (checkboxoption) {
        return checkboxoption.trim();
    });

    $mainContainerClass = $(this).closest(".container").attr("class");
    var classArray = $mainContainerClass.split(" ");
    var containerClass = classArray.find(className => className.startsWith("container_"));
    $mainContainer = $(".block-container");
    $mainContainer.find("." + containerClass + " input").prop('checked', false);
    var $valuesToCheck = $mainContainer.find("." + containerClass + " li");
    $valuesToCheck.each(function () {
        $checkboxvalue = $(this).find('input').val();
        if (checkbooxArray.includes($checkboxvalue)) {
            $(this).find('input[value="' + $checkboxvalue + '"]').prop('checked', true);
        }

    });
});

$(document).on("change", ".allowMultipleCheckbox", function () {
    $mainContainerClass = $(this).closest(".container").attr("class");
    var classArray = $mainContainerClass.split(" ");
    var containerClass = classArray.find(className => className.startsWith("container_"));
    $mainContainer = $(".block-container");

    if (this.checked) {
        $mainContainer.find('.' + containerClass + ' input').attr('name', 'files[]');
        $mainContainer.find('.' + containerClass + ' input').attr('multiple', 'multiple');
    } else {
        $mainContainer.find('.' + containerClass + ' input').removeAttr('name');
        $mainContainer.find('.' + containerClass + ' input').removeAttr('multiple');
    }
});

// copy input enbed value
$(document).on("click", ".copyButton", function () {
    var copyText = document.querySelector(".embed_code");
    copyText.select();
    copyText.setSelectionRange(0, 99999); // For mobile devices
    navigator.clipboard.writeText(copyText.value);
});
// copy input enbed value
