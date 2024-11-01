;(function ( $ ) {
	'use strict';

	$.fn.woot_variation_swatches_form = function () {
		return this.each( function() {
			var $form = $( this ),
				clicked = null,
				selected = [];

			$form
				.addClass( 'swatches-support' )
				.on( 'click', '.woot', function ( e ) {
					e.preventDefault();
					var $el = $( this ),
						$select = $el.closest( '.value' ).find( 'select' ),
						attribute_name = $select.data( 'attribute_name' ) || $select.attr( 'name' ),
						value = $el.data( 'value' );
 
					if ($(".woot_error_validation")[0]){
						$(".woot_error_validation").remove();
					}
					 
					$select.trigger( 'focusin' );
					//colorSelect();
					
					// Check if this variation combination is available
					if ( ! $select.find( 'option[value="' + value + '"]' ).length ) {
						
						$el.siblings( '.woot' ).removeClass( 'selected' );
						$select.val( '' ).change();
						$form.trigger( 'woot_no_matching_variations', [$el] );
						return;
					}

					clicked = attribute_name;

					if ( selected.indexOf( attribute_name ) === -1 ) {
						selected.push(attribute_name);
					}

					if ( $el.hasClass( 'selected' ) ) {
						$select.val( '' );
						$el.removeClass( 'selected' );

						delete selected[selected.indexOf(attribute_name)];
					} else {
						$el.addClass( 'selected' ).siblings( '.selected' ).removeClass( 'selected' );
						$select.val( value );
					}
					
					 // detect disabled
					
					
					 $(this).find('select').each(function () {
                            var li = $(this).find('.woot');
                            var attribute = $(this).data('attribute_name');
                            //var attribute_values = object._generated[attribute];
                            //var out_of_stock_values = object._out_of_stock[attribute];

                            //console.log(attribute);

                            li.each(function () {
                                var attribute_value = $(this).attr('data-value');

                                // if (!_.isEmpty(attribute_values) && !_.contains(attribute_values, attribute_value)){}

                                if (!_.isEmpty(attribute_values) && _.indexOf(attribute_values, attribute_value) === -1) {
                                    $(this).removeClass('selected');
                                    $(this).addClass('disabled');

                                  
                                }
                            });
                        });
					 
					 // detect disabled

					$select.change();
				} )
				.on( 'click', '.reset_variations', function () {
					$( this ).closest( '.variations_form' ).find( '.woot.selected' ).removeClass( 'selected' );
					selected = [];
				} )
				.on( 'woot_no_matching_variations', function(e) {
					// Display Error message if variation combination is not found
					if ($(".woot_error_validation")[0]){
						$(".woot_error_validation").text(wc_add_to_cart_variation_params.i18n_no_matching_variations_text);
					} else {
						$(e.target).find( '.woocommerce-variation-add-to-cart' ).first().before( "<div class='woot_error_validation'>"+ wc_add_to_cart_variation_params.i18n_no_matching_variations_text +"</div>" );
					}
					
					
				} );
		} );
	};

	$( function () {
		$( '.variations_form' ).woot_variation_swatches_form();
		$( document.body ).trigger( 'woot_initialized' );
		 //colorSelect();
		
	} );

	// convert to select option to radio button. change data, when radio button has been changed.
    $(document).on('change', '.variation-radios input', function() {
	  $('select[name="'+$(this).attr('name')+'"]').val($(this).val()).trigger('change');
	});

	
})( jQuery );

// Set disabled non interactable combination
function colorSelect(){
	
	var colorExist = Array();
	var designExist = Array();
	
	jQuery("#pa_color > option").each(function() {
    //console.log(this.text + ' ' + this.value);
	colorExist.push(this.value);
	
	jQuery("div[data-attribute='attribute_pa_color'] .woot").each(function(){
    //var swatch = jQuery(this).find(".woot");
    var swatchValue = jQuery(this).attr("data-value");
	//console.log(colorExist);
	
	if(jQuery.inArray(swatchValue, colorExist) !== -1){
		jQuery(this).addClass("disabled");
	} else {
		jQuery(this).removeClass("disabled");
	}
	
	});
	colorExist = [];

});

jQuery("#pa_design > option").each(function() {
    //console.log(this.text + ' ' + this.value);
	designExist.push(this.value);
	jQuery("div[data-attribute='attribute_pa_design'] .woot").each(function(){
    //var swatch = jQuery(this).find(".woot");
    var swatchValue = jQuery(this).attr("data-value");
	//console.log(designExist);
	
	if(jQuery.inArray(swatchValue, designExist) !== -1){
		jQuery(this).addClass("disabled");
	} else {
		jQuery(this).removeClass("disabled");
	}
    
	});
	designExist = [];
	
});
}