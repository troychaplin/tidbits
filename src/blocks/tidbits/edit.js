import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	InnerBlocks,
	InspectorControls,
} from '@wordpress/block-editor';
import { PanelBody, SelectControl } from '@wordpress/components';

const ALLOWED_BLOCKS = [ 'tidbits/morsel' ];
const TEMPLATE = [ [ 'tidbits/morsel' ] ];

const DISPLAY_MODE_OPTIONS = [
	{ value: 'accordion', label: __( 'Accordion', 'tidbits' ) },
	{ value: 'stacked', label: __( 'Stacked', 'tidbits' ) },
	{ value: 'columns', label: __( 'Columns', 'tidbits' ) },
];

export default function Edit( { attributes, setAttributes } ) {
	const { displayMode } = attributes;

	const blockProps = useBlockProps( {
		className: `tidbits tidbits--${ displayMode }`,
	} );

	// Accordion uses a heading-based disclosure structure, so the wrapper is a
	// plain <div>; static modes remain a description list.
	const WrapperTag = displayMode === 'accordion' ? 'div' : 'dl';

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Display Settings', 'tidbits' ) }>
					<SelectControl
						label={ __( 'Layout', 'tidbits' ) }
						value={ displayMode }
						options={ DISPLAY_MODE_OPTIONS }
						onChange={ ( value ) =>
							setAttributes( { displayMode: value } )
						}
					/>
				</PanelBody>
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
