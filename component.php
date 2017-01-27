<?php
namespace DustPress\Components;

class Component {
    var $path;

    function __construct() {
        $class = get_class($this);

        if ( method_exists( $this, 'before' ) ) {
            add_filter( 'dustpress/data/component=' . $this->name, function( $d ) use ($class) {
                if ( ! isset( static::$before_run ) ) {
                    $this->before();
                    static $before_run = true;
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
                if ( ! isset( static::$after_run ) ) {
                    $this->after();
                    static $after_run = true;
                }

                return $d;
            }, 3, 1 );
        }

        $componentReflection = new \ReflectionClass( $this );

        $this->path = dirname( $componentReflection->getFileName() );
        $this->version = get_plugin_data( $this->path . '/plugin.php' )['Version'];

        // $this->url can be overriden in components plugin.php
        if ( ! isset( $this->url ) ) {
            $this->url = plugin_dir_url( $componentReflection->getFileName() );
        }

        add_filter( 'dustpress/partials', [ $this, 'add_partial_path' ], 1, 1 );

        add_filter( 'wp_enqueue_scripts', [ $this, 'enqueue_styles' ] );
    }

    function add_partial_path( $p ) {
        $p[] = $this->path;

        return $p;
    }

    function enqueue_styles() {
        if ( is_readable( $this->path . '/dist/plugin.css' ) ) {
            wp_enqueue_style( 'dustpress_component_css_' . $this->name, $this->url . 'dist/plugin.css', '', $this->version );
        }

        if ( is_readable( $this->path . '/dist/plugin.js' ) ) {
            wp_enqueue_script( 'dustpress_component_js_' . $this->name, $this->url . 'dist/plugin.js', array( 'jquery' ), $this->version );
        }
    }
}