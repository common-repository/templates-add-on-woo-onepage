<?php
/**
 * Variable product add to cart
 * @package WooCommerce/Templates
 * @version 3.5.5
 */

defined( 'ABSPATH' ) || exit;

global $product;

$attribute_keys  = array_keys( $attributes );
$variations_json = wp_json_encode( $available_variations );
$variations_attr = function_exists( 'wc_esc_json' ) ? wc_esc_json( $variations_json ) : _wp_specialchars( $variations_json, ENT_QUOTES, 'UTF-8', true );

// do_action( 'woocommerce_before_add_to_cart_form' ); 

?>

<form class="variations_form cart cartToVariable-<?php the_ID(); ?>" action="<?php echo esc_url( apply_filters( 'woocommerce_add_to_cart_form_action', $product->get_permalink() ) ); ?>" method="post" enctype='multipart/form-data' data-product_id="<?php echo absint( $product->get_id() ); ?>" data-product_variations="<?php echo $variations_attr; // WPCS: XSS ok. ?>">

	<?php do_action( 'woocommerce_before_variations_form' ); ?>

	<?php if ( empty( $available_variations ) && false !== $available_variations ) : ?>
		<p class="stock out-of-stock"><?php echo esc_html( apply_filters( 'woocommerce_out_of_stock_message', __( 'This product is currently out of stock and unavailable.', 'woocommerce' ) ) ); ?></p>
	<?php else : ?>
		<div class="variations woosc-variations" cellspacing="0">
				<?php foreach ( $attributes as $attribute_name => $options ) : ?>
					<div class="<?php echo strtolower(esc_attr(sanitize_title($attribute_name))).'-variation' ?>">
						<div class="label">
							<label style="color: red;" for="<?php echo esc_attr( sanitize_title( $attribute_name ) ); ?>"><?php echo wc_attribute_label( $attribute_name ); // WPCS: XSS ok. ?></label>
						</div>
						<div class="value">
							<?php
								woot_dropdown_variation_attribute_options( array(
									'options'   => $options,
									'attribute' => $attribute_name,
									'product'   => $product,
								) );
								echo end( $attribute_keys ) === $attribute_name ? wp_kses_post( apply_filters( 'woocommerce_reset_variations_link', '<a class="reset_variations" href="#">' . esc_html__( 'Clean', 'woocommerce' ) . '</a>' ) ) : '';
							?>
						</div>
					</div>
				<?php endforeach; ?>
		</div>

		<div class="single_variation_wrap">
			<?php
				/**
				 * Hook: woocommerce_before_single_variation.
				 */
				do_action( 'woocommerce_before_single_variation' );

				/**
				 * Hook: woocommerce_single_variation. Used to output the cart button and placeholder for variation data.
				 *
				 * @since 2.4.0
				 * @hooked woocommerce_single_variation - 10 Empty div for variation data.
				 * @hooked woocommerce_single_variation_add_to_cart_button - 20 Qty and cart button.
				 */
				// do_action( 'woocommerce_single_variation' );
					woot_single_variation_add_to_cart_button();

				// do_action( 'woocommerce_single_variation' )

				/**
				 * Hook: woocommerce_after_single_variation.
				 */
				do_action( 'woocommerce_after_single_variation' );
			?>
		</div>
	<?php endif; ?>

	<?php  do_action( 'woocommerce_after_variations_form' ); ?>
</form>
<?php
 do_action( 'woocommerce_after_add_to_cart_form' );
?>

<script>
jQuery.noConflict();
(function ($) {

		// add to cart button disable
        $('.add_to_cart_btn_action-<?php the_ID(); ?>').prop('disabled', true);

        // prevent submit form
        $(".cartToVariable-<?php the_ID(); ?>").on('submit', function ( event ) {
            $.ajax({
                url: $(this).attr('action'),
                type: "POST",
                data: $(this).serialize(),
                success: function (data) {
                    // reload cart
                },
                error: function () {
                    alert('Error !');
                }
            });

            event.preventDefault();

        });

        $('.cartToVariable-<?php the_ID(); ?>').each( function () {

            var old_description = $('.short_description-<?php the_ID(); ?>').html(); //if variation sort  description not found.
            
            $(this).on( 'found_variation', function( event, variation ) {
            	var current_thumbnil = $(".thumbnil_img-<?php the_ID(); ?>").children();
                current_thumbnil.attr('src', variation.image.thumb_src);
                current_thumbnil.attr('srcset', variation.image.gallery_thumbnail_src);

                $('.single_price-<?php the_ID(); ?>').html(variation.price_html);
                $('.sku_code-<?php the_ID(); ?>').html( variation.sku );
                if(variation.variation_description != '') {
                    $('.short_description-<?php the_ID(); ?>').html(variation.variation_description);
                } else {
                    $('.short_description-<?php the_ID(); ?>').html(old_description);
                }

                // add to cart button enable
        		$('.add_to_cart_btn_action-<?php the_ID(); ?>').prop({'disabled': false, 'color': 'red'});

            });
        });

        // convert to select option to radio button. change data, when radio button has been changed.
        $(document).on('change', '.variation-radios input', function() {
		  $('select[name="'+$(this).attr('name')+'"]').val($(this).val()).trigger('change');
		});

    })(jQuery);
</script>