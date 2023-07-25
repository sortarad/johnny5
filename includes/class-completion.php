<?php

namespace Posty\Johnny5;

class Completion {
	/**
	 * Get the prompt from the post ID and block ID.
	 *
	 * @param int $post_id  The ID of the post.
	 * @param int $block_id The ID of the block.
	 * @return string
	 */
	public function get_prompt_from_post_id_and_block_id( $post_id, $block_id ) {
		$post = get_post( $post_id );

		if ( ! $post ) {
			return '';
		}

		$blocks = array_filter(
			parse_blocks( $post->post_content ),
			function( $block ) use ( $block_id ) {
				return $block['blockName'] === 'johnny5/johnny5' && isset( $block['attrs']['id'] ) && $block['attrs']['id'] === $block_id;
			}
		);

		if ( empty( $blocks ) ) {
			return '';
		}

		return array_values( $blocks )[0]['attrs']['prompt'];
	}

	/**
	 * Replace placeholders in the prompt with the provided substitutes.
	 *
	 * @param string $prompt      The prompt, with {{placeholders}} in curly braces.
	 * @param array  $substitutes Array of substitutes. Keys will be replaced with their values.
	 * @return string
	 */
	public function replace_placeholders_in_prompt( $prompt, $substitutes ) {
		$placeholders = array_map(
			function( $key ) {
				return '{{' . $key . '}}';
			},
			array_keys( $substitutes )
		);

		return str_replace( $placeholders, array_values( $substitutes ), $prompt );
	}
}
