<?php

/**
 * This class is responsible for all that is related to "TE Portfolio" item post type, a post type for portfolio items.
 * 
 * Class structure:
 * 
 * Activate filters
 * Post type 
 * Taxonomies
 * Manage portfolio columns
 * Meta box "slider" and "progress bars"  
 * Update data meta box
 * Tinymce plugin and custom button
 *
 * @author TrubinE
 */
class post_type_portfolio {

    /**
     * Class constructor will set the needed filter and action hooks.
     * Activate filters to create: post type, taxonomies, meta box, portfolio columns, tinymce plugin.
     */
    function __construct() {
        add_filter('init', array(__CLASS__, 'post_type'));
        add_filter('init', array(__CLASS__, 'taxonomy'));
        add_filter('manage_te_portfolio_posts_columns', array(__CLASS__, 'portfolio_post_manage_columns'));
        add_filter("manage_te_portfolio_posts_custom_column", array(__CLASS__, 'portfolio_post_custom_column'), 10, 3);
        add_filter('admin_init', array(__CLASS__, 'add_meta_box'), 10, 1);
        add_filter('save_post', array(__CLASS__, 'update_meta_box_data'), 10, 1);
        add_action('admin_head', array(__CLASS__, 'add_tinymce_button'), 10, 1);
    }

    /**
     * Register the post type for portfolio      
     */
    function post_type() {
        // add support for thumbnails
        add_theme_support('post-thumbnails');
        // create thumbnails for gallery
        add_image_size('tegallery-thumb', 300, 300, true);
        set_post_thumbnail_size(300, 300);
        add_image_size('tegallery-content', 600, 250, true);
        set_post_thumbnail_size(600, 250);

        // create the labels for the post type
        $labels =
                array(
                    'name' => __('Portfolio'),
                    'singular_name' => __('Portfolio'),
                    'rewrite' =>
                    array(
                        'slug' => __('portfolio')
                    ),
                    'add_new' => _x('Add Item', 'portfolio'),
                    'edit_item' => __('Edit Portfolio Item'),
                    'new_item' => __('New Portfolio Item'),
                    'view_item' => __('View Portfolio'),
                    'search_items' => __('Search Portfolio'),
                    'not_found' => __('No Portfolio Items Found'),
                    'not_found_in_trash' => __('No Portfolio Items Found In Trash'),
                    'parent_item_colon' => ''
        );
        // set up the arguements for post type
        $args =
                array(
                    'labels' => $labels,
                    'public' => true,
                    'publicly_queryable' => true,
                    'show_ui' => true,
                    'query_var' => true,
                    'rewrite' => true,
                    'capability_type' => 'post',
                    'hierarchical' => false,
                    'menu_position' => 111,
                    'supports' =>
                    array(
                        'title',
                        'editor',
                        'thumbnail'
                    )
        );

        // register the post type
        register_post_type(__('te_portfolio'), $args);
    }

    /**
     * Register category taxonomy to organize portfolio items
     * into different categories and assign them to portfolios
     */
    function taxonomy() {
        $labels = array(
            'name' => 'Categories',
            'singular_name' => 'Category',
            'search_items' => 'Search Categories',
            'all_items' => 'All Groups',
            'parent_item' => 'Parent Category',
            'parent_item_colon' => 'Parent Category:',
            'edit_item' => 'Edit Category',
            'update_item' => 'Update Category',
            'add_new_item' => 'Add Category',
            'new_item_name' => 'New Category Name',
            'menu_name' => 'Categories'
        );
        $args = array(
            'hierarchical' => true,
            'labels' => $labels,
            'public' => true,
            'publicly_queryable' => true,
            'show_ui' => true,
            'show_in_nav_menus' => false,
            'show_tagcloud' => false,
            'query_var' => true,
            'rewrite' => array('slug' => 'te-portfolio')
        );
        register_taxonomy('te_category', array('te_portfolio'), $args);
    }

    /**
     * Function add custom columns to te_portfolio item post type
     *
     * @param array $columns Array with column names
     */
    function portfolio_post_manage_columns($columns) {
        return array(
            'cb' => '<input type="checkbox" />',
            'title' => 'Title',
            'previews' => 'Previews'
        );
    }

    /**
     * Function output values for custom columns for te_portfolio item post type
     *
     * @param string $column_name Column
     * @param int $post_id te_portfolio item post ID
     */
    function portfolio_post_custom_column($column_name, $post_id) {
        switch ($column_name) {
            case "previews":
                if (has_post_thumbnail($post_id)) {
                    $atts = array('class' => 'te_portfolio_item-thumb');
                    echo get_the_post_thumbnail($post_id, array(80, 80), $atts);
                } else {
                    echo '---';
                }
                break;
        }
    }

