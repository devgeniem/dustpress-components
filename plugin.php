<?php
/**
 * Plugin Name: DustPress Components
 * Plugin URI: https://github.com/devgeniem/dustpress-components
 * Description: A WordPress, DustPress and ACF Flexible Contents plugin for modular component structures.
 * Version: 1.2.4
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
        add_action( 'init', __NAMESPACE__ . '\Components::add_options_page', 1, 1 );
        add_action( 'init', __NAMESPACE__ . '\Components::hook', 20, 1 );
        add_action( 'dustpress/partials', __NAMESPACE__ . '\Components::add_partial_path', 1, 1 );
        add_action( 'activated_plugin', __NAMESPACE__ . '\Components::load_first', 1, 1 );
        add_filter( 'acf/format_value/type=group', __NAMESPACE__ . '\Components::add_layout_static', 150, 3 );
        add_filter( 'acf/format_value/type=group', __NAMESPACE__ . '\Data::component_handle', 200, 3 );
        add_filter( 'acf/format_value/type=flexible_content', __NAMESPACE__ . '\Data::component_handle', 200, 3 );
    }

    /**
    * Adds Components settings options page
    */
    public static function add_options_page() {

        if ( is_admin() && function_exists( 'acf_add_options_page' ) ) {

            $dustpress_components = array(
                'page_title'    => __( 'DustPress components settings', 'dustpress-components' ),
                'menu_title'    => __( 'Components settings', 'dustpress-components' ),
                'menu_slug'     => __( 'components-settings', 'dustpress-components' ),
                'capability'    => 'manage_options',
            );
            acf_add_options_page( $dustpress_components );
        }

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

    /**
     * Get registered components
     *
     * @return array
     */
    public static function get_instances() {
        return self::$components;
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

                    if ( $fields instanceof \Geniem\ACF\Field\Flexible\Layout ) {
                        $fields = $fields->export();
                    }

                    $return[] = $fields;
                }
            }
        }
        ksort( $return );

        return $return;
    }

    /**
    * Gets component specific options from components plugin.php
    */
    private static function get_components_options() {

        $return = [];
        $options_placement = apply_filters( 'dustpress/components/options_placement', 'top' );


        if ( is_array( self::$components ) && count( self::$components ) > 0 ) {
            foreach ( apply_filters( 'dustpress/components', self::$components ) as $component ) {

                $tab_label = __( $component->label, $component->textdomain );

                if ( method_exists( $component, 'options' ) ) {

                    $component_options  = apply_filters( 'dustpress/components/options=' . $component->name, $component->options() );

                    if ( $component_options instanceof \Geniem\ACF\Field\Tab ) {
                        $component_options = \array_map( function( $field ) { return $field->export(); }, $component_options->get_fields() );
                    }

                    // if options were found add tab to component settings page
                    if ( ! empty( $component_options ) && is_array( $component_options ) ) {
                        if ( method_exists( $component_options, 'get_label' ) ) {
                            $label = $component_options->get_label();
                        }
                        else {
                            $label = $component->label;
                        }

                        $component_tab = array (
                            'key'                   => 'field_dpc_settings_' . $component->name,
                            'label'                 => $label,
                            'name'                  => 'dpc_' . $component->name . '_tab',
                            'type'                  => 'tab',
                            'instructions'          => '',
                            'required'              => 0,
                            'conditional_logic'     => 0,
                            'wrapper' => array (
                                'width' => '',
                                'class' => '',
                                'id'    => '',
                            ),
                            'placement' => $options_placement,
                            'endpoint'  => 0,
                        );

                        $component_tab      = apply_filters( 'dustpress/components/component_tab=' . $component->name, $component_tab );
                        $return[]           = $component_tab;

                        // merge component options
                        $return = array_merge( $return, $component_options );

                    }
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

                    if ( $fields instanceof \Geniem\ACF\Field\Flexible\Layout ) {
                        $fields = $fields->export();
                    }

                    $subfields = [];

                    foreach ( $fields['sub_fields'] as $subfield ) {
                        $subfields[] = $subfield['key'];
                    }
                    $item = array(
                        'key'               => 'clonable_' . $component->name,
                        'label'             => $component->label,
                        'name'              => $component->name,
                        'type'              => 'group',
                        'instructions'      => '',
                        'required'          => 0,
                        'conditional_logic' => 0,
                        'wrapper'           => array(
                            'width' => '',
                            'class' => '',
                            'id'    => '',
                        ),
                        'collapsed'         => '',
                        'layout'            => 'block',
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
                                'prefix_name'       => 0,
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
            'key'                   => apply_filters( 'dustpress/components/group_key', 'dpc_field_group' ),
            'title'                 => apply_filters( 'dustpress/components/group_title', __( 'Components', 'dustpress-components' ) ),
            'fields'                => array(
                array(
                    'key'               => apply_filters( 'dustpress/components/field_key', 'dpc_flexible_field' ),
                    'label'             => apply_filters( 'dustpress/components/field_label', __( 'Components', 'dustpress-components' ) ),
                    'name'              => apply_filters( 'dustpress/components/field_name', 'c' ),
                    'type'              => 'flexible_content',
                    'instructions'      => apply_filters( 'dustpress/components/field_instructions', '' ),
                    'required'          => 0,
                    'conditional_logic' => 0,
                    'wrapper'           => array(
                        'width' => apply_filters( 'dustpress/components/field_width', '' ),
                        'class' => apply_filters( 'dustpress/components/field_class', '' ),
                        'id'    => apply_filters( 'dustpress/components/field_id', '' ),
                    ),
                    'button_label'      => apply_filters( 'dustpress/components/field_button_label', __( 'Add component', 'dustpress-components' ) ),
                    'min'               => apply_filters( 'dustpress/components/field_min', '' ),
                    'max'               => apply_filters( 'dustpress/components/field_max', '' ),
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
            'menu_order'            => apply_filters( 'dustpress/components/field_menu_order', 0 ),
            'position'              => apply_filters( 'dustpress/components/field_position', 'normal' ),
            'style'                 => apply_filters( 'dustpress/components/field_style', 'default' ),
            'label_placement'       => apply_filters( 'dustpress/components/field_label_placement', 'top' ),
            'instruction_placement' => apply_filters( 'dustpress/components/field_instruction_placement', 'label' ),
            'hide_on_screen'        => '',
            'active'                => 0,
            'description'           => apply_filters( 'dustpress/components/field_description', '' ),
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

        // Adds component specific tab and options to options page component-settings
        acf_add_local_field_group( array(
            'key'       => 'group_dpc_settings',
            'title'     => apply_filters( 'dustpress/components/settings_title', __( 'Components Settings', 'dustpress-components' ) ),
            'fields'    => self::get_components_options(),
            // DustPress component options location
            'location' => array(
                array(
                    array(
                        'param'     => 'options_page',
                        'operator'  => '==',
                        'value'     => 'components-settings',
                    ),
                ),
            ),
            'menu_order'                => apply_filters( 'dustpress/components/settings_menu_order', 0 ),
            'position'                  => apply_filters( 'dustpress/components/settings_position', 'normal' ),
            'style'                     => apply_filters( 'dustpress/components/settings_style', 'default' ),
            'label_placement'           => apply_filters( 'dustpress/components/settings_label_placement', 'top' ),
            'instruction_placement'     => apply_filters( 'dustpress/components/settings_instruction_placement', 'label' ),
            'hide_on_screen'            => '',
            'active'                    => 1,
            'description'               => apply_filters( 'dustpress/components/settings_description', '' ),
        ));

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
     * Adds acf_fc_layout value to static components
     *
     * @param array  $value   Field values.
     * @param string $post_id Where field is saved.
     * @param array  $field   Field settings.
     */
    public static function add_layout_static( $value, $post_id, $field ) {
        if ( strpos( $field['key'], 'clonable' ) !== false ) {
            // Remove clonable from key so it matches the key format in the normal flexible content
            $component = str_replace( 'clonable_', '', $field['__key'] );
            if ( ! empty( $component ) ) {
                $value['acf_fc_layout'] = $component;
            }
        }
        return $value;
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
