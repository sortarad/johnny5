<?php
$label   = $attributes['label'] ?? '';
$name    = $attributes['name'] ?? '';
$options = $attributes['options'] ?? '';

if ( empty( $label ) || empty( $name ) || empty( $options ) ) {
	return;
}

$options = explode( "\n", $options );
?>

<select name="substitutes[<?php echo esc_attr( $name ); ?>]" id="<?php echo esc_attr( $name ); ?>">
	<option value="" disabled selected><?php echo esc_html( $label ); ?></option>
	<?php foreach ( $options as $option ) : ?>
		<option value="<?php echo esc_attr( $option ); ?>"><?php echo esc_html( $option ); ?></option>
	<?php endforeach; ?>
</select>
