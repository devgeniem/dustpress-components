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
     *
     * @param  mixed $value   Field value.
     * @param  int   $post_id Wp post id.
     * @param  array $field   Field data.
     * @return mixed          Modified $value.
     */
    public static function component_handle( $value, $post_id, $field = null ) {
        // If filtering is disabled, bail early.
        if (
            defined( 'DPC_DISABLE_DATA_FILTERING' ) &&
            DPC_DISABLE_DATA_FILTERING === true
        ) {
            return $value;
        }

        // If field is flexible content, loop its fields
        if ( $field && $field['type'] === 'flexible_content' && ! empty( $value ) ) {
            foreach ( $value as $i => $component ) {
                $value[ $i ] = static::component_handle( $component, $post_id );
            }
        }
        else {
            // If field has acf_fc_layout
            if ( is_array( $value ) && array_key_exists( 'acf_fc_layout', $value ) ) {

                // Run component specific data function if it exists
                $filter_slug = 'dustpress/components/data=' . $value['acf_fc_layout'];
                if ( has_filter( $filter_slug ) ) {
                    $value = apply_filters( $filter_slug, $value );
                }
            }
        }

        return $value;
    }
}
