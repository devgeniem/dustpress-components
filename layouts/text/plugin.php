<?php

namespace DustPress\Components;

class Text extends Component {
	var $label = 'Text';
	var $name = 'text';

	public function fields() {
		return array (
			'key' => 'dpc_text',
			'name' => $this->name,
			'label' => $this->label,
			'display' => 'block',
			'sub_fields' => array (
				array (
					'key' => 'dpc_text_text',
					'label' => 'Text',
					'name' => 't',
					'type' => 'text',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array (
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'default_value' => '',
					'placeholder' => '',
					'prepend' => '',
					'append' => '',
					'maxlength' => '',
				),
			),
			'min' => '',
			'max' => '',
		);
	}

	public function data( $data ) {
		return $data;
	}
}

Components::add( new Text() );
