<?php
/**
 * Text component class file
 */

namespace DustPress\Components;

use Geniem\ACF\Field;

/**
 * Text component class
 */
class Text extends Component {

	/**
	 * Component label
	 *
	 * @var string
	 */
	public $label = 'Text';

	/**
	 * Component name
	 *
	 * @var string
	 */
	public $name = 'text';

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->label = __( 'Text', 'dustpress-components' );
	}

	/**
	 * Fields of the component
	 *
	 * @return Field\Flexible\Layout
	 */
	public function fields() {
		return (new Field\Flexible\Layout( $this->name ))
			->set_key( 'dpc_text' )
			->set_name( $this->name )
			->set_label( $this->label )
			->add_field( (new Field\Textarea( 'Text' ))
				->set_key( 'dpc_text_text' )
				->set_name( 't' )
			);
	}
}

Components::add( new Text() );
