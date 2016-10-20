<?php

namespace DustPress\Components;

class Image extends Component {
	var $label = 'Image';
	var $name = 'image';

	public function fields() {
		return array (
			'key' => 'dpc_image',
			'name' => $this->name,
			'label' => $this->label,
			'display' => 'block',
			'sub_fields' => array (
				array (
					'key' => 'dpc_image_image',
					'label' => 'Image',
					'name' => 'i',
					'type' => 'image',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array (
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'return_format' => 'array',
					'preview_size' => 'thumbnail',
					'library' => 'all',
					'min_width' => '',
					'min_height' => '',
					'min_size' => '',
					'max_width' => '',
					'max_height' => '',
					'max_size' => '',
					'mime_types' => '',
				),
			),
			'min' => '',
			'max' => '',
		);
	}
}

Components::add( new Image() );