<?php

namespace DustPress\Components;
use Geniem\ACF\Field;

class Content extends Component {
	var $label = 'Content';
	var $name = 'content';

	public function fields() {
		return (new Field\Flexible\Layout( $this->name ))
			->set_key( 'dpc_content' )
			->set_name( $this->name )
			->set_label( $this->label )
			->add_field( (new Field\Wysiwyg( 'Content' ))
				->set_key( 'dpc_content_content' )
				->set_name( 'c' )
			);
	}

	public function data( $data ) {
		$data['c'] = apply_filters( 'the_content', $data['c'] );

		return $data;
	}
}

Components::add( new Content() );