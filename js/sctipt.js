var $containerClone; // clone portfolio
// Here we apply the actual CollagePlus plugin
function collage() {
    jQuery('.te-collage').removeWhitespace().collagePlus(
            {
                'fadeSpeed': 1000,
                'targetHeight': 200
            }
    );
}
;
// This is just for the case that the browser window is resized
function preview_resize($te_filter) {
    var resizeTimer = null;
    // hide all the images until we resize them
    jQuery('.te-collage .te-image-wrapper').css("opacity", 0);
    // set a timer to re-apply the plugin collage
    if (resizeTimer) {
        clearTimeout(resizeTimer);
    }
    resizeTimer = setTimeout(collage, 200);
    // hide all the images until we resize them
    img_height();
    if ($te_filter) {
        jQuery('.te-image-wrapper').css("display", 'none');
        jQuery('.te-image-wrapper').fadeIn(1000);
    }
}
// optimize the image height in columns
function img_height() {
    var width_img = jQuery('.te-column-2 img, .te-column-3 img, .te-column-4 img').eq(0).width();
    jQuery('.te-column-2 img, .te-column-3 img, .te-column-4 img').height(width_img);
}
// activate the popup panel
function te_content_right() {
    jQuery('html, body').css('overflow', 'auto');
    //open the panel
    jQuery(document).on('click', '.te-image-wrapper .te-hover-effect a', function(event) {
        event.preventDefault();
        var selected_member = jQuery(this).attr('data-type');
        console.log(selected_member);
        jQuery('.' + selected_member).addClass('is-visible');
        te_slider();
        te_progress_bars_open();
        return false;
    });
    //clode the panel
    jQuery(document).on('click', '.te-panel', function(event) {
        if (jQuery(event.target).is('.te-panel') || jQuery(event.target).is('.te-panel-close')) {
            te_slider_destroy();
            te_progress_bars_delete();
            jQuery('.te-panel').removeClass('is-visible');
            event.preventDefault();
        }
    });
}
// activate progress bars
function te_progress_bars_open() {
    jQuery('.te-panel.is-visible .te-progress-bars').each(function() {
        jQuery('.te-progress-bars-line', this).goalProgress({
            goalAmount: 100,
            currentAmount: jQuery('.te-progress-bars-prcent', this).val(),
            background: jQuery('.te-progress-bars-color', this).val()
        });
    });
}
// clean progress bars
function te_progress_bars_delete() {
    jQuery('.te-panel.is-visible .te-progress-bars').each(function() {
        jQuery('.te-progress-bars-line').html('');
    });
}
// activate slider
function te_slider() {
    jQuery(".te-panel.is-visible .owl-carousel").owlCarousel({
        slideSpeed: 300,
        paginationSpeed: 400,
        singleItem: true
    });
}
// destroy slider
function te_slider_destroy() {
    jQuery(".te-panel.is-visible .owl-carousel").data('owlCarousel').destroy();
}
// filter the content portfolio
function te_portfolio_filter(thisClass) {
    var $filter = '';
    var $filteredItems = '';
    // Set Our Filter
    $filter = $(thisClass).attr('class');
    if ($filter == 'all') {
        $filteredItems = $containerClone.find('.te-image-wrapper').clone();
    } else {
        $filteredItems = $containerClone.find('[data-type=' + $filter + ']').clone();
    }
    jQuery('#te-portfolio').html($filteredItems);
    preview_resize(true);
}
// load DOM
jQuery(window).load(function() {
    jQuery(document).ready(function() {
//        var posts = document.querySelectorAll('#te-portfolio .te-image-wrapper');
//imagesLoaded( posts, function() {
//console.log('all images are loaded');  
//
//}); 

        // check whether there is a portfolio page
        if (jQuery('#te-portfolio').length !== 0) {
            $containerClone = $('#te-portfolio').clone(true);
            // optimize the size of images      
            preview_resize();
            // activate the popup content
            te_content_right();
            // activate click on the filter
            jQuery('#te-filter li a').on('click', function(event) {
                te_portfolio_filter(this);
            });
            // activate litebox
            jQuery('.litebox').liteBox();
            // optimize the image at resize
            jQuery(window).bind('resize', function() {
                preview_resize();
            });
        }

    });
});

