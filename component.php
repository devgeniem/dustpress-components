<?php
namespace DustPress\Components;

class Component {
	function __construct() {
		if ( method_exists( $this, 'data' ) ) {
			add_filter( 'dustpress/data/component=' . $this->name, [ $this, 'data' ], 1, 1 );
		}

		add_filter( 'dustpress/partials', [ $this, 'addPartialPath' ], 1, 1 );
	}

	function addPartialPath( $p ) {
		$componentReflection = new \ReflectionClass( $this );

		$p[] = dirname( $componentReflection->getFileName() );

		return $p;
	}
}