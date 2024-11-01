<div class="woosc-products-search">
    <form class="woosc-products-search-form" action="">
        <div class="woosc-products-search-field">
            <label for="woosc-product-search-term"> <?php _e('Search Here', 'woocommerce-one-page-checkout-shop'); ?></label>
            <input id="woosc-product-search-term" type="text" placeholder="<?php _e('Pizza,bugrer etc ', 'woocommerce-one-page-checkout-shop'); ?>">
        </div>
        <?php if(isset($cat) && $cat=="all"){?>
            <div class="woosc-products-search-field">
                <label for="woosc-categories-list"> <?php _e('Product Category', 'woocommerce-one-page-checkout-shop'); ?></label>
                <select class="woosc-categories-list" id="woosc-categories-list">
                    <option value="all"> <?php _e('All', 'woocommerce-one-page-checkout-shop'); ?></option>
                    <?php
                    $woosc_terms = get_terms(array(
                        'taxonomy' => 'product_cat',
                        'hide_empty' => true,
                    ));
                    if(count($woosc_terms)>0){
                        foreach ($woosc_terms as $woosc_term) {
                            ?>
                            <option value="<?php echo $woosc_term->term_id; ?>"> <?php echo $woosc_term->name; ?></option>
                            <?php
                        }
                    }
                    //wp_dropdown_categories($args);
                    ?>
                </select>
            </div>
        <?php } ?>
        <div class="woosc-products-search-field">
            <input type="submit" id="woosc-search-btn" class="" value="Product Search">
        </div>
    </form>
</div>
<!--woosc-products-search-->