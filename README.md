# DustPress Components

A WordPress, DustPress and ACF Flexible Contents plugin for modular component structures.

## Installation

Install DustPress Components with Composer:

```
composer config repositories.dustpress-components git git@github.com:devgeniem/dustpress-components.git
composer require devgeniem/dustpress-components
```

## Usage

### Clone Flexible Fields from Dustpress Components
Create an ACF Clone Field that clones the `dpc_flexible_field` which is defined by the DustPress Components plugin.

Example of a field group with a cloned `dpc_flexible_field`:

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

or if you prefer to use ACF Codifier which comes as a depedency with DustPress Components:

```php
<?php
namespace Geniem\ACF;

// Create new group
$group = (new Group( __( 'Components', 'theme_textdomain' ) ))
            ->set_key( 'group_cloned_dpc_flexible' )
            ->set_menu_order( 100 )
            ->hide_element( 'the_content' );

// Create a new rule group.
$rule_group = (new RuleGroup())
            ->add_rule( 'post_type', '==', 'post' );

// Add rule group to the field group.
$group->add_rule_group( $rule_group );

$clone = (new Field\CloneField( __( 'Components', 'theme-textdomain' ) ))
            ->set_key( 'field_cloned_dpc_flexible' )
            ->set_name( 'c' )
            ->set_instructions( __( 'Add new components from add_component', 'theme-textdomain' ) )
            ->add_clone( 'dpc_flexible_field' )
            ->set_name_prefix();

$group->add_field( $clone )
      ->register();
```

DustPress Components contains a components.dust file which loops through the created components. If you need to insert HTML code before or after components, you can do so without modifying the component itself.
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

```dust
{#Page.Content}

    {! Components !}
    {>"components" data=fields.c /}

{/Page.Content}
```

## Static fields

You can use DustPress Components as static components within your post templates as well.

Create an ACF Clone Field that clones your component's __clonable__ version which is automatically generated by the plugin.

```php
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

or with ACF Codifier:

```php
$clone = (new Field\CloneField( __('My static dustpress-component', 'theme-textdomain') ))
            ->set_key( 'field_cloned_dpc_buttons' )
            ->set_name( 'cloned_dpc_buttons' )
            ->add_clone( 'clonable_dpc_buttons' )
            ->set_name_prefix();
```

## Data filtering

### Filter component fields

You can manipulate and add fields to a component through the `dustpress/components/fields={field_name}` filter. The filtering needs to be done with the same method the fields have been originally defined, i.e. if they've been defined with the array syntax, they can't be modified with the Codifier.

All DustPress Components' built-in components have been built with the Codifier and thus need to be modified with it.

```php
<?php

// add a text field to content component as its first field
function add_fields_to_dpc_content( $fields ) {

    $heading = (new \Geniem\ACF\Field\Text( __( 'Heading', 'theme-textdomain' ) ))
        ->set_key( 'field_added_text_field' )
        ->set_name( 'added_heading' );

    $fields->add_field( $heading, 'first' );

    return $fields;
}

add_filter( 'dustpress/components/fields=content', 'add_fields_to_dpc_content' );
```

You can also add fields before or after existing fields with Codifier's `add_field_before()` and `add_field_after()` functions, which take the target field's key as their second parameter.

Obviously you can also remove fields from the components. Following example removes the original text field from the aforementioned Text component:

```php
<?php

// add a text field to content component as its first field
function remove_fields_from_dpc_content( $fields ) {

    $fields->remove_field( 'dpc_text_text' );

    return $fields;
}

add_filter( 'dustpress/components/fields=content', 'remove_fields_from_dpc_content' );
```

### Filter component data

You can manipulate the metadata stored for the component with the `dustpress/data/component={field_name}` filter. Use this to modify or add data to your component.

```php
<?php

// Modify the text component data to make the string $data['t'] uppercase.
function modify_component_data( $data ) {
    $data['t'] = strtoupper( $data['t'] );
    return $data;
}

add_filter( 'dustpress/components/data=text', 'modify_component_data' );
```

#### Disable data filtering

To disable data filtering define the constant `DPC_DISABLE_DATA_FILTERING` with a boolean value of `true`. This is useful if data filtering is done elsewhere to prevent double handling.

```php
<?php
define( 'DPC_DISABLE_DATA_FILTERING', true );
```

### Override component data function

Sometimes you may want to override the whole data function of a component. It can be done via another filter and closures:

```php
<?php

function override_component_data_filter( $original ) {
    return function( $data ) {
        // Alter the data here.
        return $data;
    };
}

add_filter( 'dustpress/components/data_method=text', 'override_component_data_filter' );
```

You can also call the original data function either before or after your custom code:

```php
<?php

function override_component_data_filter( $original ) {
    return function( $data ) use ( $original ) {
        // Alter the data here.

        // Run the original data function after your custom modifications.
        return $original( $data );
    };
}

add_filter( 'dustpress/components/data_method=text', 'override_component_data_filter' );
```

## Component settings

Dustpress Components creates an options page called "Components settings" to the WordPress admin side. Components can add options to that page as easily as their own fields are defined. If component has option fields, they are displayed in their own tab on the options page.

With the Codifier the options are gathered under a Tab field. If you rather use the array format, you can just define an array of fields.

```php
public function options() {
    $options = new \Geniem\ACF\Field\Tab( $this->label );
    
    $text    = new \Geniem\ACF\Field\Text( 'API key' );
    $text->set_key( 'field_dpc_gmaps_options_api_key' )
         ->set_name( 'dpc_gmaps_options_api_key' );

    $options->add_field( $text );

    return $options;
}
```

## Disable registered components

You can disable registered components with filtering them in the `dustpress/components` hook.

```php
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
