<?php

namespace DustPress\Components;

class Content extends Component {
	var $label = 'Content';
	var $name = 'content';

	public function fields() {
		return array (
			'key' => 'dpc_content',
			'name' => $this->name,
			'label' => $this->label,
			'display' => 'block',
			'sub_fields' => array (
				array (
					'key' => 'dpc_content_content',
					'label' => 'Content',
					'name' => 'c',
					'type' => 'wysiwyg',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array (
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'default_value' => '',
					'tabs' => 'all',
					'toolbar' => 'full',
					'media_upload' => 1,
				),
			),
			'min' => '',
			'max' => '',
		);
	}

	public function data( $data ) {
		$data['c'] = apply_filters( 'the_content', $data['c'] );

		return $data;
	}
}

Components::add( new Content() );