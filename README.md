***

# Custom Product Filter

This WordPress plugin allows you to add a custom filter for WooCommerce products based on dimensions. It provides a slider that lets users filter products by their minimum length and width requirements.

## Features

 - Slider filters for length and width dimensions
 - AJAX-powered dynamic filtering of products
 - Responsive grid layout for displaying filtered products
 - Clean and modern design with customizable styles

## Installation

 1. Upload the `custom-product-filter.zip` folder to the `/wp-content/plugins/`
    directory or install the plugin through the WordPress plugin
    installer.
 2. Activate the plugin through the 'Plugins' menu in WordPress.
 3. Install the official Font Awesome plugin for the checkmark icon to work or remove from PHP file.

 ## Updating/contributing

 1. To update the plugin, edit files as needed
 2. Update sliderfilter.php version and release notes (top section) with useful information
 3. Compress the custom-product-filter into zip following the version format
 4. Upload to plugin install page https://apexcountertopsnw.com/wp-admin/plugin-install.php?tab=upload
 5. If making big changes to anything, it may be best to delete the plugin and upload the new version, instead of overwriting.
 
## Remnants Inventory

Please see [ADDINGPRODUCT.MD](/ADDINGPRODUCT.MD) for how to update the remnant inventory

## Usage

After activating the plugin, you can use the shortcode `[custom_product_filter]` to display the filter and product grid on any page or post.

Alternatively, you can add the following code to your theme's template files:

```<?php echo do_shortcode('[custom_product_filter]'); ?>```

## Customization

You can customize the appearance of the filter and product grid by modifying the CSS styles in `custom-filter.css.`

## Development

This plugin is built with the following technologies:

- WordPress
- WooCommerce
- jQuery
- AJAX
- HTML
- CSS
- PHP

**Files**
`custom-filter.css`: Styles for the filter and product grid.
`custom-filter.js`: JavaScript code for handling slider interactions and AJAX requests.
`sliderfilter.php`: The main plugin file containing PHP functions and shortcode implementation.

**Release Date**

4/4/2024

## Credits
This plugin was developed by Devon Potter.