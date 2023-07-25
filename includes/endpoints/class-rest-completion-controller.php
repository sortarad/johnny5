<?php
/**
 * Class used to register REST API completion endpoints.
 *
 * @package johnny5/johnny5
 */

namespace Posty\Johnny5\Endpoints;

use WP_Error;
use WP_REST_Controller;
use WP_REST_Server;

/**
 * Defines our endpoints.
 */
class REST_Completion_Controller extends WP_REST_Controller {
	/**
	 * Constructs the controller.
	 *
	 * @since 5.0.0
	 */
	public function __construct() {
		$this->namespace = 'johnny5/v1';
		$this->rest_base = 'completion';
	}

	/**
	 * Register REST API endpoints.
	 */
	public function register_routes() {
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base,
			array(
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'create_item' ),
					'permission_callback' => '__return_true',
					'args'                => $this->get_endpoint_args_for_item_schema( WP_REST_Server::CREATABLE ),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);
	}

	/**
	 * POST `/johnny5/v1/completion`
	 *
	 * @param WP_REST_Request $request The API request.
	 * @return WP_REST_Response
	 */
	public function create_item( $request ) {
		$post_id     = $request->get_param( 'post_id' );
		$block_id    = $request->get_param( 'block_id' );
		$substitutes = $request->get_param( 'substitutes' );
		$completion  = new \Posty\Johnny5\Completion();
		$prompt      = $completion->get_prompt_from_post_id_and_block_id( $post_id, $block_id );
		$settings    = \Posty\Johnny5\Config::get_settings();

		if ( count( $settings ) !== count( array_filter( $settings ) ) ) {
			return new WP_Error(
				'johnny5_settings_error',
				__( 'There was an error getting the result.', 'johnny5' ),
				array( 'status' => 500 )
			);
		}

		if ( $prompt === '' ) {
			return new WP_Error(
				'johnny5_prompt_error',
				__( 'There was an error getting the result.', 'johnny5' ),
				array( 'status' => 500 )
			);
		}

		if ( count( array_filter( $substitutes ) ) !== substr_count( $prompt, '{{' ) ) {
			return new WP_Error(
				'johnny5_substitutes_error',
				__( 'There was an error getting the result.', 'johnny5' ),
				array( 'status' => 500 )
			);
		}

		$prompt = $completion->replace_placeholders_in_prompt( $prompt, $substitutes );

		try {
			$stream = new \Posty\Johnny5\Stream();

			do {
				$stream->start();

				$client = \OpenAI::factory()
					->withApiKey( $settings['api_key'] )
					->withHttpClient( new \GuzzleHttp\Client( array() ) )
					->make();

				$completion = $client->chat()->createStreamed(
					array(
						'model'      => $settings['model'],
						'messages'   => array(
							array(
								'role'    => 'user',
								'content' => $prompt,
							),
						),
						'max_tokens' => $settings['max_tokens'] - strlen( $prompt ),
					)
				);

				foreach ( $completion as $response ) {
					$content       = $response->choices[0]->delta->content;
					$finish_reason = $response->choices[0]->finishReason;

					if ( $finish_reason ) {
						if ( $finish_reason === 'length' ) {
							$stream->send_data( wp_json_encode( '…' ) );
						}

						$stream->send_data( '[DONE]' );
						break;
					}

					$stream->send_data( wp_json_encode( $content ) );
				}
			} while ( ! $stream->should_abort() );
		} catch ( \Exception $e ) {
			return new WP_Error(
				'johnny5_completion_error',
				__( 'There was an error getting the result.', 'johnny5' ),
				array( 'status' => 500 )
			);
		}
	}

	/**
	 * Retrieves the endpoint schema, conforming to JSON Schema.
	 *
	 * @return array Schema data.
	 */
	public function get_item_schema() {
		if ( $this->schema ) {
			return $this->add_additional_fields_schema( $this->schema );
		}

		$schema = array(
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => 'posty-johnny5-completion',
			'type'       => 'object',
			'properties' => array(
				'block_id'    => array(
					'description' => __( 'The ID of the block that is being submitted.', 'johnny5' ),
					'type'        => 'string',
					'format'      => 'uuid',
					'required'    => true,
					'context'     => array( 'edit' ),
				),
				'post_id'     => array(
					'description' => __( 'The ID of the post that has the block.', 'johnny5' ),
					'type'        => 'number',
					'required'    => true,
					'context'     => array( 'edit' ),
				),
				'substitutes' => array(
					'description'          => __( 'Key/value pairs of placeholders to substitute.', 'textdomain' ),
					'type'                 => 'object',
					'context'              => array( 'edit' ),
					'required'             => true,
					'additionalProperties' => false,
					'patternProperties'    => array(
						'^\\w+$' => array(
							'type' => array( 'string', 'number' ),
						),
					),
					'arg_options'          => array(
						'sanitize_callback' => function( $value ) {
							return array_map( 'sanitize_text_field', $value );
						},
					),
				),
			),
		);

		$schema = rest_default_additional_properties_to_false( $schema );

		$this->schema = $schema;

		return $this->add_additional_fields_schema( $this->schema );
	}
}
