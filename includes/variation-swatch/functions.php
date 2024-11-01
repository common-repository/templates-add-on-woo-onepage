<?php 
/**
 * @package Woot Swatch
 */

defined( 'ABSPATH' ) or die('do not access to directly !');

		
        remove_action( 'woot_variable_add_to_cart', 'woocommerce_variable_add_to_cart', 30 );

        add_action( 'woot_variable_add_to_cart', 'woot_variable_add_to_cart', 30 );
        add_action('woot_simple_add_to_cart', 'woot_simple_add_to_cart', 30);
		    /**
         * Output the variable product add to cart area. 
         */
        function woot_variable_add_to_cart() {
            global $product;

            // Enqueue variation scripts
            wp_enqueue_script( 'wc-add-to-cart-variation' );

            $attributes = $product->get_variation_attributes();

            // get default attributes
            $selected_attributes = is_callable( array( $product, 'get_default_attributes' ) ) ? $product->get_default_attributes() : $product->get_variation_default_attributes();

            /** FIX WOO 2.1 */
            $wc_get_template = function_exists('wc_get_template') ? 'wc_get_template' : 'woocommerce_get_template';

            // Load the template
            $wc_get_template( 'add-to-cart/variable.php', array(
                'available_variations'  => $product->get_available_variations(),
                'attributes'            => $attributes,
                'selected_attributes'   => $selected_attributes,
                'attributes_types'      => woot_get_variation_attributes_types( $attributes )
            ), '', ACL_WOOT_PATH . 'includes/variation-swatch/templates/' );
        }

        function woot_simple_add_to_cart()
        {
          global $product;
          ?>
          <div>
            <div class="woosc-product-quantity">
                <div class="woosc-product-quantity-decrement">
                    <i class="fas fa-minus"></i>
                </div>
                <!--decrement-->

                <div class="woosc-product-quantity-number">
                    <div class="woosc-product-quantity-number-text">1</div>
                </div>
                <!--number-->
                <div class="woosc-product-quantity-increment">
                    <i class="fas fa-plus"></i>
                </div>
                <!--increment-->
            </div>
                            <!--woosc-product-quantity-->
          </div>
            <a rel="nofollow"
              href="<?php echo $product->add_to_cart_url(); ?>"
              data-quantity="1"
              data-product_id="<?php the_ID(); ?>"
              data-product_sku=""
              class="add_to_cart_button ajax_add_to_cart"> <?php _e('Add to Cart', 'woocommerce-one-page-checkout-shop'); ?></a>
          <?php 
        }

        function woot_get_variation_attributes_types( $attributes ) {
            global $wpdb;
            
            $types = array();

            if( !empty($attributes) ) {
                foreach( $attributes as $name => $options ) {
                    $attribute_name = substr($name, 3);
                    $attribute = $wpdb->get_row("SELECT * FROM " . $wpdb->prefix . "woocommerce_attribute_taxonomies WHERE attribute_name = '$attribute_name'");
                    if ( isset( $attribute ) ) {
                        $types[$name] = $attribute->attribute_type;
                    }
                    else {
                        $types[$name] = 'select';
                    }
                }
            }

            // print_r($types);
            return $types;
        }

    if ( ! function_exists( 'woot_single_variation_add_to_cart_button' ) ) {

		/**
		 * Output the add to cart button for variations.
		 */
		function woot_single_variation_add_to_cart_button() {
			wc_get_template( 'add-to-cart/variation-add-to-cart-button.php', array(), '', ACL_WOOT_PATH . 'includes/variation-swatch/templates/');
		}
	}

    if ( ! function_exists( 'woot_quantity_input' ) ) {

    /**
     * Output the quantity input for add to cart forms.
     *
     * @param  array           $args Args for the input.
     * @param  WC_Product|null $product Product.
     * @param  boolean         $echo Whether to return or echo|string.
     *
     * @return string
     */
    function woot_quantity_input( $args = array(), $product = null, $echo = true ) {
        if ( is_null( $product ) ) {
            $product = $GLOBALS['product'];
        }

        $defaults = array(
            'input_id'     => uniqid( 'quantity_' ),
            'input_name'   => 'quantity',
            'input_value'  => '1',
            'classes'      => apply_filters( 'woot_quantity_input_classes', array( 'input-text', 'qty', 'text' ), $product ),
            'max_value'    => apply_filters( 'woot_quantity_input_max', -1, $product ),
            'min_value'    => apply_filters( 'woot_quantity_input_min', 0, $product ),
            'step'         => apply_filters( 'woot_quantity_input_step', 1, $product ),
            'pattern'      => apply_filters( 'woot_quantity_input_pattern', has_filter( 'woot_stock_amount', 'intval' ) ? '[0-9]*' : '' ),
            'inputmode'    => apply_filters( 'woot_quantity_input_inputmode', has_filter( 'woot_stock_amount', 'intval' ) ? 'numeric' : '' ),
            'product_name' => $product ? $product->get_title() : '',
        );

        $args = apply_filters( 'woot_quantity_input_args', wp_parse_args( $args, $defaults ), $product );

        // Apply sanity to min/max args - min cannot be lower than 0.
        $args['min_value'] = max( $args['min_value'], 0 );
        $args['max_value'] = 0 < $args['max_value'] ? $args['max_value'] : '';

        // Max cannot be lower than min if defined.
        if ( '' !== $args['max_value'] && $args['max_value'] < $args['min_value'] ) {
            $args['max_value'] = $args['min_value'];
        }

        ob_start();

        // wc_get_template( 'global/quantity-input.php', $args );

        wc_get_template( 'quantity-input.php', $args, '', ACL_WOOT_PATH . 'includes/variation-swatch/templates/');

        if ( $echo ) {
            echo ob_get_clean(); // WPCS: XSS ok.
        } else {
            return ob_get_clean();
        }
    }
}


