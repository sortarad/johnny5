<?php
$label = $attributes['label'] ?? '';
$name  = $attributes['name'] ?? '';

if ( empty( $label ) || empty( $name ) ) {
	return;
}
?>

<input type="text" name="substitutes[<?php echo esc_attr( $name ); ?>]" id="<?php echo esc_attr( $name ); ?>" placeholder="<?php echo esc_attr( $label ); ?>" />
