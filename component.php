<?php
namespace DustPress\Components;

class Component {
	var $path;

	static $before_run = false;
	static $after_run = false;

	function __construct() {
		$class = __CLASS__;

		if ( method_exists( $this, 'before' ) ) {
			add_filter( 'dustpress/data/component=' . $this->name, function( $d ) use ($class) {
				if ( ! $class::$before_run ) {
					$this->before();
					$class::$before_run = true;
				}

				return $d;
			}, 1, 1 );
		}

		if ( method_exists( $this, 'data' ) ) {
			add_filter( 'dustpress/data/component=' . $this->name, function( $d ) {
				return apply_filters( 'dustpress/components/data=' . $this->name, $this->data( $d ) );
			}, 2, 1 );
		}
		
		if ( method_exists( $this, 'after' ) ) {
			add_filter( 'dustpress/data/main', function( $d ) use ($class) {
				if ( ! $class::$after_run ) {
					$this->after();
					$class::$after_run = true;
				}

				return $d;
			}, 3, 1 );
		}

		$componentReflection = new \ReflectionClass( $this );

		$this->path = dirname( $componentReflection->getFileName() );

		add_filter( 'dustpress/partials', [ $this, 'add_partial_path' ], 1, 1 );

		add_filter( 'wp_enqueue_scripts', [ $this, 'enqueue_styles' ] );
	}

	function add_partial_path( $p ) {
		$p[] = $this->path;

		return $p;
	}

	function enqueue_styles() {
		if ( is_readable( $this->path . '/plugin.css' ) ) {
			wp_enqueue_style( 'dustpress_component_' . $this->name, $this->path . '/plugin.css' );
		}
	}
}