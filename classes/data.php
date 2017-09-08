<?php
/**
 * The component data filtering class.
 */

namespace DustPress\Components;

/**
 * Class Data
 *
 * @package DustPress\Components
 */
class Data {

    /**
     * A filter to alter the data of an ACF Flexible Content component.
     * @param  mixed $data  DustPress data
     * @return mixed
     */
    public static function component_invoke( $data ) {
        if ( is_array( $data ) || is_object( $data ) ) {
            foreach ( (array) $data as $key => $item ) {

                // prevent null bytes from raising notices, we don't need them anyway
                $key = str_replace( chr(0), "", $key );

                // Run component_handle function for ACF fields array.
                if ( is_object( $item ) && isset( $item->fields ) ) {
                    self::component_handle( $item->fields );
                }
                else if ( is_array( $item ) && isset( $item['fields'] ) ) {
                    self::component_handle( $item['fields'] );
                }
                // No ACF fields array, continue recursively.
                else if ( is_object( $item ) || is_array( $item ) ) {
                    self::component_invoke( $item );
                }

                // Store possible modifications back to the referenced data.
                if ( is_array( $data ) ) {
                    $data[ $key ] = $item;
                }
                else if ( is_object( $data ) ) {
                    $data->{ $key } = $item;
                }
            }
        }

        return $data;
    }

    /**
     * Go through fields recursively until a component is found then handle it
     *
     * @param  array $data Array of ACF fields data.
     */
    public static function component_handle( &$data ) {
        if ( is_array( $data ) ) {
            foreach ( $data as &$val ) {
                self::component_handle( $val );
            }
            if ( array_key_exists( 'acf_fc_layout', $data ) ) {
                $filter_slug = 'dustpress/components/data=' . $data['acf_fc_layout'];
                if ( has_filter( $filter_slug ) ) {
                    $data = apply_filters( $filter_slug, $data );
                }
            }
        }
    }
}
