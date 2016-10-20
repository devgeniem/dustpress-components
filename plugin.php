<?php
/**
 * Plugin Name: DustPress Components
 * Plugin URI: https://github.com/devgeniem/dustpress-components
 * Description: A WordPress, DustPress and ACF Flexible Contents plugin for modular component structures.
 * Version: 0.0.1
 * Author: Geniem Oy / Miika Arponen
 * Author URI: http://www.geniem.com
 */

namespace DustPress\Components;

require_once('component.php');

class Components {
	private static $components;

	/**
	 * Creates or returns an instance of the class
	 * 
	 * @return Mongo
	 */
	public static function Execute() {
		if ( defined('DPC_EXECUTED') ) {
			return false;
		}
		else {
			define('DPC_EXECUTED',true);
		}

		add_action( 'acf/init', __NAMESPACE__ . '\Components::hook', 1, 1 );

		add_action( 'dustpress/partials', __NAMESPACE__ . '\Components::addPartialPath', 1, 1 );
	}

	public static function addPartialPath( $p ) {
		$p[] = dirname( __FILE__ );

		return $p;
	}

	public static function hook() {
		self::gatherLocalComponents();
		self::registerFieldGroup();
	}

	private static function gatherLocalComponents() {
		if ( is_readable( __DIR__ . '/layouts/' ) ) {
			foreach ( new \RecursiveIteratorIterator( new \RecursiveDirectoryIterator( __DIR__ . '/layouts/', \RecursiveDirectoryIterator::SKIP_DOTS ) ) as $file ) {
				$meta = pathinfo( $file );

				if ( $meta['basename'] === 'plugin.php' ) {
					require_once( $file );
				}
			}
		}
	}

	public static function add( $component ) {
		self::$components[] = $component;
	}

	private static function getComponents() {
		$return = [];

		if ( is_array( self::$components ) && count( self::$components ) > 0 ) {
			foreach ( self::$components as $component ) {
				if ( method_exists( $component, 'fields' ) ) {
					$return[ $component->label ] = $component->fields();
				}
			}
		}

		ksort( $return );

		return $return;
	}

	public static function registerFieldGroup() {
		acf_add_local_field_group(array (
			'key' => 'dpc_field_group',
			'title' => 'Components',
			'fields' => array (
				array (
					'key' => 'dpc_flexible_field',
					'label' => 'Components',
					'name' => 'c',
					'type' => 'flexible_content',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array (
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'button_label' => 'Add a component',
					'min' => '',
					'max' => '',
					'layouts' => self::getComponents()
				),
			),
			'location' => array (
				array (
					array (
						'param' => 'post_type',
						'operator' => '==',
						'value' => 'post',
					),
				),
			),
			'menu_order' => 0,
			'position' => 'normal',
			'style' => 'default',
			'label_placement' => 'top',
			'instruction_placement' => 'label',
			'hide_on_screen' => '',
			'active' => 0,
			'description' => '',
		));
	}


	/**
	 * A private constructor.
	 * 
	 */
	private function __construct() {}
}

Components::Execute();