    /**
     * Adding meta box to the post type te_portfolio.
     * Meta box: "progress bars", "slider" and "social buttons"
     */
    function add_meta_box() {
        add_meta_box('meta_box_slider', 'Slider', array(__CLASS__, 'meta_box_slider'), 'te_portfolio', 'normal', 'high');
        add_meta_box('meta_box_progress_bars', 'Progress Bars', array(__CLASS__, 'meta_box_progress_bars'), 'te_portfolio', 'normal', 'high');
        add_meta_box('meta_box_social_buttons', 'Social buttons', array(__CLASS__, 'meta_box_social_buttons'), 'te_portfolio', 'normal', 'high');
    }

    /**
     * Meta box "slider"
     * 
     * @param object $post Post object
     */
    function meta_box_slider($post) {
        ?>
        <div class="te-fields-slider">            
            <?php
            // output of stored "sliders" 
            $content_array = get_post_meta($post->ID, 'teSlide', 1);
            if (is_array($content_array)) {
                foreach ($content_array as $value) {
                    ?>
                    <div class="te-slide-img">
                        <img src="<?php echo $value; ?>" width="120" height="100" />
                        <div class="te-slide-img-delete">delete</div>
                        <input name="teSlide[]" type="hidden" value="<?php echo $value; ?>">
                    </div>
                    <?
                }
            }
            ?>
            <div class="te-fpb-block te-fpb-img-back">
                <div class="te-slide-add">add new</div>
            </div>
        </div>
        <?
    }

    /**
     * Meta box "progress bars"
     * 
     * @param object $post Post object
     */
    function meta_box_progress_bars($post) {
        ?> 
        <div class="te-fields-progress-bars">            
            <div class="te-fpb-block">
                <div class="te-fpb-checkbox">
                    <?php $te_fpb_ok = get_post_meta($post->ID, 'te-fpb-ok', 1); ?>
                    <select class="te-fpb-ok" name="extra[te-fpb-ok]">
                        <option value="2" <?php selected($te_fpb_ok, 2); ?>>Yes</option>
                        <option value="1" <?php selected($te_fpb_ok, 1); ?>>No</option>                        
                    </select>
                </div><div class="te-fpb-checkbox-title">Display on your site "progress bars"?</div>
            </div>
            <?php
            // output of stored "progress bars" 
            $content_array = get_post_meta($post->ID, 'fpb', 1);
            if (is_array($content_array)) {
                $mumber = 0;
                foreach ($content_array as $value) {
                    ?>
                    <div class="te-fpb-content">
                        <div class="te-fpb-box">
                            <p>Title</p>
                            <input class="te-fpb-title" name="te-fpb[<? echo $mumber; ?>][title]" type="text" value="<?php echo htmlspecialchars($value['title']); ?>" />
                        </div>
                        <div class="te-fpb-box">
                            <p>Prcent</p>
                            <select name="te-fpb[<? echo $mumber; ?>][prcent]" class="te-fpb-prcent">
                                <option value="10" <?php selected($value['prcent'], 10); ?>>10%</option>
                                <option value="20" <?php selected($value['prcent'], 20); ?>>20%</option>
                                <option value="30" <?php selected($value['prcent'], 30); ?>>30%</option>
                                <option value="40" <?php selected($value['prcent'], 40); ?>>40%</option>
                                <option value="50" <?php selected($value['prcent'], 50); ?>>50%</option>
                                <option value="60" <?php selected($value['prcent'], 60); ?>>60%</option>
                                <option value="70" <?php selected($value['prcent'], 70); ?>>70%</option>
                                <option value="80" <?php selected($value['prcent'], 80); ?>>80%</option>
                                <option value="90" <?php selected($value['prcent'], 90); ?>>90%</option>
                                <option value="100" <?php selected($value['prcent'], 100); ?>>100%</option>
                            </select>
                        </div>
                        <div class="te-fpb-box">
                            <p>Color</p>
                            <input class="te-fpb-color"  name="te-fpb[<? echo $mumber; ?>][color]" type="text" value="<?php echo $value['color']; ?>" />                       
                        </div>
                        <div class="te-fpb-box">
                            <div class="te-fpb-delete">delete</div>
                        </div>                
                    </div>
                    <?
                    $mumber++;
                }
            }
            ?>
            <div class="te-fpb-block te-fpb-content-back">
                <div class="te-fpb-add">add new</div>
            </div>           
        </div>
        <input type="hidden" name="te_portfolio_meta_box_nonce" value="<?php echo wp_create_nonce(__FILE__); ?>" />
        <?php
    }

