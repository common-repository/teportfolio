<?php

/**
 * This class is displays on the main page content portfolio.
 * 
 * Class structure:
 * 
 * Initialization
 * Construct html 
 * Loop
 * Filter
 * Preview  
 * Content
 * Content slider
 * Social buttons
 *
 * @author TrubinE
 */
class content_portfolio {

    private $HTML;
    private $HTML_POPUP;

    /**
     * Initialization of the main variables
     * 
     * @param int $posts_per_page - number of posts on the page
     * @param int $preview - display type
     * @param int $float - side opening popup (2 - left, 1 - right)
     * @param int $filter - filter on/off (2 - on, 1 - off)
     * @param string $category - category id
     * @param string $title_button - name button
     * @param string $style - color style
     * @param string $hover - hover effect
     * @param int $uniq - uniq id
     */
    function init($posts_per_page = 99999, $preview = 1, $float = 2, $filter = 2, $category = '', $title_button = '', $style = '', $hover = '', $uniq = 1) {
        // variables that contain html content
        $this->HTML = '';
        $this->HTML_POPUP = '';
        // settings portfolio 
        $this->posts_per_page = (int) $posts_per_page;
        // obtain category
        $this->category_string = $category;
        $this->category_array = explode(',', $category);
        // get display options
        if (in_array($preview, array(1, 2, 3, 4))) {
            $this->preview = ($preview == 1) ? 'collage' : 'column-' . $preview;
        } else {
            $this->preview = 'collage';
        }
        $this->float = ($float == 2) ? 'from-left' : 'from-right'; // popup form align
        $this->filter = ($filter == 2) ? 'on' : 'off'; // filter on/off        
        $this->title_button = ($title_button !== '') ? $title_button : 'View'; // button title
        $this->style = ($style == '') ? 'red' : $style; // style color
        $this->hover = ($hover == '') ? '5' : $hover; // hover effect
        $this->uniq = $uniq; // uniq id
        // collect html
        $this->html();
        // output html potfolio
        return $this->HTML;
    }

    /**
     * Function to construct html for portfolio
     */
    function html() {
        $this->filter();
        $this->loop();
    }

    /**
     * Function to add html for content to portfolio
     */
    function loop() {
        $portfolio = new WP_Query(array(
            'post_type' => 'te_portfolio',
            'posts_per_page' => $this->posts_per_page,
            'tax_query' => array(
                array(
                    'taxonomy' => 'te_category',
                    'field' => 'id',
                    'terms' => $this->category_array
                )
            )
        ));

        // $i - id img preview and popup form 
        $i = 1;
        // query posts
        if ($portfolio->have_posts()) {
            $this->HTML .= '<div id="te-portfolio" class="te-' . $this->preview . ' te-style-' . $this->style . '">';
            while ($portfolio->have_posts()) {
                $portfolio->the_post();

                // add html content portfolio
                $this->HTML .= $this->preview(get_the_ID() . '-' . $i);
                $this->HTML .= $this->content(get_the_ID() . '-' . $i);
                $i++;
            }
            $this->HTML .= '</div>' . $this->HTML_POPUP;
        }
        wp_reset_query();
    }

    /**
     * Function to add html for filter to portfolio
     */
    function filter() {
        // check whether the output filter
        if ($this->filter == 'on') {
            $category = get_terms('te_category');
            if (is_array($category)) {
                $this->HTML .= '<ul id="te-filter" class="te-style-' . $this->style . '">
                    <li><a href="#" class="all">All</a></li>';
                foreach ($category as $term) {
                    if (in_array($term->term_taxonomy_id, $this->category_array)) {
                        $this->HTML .= '<li><a href="#" class="' . $term->term_taxonomy_id . '">' . $term->name . '</a></li>';
                    }
                }
                $this->HTML .= '</ul>';
            }
        }
    }

    /**
     * Function output preview
     * 
     * @param int $id_block id to preview and popup block
     */
    function preview($id_block) {
        $large_image_url = '';
        $term = get_the_terms(get_the_ID(), 'te_category');
        $term = ($term) ? array_shift($term) : false;
        $term_id = (is_object($term)) ? $term->term_taxonomy_id : '';
        if (has_post_thumbnail()) {
            // size preview
            $prev = ($this->preview == 'collage') ? 'large' : 'tegallery-thumb';
            $large_image_url = wp_get_attachment_image_src(get_post_thumbnail_id(), $prev);
        }
        $this->HTML .= ' <div class="te-image-wrapper te-effect-' . $this->hover . '" data-type="' . $term_id . '">
        <img src="' . $large_image_url[0] . '" />
            <div class="te-hover-effect">
                <a href="#" data-type="te-content-' . $id_block . '">' . $this->title_button . '</a>
            </div> 
        </div>';
    }

    /**
     * Function adding html the popup content
     * 
     * @param int $id_block id to preview and popup block
     */
    function content($id_block) {
        $shortcode = '';
        $large_image_url = '';
        // add thumbnail 
        if (has_post_thumbnail()) {
            $large_image_url = wp_get_attachment_image_src(get_post_thumbnail_id(), 'tegallery-content');
            $large_image_url_orig = wp_get_attachment_image_src(get_post_thumbnail_id(), 'full');
        }
        // add slide 
        $slider = $this->content_slider($id_block);
        // add progress bar 
        if (get_post_meta(get_the_ID(), 'te-fpb-ok', 1) == 2) {
            $shortcode = do_shortcode("[teBar id=\"" . get_the_ID() . "\" to=\"content\"][/teBar]");
        }
        // add socia buttons
        $social_buttons = $this->social_buttons();
        // add popup content
        $te_content = apply_filters('the_content', get_the_content());
        $this->HTML_POPUP .= '
        <div class="te-panel ' . $this->float . ' te-content-' . $id_block . ' te-style-' . $this->style . '">
            <header class="te-panel-header">
                <h2>' . get_the_title() . '</h2>
                <a href="#0" class="te-panel-close">&nbsp;</a>
            </header>
            <div class="te-panel-container">                
                <div class="te-panel-content">
                    <div class="te-panel-content-img">
                        <div class="owl-carousel owl-theme">     
                            <a href="' . $large_image_url_orig[0] . '" class="item litebox" data-litebox-group="group-' . $id_block . '"><img src="' . $large_image_url[0] . '" alt=""></a>        
                            ' . $slider . ' 
                        </div>                                
                    </div>
                        ' . $shortcode . '
                        <div class="te-panel-content-text">                            
                        ' . $te_content . '
                        </div> 
                        ' . $social_buttons . '
                </div> 
            </div> 
        </div>';
    }

    /**
     * Function add slider to popup
     * 
     * @param int $id_block id to preview and popup block
     */
    function content_slider($id_block) {
        $html = '';
        $teSlide = get_post_meta(get_the_ID(), 'teSlide', 1);
        if (is_array($teSlide)) {
            foreach ($teSlide as $value) {
                $html .= '<a href="' . $value . '" class="item litebox" data-litebox-group="group-' . $id_block . '"><img src="' . $value . '"></a>';
            }
        }
        return $html;
    }

    /**
     * Function add social buttons to popup
     */
    function social_buttons() {
        $html = '';
        $teSB = get_post_meta(get_the_ID(), 'teSB', 1);
        if (is_array($teSB)) {
            $html .= '<div class="te-social-buttons">';
            foreach ($teSB as $value) {
                $html .= '<a href="http://' . $value['link'] . '" target="_blank"><img src="' . TE_PORTFOLIO_DIR . 'img/social/' . $value['name'] . '.png' . '" /></a>';
            }
            $html .= '</div>';
        }
        return $html;
    }

}

?>
