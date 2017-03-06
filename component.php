<?php
/**
 * The component base class.
 */

namespace DustPress\Components;

/**
 * Class Component
 *
 * @package DustPress\Components
 */
class Component {

    /**
     * The component root directory.
     *
     * @var string
     */
    protected $path;

    /**
     * The display name.
     *
     * @var string
     */
    public $label;

    /**
     * The slug of the component.
     * This must match the suffix of the dust template.
     * component-{suffix}.dust
     *
     * @var string
     */
    public $name;

    /**
     * Acf field key.
     *
     * @var string
     */
    public $key;

    /**
     * The plugin version.
     *
     * @var string
     */
    public $version;

    /**
     * The plugin textdomain.
     *
     * @var string
     */
    public $textdomain;

    /**
     * Do we want to enqueue component styles automatically?
     *
     * @var boolean
     */
    public $enqueue_style = true;

    /**
     * Do we want to enqueue component scripts automatically?
     *
     * @var boolean
     */
    public $enqueue_script = true;

    /**
     * The class constructor.
     * This must be called in the subclass constuctor.
     */
    function __construct() {
        $class                = get_class( $this );
        $component_reflection = new \ReflectionClass( $this );

        // If there is no path set, default to component directory.
        if ( empty( $this->path ) ) {
            $this->path = dirname( $component_reflection->getFileName() );
        }
        $plugin_file_path = $this->path . '/plugin.php';
        $plugin           = get_plugin_data( $plugin_file_path );
        $this->version    = $plugin['Version'] ?? '';
        $this->textdomain = $plugin['TextDomain'] ?? '';

        if ( is_readable( $this->path . '/languages/' . get_locale() . '.mo' ) &&
             ! empty( $this->textdomain )
        ) {
            load_textdomain( $this->textdomain, $this->path . '/languages/' . get_locale() . '.mo' );
        }

        if ( method_exists( $this, 'before' ) ) {
            add_filter( 'dustpress/data/component=' . $this->name, function( $d ) use ( $class ) {
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
            add_filter( 'dustpress/data/main', function( $d ) use ( $class ) {
                if ( ! isset( static::$after_run ) ) {
                    $this->after();
                    static $after_run = true;
                }

                return $d;
            }, 3, 1 );
        }

        // $this->url can be overriden in the component subclass.
        if ( ! isset( $this->url ) ) {
            $this->url = plugin_dir_url( $plugin_file_path );
        }

        // Enqueues can be globally disabled with the following constants.
        if ( defined( 'DPC_NO_STYLES' ) ) {
            $this->enqueue_style = false;
        }
        if ( defined( 'DPC_NO_SCRIPTS' ) ) {
            $this->enqueue_script = false;
        }

        add_filter( 'dustpress/partials', [ $this, 'add_partial_path' ], 1, 1 );
        add_filter( 'wp_enqueue_scripts', [ $this, 'enqueue_style' ] );
    }

    /**
     * Add the component partial path to DustPress partials.
     *
     * @param string $p DustPress partial path array.
     *
     * @return array
     */
    public function add_partial_path( $p ) {
        $p[] = $this->path;

        return $p;
    }

    /**
     * Enqueue component styles.
     * Enqueues can be controlled with the boolean class attributes.
     */
    public function enqueue_style() {
        if ( $this->enqueue_style && is_readable( $this->path . '/dist/plugin.css' ) ) {
            wp_enqueue_style(
                'dustpress_component_css_' . $this->name,
                $this->url . 'dist/plugin.css',
                '',
                $this->version,
                true
            );
        }
    }


    /**
     * Run before the first iteration of the component is excecuted on the front-end.
     *
     * This can be overridden in a subclass.
     */
    public function before() {}

    /**
     * Run after the last component iteration is excecuted on the front-end.
     *
     * This can be overridden in a subclass.
     */
    public function after() {}
}