    /**
     * Meta box "social buttons"
     * 
     * @param object $post Post object
     */
    function meta_box_social_buttons($post) {
        ?>
        <div class="te-fpb-block">
            <div class="te-fpb-checkbox">                   
                <select class="teSB-select" >
                    <option value="0">--</option>
                    <option value="facebook">facebook</option>
                    <option value="twitter" >twitter</option>                        
                    <option value="google" >google</option>                        
                    <option value="pinterest" >pinterest</option>                        
                    <option value="instragram" >instragram</option>                        
                    <option value="linkedin" >linkedin</option>                        
                    <option value="youtube" >youtube</option>                        
                </select>
            </div><div class="te-fpb-checkbox-title">Select button to add</div>
        </div>

        <div class="te-social-buttons-list">
            <?php
            // output of stored "social buttons" 
            $content_array = get_post_meta($post->ID, 'teSB', 1);
            if (is_array($content_array)) {
                $mumber = 0;
                foreach ($content_array as $value) {
                    ?>
                    <div class="te-social-buttons-block">
                        <div class="te-social-buttons-img"><img src="<?php echo TE_PORTFOLIO_DIR . 'img/social/' . $value['name'] . '.png'; ?>" /></div>
                        <div class="te-social-buttons-links">
                            <p><?php echo $value['name']; ?> <small>(without http: //)</small></p>
                            <input class="te-social-buttons-link" type="text" name="teSB[<? echo $mumber; ?>][link]" value="<?php echo htmlspecialchars($value['link']); ?>" />
                            <input class="te-social-buttons-name" type="hidden" name="teSB[<? echo $mumber; ?>][name]" value="<?php echo $value['name']; ?>" />
                            <div class="te-social-buttons-delete">delete</div>
                        </div>
                    </div>
                    <?
                    $mumber++;
                }
            }
            ?>
        </div>     
        <?
    }

    /**
     * Function to save "slider" and "progress_bars" metaboxes 
     * when post is updated
     * 
     * @param int $post_id Post ID
     */
    function update_meta_box_data($post_id) {
        // checks post type and $_POST['extra'], to make sure that this update meta box
        if (get_post_type($post_id) == 'te_portfolio' && isset($_POST['extra'])) {
            // checks passed verification code
            if (!wp_verify_nonce($_POST['te_portfolio_meta_box_nonce'], __FILE__)) {
                return false;
            }
            // if autosave
            if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
                return false;
            }
            // if the user does not have permission to edit the record    
            if (!current_user_can('edit_post', $post_id)) {
                return false;
            }
            // everything is OK! Now, you want to save / delete data
            if (isset($_POST['te-fpb'])) {
                update_post_meta($post_id, 'fpb', $_POST['te-fpb']);
            } else {
                delete_post_meta($post_id, 'fpb');
            }
            if (isset($_POST['teSlide'])) {
                update_post_meta($post_id, 'teSlide', $_POST['teSlide']);
            } else {
                delete_post_meta($post_id, 'teSlide');
            }
            if (isset($_POST['teSB'])) {
                update_post_meta($post_id, 'teSB', $_POST['teSB']);
            } else {
                delete_post_meta($post_id, 'teSB');
            }
            $_POST['extra'] = array_map('trim', $_POST['extra']);
            foreach ($_POST['extra'] as $key => $value) {
                if (empty($value))
                // delete a field if the value is empty 
//                    continue delete_post_meta($post_id, $key);
                delete_post_meta($post_id, $key);
                update_post_meta($post_id, $key, $value);
            }

            return $post_id;
        }
    }

    /**
     * Connect plugin and adds a button to the editor tinymce
     */
    function add_tinymce_button() {
        global $typenow;
        if (!in_array($typenow, array('page', 'post'))) {
            return;
        }
        add_filter('mce_external_plugins', array(__CLASS__, 'tinymce_plugin'));
        add_filter('mce_buttons', array(__CLASS__, 'tinymce_button'));
        add_meta_box('meta_box_tinymce_button', 'meta_box_tinymce_button', array(__CLASS__, 'meta_box_tinymce_button'), 'page', 'normal', 'high');
        add_meta_box('meta_box_tinymce_button', 'meta_box_tinymce_button', array(__CLASS__, 'meta_box_tinymce_button'), 'post', 'normal', 'high');
    }

    /**
     * Function add meta box  to 'post' and 'page' !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
     */
    function meta_box_tinymce_button() {
        $category = get_terms('te_category');
        if (is_array($category)) {
            $iterator = 0;
            $array = array();
            foreach ($category as $term) {
                $array[$iterator]['id'] = $term->term_taxonomy_id;
                $array[$iterator]['name'] = $term->name;
                $iterator++;
            }
            $json = json_encode($array);
            echo "<input class='meta_box_tinymce_te_portfolio' type='hidden' value='" . $json . "' />";
        }
    }

    /**
     * Function connect plugin editor tinymce
     * 
     * @param array $button Array with plugin settings
     */
    function tinymce_plugin($plugin_array) {
        $plugin_array['te_tinymce_plugin'] = TE_PORTFOLIO_DIR . '/js/admin.js';
        return $plugin_array;
    }

    /**
     * Output the button to display the settings plugin "te_portfolio"
     * 
     * @param array $button Array with all buttons names
     */
    function tinymce_button($button) {
        array_push($button, 'te_tinymce_button');
        return $button;
    }

}
?>
