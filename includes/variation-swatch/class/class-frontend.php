<?php

/**
 * Class for the Frontend
 */
class Woot_WCVariation_Swatches_Frontend {
	/**
	 * The single class instance
	 *
	 * @var woot_WCVariation_Swatches_Frontend
	 */
	protected static $instance = null;

	/**
	 * Main instance
	 *
	 * @return woot_WCVariation_Swatches_Frontend
	 */
	public static function instance() {
		if ( null == self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Class constructor.
	 */
	public function __construct() {
		
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_settings' ) );


		add_filter( 'woot_dropdown_variation_attribute_options_html', array( $this, 'get_swatch_html' ), 100, 2 );
		add_filter( 'woocommerce_dropdown_variation_attribute_options_html', array( $this, 'get_swatch_html' ), 100, 2 );
		add_filter( 'woot_swatch_html', array( $this, 'swatch_html' ), 5, 4 );
	}

	/**
	 * Enqueue custom settings
	 */


function enqueue_settings()
{
	
   $woot_css = "";
   $woot_options = get_option('woot');
   $woot_css .= "<style id='super-products-swatches' type='text/css'>";
		if(htmlspecialchars($woot_options['clear_variation'])=="0"){
		$woot_css .= ".reset_variations {
				display: none !important;
			}";
		}
		
		if(htmlspecialchars($woot_options['image_variation'])=="full-image"){
		$woot_css .= ".textureImage {
				-webkit-background-size: cover;
				-moz-background-size: cover;
				-o-background-size: cover;
				background-size: cover;
			}";
		}
		
		if(htmlspecialchars($woot_options['swatches_style'])=="squared"){
		$woot_css .= ".woot-swatches .swatch {
			border-radius: 0% !important;
			}
			
			.woot-swatches>.swatchColor {
				border-radius: 0% !important;
			}";
		}
		
		if(htmlspecialchars($woot_options['swatch_size'])!=""){
		$woot_css .= ".woot-swatches>.swatchColor, .woot-swatches>.swatchColor>div {
			width: ".htmlspecialchars($woot_options['swatch_size'])."px !important;
			height: ".htmlspecialchars($woot_options['swatch_size'])."px !important;
			}
			
