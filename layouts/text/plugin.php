<?php

namespace DustPress\Components;
use Geniem\ACF\Field;

class Text extends Component {
	var $label = 'Tekstikenttä';
	var $name = 'text';

	public function fields() {
		return (new Field\Flexible\Layout( $this->name ))
			->set_key( 'dpc_text' )
			->set_name( $this->name )
			->set_label( $this->label )
			->add_field( (new Field\Textarea( 'Tekstikenttä' ))
				->set_key( 'dpc_text_text' )
				->set_name( 't' )
			);
	}

	public function data( $data ) {
		return $data;
	}
}

Components::add( new Text() );
