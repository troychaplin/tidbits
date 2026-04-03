import {
	useBlockProps,
	InnerBlocks,
} from '@wordpress/block-editor';

export default function Edit() {
	return (
		<div { ...useBlockProps() }>
			<InnerBlocks
                allowedBlocks={ [ 'core/paragraph' ] }
                template={ [
                    [ 'core/paragraph' ],
                ] }
            />
		</div>
	);
}
