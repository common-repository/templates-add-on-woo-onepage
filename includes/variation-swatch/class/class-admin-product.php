<?php

/**
 * Class Woot_WCVariation_Swatches_Admin_Product
 */
class Woot_WCVariation_Swatches_Admin_Product {
	/**
	 * Class constructor.
	 */
	public function __construct() {
		add_action( 'woocommerce_product_option_terms', array( $this, 'product_option_terms' ), 10, 2 );

		add_action( 'wp_ajax_woot_add_new_attribute', array( $this, 'add_new_attribute_ajax' ) );
		add_action( 'admin_footer', array( $this, 'add_attribute_term_template' ) );
	}

	/**
	 * Add selector for extra attribute types
	 *
	 * @param $taxonomy
	 * @param $index
	 */
	public function product_option_terms( $taxonomy, $index ) {
		if ( ! array_key_exists( $taxonomy->attribute_type, woot_swatch()->types ) ) {
			return;
		}

		$taxonomy_name = wc_attribute_taxonomy_name( $taxonomy->attribute_name );
		global $thepostid;

		$product_id = isset( $_POST['post_id'] ) ? wc_clean(absint( $_POST['post_id'] )) : $thepostid;
		?>

		<select multiple="multiple" data-placeholder="<?php esc_attr_e( 'Select terms', 'woot' ); ?>" class="multiselect attribute_values wc-enhanced-select" name="attribute_values[<?php echo $index; ?>][]">
			<?php

			$all_terms = get_terms( $taxonomy_name, apply_filters( 'woocommerce_product_attribute_terms', array( 'orderby' => 'name', 'hide_empty' => false ) ) );
			if ( $all_terms ) {
				foreach ( $all_terms as $term ) {
					echo '<option value="' . esc_attr( $term->term_id ) . '" ' . selected( has_term( absint( $term->term_id ), $taxonomy_name, $product_id ), true, false ) . '>' . esc_attr( apply_filters( 'woocommerce_product_attribute_term_name', $term->name, $term ) ) . '</option>';
				}
			}
			?>
		</select>
		<button class="button plus select_all_attributes"><?php esc_html_e( 'Select all', 'woot' ); ?></button>
		<button class="button minus select_no_attributes"><?php esc_html_e( 'Select none', 'woot' ); ?></button>
		<button class="button fr plus woot_add_new_attribute" data-type="<?php echo $taxonomy->attribute_type ?>"><?php esc_html_e( 'Add new', 'woot' ); ?></button>

		<?php
	}

	/**
	 * Ajax function that handles adding new attribute term
	 */
	public function add_new_attribute_ajax() {
		$nonce  = isset( $_POST['nonce'] ) ? wc_clean($_POST['nonce']) : '';
		$tax    = isset( $_POST['taxonomy'] ) ? sanitize_text_field( wp_unslash( $_POST['taxonomy'] ) ) : '';
		$type   = isset( $_POST['type'] ) ? wc_clean($_POST['type']) : '';
		$name   = isset( $_POST['name'] ) ? sanitize_text_field($_POST['name']) : '';
		$slug   = isset( $_POST['slug'] ) ? wc_clean($_POST['slug']) : '';
		$swatch = isset( $_POST['swatch'] ) ? wc_clean($_POST['swatch']) : '';

		if ( ! wp_verify_nonce( $nonce, '_woot_create_attribute' ) ) {
			wp_send_json_error( esc_html__( 'Wrong request', 'woot' ) );
		}

		if ( empty( $name ) || empty( $swatch ) || empty( $tax ) || empty( $type ) ) {
			wp_send_json_error( esc_html__( 'Not enough data', 'woot' ) );
		}

		if ( ! taxonomy_exists( $tax ) ) {
			wp_send_json_error( esc_html__( 'Taxonomy is not exists', 'woot' ) );
		}

		if ( term_exists( $_POST['name'], $_POST['tax'] ) ) {
			wp_send_json_error( esc_html__( 'This term is exists', 'woot' ) );
		}

		$term = wp_insert_term( $name, $tax, array( 'slug' => $slug ) );

		if ( is_wp_error( $term ) ) {
			wp_send_json_error( $term->get_error_message() );
		} else {
			$term = get_term_by( 'id', $term['term_id'], $tax );
			update_term_meta( $term->term_id, $type, $swatch );
		}

		wp_send_json_success(
			array(
				'msg'  => esc_html__( 'Added successfully', 'woot' ),
				'id'   => $term->term_id,
				'slug' => $term->slug,
				'name' => $term->name,
			)
		);
	}

