<?php
// This file is generated. Do not modify it manually.
return array(
	'morsel' => array(
		'$schema' => 'https://schemas.wp.org/trunk/block.json',
		'apiVersion' => 3,
		'name' => 'tidbits/morsel',
		'version' => '0.1.0',
		'title' => 'Morsel',
		'category' => 'text',
		'description' => 'A single Tidbit post displayed within a Tidbits block.',
		'parent' => array(
			'tidbits/tidbit'
		),
		'example' => array(
			'viewportWidth' => 360
		),
		'attributes' => array(
			'postId' => array(
				'type' => 'number',
				'default' => 0
			)
		),
		'usesContext' => array(
			'tidbits/displayMode',
			'tidbits/dividerBorder',
			'tidbits/itemPadding'
		),
		'supports' => array(
			'html' => false,
			'reusable' => false,
			'color' => array(
				'text' => true,
				'background' => true,
				'__experimentalDefaultControls' => array(
					'text' => true
				)
			),
			'typography' => array(
				'fontSize' => true,
				'__experimentalFontFamily' => true,
				'__experimentalFontWeight' => true,
				'__experimentalDefaultControls' => array(
					'fontSize' => true,
					'__experimentalFontFamily' => true
				)
			)
		),
		'selectors' => array(
			'root' => '.tidbits-morsel',
			'color' => array(
				'text' => '.tidbits-morsel',
				'background' => '.tidbits-morsel'
			),
			'typography' => '.tidbits-morsel__term, .tidbits-morsel__trigger, .tidbits-morsel__content, .tidbits-morsel__inner'
		),
		'textdomain' => 'tidbits',
		'editorScript' => 'file:./index.js',
		'editorStyle' => 'file:./index.css',
		'render' => 'file:./render.php'
	),
	'tidbits' => array(
		'$schema' => 'https://schemas.wp.org/trunk/block.json',
		'apiVersion' => 3,
		'name' => 'tidbits/tidbit',
		'version' => '0.1.0',
		'title' => 'Tidbits',
		'category' => 'text',
		'keywords' => array(
			'tidbits',
			'tips',
			'tricks',
			'insights',
			'terms',
			'definitions',
			'glossary',
			'facts',
			'fun facts',
			'quick tips',
			'short insights'
		),
		'description' => 'Display hand-picked Tidbit posts in a variety of layouts.',
		'example' => array(
			'viewportWidth' => 480,
			'attributes' => array(
				'displayMode' => 'accordion'
			),
			'innerBlocks' => array(
				array(
					'name' => 'tidbits/morsel'
				),
				array(
					'name' => 'tidbits/morsel'
				),
				array(
					'name' => 'tidbits/morsel'
				)
			)
		),
		'attributes' => array(
			'displayMode' => array(
				'type' => 'string',
				'default' => 'accordion',
				'enum' => array(
					'accordion',
					'stacked',
					'columns'
				)
			),
			'iconColor' => array(
				'type' => 'string',
				'default' => ''
			),
			'dividerBorder' => array(
				'type' => 'object',
				'default' => array(
					
				)
			),
			'itemPadding' => array(
				'type' => 'string',
				'default' => ''
			)
		),
		'providesContext' => array(
			'tidbits/displayMode' => 'displayMode',
			'tidbits/dividerBorder' => 'dividerBorder',
			'tidbits/itemPadding' => 'itemPadding'
		),
		'supports' => array(
			'html' => false,
			'anchor' => true,
			'align' => array(
				'wide',
				'full'
			),
			'layout' => array(
				'allowEditing' => false
			),
			'color' => array(
				'background' => true,
				'gradients' => true,
				'__experimentalDefaultControls' => array(
					'background' => true
				)
			),
			'spacing' => array(
				'margin' => array(
					'top',
					'bottom'
				),
				'padding' => true
			),
			'__experimentalBorder' => array(
				'color' => true,
				'width' => true,
				'style' => true,
				'__experimentalDefaultControls' => array(
					'color' => true,
					'width' => true,
					'style' => true
				)
			)
		),
		'selectors' => array(
			'root' => '.tidbits',
			'color' => array(
				'background' => '.tidbits'
			),
			'border' => '.tidbits',
			'spacing' => array(
				'padding' => '.tidbits',
				'margin' => '.tidbits'
			)
		),
		'textdomain' => 'tidbits',
		'editorScript' => 'file:./index.js',
		'editorStyle' => 'file:./index.css',
		'style' => 'file:./style-index.css',
		'render' => 'file:./render.php',
		'viewScriptModule' => 'file:./view.js'
	)
);
