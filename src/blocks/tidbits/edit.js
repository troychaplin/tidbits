import { __ } from '@wordpress/i18n';
import { useMemo } from '@wordpress/element';
import {
	useBlockProps,
	InnerBlocks,
	InspectorControls,
	useSettings,
	DimensionControl,
} from '@wordpress/block-editor';
import {
	BaseControl,
	ColorPalette,
	SelectControl,
	BorderControl,
	__experimentalToolsPanel as ToolsPanel,
	__experimentalToolsPanelItem as ToolsPanelItem,
} from '@wordpress/components';

const ALLOWED_BLOCKS = [ 'tidbits/morsel' ];
const TEMPLATE = [ [ 'tidbits/morsel' ] ];

const DISPLAY_MODE_OPTIONS = [
	{ value: 'accordion', label: __( 'Accordion', 'tidbits' ) },
	{ value: 'stacked', label: __( 'Stacked', 'tidbits' ) },
	{ value: 'columns', label: __( 'Columns', 'tidbits' ) },
];

const SPACING_PRESET_PREFIX = 'var:preset|spacing|';
const DIMENSION_PRESET_PREFIX = 'var:preset|dimension|';

// Resolve a stored spacing value to a CSS value: a preset reference becomes its
// custom-property var, anything else (e.g. '0', '24px') passes through unchanged.
const spacingPresetCssVar = ( value ) => {
	const slug = value?.match( /var:preset\|spacing\|(.+)/ );
	return slug ? `var(--wp--preset--spacing--${ slug[ 1 ] })` : value;
};

// DimensionControl speaks the `dimension` preset type; our presets and CSS var
// are `spacing`. Swap the prefix at the control boundary so the stored value
// stays a spacing preset (which render.php and the editor preview understand).
const toDimensionValue = ( value ) =>
	value?.startsWith( SPACING_PRESET_PREFIX )
		? value.replace( SPACING_PRESET_PREFIX, DIMENSION_PRESET_PREFIX )
		: value;
const toSpacingValue = ( value ) =>
	value?.startsWith( DIMENSION_PRESET_PREFIX )
		? value.replace( DIMENSION_PRESET_PREFIX, SPACING_PRESET_PREFIX )
		: value;

export default function Edit( { attributes, setAttributes, clientId } ) {
	const { displayMode, iconColor, dividerBorder, itemPadding } = attributes;

	const [ themeColors ] = useSettings( 'color.palette' );

	// The theme defines spacing presets but no dimension presets, so feed the
	// spacing scale to DimensionControl via its `dimensionSizes` prop.
	const [ themeSpacingSizes ] = useSettings( 'spacing.spacingSizes.theme' );
	const dimensionSizes = useMemo(
		() => ( { theme: themeSpacingSizes ?? [] } ),
		[ themeSpacingSizes ]
	);

	// Mirror context values as CSS custom properties on the editor canvas so
	// the morsel previews update live without needing a full re-render.
	const customStyle = {};
	if ( iconColor ) {
		customStyle[ '--tidbits-icon-color' ] = iconColor;
	}
	if ( dividerBorder?.color ) {
		customStyle[ '--tidbits-divider-color' ] = dividerBorder.color;
	}
	if ( dividerBorder?.width ) {
		customStyle[ '--tidbits-divider-width' ] = dividerBorder.width;
	}
	if ( dividerBorder?.style && dividerBorder.style !== 'none' ) {
		customStyle[ '--tidbits-divider-style' ] = dividerBorder.style;
	}
	if ( itemPadding ) {
		customStyle[ '--tidbits-padding-block' ] =
			spacingPresetCssVar( itemPadding );
	}

	const blockProps = useBlockProps( {
		className: `tidbits tidbits--${ displayMode }`,
		style: customStyle,
	} );

	// Accordion uses a heading-based disclosure structure, so the wrapper is a
	// plain <div>; static modes remain a description list.
	const WrapperTag = displayMode === 'accordion' ? 'div' : 'dl';

	return (
		<>
			<InspectorControls>
				<ToolsPanel
					label={ __( 'Tidbits', 'tidbits' ) }
					resetAll={ () =>
						setAttributes( {
							displayMode: 'accordion',
						} )
					}
					panelId={ clientId }
				>
					<ToolsPanelItem
						label={ __( 'Layout', 'tidbits' ) }
						hasValue={ () => ! ( displayMode === 'accordion' ) }
						onDeselect={ () =>
							setAttributes( { displayMode: 'accordion' } )
						}
						isShownByDefault
						panelId={ clientId }
					>
						<SelectControl
							label={ __( 'Layout', 'tidbits' ) }
							value={ displayMode }
							options={ DISPLAY_MODE_OPTIONS }
							onChange={ ( value ) =>
								setAttributes( { displayMode: value } )
							}
						/>
					</ToolsPanelItem>
                </ToolsPanel>
				
                <ToolsPanel
					label={ __( 'Morsels', 'morsel' ) }
					resetAll={ () =>
						setAttributes( {
                            dividerBorder: {},
							itemPadding: '',
						} )
					}
					panelId={ clientId }
				>
					<ToolsPanelItem
						label={ __( 'Divider', 'morsel' ) }
						hasValue={ () =>
							!! ( dividerBorder?.color || dividerBorder?.width )
						}
						onDeselect={ () =>
							setAttributes( { dividerBorder: {} } )
						}
						isShownByDefault
						panelId={ clientId }
					>
						<BorderControl
							label={ __( 'Divider', 'morsel' ) }
							value={ dividerBorder }
							onChange={ ( value ) =>
								setAttributes( { dividerBorder: value || {} } )
							}
							colors={ themeColors }
							withSlider
							width="120px"
							__next40pxDefaultSize
						/>
					</ToolsPanelItem>

					<ToolsPanelItem
						label={ __( 'Item padding', 'morsel' ) }
						hasValue={ () => itemPadding !== '' }
						onDeselect={ () =>
							setAttributes( { itemPadding: '' } )
						}
						isShownByDefault
						panelId={ clientId }
					>
						<DimensionControl
							label={ __( 'Item padding', 'morsel' ) }
							value={ toDimensionValue( itemPadding ) }
							dimensionSizes={ dimensionSizes }
							onChange={ ( next ) =>
								setAttributes( {
									itemPadding: toSpacingValue( next ) ?? '',
								} )
							}
						/>
					</ToolsPanelItem>
                </ToolsPanel>
				
                <ToolsPanel
					label={ __( 'Icon', 'morsel' ) }
					resetAll={ () =>
						setAttributes( {
							iconColor: '',
						} )
					}
					panelId={ clientId }
				>
					<ToolsPanelItem
						label={ __( 'Icon color', 'morsel' ) }
						hasValue={ () => !! iconColor }
						onDeselect={ () => setAttributes( { iconColor: '' } ) }
						isShownByDefault
						panelId={ clientId }
					>
						<BaseControl.VisualLabel>
							{ __( 'Icon colour', 'tidbits' ) }
						</BaseControl.VisualLabel>
						<ColorPalette
							__experimentalIsRenderedInSidebar
							colors={ themeColors }
							value={ iconColor || undefined }
							onChange={ ( value ) =>
								setAttributes( { iconColor: value ?? '' } )
							}
						/>
					</ToolsPanelItem>
				</ToolsPanel>
			</InspectorControls>

			<WrapperTag { ...blockProps }>
				<InnerBlocks
					allowedBlocks={ ALLOWED_BLOCKS }
					template={ TEMPLATE }
				/>
			</WrapperTag>
		</>
	);
}
