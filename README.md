# DustPress Components

A WordPress, DustPress and ACF Flexible Contents plugin for modular component structures.

## Installation

Install DustPress Components with Composer:

```
composer config repositories.dustpress-components git git@github.com:devgeniem/dustpress-components.git
composer require devgeniem/dustpress-components
```

## Components usage

### Clone Flexible Fields from Dustpress Components
Create an ACF Clone Field which clones `dpc_flexible_field` that is defined by DustPress Components plugin.

Example of a field group with cloned `dpc_flexible_field`:

```php
<?php

if( function_exists( 'acf_add_local_field_group' ) ):

acf_add_local_field_group(array (
    'key'       => 'group_cloned_dpc_flexible',
    'title'     => __( 'Components', 'theme_textdomain' ),
    'fields' => array (
        array (
            'key'               => 'field_cloned_dpc_flexible',
            'label'             => __( 'Components', 'theme-textdomain' ),
            'name'              => 'c',
            'type'              => 'clone',
            'instructions'      => __( 'Add new components from add_component', 'theme-textdomain' ),
            'required'          => 0,
            'conditional_logic' => 0,
            'wrapper' => array (
                'width' => '',
                'class' => '',
                'id'    => '',
            ),
            'clone' => array (
                0 => 'dpc_flexible_field',
            ),
            'display'       => 'seamless',
            'layout'        => 'block',
            'prefix_label'  => 0,
            'prefix_name'   => 1,
        ),
    ),
    // Location default page
    'location' => array (
        array (
            array (
                'param'     => 'page_template',
                'operator'  => '==',
                'value'     => 'default',
            ),
        )
    ),
    'menu_order'            => 100,
    'position'              => 'normal',
    'style'                 => 'default',
    'label_placement'       => 'top',
    'instruction_placement' => 'label',
    'hide_on_screen' => array (
        1 => 'the_content',
    ),
    'active'                => 1,
    'description'           => '',
));

endif;
```

DustPress Components contains components.dust file which loops through created components.
```dust
{#data.c}
	{>"before_component" /}
	{>"before_component_{acf_fc_layout}" /}
	{>"component_{acf_fc_layout}" /}
	{>"after_component_{acf_fc_layout}" /}
	{>"after_component" /}
{/data.c}
```

In your template file include `components.dust` with your cloned flexible field named as `c`.
You can have multiple component fields adding more allowed names (in addition to `c` with "dustpress/components/field_group_keys" filter).

```dust
{#Page.Content}

    {! Components !}
    {>"components" data=fields.c /}

{/Page.Content}
```

## Static fields

You can use DustPress Components as static components within your post templates as well.

Create an ACF Clone Field that clones your component's __clonable__ version that is automatically generated by the plugin.

```
    array (
        'key'                   => 'field_cloned_dpc_buttons',
        'label'                 => __('My static dustpress-component', 'theme-textdomain'),
        'name'                  => 'cloned_dpc_buttons',
        'type'                  => 'clone',
        'instructions'          => '',
        'required'              => 0,
        'conditional_logic'     => 0,
        'wrapper' => array (
            'width' => '',
            'class' => '',
            'id'    => ''
        ),
        'clone' => array (
            0 => 'clonable_dpc_buttons',
        ),
        'display'       => 'seamless',
        'layout'        => 'block',
        'prefix_label'  => 0,
        'prefix_name'   => 1
    ),
```

## Data filtering

### Filter component fields

You can manipulate and add fields to a component through the `dustpress/components/fields={field_name}` filter.

```php
<?php

// add field Two columns to content component
function add_fields_to_dpc_content( $fields ) {
 
    $fields['sub_fields'][] = array (
            'key'               => 'field_added_text_field',
            'label'             => __( 'Heading', 'theme-textdomain' ),
            'name'              => 'added_heading',
            'type'              => 'text',
            'instructions'      => '',
            'required'          => 0,
            'conditional_logic' => 0,
            'wrapper' => array (
                'width' => '',
                'class' => '',
                'id'    => '',
            ),
            'message'       => '',
            'default_value' => 0,
    );
 
    return $fields;
}
 
add_filter( 'dustpress/components/fields=content', 'add_fields_to_dpc_content' );
```

### Filter component data

You can manipulate the metadata stored for the component with the `dustpress/data/component={field_name}` filter. Use this to modify or add data for your component.

```php
<?php

// Modify the text component data to make the string $data['t'] uppercase.
function modify_component_data( $data ) {
    $data['t'] = strtoupper( $data['t'] );
    return $data;
}

add_filter( 'dustpress/data/component=text', 'modify_component_data' );
```

## Component settings

`dustpress-components` creates options page "Components settings" to WordPress admin side.
If component has options `dustpress-components` creates tab for components options on "Components settings" page.

You can add options to a component in plugin.php

```
    /**
        * Sets the Component options
        *
        * @return array Returns component option fields
        */
    public function options() {
        return array(
            array (
                'key'               => 'field_dpc_gmaps_options_api_key',
                'label'             => 'API key',
                'name'              => 'dpc_gmaps_options_api_key',
                'type'              => 'text',
                'instructions'      => '',
                'required'          => 0,
                'conditional_logic' => 0,
                'wrapper' => array (
                    'width' => '',
                    'class' => '',
                    'id'    => '',
                ),
                'default_value'     => '',
                'placeholder'       => '',
                'maxlength'         => '',
                'rows'              => '',
                'new_lines'         => '',
            )
        );
    }
```

## Disable registered components

You can disable registered components with filtering them in the `dustpress/components` hook.

```
add_filter( 'dustpress/components', 'disable_components' );
/**
 * Disable selected components.
 *
 * @param array $components All registered components.
 *
 * @return array $return Filtered components.
 */
function disable_components( $components ) {
    $return = array_filter( $components, function( $item ) {
            $components_to_disable = [
                'text',
                'content',
                'image',
            ];

            if ( in_array( $item->name, $components_to_disable, true ) ) {
                return false;
            }
            return true;
        }
    );
    return $return;
}
```
