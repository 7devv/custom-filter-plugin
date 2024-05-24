<?php
/**
 * Plugin Name: Custom Product Filter
 * Description: Adds a custom filter for WooCommerce products based on dimensions.
 * Version: 2.72
 * Author: Devon Potter
 * Release Notes: Update CSS to have hover effect and enable fullsize images (lightbox)
 * Release Date: 4/24/2024
 */

add_action('wp_ajax_filter_products', 'custom_filter_products');
add_action('wp_ajax_nopriv_filter_products', 'custom_filter_products');

function custom_filter_products() {
    global $wpdb;

    $length = isset($_POST['length']) ? floatval($_POST['length']) : 0;
    $width  = isset($_POST['width']) ? floatval($_POST['width']) : 0;

    $query = "
        SELECT p.ID
        FROM {$wpdb->posts} p
        LEFT JOIN {$wpdb->postmeta} pm1 ON p.ID = pm1.post_id AND pm1.meta_key = '_length'
        LEFT JOIN {$wpdb->postmeta} pm2 ON p.ID = pm2.post_id AND pm2.meta_key = '_width'
        LEFT JOIN {$wpdb->postmeta} pm3 ON p.ID = pm3.post_id AND pm3.meta_key = '_additional_dimension_1'
        LEFT JOIN {$wpdb->postmeta} pm4 ON p.ID = pm4.post_id AND pm4.meta_key = '_additional_dimension_2'
        WHERE p.post_type = 'product' AND p.post_status = 'publish'
        AND (
            (CAST(pm1.meta_value AS DECIMAL(10,2)) >= %f AND CAST(pm2.meta_value AS DECIMAL(10,2)) >= %f)
            OR (
                CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(pm3.meta_value, 'x', 1), 'x', -1) AS DECIMAL(10,2)) >= %f
                AND CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(pm3.meta_value, 'x', 2), 'x', -1) AS DECIMAL(10,2)) >= %f
            )
            OR (
                CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(pm4.meta_value, 'x', 1), 'x', -1) AS DECIMAL(10,2)) >= %f
                AND CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(pm4.meta_value, 'x', 2), 'x', -1) AS DECIMAL(10,2)) >= %f
            )
        )
    ";

    $query = $wpdb->prepare($query, $length, $width, $length, $width, $length, $width);
    $product_ids = $wpdb->get_col($query);

    if (!empty($product_ids)) {
        $args = array(
            'post_type'      => 'product',
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'post__in'       => $product_ids,
        );

        $query = new WP_Query($args);

        if ($query->have_posts()) :
            echo '<div class="product-grid">';
            while ($query->have_posts()): $query->the_post();
                $product = wc_get_product(get_the_ID());
                $brand_terms = get_the_terms(get_the_ID(), 'product_brand'); // Retrieve the brand terms
                $brand = !empty($brand_terms) ? $brand_terms[0]->name : ''; // Get the first brand term name
                ?>
                <div class="product-item">
                    <div class="product-image" style="border-radius: 10px;">
                        <a href="<?php echo wp_get_attachment_url($product->get_image_id()); ?>" class="woocommerce-main-image zoom"
                        title="<?php the_title_attribute(); ?>">
                        <?php echo woocommerce_get_product_thumbnail(); ?>
                        </a>
                    </div>

                    <h3 class="product-title"><?php the_title(); ?></h3>
                    <?php if (!empty($brand)) : ?>
                        <div class="product-brand"><?php echo esc_html($brand); ?></div>
                    <?php endif; ?>
                    <div class="product-tags">
                        <?php echo wc_get_product_tag_list(get_the_ID()); ?>
                    </div>
                    <div class="product-stock" style="line-height: 0.2;">
                        <p class="stock-confirm"><i class="fa-solid fa-circle-check"></i> In stock</p>
                    </div>
                </div>
                <?php
            endwhile;
            echo '</div>';
        else :
            echo 'No products found';
        endif;

        wp_reset_postdata();
    } else {
        echo 'No products found';
    }

    wp_die();
}


// Enqueue scripts
add_action('wp_enqueue_scripts', 'custom_filter_scripts');
function custom_filter_scripts() {
    wp_enqueue_script('custom-filter-script', plugin_dir_url(__FILE__) . 'js/custom-filter.js', array('jquery'), '1.0', true);
    wp_localize_script('custom-filter-script', 'ajax_object', array('ajax_url' => admin_url('admin-ajax.php')));
}

