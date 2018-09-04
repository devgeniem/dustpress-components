<?php
/**
 * Image component
 */

namespace DustPress\Components;

use Geniem\ACF\Field;

/**
 * Image component class
 */
class Image extends Component {
	/**
	 * Component label
	 *
	 * @var string
	 */
	public $label = 'Image';

	/**
	 * Component name
	 *
	 * @var string
	 */
	public $name = 'image';

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->label = __( 'Image', 'dustpress-components' );
	}

	/**
	 * Fields of the component
	 *
	 * @return Field\Flexible\Layout
	 */
	public function fields() {
		return ( new Field\Flexible\Layout( $this->name ) )
			->set_key( 'dpc_image' )
			->set_name( $this->name )
			->set_label( $this->label )
			->add_field( ( new Field\Image( 'Image' ) )
				->set_key( 'dpc_image_image' )
				->set_name( 'i' )
			);
	}
}

Components::add( new Image() );
