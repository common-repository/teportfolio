<?php

/*
  Plugin Name: tePortfolio
  Plugin URI: http://te-portfolio.editpage.ru
  Description: Responsive Portfolio Wordpress Plugin
  Version: 1.0
  Author: TrubinE
  Author URI: http://te-portfolio.editpage.ru
 */


/*  Copyright 2015  TrubinE  (email: E-MAIL_АВТОРА)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation; either version 2 of the License, or
  (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

if (!class_exists('te_portfolio')) {

    /**
     * Globals
     */
    define('TE_PORTFOLIO_DIR', plugins_url('/teportfolio/'));

    class te_portfolio {

        function __construct() {
            $this->add_admin_script();
            $this->add_site_script();
            $this->add_post_type();
            $this->add_shortcode();
        }

        /**
         * Function add_admin_script will set the needed filter and action hooks
         * browser to connect js and css files to the admin panel of the site
         */
        function add_admin_script() {
            if (is_admin()) {
                add_action('admin_enqueue_scripts', array(__CLASS__, 'enqueue_admin_scripts'));
                add_action('admin_enqueue_scripts', array(__CLASS__, 'enqueue_admin_styles'));
            }
        }

        /**
         * Function add_site_script will set the needed filter and action hooks
         * browser to connect js and css files to the frontend
         */
        function add_site_script() {
            if (!is_admin()) {
                add_action('wp_enqueue_scripts', array(__CLASS__, 'enqueue_site_scripts'));
                add_action('wp_enqueue_scripts', array(__CLASS__, 'enqueue_site_styles'));
            }
        }

        /**
         * Enqueue all admin panel scripts for plugin
         */
        function enqueue_admin_scripts() {
            wp_enqueue_script('te_portfolio_admin_js', TE_PORTFOLIO_DIR . 'js/admin.js', array('wp-color-picker'), false, true);
        }

        /**
         * Enqueue all admin panel styles for plugin
         */
        function enqueue_admin_styles() {
            wp_enqueue_style('te_portfolio_admin_css', TE_PORTFOLIO_DIR . 'css/admin/style_admin.css');
            wp_enqueue_style('wp-color-picker');
        }

        /**
         * Enqueue all frontend scripts for plugin
         */
        function enqueue_site_scripts() {
            wp_enqueue_script('te_portfolio_modernizr', TE_PORTFOLIO_DIR . 'js/modernizr.js', array('jquery'), false, true);
            wp_enqueue_script('te_portfolio_imagesloaded', TE_PORTFOLIO_DIR . 'js/imagesloaded.js', array('jquery'), false, true);
            wp_enqueue_script('te_portfolio_collagePlus', TE_PORTFOLIO_DIR . 'js/grid/jquery.collagePlus.min.js', array('jquery'), false, true);
            wp_enqueue_script('te_portfolio_removeWhitespace', TE_PORTFOLIO_DIR . 'js/grid/jquery.removeWhitespace.min.js', array('jquery'), false, true);
            wp_enqueue_script('te_portfolio_owl_carousel', TE_PORTFOLIO_DIR . 'js/owl.carousel.min.js', array('jquery'), false, true);
            wp_enqueue_script('te_portfolio_goalProgress', TE_PORTFOLIO_DIR . 'js/goalProgress.js', array('jquery'), false, true);
            wp_enqueue_script('te_portfolio_sctipt', TE_PORTFOLIO_DIR . 'js/sctipt.js', array('jquery'), false, true);
            wp_enqueue_script('te_portfolio_litebox', TE_PORTFOLIO_DIR . 'js/litebox/litebox.js', array('jquery'), false, true);
            wp_enqueue_script('te_portfolio_images_loaded', TE_PORTFOLIO_DIR . 'js/litebox/images-loaded.min.js', array('jquery'), false, true);
        }

        /**
         * Enqueue all frontend styles for plugin
         */
        function enqueue_site_styles() {
            wp_enqueue_style('te_portfolio_style', TE_PORTFOLIO_DIR . 'css/style.css');
            wp_enqueue_style('te_portfolio_owl_transitions', TE_PORTFOLIO_DIR . 'css/owl_transitions.css');
            wp_enqueue_style('te_portfolio_content_litebox', TE_PORTFOLIO_DIR . 'css/litebox.css');
        }

        /**
         * Connecting the class to work with "TE Portfolio" post type
         */
        function add_post_type() {
            require(plugin_dir_path(__FILE__) . 'includes/class.post-type-portfolio.php');
            global $post_type_portfolio;
            $post_type_portfolio = new post_type_portfolio();
        }

        /**
         * Function adds support for shortcodes
         */
        function add_shortcode() {
            // add shortcode to the portfolio
            add_shortcode('tePortfolio', array(__CLASS__, 'portfolio_show'));
            // add shortcode to the 'progress bars'
            add_shortcode('teBar', array(__CLASS__, 'progress_bar_show'));
        }

        /**
         * Register [tePortfolio][/tePortfolio] shortcode
         *
         * @param array $atts Array with attributes shortcode
         * @param string $content string with shortcode content
         */
        function portfolio_show($atts, $content = null) {
            // attributes shortcode and default settings portfolio
            extract(shortcode_atts(array(
                'float' => 2, // float left
                'portfolio_preview' => 1, // type collage
                'portfolio_filter' => 2, // filter yes
                'portfolio_category' => '', // portfolio category
                'portfolio_style' => '', // style color
                'portfolio_hover' => '', // hover effect
                'title_button' => '' // portfolio category
                            ), $atts));
            // add class responsible for showing portfolio
            require_once dirname(__FILE__) . '/includes/class.content-portfolio.php';
            $content_portfolio = new content_portfolio();
            // show portfolio
            $portfolio_uniq = rand();
            return $content_portfolio->init(99999, $portfolio_preview, $float, $portfolio_filter, $portfolio_category, $title_button, $portfolio_style, $portfolio_hover, $portfolio_uniq);
        }

        /**
         * Register [teBar][/teBar] shortcode
         *
         * @param array $atts Array with attributes shortcode
         * @param string $content string with shortcode content
         */
        function progress_bar_show($atts, $content = null) {
            // attributes shortcode and default settings 'progress bars' to the portfolio
            extract(shortcode_atts(array("id" => '', "to" => ''), $atts));
            $html = '';
            $on_pr_bar = get_post_meta($id, 'te-fpb-ok', 1);
            $array_pr_bar = get_post_meta($id, 'fpb', 1);
            // check whether the display of the shortcode
            if ($on_pr_bar == 2) {
                // check availability 'progress bars' content
                if (is_array($array_pr_bar)) {
                    foreach ($array_pr_bar as $value) {
                        $html .= '<div class="te-progress-bars">
                                  <div class="te-progress-bars-title">' . $value['title'] . '</div>
                                  <div class="te-progress-bars-line"></div>
                                  <input type="hidden" class="te-progress-bars-prcent" value="' . $value['prcent'] . '" />
                                  <input type="hidden" class="te-progress-bars-color" value="' . $value['color'] . '" /></div>';
                    }
                }
                return $html;
            }
        }

    }

}


global $te_portfolio;
$te_portfolio = new te_portfolio();
?>
