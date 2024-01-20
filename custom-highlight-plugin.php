<?php
/**
 * Plugin Name: Custom Highlight Plugin
 * Description: Highlight plugin
 * Version: 1.0
 * Author: Microweb Global (PVT) LTD
 * Author URI: https://microweb.global/
 * License: Proprietary License - All rights reserved
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

function custom_search_term_extractor($q)
{
    if (!is_admin() && $q->is_main_query() && $q->is_search()) {
        $search_query = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '';

        if (!empty($search_query)) {
            setcookie('search_term', $search_query, time() + 3600, '/'); 

            wp_enqueue_script('custom-search-term-script', plugin_dir_url(__FILE__) . 'custom-search-term-script.js', array('jquery'), '1.0', true);
            add_action('woocommerce_before_shop_loop', 'custom_highlight_search_term_on_search_results_page');
        }
    }
}

add_action('woocommerce_product_query', 'custom_search_term_extractor');

function custom_highlight_search_term_on_search_results_page()
{
    if (isset($_COOKIE['search_term'])) {
        $search_term = sanitize_text_field($_COOKIE['search_term']);

        echo '<script>
            jQuery(document).ready(function($) {
                var searchTerm = ' . json_encode($search_term) . ';
                $(".products li.product .woocommerce-loop-product__title, .products li.product .woocommerce-product-details__short-description").each(function() {
                    var productTitle = $(this).html();
                    var highlightedTitle = productTitle.replace(new RegExp(searchTerm, "ig"), "<span style=\'background-color: yellow;\'>$&</span>");
                    $(this).html(highlightedTitle);
                });
            });
        </script>';
    }
}

function custom_highlight_search_term_on_product_page()
{
    echo '<script>console.log("Product viewed.");</script>';

    if (isset($_COOKIE['search_term'])) {
        $search_term = sanitize_text_field($_COOKIE['search_term']);

        echo '<script>
            jQuery(document).ready(function($) {
                var searchTerm = ' . json_encode($search_term) . ';
                var productTitle = $(".product_title").html();
                var highlightedTitle = productTitle.replace(new RegExp(searchTerm, "ig"), "<span style=\'background-color: yellow;\'>$&</span>");
                $(".product_title").html(highlightedTitle);

                var productDescription = $(".woocommerce-product-details__short-description").html();
                if (productDescription) {
                    var highlightedDescription = productDescription.replace(new RegExp(searchTerm, "ig"), "<span style=\'background-color: yellow;\'>$&</span>");
                    $(".woocommerce-product-details__short-description").html(highlightedDescription);
                }
            });
        </script>';

        echo '<script>
            jQuery(document).ready(function($) {
                document.cookie = "search_term=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
            });
        </script>';
    }
}

add_action('woocommerce_before_single_product_summary', 'custom_highlight_search_term_on_product_page');