// Enqueue styles
function custom_filter_enqueue_styles() {
    wp_enqueue_style('custom-filter-styles', plugin_dir_url(__FILE__) . 'css/custom-filter.css', array(), '1.0', 'all');
}
add_action('wp_enqueue_scripts', 'custom_filter_enqueue_styles');

// Enable WooCommerce lightbox
add_action('after_setup_theme', 'enable_woocommerce_lightbox_plugin');
function enable_woocommerce_lightbox_plugin()
{
    add_theme_support('wc-product-gallery-zoom');
    add_theme_support('wc-product-gallery-lightbox');
    add_theme_support('wc-product-gallery-slider');
}

// Add short code for easier WordPress integration
function custom_product_filter_shortcode() {
    ob_start();
    ?>
    <div class="custom-filter-wrapper">
        <button type="button" id="toggle-filter" class="wp-element-button">Toggle Size Filter</button>
        <div id="custom-filter-form-wrapper" style="display: none;">
            <h3>Dimension Filter</h3>
            <p>Enter your <strong>minimum</strong> slab requirements to see material that will work in your space</p>
            <form id="custom-filter-form">
                <div class="form-group">
                    <label for="length">Depth (inches):</label>
                    <input type="range" id="length" name="length" min="1" max="99" step="1" value="1">
                    <span id="length-value">1</span>
                </div>
                <div class="form-group">
                    <label for="width">Length (inches):</label>
                    <input type="range" id="width" name="width" min="1" max="140" step="1" value="1">
                    <span id="width-value">1</span>
                </div>
                <button type="button" id="filter-button" class="wp-element-button">Filter</button>
            </form>
        </div>
        <div id="filtered-products-container"></div>
    </div>
    <?php
    return ob_get_clean();
}

add_shortcode('custom_product_filter', 'custom_product_filter_shortcode');

/**
 * Add custom dimension fields
 */
function custom_product_dimensions_fields() {
    global $woocommerce, $post;

    echo '<div class="options_group">';

    // Additional Dimension 1
    woocommerce_wp_text_input(
        array(
            'id'          => '_additional_dimension_1',
            'label'       => __( 'Additional Dimension 1', 'woocommerce' ),
            'placeholder' => __( 'Enter the additional dimension 1 value', 'woocommerce' ),
            'desc_tip'    => true,
            'description' => __( 'Enter the additional dimension 1 value in the format: length x width x height (inches).', 'woocommerce' ),
            'value'       => get_post_meta( $post->ID, '_additional_dimension_1', true ),
        )
    );

    // Additional Dimension 2
    woocommerce_wp_text_input(
        array(
            'id'          => '_additional_dimension_2',
            'label'       => __( 'Additional Dimension 2', 'woocommerce' ),
            'placeholder' => __( 'Enter the additional dimension 2 value', 'woocommerce' ),
            'desc_tip'    => true,
            'description' => __( 'Enter the additional dimension 2 value in the format: length x width x height (inches).', 'woocommerce' ),
            'value'       => get_post_meta( $post->ID, '_additional_dimension_2', true ),
        )
    );

    echo '</div>';
}
add_action( 'woocommerce_product_options_dimensions', 'custom_product_dimensions_fields' );

/**
 * Save custom dimension fields
 */
function save_custom_product_dimensions_fields( $post_id ) {
    if ( isset( $_POST['_additional_dimension_1'] ) ) {
        update_post_meta( $post_id, '_additional_dimension_1', sanitize_text_field( $_POST['_additional_dimension_1'] ) );
    }

    if ( isset( $_POST['_additional_dimension_2'] ) ) {
        update_post_meta( $post_id, '_additional_dimension_2', sanitize_text_field( $_POST['_additional_dimension_2'] ) );
    }
}
add_action( 'woocommerce_process_product_meta', 'save_custom_product_dimensions_fields' );

/**
 * Custom comparison function for dimensions
 */
function custom_dimension_gte_comparison($meta_value, $values) {
    if (!is_array($values) || count($values) !== 2) {
        return false;
    }

    $length = $values[0];
    $width = $values[1];

    $dimensions = array_map('trim', explode('x', $meta_value));

    if (count($dimensions) !== 2) {
        error_log('Custom Product Filter: Invalid additional dimension format: ' . $meta_value);
        return false;
    }

    $product_length = (float)$dimensions[0];
    $product_width = (float)$dimensions[1];

    return $product_length >= $length && $product_width >= $width;
}

add_filter('woocommerce_product_query_meta_query_compare_numeric', 'custom_dimension_gte_comparison', 10, 3);
