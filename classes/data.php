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
class Data
{

    private static $field_group_keys;

    /**
     * A filter to alter the data of an ACF Flexible Content component.
     * @param  mixed $data  DustPress data
     * @return mixed
     */
    public static function component_invoke($data)
    {
        // Set allowed component field group keys, by default 'c'.
        if (is_null(self::$field_group_keys)) {
            self::$field_group_keys = apply_filters('dustpress/components/field_group_keys', ['c']);
        }

        if (is_array($data) || is_object($data)) {
            foreach ((array) $data as $key => $item) {
                // prevent null bytes from raising notices, we don't need them anyway
                $key = str_replace(chr(0), "", $key);

                // Run component_handle function for ACF fields array.
                if (is_object($item) && isset($item->fields)) {
                    self::component_handle($item->fields);
                } elseif (is_array($item) && isset($item['fields'])) {
                    self::component_handle($item['fields']);
                } // No ACF fields array, continue recursively.
                elseif (is_object($item) || is_array($item)) {
                    self::component_invoke($item);
                }

                // Store possible modifications back to the referenced data.
                if (is_array($data)) {
                    $data[ $key ] = $item;
                } elseif (is_object($data)) {
                    $data->{ $key } = $item;
                }
            }
        }

        return $data;
    }

    /**
     * Handle ACF fields.
     * @param  array &$data  Array of ACF fields data
     */
    public static function component_handle(&$data)
    {
        if (is_array($data)) {
            // Loop through ACF fields.
            foreach ($data as $field_key => &$field) {
                // Loop through all allowed component field group keys.
                foreach (self::$field_group_keys as $field_group_key) {
                    if (is_array($field) && isset($field[ $field_group_key ])) {
                        if (is_array($field[ $field_group_key ])) {
                            // Loop through all defined components.
                            foreach ($field[ $field_group_key ] as &$component) {
                                // If component is in a flexible field, run appropriate filter.
                                if (isset($component[ 'acf_fc_layout' ])) {
                                    $component = apply_filters("dustpress/data/component=" . $component[ 'acf_fc_layout' ], $component);
                                }
                            }
                        }
                    } // If component is statically defined, run appropriate filter.
                    elseif ($field_key === $field_group_key && ! isset($field[0][ 'acf_fc_layout'])) {
                        $field = apply_filters("dustpress/data/component=" . $field_key, $field);
                    }
                }
            }
        }
    }
}