if ( ! function_exists( 'woot_dropdown_variation_attribute_options' ) ) {

    /**
     * Output a list of variation attributes for use in the cart forms.
     *
     * @param array $args Arguments.
     * @since 2.4.0
     */
    function woot_dropdown_variation_attribute_options( $args = array() ) {
        $args = wp_parse_args(
            apply_filters( 'woocommerce_dropdown_variation_attribute_options_args', $args ),
            array(
                'options'          => false,
                'attribute'        => false,
                'product'          => false,
                'selected'         => false,
                'name'             => '',
                'id'               => '',
                'class'            => '',
                'show_option_none' => __( 'Select Option', 'woocommerce' ),
            )
        );

        // Get selected value.
        if ( false === $args['selected'] && $args['attribute'] && $args['product'] instanceof WC_Product ) {
            $selected_key     = 'attribute_' . sanitize_title( $args['attribute'] );
            $args['selected'] = isset( $_REQUEST[ $selected_key ] ) ? wc_clean( wp_unslash( $_REQUEST[ $selected_key ] ) ) : $args['product']->get_variation_default_attribute( $args['attribute'] ); // WPCS: input var ok, CSRF ok, sanitization ok.
        }

        $options               = $args['options'];
        $product               = $args['product'];
        $attribute             = $args['attribute'];
        $name                  = $args['name'] ? $args['name'] : 'attribute_' . sanitize_title( $attribute );
        $id                    = $args['id'] ? $args['id'] : sanitize_title( $attribute );
        $class                 = $args['class'];
        $show_option_none      = (bool) $args['show_option_none'];
        $show_option_none_text = $args['show_option_none'] ? $args['show_option_none'] : __( 'Select Option', 'woocommerce' ); // We'll do our best to hide the placeholder, but we'll need to show something when resetting options.

        if ( empty( $options ) && ! empty( $product ) && ! empty( $attribute ) ) {
            $attributes = $product->get_variation_attributes();
            $options    = $attributes[ $attribute ];
        }

        $html  = '<select style="display:none" id="' . esc_attr( $id ) . '" class="' . esc_attr( $class ) . '" name="' . esc_attr( $name ) . '" data-attribute_name="attribute_' . esc_attr( sanitize_title( $attribute ) ) . '" data-show_option_none="' . ( $show_option_none ? 'yes' : 'no' ) . '">';
        $html .= '<option value="">' . esc_html( $show_option_none_text ) . '</option>';

        if ( ! empty( $options ) ) {
            if ( $product && taxonomy_exists( $attribute ) ) {
                // Get terms if this is a taxonomy - ordered. We need the names too.
                $terms = wc_get_product_terms(
                    $product->get_id(),
                    $attribute,
                    array(
                        'fields' => 'all',
                    )
                );

                foreach ( $terms as $term ) {
                    if ( in_array( $term->slug, $options, true ) ) {
                        $html .= '<option value="' . esc_attr( $term->slug ) . '" ' . selected( sanitize_title( $args['selected'] ), $term->slug, false ) . '>' . esc_html( apply_filters( 'woocommerce_variation_option_name', $term->name, $term, $attribute, $product ) ) . '</option>';
                    }
                }
            } else {
                foreach ( $options as $option ) {
                    // This handles < 2.4.0 bw compatibility where text attributes were not sanitized.
                    $selected = sanitize_title( $args['selected'] ) === $args['selected'] ? selected( $args['selected'], sanitize_title( $option ), false ) : selected( $args['selected'], $option, false );
                    $html    .= '<option value="' . esc_attr( $option ) . '" ' . $selected . '>' . esc_html( apply_filters( 'woocommerce_variation_option_name', $option, null, $attribute, $product ) ) . '</option>';
                }
            }
        }

        $html .= '</select>';

        echo apply_filters( 'woot_dropdown_variation_attribute_options_html', $html, $args ); // WPCS: XSS ok.
    }
}




