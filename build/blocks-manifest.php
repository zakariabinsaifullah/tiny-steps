<?php
// This file is generated. Do not modify it manually.
return array(
	'carousel' => array(
		'$schema' => 'https://schemas.wp.org/trunk/block.json',
		'apiVersion' => 3,
		'name' => 'tnys/carousel',
		'version' => '0.0.1',
		'title' => 'Carousel',
		'category' => 'tnys',
		'description' => 'A swipeable row of slides. Pair it with Carousel Arrows for remote navigation.',
		'keywords' => array(
			'slider',
			'carousel',
			'slides'
		),
		'allowedBlocks' => array(
			'tnys/slide'
		),
		'supports' => array(
			'html' => false,
			'anchor' => true,
			'align' => array(
				'wide',
				'full'
			),
			'spacing' => array(
				'margin' => true
			)
		),
		'attributes' => array(
			'perView' => array(
				'type' => 'object',
				'default' => array(
					'Desktop' => 3,
					'Tablet' => 2,
					'Mobile' => 1.2
				)
			),
			'gap' => array(
				'type' => 'object',
				'default' => array(
					'Desktop' => 24,
					'Tablet' => 24,
					'Mobile' => 12
				)
			),
			'height' => array(
				'type' => 'object'
			),
			'gridDesktop' => array(
				'type' => 'boolean',
				'default' => false
			),
			'autoplay' => array(
				'type' => 'boolean',
				'default' => false
			),
			'interval' => array(
				'type' => 'number',
				'default' => 5
			),
			'loop' => array(
				'type' => 'boolean',
				'default' => false
			),
			'resMode' => array(
				'type' => 'string',
				'default' => 'Desktop'
			)
		),
		'textdomain' => 'tiny-steps',
		'editorScript' => 'file:./index.js',
		'editorStyle' => 'file:./index.css',
		'style' => 'file:./style-index.css',
		'viewScript' => 'file:./view.js'
	),
	'carousel-arrows' => array(
		'$schema' => 'https://schemas.wp.org/trunk/block.json',
		'apiVersion' => 3,
		'name' => 'tnys/carousel-arrows',
		'version' => '0.0.1',
		'title' => 'Carousel Arrows',
		'category' => 'tnys',
		'description' => 'Previous and next buttons that control a Carousel anywhere on the page.',
		'keywords' => array(
			'slider',
			'carousel',
			'navigation',
			'arrows'
		),
		'supports' => array(
			'html' => false,
			'spacing' => array(
				'margin' => true
			)
		),
		'attributes' => array(
			'target' => array(
				'type' => 'string',
				'default' => ''
			),
			'justify' => array(
				'type' => 'object',
				'default' => array(
					'Desktop' => 'left'
				)
			),
			'resMode' => array(
				'type' => 'string',
				'default' => 'Desktop'
			),
			'size' => array(
				'type' => 'number',
				'default' => 48
			),
			'hideWhenStatic' => array(
				'type' => 'boolean',
				'default' => true
			)
		),
		'textdomain' => 'tiny-steps',
		'editorScript' => 'file:./index.js',
		'editorStyle' => 'file:./index.css',
		'style' => 'file:./style-index.css'
	),
	'icon' => array(
		'$schema' => 'https://schemas.wp.org/trunk/block.json',
		'apiVersion' => 3,
		'name' => 'tnys/icon',
		'version' => '0.0.1',
		'title' => 'Icon',
		'category' => 'tnys',
		'description' => 'Display SVG icons from library or upload custom SVGs.',
		'supports' => array(
			'reusable' => false,
			'html' => false,
			'align' => array(
				'wide',
				'full'
			),
			'color' => array(
				'__experimentalSkipSerialization' => true,
				'gradients' => true,
				'link' => false,
				'__experimentalDefaultControls' => array(
					'background' => true,
					'text' => true
				)
			),
			'__experimentalBorder' => array(
				'__experimentalSkipSerialization' => true,
				'color' => true,
				'radius' => true,
				'style' => true,
				'width' => true
			),
			'spacing' => array(
				'__experimentalSkipSerialization' => true,
				'margin' => true,
				'padding' => true,
				'__experimentalDefaultControls' => array(
					'margin' => false,
					'padding' => false
				)
			),
			'shadow' => array(
				'__experimentalSkipSerialization' => true
			)
		),
		'example' => array(
			
		),
		'attributes' => array(
			'listTitle' => array(
				'type' => 'string'
			),
			'listDesc' => array(
				'type' => 'string'
			),
			'resMode' => array(
				'type' => 'string',
				'default' => 'Desktop'
			),
			'blockStyle' => array(
				'type' => 'object'
			),
			'tagName' => array(
				'type' => 'string',
				'default' => 'div'
			),
			'iconName' => array(
				'type' => 'string',
				'default' => 'wordpress'
			),
			'customSvgCode' => array(
				'type' => 'string'
			),
			'iconType' => array(
				'type' => 'string',
				'default' => 'fill'
			),
			'strokeWidth' => array(
				'type' => 'number',
				'default' => 1.2
			),
			'sizes' => array(
				'type' => 'object',
				'default' => array(
					'Desktop' => 24
				)
			),
			'iconSize' => array(
				'type' => 'number',
				'default' => 24
			),
			'iconMarginTop' => array(
				'type' => 'string'
			),
			'width' => array(
				'type' => 'number'
			),
			'justifyContent' => array(
				'type' => 'string'
			),
			'href' => array(
				'type' => 'string'
			),
			'linkTarget' => array(
				'type' => 'string'
			),
			'linkRel' => array(
				'type' => 'string'
			),
			'showTitle' => array(
				'type' => 'boolean',
				'default' => false
			),
			'heading' => array(
				'type' => 'string'
			),
			'headingTag' => array(
				'type' => 'string',
				'default' => 'p'
			),
			'listGap' => array(
				'type' => 'string'
			),
			'titleColor' => array(
				'type' => 'string'
			),
			'titleSize' => array(
				'type' => 'string'
			),
			'titleFontFamily' => array(
				'type' => 'string'
			),
			'titleFontWeight' => array(
				'type' => 'string'
			),
			'titleMarginBottom' => array(
				'type' => 'string'
			),
			'showDesc' => array(
				'type' => 'boolean',
				'default' => false
			),
			'description' => array(
				'type' => 'string'
			),
			'descTag' => array(
				'type' => 'string',
				'default' => 'p'
			),
			'descColor' => array(
				'type' => 'string'
			),
			'descSize' => array(
				'type' => 'string'
			),
			'descFontFamily' => array(
				'type' => 'string'
			),
			'showButton' => array(
				'type' => 'boolean',
				'default' => false
			),
			'buttonText' => array(
				'type' => 'string'
			),
			'buttonUrl' => array(
				'type' => 'string'
			),
			'buttonLinkTarget' => array(
				'type' => 'string'
			),
			'buttonLinkRel' => array(
				'type' => 'string'
			),
			'buttonMarginTop' => array(
				'type' => 'string'
			),
			'buttonIconName' => array(
				'type' => 'string'
			),
			'buttonCustomSvgCode' => array(
				'type' => 'string'
			),
			'buttonIconType' => array(
				'type' => 'string',
				'default' => 'fill'
			),
			'buttonStrokeWidth' => array(
				'type' => 'number'
			),
			'iconVerticalAlign' => array(
				'type' => 'string',
				'default' => 'center'
			)
		),
		'textdomain' => 'tiny-steps',
		'editorScript' => 'file:./index.js',
		'editorStyle' => 'file:./index.css',
		'style' => 'file:./style-index.css'
	),
	'slide' => array(
		'$schema' => 'https://schemas.wp.org/trunk/block.json',
		'apiVersion' => 3,
		'name' => 'tnys/slide',
		'version' => '0.0.1',
		'title' => 'Slide',
		'category' => 'tnys',
		'description' => 'One slide of a carousel. Add any blocks inside it.',
		'parent' => array(
			'tnys/carousel'
		),
		'supports' => array(
			'html' => false,
			'reusable' => false,
			'color' => array(
				'background' => true,
				'text' => true,
				'link' => true
			),
			'spacing' => array(
				'padding' => true,
				'blockGap' => true
			),
			'dimensions' => array(
				'minHeight' => true
			),
			'__experimentalBorder' => array(
				'color' => true,
				'radius' => true,
				'style' => true,
				'width' => true
			),
			'shadow' => true,
			'layout' => array(
				'default' => array(
					'type' => 'flex',
					'orientation' => 'vertical',
					'justifyContent' => 'stretch'
				),
				'allowSwitching' => false,
				'allowOrientation' => false,
				'allowInheriting' => false
			)
		),
		'attributes' => array(
			'imageId' => array(
				'type' => 'number'
			),
			'imageUrl' => array(
				'type' => 'string'
			),
			'imageAlt' => array(
				'type' => 'string',
				'default' => ''
			),
			'imagePosition' => array(
				'type' => 'string',
				'default' => '50% 50%'
			),
			'shade' => array(
				'type' => 'boolean',
				'default' => false
			)
		),
		'textdomain' => 'tiny-steps',
		'editorScript' => 'file:./index.js',
		'editorStyle' => 'file:./index.css',
		'style' => 'file:./style-index.css'
	),
	'social-share' => array(
		'$schema' => 'https://schemas.wp.org/trunk/block.json',
		'apiVersion' => 3,
		'name' => 'tnys/social-share',
		'version' => '0.0.1',
		'title' => 'Social Share',
		'category' => 'tnys',
		'icon' => 'share',
		'description' => 'Add social sharing buttons for the current post.',
		'example' => array(
			
		),
		'supports' => array(
			'html' => false,
			'align' => array(
				'wide',
				'full'
			),
			'spacing' => array(
				'__experimentalSkipSerialization' => true,
				'margin' => true,
				'padding' => true,
				'__experimentalDefaultControls' => array(
					'margin' => false,
					'padding' => false
				)
			)
		),
		'attributes' => array(
			'showCopyLink' => array(
				'type' => 'boolean',
				'default' => true
			),
			'showLinkedIn' => array(
				'type' => 'boolean',
				'default' => true
			),
			'showTwitter' => array(
				'type' => 'boolean',
				'default' => true
			),
			'showFacebook' => array(
				'type' => 'boolean',
				'default' => true
			),
			'iconSize' => array(
				'type' => 'string',
				'default' => '24px'
			),
			'gap' => array(
				'type' => 'string',
				'default' => '12px'
			),
			'iconColor' => array(
				'type' => 'string'
			),
			'iconBgColor' => array(
				'type' => 'string'
			),
			'iconRadius' => array(
				'type' => 'string'
			),
			'iconPadding' => array(
				'type' => 'string'
			),
			'blockStyle' => array(
				'type' => 'object'
			),
			'justifyContent' => array(
				'type' => 'string'
			)
		),
		'textdomain' => 'tiny-steps',
		'editorScript' => 'file:./index.js',
		'editorStyle' => 'file:./index.css',
		'style' => 'file:./style-index.css',
		'viewScript' => 'file:./view.js',
		'render' => 'file:./render.php'
	),
	'group-full-height' => array(
		'$schema' => 'https://schemas.wp.org/trunk/block.json',
		'apiVersion' => 3,
		'name' => 'tnys/group-full-height-extension',
		'title' => 'Group Force Full Height Extension',
		'editorScript' => 'file:./index.js',
		'style' => 'file:./style-index.css'
	),
	'highlight' => array(
		'$schema' => 'https://schemas.wp.org/trunk/block.json',
		'apiVersion' => 3,
		'name' => 'tnys/highlight-extension',
		'title' => 'Highlight Extension',
		'editorScript' => 'file:./index.js',
		'editorStyle' => 'file:./index.css',
		'style' => 'file:./style-index.css'
	),
	'hover-color' => array(
		'$schema' => 'https://schemas.wp.org/trunk/block.json',
		'apiVersion' => 3,
		'name' => 'tnys/hover-color-extension',
		'title' => 'Hover Color Extension',
		'editorScript' => 'file:./index.js',
		'style' => 'file:./style-index.css'
	),
	'iconic-button' => array(
		'$schema' => 'https://schemas.wp.org/trunk/block.json',
		'apiVersion' => 3,
		'name' => 'tnys/iconic-button-extension',
		'title' => 'Iconic Button Extension',
		'editorScript' => 'file:./index.js',
		'editorStyle' => 'file:./index.css',
		'style' => 'file:./style-index.css'
	),
	'kadence-row-overlay' => array(
		'$schema' => 'https://schemas.wp.org/trunk/block.json',
		'apiVersion' => 3,
		'name' => 'tnys/kadence-row-overlay-extension',
		'title' => 'Kadence Row Overlay Extension',
		'editorScript' => 'file:./index.js',
		'style' => 'file:./style-index.css'
	),
	'read-more-button' => array(
		'$schema' => 'https://schemas.wp.org/trunk/block.json',
		'apiVersion' => 3,
		'name' => 'tnys/read-more-button-extension',
		'title' => 'Read More Button Extension',
		'editorScript' => 'file:./index.js',
		'style' => 'file:./style-index.css'
	),
	'text-max-width' => array(
		'$schema' => 'https://schemas.wp.org/trunk/block.json',
		'apiVersion' => 3,
		'name' => 'tnys/text-max-width-extension',
		'title' => 'Text Max Width Extension',
		'editorScript' => 'file:./index.js',
		'style' => 'file:./style-index.css'
	)
);
