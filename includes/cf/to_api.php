<?php
/**
 * Generated by the WordPress Meta Box Generator
 * https://jeremyhixon.com/tool/wordpress-meta-box-generator/
 * 
 * Retrieving the values:
 * Title = get_post_meta( get_the_ID(), 'to_apititle', true )
 * start date = get_post_meta( get_the_ID(), 'to_apistart-date', true )
 * start time = get_post_meta( get_the_ID(), 'to_apistart-time', true )
 * is_serie = get_post_meta( get_the_ID(), 'to_apiis_serie', true )
 * Duration = get_post_meta( get_the_ID(), 'to_apiduration', true )
 * categoria = get_post_meta( get_the_ID(), 'to_apicategoria', true )
 */
class to_api {
	private $config = '{"title":"To API","prefix":"to_api","domain":"to_api","class_name":"to_api","context":"normal","priority":"core","cpt":"ego_stream","fields":[{"type":"text","label":"Title","default":"Name Program","id":"to_apititle"},{"type":"date","label":"start date","max":"2048-12-09","min":"2022-09-01","step":"1","id":"to_apistart-date"},{"type":"time","label":"start time","default":"00:00","max":"23:59","min":"00:00","step":"1","id":"to_apistart-time"},{"type":"checkbox","label":"is_serie","id":"to_apiis_serie"},{"type":"number","label":"Duration","max":"1440","min":"1","step":"1","id":"to_apiduration"},{"type":"select","label":"categoria","default":"option-one","options":"option-one: Science & Technology\r\noption-two: Art\r\noption-three: Sports and Fitness\r\noption-four: Just Chatting\r\noption-five: Talk Shows & Podcasts\r\noption-six: Makers and Crafting\r\noption-seven: Tabletop RPG\r\noption-eigth: Music & Performing Arts\r\noption-nine: Special Events\r\noption-ten: Food & Drink\r\noption-eleven: Beauty & Body Art\r\noption-twelve: Travel & Outdoors\r\noption-thirteen: ASMR","id":"to_apicategoria"}]}';

	public function __construct() {
		$this->config = json_decode( $this->config, true );
		$this->process_cpts();
		add_action( 'add_meta_boxes', [ $this, 'add_meta_boxes' ] );
		add_action( 'admin_head', [ $this, 'admin_head' ] );
		add_action( 'save_post', [ $this, 'save_post' ] );
	}

	public function process_cpts() {
		if ( !empty( $this->config['cpt'] ) ) {
			if ( empty( $this->config['post-type'] ) ) {
				$this->config['post-type'] = [];
			}
			$parts = explode( ',', $this->config['cpt'] );
			$parts = array_map( 'trim', $parts );
			$this->config['post-type'] = array_merge( $this->config['post-type'], $parts );
		}
	}

	public function add_meta_boxes() {
		foreach ( $this->config['post-type'] as $screen ) {
			add_meta_box(
				sanitize_title( $this->config['title'] ),
				$this->config['title'],
				[ $this, 'add_meta_box_callback' ],
				$screen,
				$this->config['context'],
				$this->config['priority']
			);
		}
	}

	public function admin_head() {
		global $typenow;
		if ( in_array( $typenow, $this->config['post-type'] ) ) {
			?><?php
		}
	}

	public function save_post( $post_id ) {
		foreach ( $this->config['fields'] as $field ) {
			switch ( $field['type'] ) {
				case 'checkbox':
					update_post_meta( $post_id, $field['id'], isset( $_POST[ $field['id'] ] ) ? $_POST[ $field['id'] ] : '' );
					break;
				default:
					if ( isset( $_POST[ $field['id'] ] ) ) {
						$sanitized = sanitize_text_field( $_POST[ $field['id'] ] );
						update_post_meta( $post_id, $field['id'], $sanitized );
					}
			}
		}
	}

	public function add_meta_box_callback() {
		$this->fields_table();
	}

	private function fields_table() {
		?><table class="form-table" role="presentation">
			<tbody><?php
				foreach ( $this->config['fields'] as $field ) {
					?><tr>
						<th scope="row"><?php $this->label( $field ); ?></th>
						<td><?php $this->field( $field ); ?></td>
					</tr><?php
				}
			?></tbody>
		</table><?php
	}

	private function label( $field ) {
		switch ( $field['type'] ) {
			default:
				printf(
					'<label class="" for="%s">%s</label>',
					$field['id'], $field['label']
				);
		}
	}

	private function field( $field ) {
		switch ( $field['type'] ) {
			case 'checkbox':
				$this->checkbox( $field );
				break;
			case 'date':
			case 'number':
			case 'time':
				$this->input_minmax( $field );
				break;
			case 'select':
				$this->select( $field );
				break;
			default:
				$this->input( $field );
		}
	}

	private function checkbox( $field ) {
		printf(
			'<label class="rwp-checkbox-label"><input %s id="%s" name="%s" type="checkbox"> %s</label>',
			$this->checked( $field ),
			$field['id'], $field['id'],
			isset( $field['description'] ) ? $field['description'] : ''
		);
	}

	private function input( $field ) {
		printf(
			'<input class="regular-text %s" id="%s" name="%s" %s type="%s" value="%s">',
			isset( $field['class'] ) ? $field['class'] : '',
			$field['id'], $field['id'],
			isset( $field['pattern'] ) ? "pattern='{$field['pattern']}'" : '',
			$field['type'],
			$this->value( $field )
		);
	}

	private function input_minmax( $field ) {
		printf(
			'<input class="regular-text" id="%s" %s %s name="%s" %s type="%s" value="%s">',
			$field['id'],
			isset( $field['max'] ) ? "max='{$field['max']}'" : '',
			isset( $field['min'] ) ? "min='{$field['min']}'" : '',
			$field['id'],
			isset( $field['step'] ) ? "step='{$field['step']}'" : '',
			$field['type'],
			$this->value( $field )
		);
	}

	private function select( $field ) {
		printf(
			'<select id="%s" name="%s">%s</select>',
			$field['id'], $field['id'],
			$this->select_options( $field )
		);
	}

	private function select_selected( $field, $current ) {
		$value = $this->value( $field );
		if ( $value === $current ) {
			return 'selected';
		}
		return '';
	}

	private function select_options( $field ) {
		$output = [];
		$options = explode( "\r\n", $field['options'] );
		$i = 0;
		foreach ( $options as $option ) {
			$pair = explode( ':', $option );
			$pair = array_map( 'trim', $pair );
			$output[] = sprintf(
				'<option %s value="%s"> %s</option>',
				$this->select_selected( $field, $pair[0] ),
				$pair[0], $pair[1]
			);
			$i++;
		}
		return implode( '<br>', $output );
	}

	private function value( $field ) {
		global $post;
		if ( metadata_exists( 'post', $post->ID, $field['id'] ) ) {
			$value = get_post_meta( $post->ID, $field['id'], true );
		} else if ( isset( $field['default'] ) ) {
			$value = $field['default'];
		} else {
			return '';
		}
		return str_replace( '\u0027', "'", $value );
	}

	private function checked( $field ) {
		global $post;
		if ( metadata_exists( 'post', $post->ID, $field['id'] ) ) {
			$value = get_post_meta( $post->ID, $field['id'], true );
			if ( $value === 'on' ) {
				return 'checked';
			}
			return '';
		} else if ( isset( $field['checked'] ) ) {
			return 'checked';
		}
		return '';
	}
}
new to_api;