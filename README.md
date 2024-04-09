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