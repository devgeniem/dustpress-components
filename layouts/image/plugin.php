<?php

namespace DustPress\Components;
use Geniem\ACF\Field;

class Image extends Component {
	var $label = 'Kuva';
	var $name = 'image';

	public function fields() {
		return (new Field\Flexible\Layout( $this->name ))
			->set_key( 'dpc_image' )
			->set_name( $this->name )
			->set_label( $this->label )
			->add_field( (new Field\Image( 'Kuva' ))
				->set_key( 'dpc_image_image' )
				->set_name( 'i' )
			);
	}
}

Components::add( new Image() );