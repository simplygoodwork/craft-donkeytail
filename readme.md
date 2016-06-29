# Donkeytail for Craft

Donkeytail is a Craft CMS fieldtype which allows you to quickly and easily content manage points on images. You can use it for locations on a faux map, showcasing multiple products within an image, or even pinning the tail on a donkey.

## Installation

1. Copy the `donkeytail/` folder to your `craft/plugins/` folder.
2. Go to Settings > Plugins from your Craft control panel and install the plugin.

## Requirements

This plugin requires Craft 2.5 or greater and has only been tested PHP version 7.

## Usage

### Terminology

In Donkeytail the image you're adding points to is called the *canvas* and the points you're adding are called *dots*.

### Settings

-  **Canvas asset**: The canvas image with which you'll add your dots to.
-  **Show siblings**: Enable this to show dots from other entries the current section. These will shown with half opacity.
-  **Dot type**: Choose either a round circle or a traditional map style pin. (The circle will use the center as the anchor point while the pin uses the bottom middle point)
-  **Dot width**: The width (in pixels) to show dots on the field, it's probably best to have this match your front end as close as possible.
-  **Dot colour**: Choose a colour for the dot fill (dots automatically have a white border)

### Template tags

The field returns 3 strings:

- `topLeftStyles`: Returns the top and left percentages as CSS style properties and values. For example `top:42.1210%;left:88.1337%;`
- `leftPercentage`: The left percentage value of the dot's anchor point in relation to the canvas
- `topPercentage`: The top percentage value of the dot's anchor point in relation to the canvas

### Real world example

You'll need to render the canvas asset yourself as you normally would within a template. (the field doens't offer this incase you'd like to use a different, perhaps simplified version in the control panel)

A real would example would likely have the canvas in a parent container with `position:relative`. The dots can then be set to `position:absolute` and their positions output using an inline style attribute and `{{ entry.fieldName.topLeftStyles }}`. Don't forget to use negative margins or similar to move your front-end marker's point to the anchor.

## Changelog


### 1.0.1

- Removes potentially upcoming feature reveal.
- Fixes readme intro to match marketing page.

### 1.0

- Initial release.