			.woot-swatches .swatch {
				width: ".htmlspecialchars($woot_options['swatch_size'])."px !important;
				height: ".htmlspecialchars($woot_options['swatch_size'])."px !important;
				line-height: ".htmlspecialchars($woot_options['swatch_size'])."px !important;
			}";
		}
		
		
		if(htmlspecialchars($woot_options['border_color_selected'])!=""){
		$woot_css .= ".woot-swatches>.swatchColor.selected {
			border: 2px solid ".htmlspecialchars($woot_options['border_color_selected'])." !important;
			}
			
			.woot-swatches .swatch.selected {
				border: 2px solid ".htmlspecialchars($woot_options['border_color_selected'])." !important;
			}";
		}
		
		if(htmlspecialchars($woot_options['border_color'])!=""){
		$woot_css .= ".woot-swatches>.swatchColor {
			border: 2px solid ".htmlspecialchars($woot_options['border_color'])." !important;
			}
			
			.woot-swatches .swatch {
				border: 2px solid ".htmlspecialchars($woot_options['border_color'])." !important;
			}";
		}
		
		if(htmlspecialchars($woot_options['tooltip_bg'])!=""){
		$woot_css .= ".woot-swatches>.swatchColor>.wootTooltip>.innerText {
			background-color: ".htmlspecialchars($woot_options['tooltip_bg'])." !important;
			}
			
			.woot-swatches>.swatch>.wootTooltip>.innerText {
			background-color: ".htmlspecialchars($woot_options['tooltip_bg'])." !important;
			}
			
			.woot-swatches>.swatchColor>.wootTooltip>span {
				border-block-end-color: ".htmlspecialchars($woot_options['tooltip_bg'])." !important;
			}
			
			.woot-swatches>.swatch>.wootTooltip>span {
				border-block-end-color: ".htmlspecialchars($woot_options['tooltip_bg'])." !important;
			}
			";
		}
		
		if(htmlspecialchars($woot_options['tooltip_text'])!=""){
		$woot_css .= ".woot-swatches>.swatchColor>.wootTooltip>.innerText {
			color: ".htmlspecialchars($woot_options['tooltip_text'])." !important;
			}
			
			.woot-swatches>.swatch>.wootTooltip>.innerText {
			color: ".htmlspecialchars($woot_options['tooltip_text'])." !important;
			}";
		}
		
		if(htmlspecialchars($woot_options['label_bg'])!=""){
		$woot_css .= ".woot-swatches .swatch-label {
			background-color: ".htmlspecialchars($woot_options['label_bg'])." !important;
			}";
		}
		
		if(htmlspecialchars($woot_options['label_text'])!=""){
		$woot_css .= ".woot-swatches .swatch-label {
			color: ".htmlspecialchars($woot_options['label_text'])." !important;
			}";
		}
		
		
		
	$woot_css .= "</style>";
	echo $woot_css;

	
}


	/**
	 * Enqueue scripts and stylesheets
	 */
	public function enqueue_scripts() {
		wp_enqueue_style( 'woot-frontend', plugins_url( 'assets/css/frontend.css', dirname( __FILE__ ) ), array(), '1.0');
		wp_enqueue_script( 'woot-frontend', plugins_url( 'assets/js/frontend.js', dirname( __FILE__ ) ), array( 'jquery' ), '1.0', true );

		
	}

	/**
	 * Filter function to add Swatches underneath the default selector
	 *
	 * @param $html
	 * @param $args
	 *
	 * @return string
	 */
	public function get_swatch_html( $html, $args ) {

		$swatch_types = woot_swatch()->types;
		$attr         = woot_swatch()->get_tax_attribute( $args['attribute'] );

		// Return if this is normal attribute
		if ( empty( $attr ) ) {
			return $html;
		}

		if ( ! array_key_exists( $attr->attribute_type, $swatch_types ) ) {
			return $html;
		}

		$options   = $args['options'];
		$product   = $args['product'];
		$attribute = $args['attribute'];
		$class     = "variation-selector variation-select-{$attr->attribute_type}";
		$swatches  = '';

		if ( empty( $options ) && ! empty( $product ) && ! empty( $attribute ) ) {
			$attributes = $product->get_variation_attributes();
			$options    = $attributes[$attribute];
		}

		if ( array_key_exists( $attr->attribute_type, $swatch_types ) ) {
			if ( ! empty( $options ) && $product && taxonomy_exists( $attribute ) ) {
				// Get terms if this is a taxonomy (ordered). The names are required too.
				$terms = wc_get_product_terms( $product->get_id(), $attribute, array( 'fields' => 'all' ) );

				foreach ( $terms as $term ) {
					if ( in_array( $term->slug, $options ) ) {
						$swatches .= apply_filters( 'woot_swatch_html', '', $term, $attr->attribute_type, $args );
					}
				}
			}

			if ( ! empty( $swatches ) ) {
				$class .= ' hidden';
				$woot_allowed_html = array (
					'a' => array (),
					'div' => array('class' => array(),'style' => array(),'data-value' => array(),'data-attribute' => array()),
					'span' => array('class' => array(),'style' => array(),'data-value' => array(),'data-attribute' => array()),
				);
				$swatches = '<div class="woot-swatches" data-attribute="attribute_' . esc_attr( $attribute ) . '">' . $swatches . '</div>';
				$html     = '<div class="' . esc_attr( $class ) . '">' .  $html  . '</div>' .wp_kses($swatches,$woot_allowed_html) ;
			}
		}

		return $html;
	}
	

//

	/**
	 * Print HTML content for a single swatch Color, Image and Label
	 *
	 * @param $html
	 * @param $term
	 * @param $type
	 * @param $args
	 *
	 * @return string
	 */
	public function swatch_html( $html, $term, $type, $args ) {
		$selected = sanitize_title( $args['selected'] ) == $term->slug ? 'selected' : '';
		$name     = esc_html( apply_filters( 'woocommerce_variation_option_name', $term->name ) );

		switch ( $type ) {
			case 'label':
				$label = get_term_meta( $term->term_id, 'label', true );
				$label = $label ? $label : $name;
				$html  = sprintf(
					'<span class="woot swatch swatch-label swatch-%s %s" data-value="%s">%s<span class="wootTooltip"><span></span><span class="innerText">%s</span><span></span></span></span>',
					esc_attr( $term->slug ),
					$selected,
					esc_attr( $term->slug ),
					esc_html( $label ),
					esc_attr( $name )
				);
				break;
		}

		return $html;
	}
}