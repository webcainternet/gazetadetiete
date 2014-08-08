<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Class to create a control to accept CSS rules and preview them instantly.
 *
 * @since 1.0.0
 */
class Themify_CustomCSS_Control extends Themify_Control {

	/**
	 * Type of this control.
	 * @access public
	 * @var string
	 */
	public $type = 'themify_customcss';

	/**
	 * Render the control's content.
	 *
	 * @since 1.0.0
	 */
	public function render_content() {
		$v = $this->value();

		// Remove JSON stuff
		$v = str_replace( '{"css":"', '', $v );
		$v = str_replace( '"}', '', $v );

		// Escape double quotes
		$v = str_replace( '"', '\"', $v );
		$v = str_replace( "'", '\"', $v );

		// Rebuild JSON
		$v = '{"css":"' . $v . '"}';
		$values = json_decode( $v );
		wp_enqueue_script( 'json2' );

		// Custom CSS
		$css = isset( $values->css ) ? $values->css : '';
		?>

		<?php if ( $this->show_label && ! empty( $this->label ) ) : ?>
			<span class="customize-control-title themify-control-title"><?php echo esc_html( $this->label ); ?></span>
		<?php endif; ?>

		<div class="themify-customizer-brick">

			<textarea class="customcss" rows="20"><?php echo str_replace( array( '{', '}' ), array( "{\n  ", "\n}\n" ), $css ); ?></textarea>

		</div>

		<input <?php $this->link(); ?> value='<?php echo $v; ?>' type="hidden" class="<?php echo $this->type; ?>_control themify-customizer-value-field"/>
		<?php
	}
}