jQuery(function ($) {
    //console.log(woot_ajax_object);
    //Cart Quantity increament or decreament
    if($('.woosc-product-quantity-increment').length >0){
        $('.woosc-products-container').on('click', '.woosc-product-quantity-increment', function (e) {
            e.preventDefault();
            if($(this).parent().hasClass('quantity')){
                 var qntyText = $(this).parent().find('.woosc-product-quantity-number-text');
                 var qntyVal = $(this).siblings('input');
                 var quantity=qntyText.text();
                quantity++;
                qntyText.text(quantity);
                qntyVal.val(quantity);
            }
            else{
                var qntyText=$(this).parent().find('.woosc-product-quantity-number-text');
                var qntyVal=$(this).parent().parent().parent().find('.add_to_cart_button');
                var quantity=qntyText.text();
                quantity++;
                qntyText.text(quantity);
                qntyVal.attr('data-quantity',quantity);
            }
            
        });
    }
    if($('.woosc-product-quantity-decrement').length >0){
        $('.woosc-products-container').on('click', '.woosc-product-quantity-decrement', function (e) {
            e.preventDefault();
            if($(this).parent().hasClass('quantity')){
                 
                 var qntyText = $(this).parent().find('.woosc-product-quantity-number-text');
                 var qntyVal = $(this).siblings('input');
                 var quantity=qntyText.text();
                if (quantity>1){
                    quantity--;
                }
                //console.log(quantity); 
                qntyText.text(quantity);
                qntyVal.val(quantity);  

            } else{
                var qntyText = $(this).parent().find('.woosc-product-quantity-number-text');
                var qntyVal = $(this).parent().parent().parent().find('.add_to_cart_button');
                var quantity=qntyText.text();
                if (quantity>1){
                    quantity--;
                }
                qntyText.text(quantity);
                qntyVal.attr('data-quantity',quantity);  
            }
            


        });
    }

});


// add-to-cart
