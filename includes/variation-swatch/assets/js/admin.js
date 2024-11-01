var frame,
	woot = woot || {};

jQuery( document ).ready( function ( $ ) {
	'use strict';
	var wp = window.wp,
		$body = $( 'body' );

	$( '#term-color' ).wpColorPicker();

	// Updating attribute image
	$body.on( 'click', '.woot-upload-image-button', function ( event ) {
		event.preventDefault();

		var $button = $( this );

		// Open media frame
		if ( frame ) {
			frame.open();
			return;
		}

		// Creating the media frame.
		frame = wp.media.frames.downloadable_file = wp.media( {
			title   : woot.i18n.mediaTitle,
			button  : {
				text: woot.i18n.mediaButton
			},
			multiple: false
		} );

		// Running a callback, when image is selected.
		frame.on( 'select', function () {
			var attachment = frame.state().get( 'selection' ).first().toJSON();
			//console.log(attachment.id);
			$( 'input.woot-term-image' ).val( attachment.id );
			$( '.woot-remove-image-button' ).show();
			$( '.woot-term-image-thumbnail' ).find( 'img' ).attr( 'src', attachment.url );
			//console.log($button.parent().prev( '.woot-term-image-thumbnail' ).find( 'img' ));
		} );

		// At the end, open the modal pop-up.
		frame.open();

	} ).on( 'click', '.woot-remove-image-button', function () {
		var $button = $( this );

		$button.siblings( 'input.woot-term-image' ).val( '' );
		$button.siblings( '.woot-remove-image-button' ).show();
		$button.parent().prev( '.woot-term-image-thumbnail' ).find( 'img' ).attr( 'src', woot.placeholder );

		return false;
	} );

	// Spinner add new attribute term modal
	var $modal = $( '#woot-modal-container' ),
		$spinner = $modal.find( '.spinner' ),
		$msg = $modal.find( '.message' ),
		$metabox = null;

	$body.on( 'click', '.woot_add_new_attribute', function ( e ) {
		e.preventDefault();
		var $button = $( this ),
			taxInputTemplate = wp.template( 'woot-input-tax' ),
			data = {
				type: $button.data( 'type' ),
				tax : $button.closest( '.woocommerce_attribute' ).data( 'taxonomy' )
			};

		// Insert input data
		$modal.find( '.woot-term-swatch' ).html( $( '#tmpl-woot-input-' + data.type ).html() );
		$modal.find( '.woot-term-tax' ).html( taxInputTemplate( data ) );

		if ( 'color' == data.type ) {
			$modal.find( 'input.woot-input-color' ).wpColorPicker();
		}

		$metabox = $button.closest( '.woocommerce_attribute.wc-metabox' );
		$modal.show();
	} ).on( 'click', '.woot-modal-close, .woot-modal-backdrop', function ( e ) {
		e.preventDefault();

		closeModal();
	} );

	// Ajax request when adding new attribute term
	$body.on( 'click', '.woot-new-attribute-submit', function ( e ) {
		e.preventDefault();

		var $button = $( this ),
			type = $button.data( 'type' ),
			error = false,
			data = {};

		// Validation
		$modal.find( '.woot-input' ).each( function () {
			var $this = $( this );
			//console.log($this.attr( 'name' ));			
			//console.log($this.val());
			if ( $this.attr( 'name' ) != 'slug' && !$this.val() ) {
				$this.addClass( 'error' );
				error = true;
			} else {
				$this.removeClass( 'error' );
			}

			data[$this.attr( 'name' )] = $this.val();
		} );


		//console.log(error);
		if ( error ) {
			return;
		}

		// Send Ajax request
		$spinner.addClass( 'is-active' );
		$msg.hide();
		wp.ajax.send( 'woot_add_new_attribute', {
			data   : data,
			error  : function ( res ) {
				$spinner.removeClass( 'is-active' );
				$msg.addClass( 'error' ).text( res ).show();
			},
			success: function ( res ) {
				$spinner.removeClass( 'is-active' );
				$msg.addClass( 'success' ).text( res.msg ).show();

				$metabox.find( 'select.attribute_values' ).append( '<option value="' + res.id + '" selected="selected">' + res.name + '</option>' );
				$metabox.find( 'select.attribute_values' ).change();

				closeModal();
			}
		} );
	} );

	/**
	 * Close modal pop-up
	 */
	function closeModal() {
		$modal.find( '.woot-term-name input, .woot-term-slug input' ).val( '' );
		$spinner.removeClass( 'is-active' );
		$msg.removeClass( 'error success' ).hide();
		$modal.hide();
	}
} );

