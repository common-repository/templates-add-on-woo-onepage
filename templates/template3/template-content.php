<?php
$wc_query = new WP_Query($query);
?>
<?php if ($wc_query->have_posts()) : ?>
    <?php while ($wc_query->have_posts()) : $wc_query->the_post();
        global $product;
        ?>
        <div id="product-ID-<?php the_ID(); ?>" class="woosc-product">
            <div class="woosc-product-item-col woosc-product-thumbnail">
                <div>
                    <a class="thumbnil_img-<?php the_ID(); ?>" href="<?php the_permalink() ?>"
                       title=" <?php if (get_the_title()) echo get_the_title(); else the_ID(); ?>">
                        <?php if (has_post_thumbnail()) {
                            the_post_thumbnail('shop_catalog');
                        } else { ?>
                            <img src="<?php echo ACL_WOOSC_IMG_URL . 'dummy_product.svg'; ?>"
                                 alt="<?php if (get_the_title()) echo get_the_title(); else the_ID(); ?>">
                        <?php } ?>
                    </a>
                </div>
            </div>
            <!--woosc-product-item-col-->
            <div class="woosc-product-item-col woosc-product-details">
                <div class="woosc-product-summary">
                    <div>
                        <h3>
                            <a href="<?php the_permalink() ?>" title="">
                                <?php if (get_the_title()) echo get_the_title(); else the_ID(); ?>
                            </a>
                        </h3>
                        <p class="sku_code-<?php the_ID(); ?>">
                            <?php
                            if ($product->get_sku()) {
                                echo $product->get_sku();
                            }
                            ?>
                        </p>
                    </div>
                    <div class="single_price-<?php the_ID(); ?>">
                        <?php echo $product->get_price_html(); ?>
                    </div>
                </div>
                <!-- woosc-product-summary-->
                <div class="short_description-<?php the_ID(); ?>">
                    <?php the_excerpt(); ?>
                </div>
            </div>


            <div class="woosc-product-item-col woosc-product-cart-action">
                <div>
                    <div>
                        <?php if ($product->is_type("simple")) {
                            do_action('woot_simple_add_to_cart');
                            ?>

                            <?php
                        } else if ($product->is_type('variable')) {
                            do_action('woot_variable_add_to_cart');
                        } else {
                            global $product;
                            ?>
                            <a rel="nofollow"
                               href="<?php echo $product->add_to_cart_url(); ?>"
                               data-quantity="1"
                               data-product_id="<?php the_ID(); ?>"
                               data-product_sku=""
                               class="add_to_cart_button"> <?php _e('Select options', 'woocommerce-one-page-checkout-shop'); ?> </a>
                        <?php } ?>
                    </div>
                </div>
            </div>

        </div>
        <!--woosc-product-->
    <?php
    endwhile; ?>
    <?php wp_reset_query(); // (5) ?>
<?php else: ?>
    <p class="woosc-no-products"><?php _e('No Products Found', 'woocommerce-one-page-checkout-shop'); ?> </p>
<?php endif; ?>