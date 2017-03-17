<?php
/**
 * Plugin Name: DustPress Components
 * Plugin URI: https://github.com/devgeniem/dustpress-components
 * Description: A WordPress, DustPress and ACF Flexible Contents plugin for modular component structures.
 * Version: 0.4.1
 * Author: Geniem Oy
 * Text Domain: dustpress-components
 * Author URI: http://www.geniem.com
 */

namespace DustPress\Components;

require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
require_once( 'classes/component.php' );
require_once( 'classes/data.php' );

/**
 * Class Components
 *
 * @package DustPress\Components
 */
class Components {

    /**
     * Holds the loaded components.
     *
     * @var array
     */
    private static $components;

    /**
     * Creates or returns an instance of the class
     *
     * @return Mongo|boolean
     */
    public static function execute() {
        if ( defined( 'DPC_EXECUTED' ) ) {
            return false;
        } else {
            define( 'DPC_EXECUTED', true );
        }
        load_textdomain( 'dustpress-components', dirname( __FILE__ ) . '/languages/' . get_locale() . '.mo' );
        add_action( 'acf/init', __NAMESPACE__ . '\Components::hook', 1, 1 );
        add_action( 'dustpress/partials', __NAMESPACE__ . '\Components::add_partial_path', 1, 1 );
        add_action( 'activated_plugin', __NAMESPACE__ . '\Components::load_first', 1, 1 );
        add_filter( 'dustpress/data', __NAMESPACE__ . '\Data::component_invoke', 1, 1 );
    }

    public static function add_partial_path( $p ) {
        $p[] = dirname( __FILE__ );

        return $p;
    }

    public static function hook() {
        self::gather_local_components();
        self::register_field_group();
    }

    private static function gather_local_components() {
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

    private static function get_components() {
        $return = [];
        if ( is_array( self::$components ) && count( self::$components ) > 0 ) {
            foreach ( apply_filters( 'dustpress/components', self::$components ) as $component ) {
                $component->label = __( $component->label, $component->textdomain );
                if ( method_exists( $component, 'init' ) ) {
                    $component->init();
                }
                if ( method_exists( $component, 'fields' ) ) {
                    $fields = $component->fields();
                    $fields = apply_filters( 'dustpress/components/fields', $fields );
                    $fields = apply_filters( 'dustpress/components/fields=' . $component->name, $fields );
                    $return[] = $fields;
                }
            }
        }
        ksort( $return );

        return $return;
    }

    private static function get_local_components() {
        $return = [];
        if ( is_array( self::$components ) && count( self::$components ) > 0 ) {
            foreach ( apply_filters( 'dustpress/components', self::$components ) as $component ) {
                if ( method_exists( $component, 'fields' ) ) {
                    $component->label = __( $component->label, $component->textdomain );
                    $fields           = $component->fields();
                    $fields           = apply_filters( 'dustpress/components/fields', $fields );
                    $fields           = apply_filters( 'dustpress/components/fields=' . $component->name, $fields );
                    $subfields = [];
                    foreach ( $fields['sub_fields'] as $subfield ) {
                        $subfields[] = $subfield['key'];
                    }
                    $item = array(
                        'key'               => 'clonable_' . $component->name,
                        'label'             => $component->label,
                        'name'              => 'c',
                        'type'              => 'repeater',
                        'instructions'      => '',
                        'required'          => 1,
                        'conditional_logic' => 0,
                        'wrapper'           => array(
                            'width' => '',
                            'class' => '',
                            'id'    => '',
                        ),
                        'collapsed'         => '',
                        'min'               => 1,
                        'max'               => 1,
                        'layout'            => 'block',
                        'button_label'      => __( 'Add component', 'dustpress-components' ),
                        'sub_fields'        => array(
                            array(
                                'key'               => 'clone_' . $component->name,
                                'label'             => $component->label,
                                'name'              => $component->name,
                                'type'              => 'clone',
                                'instructions'      => '',
                                'required'          => 0,
                                'conditional_logic' => 0,
                                'wrapper'           => array(
                                    'width' => '',
                                    'class' => '',
                                    'id'    => '',
                                ),
                                'clone'             => $subfields,
                                'display'           => 'seamless',
                                'layout'            => 'block',
                                'prefix_label'      => 0,
                                'prefix_name'       => 1,
                            )
                        )
                    );
                    $return[] = $item;
                }
            }
        }
        ksort( $return );

        return $return;
    }

    public static function register_field_group() {
        acf_add_local_field_group( array(
            'key'                   => 'dpc_field_group',
            'title'                 => __( 'Components', 'dustpress-components' ),
            'fields'                => array(
                array(
                    'key'               => 'dpc_flexible_field',
                    'label'             => __( 'Components', 'dustpress-components' ),
                    'name'              => 'c',
                    'type'              => 'flexible_content',
                    'instructions'      => '',
                    'required'          => 0,
                    'conditional_logic' => 0,
                    'wrapper'           => array(
                        'width' => '',
                        'class' => '',
                        'id'    => '',
                    ),
                    'button_label'      => __( 'Add component', 'dustpress-components' ),
                    'min'               => '',
                    'max'               => '',
                    'layouts'           => self::get_components()
                ),
            ),
            'location'              => array(
                array(
                    array(
                        'param'    => 'post_type',
                        'operator' => '==',
                        'value'    => 'post',
                    ),
                ),
            ),
            'menu_order'            => 0,
            'position'              => 'normal',
            'style'                 => 'default',
            'label_placement'       => 'top',
            'instruction_placement' => 'label',
            'hide_on_screen'        => '',
            'active'                => 0,
            'description'           => '',
        ) );
        acf_add_local_field_group( array(
            'key'                   => 'dpc_local_fields',
            'title'                 => 'Local fields',
            'fields'                => self::get_local_components(),
            'location'              => array(
                array(
                    array(
                        'param'    => 'post_type',
                        'operator' => '==',
                        'value'    => 'post',
                    ),
                ),
            ),
            'menu_order'            => 0,
            'position'              => 'normal',
            'style'                 => 'default',
            'label_placement'       => 'top',
            'instruction_placement' => 'label',
            'hide_on_screen'        => '',
            'active'                => 0,
            'description'           => '',
        ) );
    }

    public static function load_first() {
        $path = str_replace( WP_PLUGIN_DIR . '/', '', __FILE__ );
        if ( $plugins = get_option( 'active_plugins' ) ) {
            $key = array_search( $path, $plugins, true );
            if ( $key ) {
                array_splice( $plugins, $key, 1 );
                array_unshift( $plugins, $path );
                update_option( 'active_plugins', $plugins );
            }
        }
    }

    /**
     * A private constructor.
     */
    private function __construct() {
    }
}

if ( is_plugin_active( 'advanced-custom-fields-pro/acf.php' ) ) {
    Components::execute();
}
