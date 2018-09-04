<?php
/**
 * Component Content
 */

namespace DustPress\Components;

use Geniem\ACF\Field;

/**
 * Component class
 */
class Content extends Component {
	/**
	 * Component label
	 *
	 * @var string
	 */
	public $label = 'Content';

	/**
	 * Component name
	 *
	 * @var string
	 */
	public $name = 'content';

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->label = __( 'Content', 'dustpress-components' );
	}

	/**
	 * Fields of the component
	 *
	 * @return Field\Flexible\Layout
	 */
	public function fields() {
		return ( new Field\Flexible\Layout( $this->name ) )
			->set_key( 'dpc_content' )
			->set_name( $this->name )
			->set_label( $this->label )
			->add_field( ( new Field\Wysiwyg( 'Content' ) )
				->set_key( 'dpc_content_content' )
				->set_name( 'c' )
			);
	}

	/**
	 * Filter the component data
	 *
	 * @param string $data Data to filter.
	 * @return string
	 */
	public function data( $data ) {
		$data['c'] = apply_filters( 'the_content', $data['c'] );

		return $data;
	}
}

Components::add( new Content() );