function woot_variation_radio_buttons($html, $args) {
  $args = wp_parse_args(apply_filters('woocommerce_dropdown_variation_attribute_options_args', $args), array(
    'options'          => false,
    'attribute'        => false,
    'product'          => false,
    'selected'         => false,
    'name'             => '',
    'id'               => '',
    'class'            => '',
    'show_option_none' => __('Choose an option', 'woocommerce'),
 ));

  if(false === $args['selected'] && $args['attribute'] && $args['product'] instanceof WC_Product) {
    $selected_key     = 'attribute_'.sanitize_title($args['attribute']);
    $args['selected'] = isset($_REQUEST[$selected_key]) ? wc_clean(wp_unslash($_REQUEST[$selected_key])) : $args['product']->get_variation_default_attribute($args['attribute']);
  }

  $options               = $args['options'];
  $product               = $args['product'];
  $attribute             = $args['attribute'];
  $name                  = $args['name'] ? $args['name'] : 'attribute_'.sanitize_title($attribute);
  $id                    = $args['id'] ? $args['id'] : sanitize_title($attribute);
  $class                 = $args['class'];
  $show_option_none      = (bool)$args['show_option_none'];
  $show_option_none_text = $args['show_option_none'] ? $args['show_option_none'] : __('Choose an option', 'woocommerce');

  if(empty($options) && !empty($product) && !empty($attribute)) {
    $attributes = $product->get_variation_attributes();
    $options    = $attributes[$attribute];
  }

  $radios = '<div class="variation-radios">';

  if(!empty($options)) {
    if($product && taxonomy_exists($attribute)) {
      $terms = wc_get_product_terms($product->get_id(), $attribute, array(
        'fields' => 'all',
      ));

      foreach($terms as $term) {
        if(in_array($term->slug, $options, true)) {
          $radios .= '<div><input type="radio" id="'.esc_attr($term->slug).'" name="'.esc_attr($name).'" value="'.esc_attr($term->slug).'" '.checked(sanitize_title($args['selected']), $term->slug, false).'><label for="'.esc_attr($term->slug).'">'.esc_html(apply_filters('woocommerce_variation_option_name', $term->name)).'</label></div>';
        }
      }
    } else {
      foreach($options as $option) {
        $checked    = sanitize_title($args['selected']) === $args['selected'] ? checked($args['selected'], sanitize_title($option), false) : checked($args['selected'], $option, false);
        $radios    .= '<div><input type="radio" name="'.esc_attr($name).'" value="'.esc_attr($option).'" id="'.sanitize_title($option).'" '.$checked.'><label for="'.sanitize_title($option).'">'.esc_html(apply_filters('woocommerce_variation_option_name', $option)).'</label></div>';
      }
    }
  }

  $radios .= '</div>';

  return $html.$radios;
}
add_filter('woot_dropdown_variation_attribute_options_html', 'woot_variation_radio_buttons', 20, 2);
add_filter('woocommerce_dropdown_variation_attribute_options_html', 'woot_variation_radio_buttons', 20, 2);
