<?php if ( ! empty( $content ) ) : ?>
	<div <?php echo get_block_wrapper_attributes(); ?>>
		<form data-johnny5-form>
			<?php wp_nonce_field( 'wp_rest' ); ?>
			<input type="hidden" name="post_id" value="<?php echo esc_attr( get_the_ID() ); ?>">
			<input type="hidden" name="block_id" value="<?php echo esc_attr( $attributes['id'] ); ?>">
			<?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<button type="submit" class="wp-element-button"><?php esc_html_e( 'Generate', 'johnny5' ); ?></button>
		</form>
	</div>
<?php endif; ?>