	/**
	 * Print HTML of modal at admin footer and add JavaScript templates
	 */
	public function add_attribute_term_template() {
		global $pagenow, $post;

		if ( $pagenow != 'post.php' || ( isset( $post ) && get_post_type( $post->ID ) != 'product' ) ) {
			return;
		}
		?>

		<div id="woot-modal-container" class="woot-modal-container">
			<div class="woot-modal">
				<button type="button" class="button-link media-modal-close woot-modal-close">
					<span class="media-modal-icon"></span></button>
				<div class="woot-modal-header"><h2><?php esc_html_e( 'Add new term', 'woot' ) ?></h2></div>
				<div class="woot-modal-content">
					<p class="woot-term-name">
						<label>
							<?php esc_html_e( 'Name', 'woot' ) ?>
							<input type="text" class="widefat woot-input" name="name">
						</label>
					</p>
					<p class="woot-term-slug">
						<label>
							<?php esc_html_e( 'Slug', 'woot' ) ?>
							<input type="text" class="widefat woot-input" name="slug">
						</label>
					</p>
					<div class="woot-term-swatch">

					</div>
					<div class="hidden woot-term-tax"></div>

					<input type="hidden" class="woot-input" name="nonce" value="<?php echo wp_create_nonce( '_woot_create_attribute' ) ?>">
				</div>
				<div class="woot-modal-footer">
					<button class="button button-secondary woot-modal-close"><?php esc_html_e( 'Cancel', 'woot' ) ?></button>
					<button class="button button-primary woot-new-attribute-submit"><?php esc_html_e( 'Add New', 'woot' ) ?></button>
					<span class="message"></span>
					<span class="spinner"></span>
				</div>
			</div>
			<div class="woot-modal-backdrop media-modal-backdrop"></div>
		</div>

		<script type="text/template" id="tmpl-woot-input-color">

			<label><?php esc_html_e( 'Color', 'woot' ) ?></label><br>
			<input type="text" class="woot-input woot-input-color" name="swatch">

		</script>

		<script type="text/template" id="tmpl-woot-input-image">

			<label><?php esc_html_e( 'Image', 'woot' ) ?></label><br>
			<div class="woot-term-image-thumbnail">
				<img src="<?php echo esc_url( WC()->plugin_url() . '/assets/images/placeholder.png' ) ?>" class="woot-img" />
			</div>
			<div class="woot-add-remove-image">
				<input type="hidden" class="woot-input woot-input-image woot-term-image" name="swatch" value="" />
				<button type="button" class="woot-upload-image-button button"><?php esc_html_e( 'Upload/Add image', 'woot' ); ?></button>
				<button type="button" class="woot-remove-image-button button hidden"><?php esc_html_e( 'Remove image', 'woot' ); ?></button>
			</div>

		</script>

		<script type="text/template" id="tmpl-woot-input-label">

			<label>
				<?php esc_html_e( 'Label', 'woot' ) ?>
				<input type="text" class="widefat woot-input woot-input-label" name="swatch">
			</label>

		</script>

		<script type="text/template" id="tmpl-woot-input-tax">

			<input type="hidden" class="woot-input" name="taxonomy" value="{{data.tax}}">
			<input type="hidden" class="woot-input" name="type" value="{{data.type}}">

		</script>
		<?php
	}
}

new Woot_WCVariation_Swatches_Admin_Product();