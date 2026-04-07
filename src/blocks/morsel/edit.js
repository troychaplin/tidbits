import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	InspectorControls,
	store as blockEditorStore,
} from '@wordpress/block-editor';
import { PanelBody, Placeholder, ComboboxControl } from '@wordpress/components';
import { useSelect } from '@wordpress/data';
import { store as coreStore } from '@wordpress/core-data';
import { useMemo, useState } from '@wordpress/element';
import { decodeEntities } from '@wordpress/html-entities';

const ChevronIcon = ( { isOpen } ) => (
	<svg
		className={ `tidbits-morsel__icon${ isOpen ? ' is-open' : '' }` }
		xmlns="http://www.w3.org/2000/svg"
		width="20"
		height="20"
		viewBox="0 0 24 24"
		fill="none"
		stroke="currentColor"
		strokeWidth="2"
		strokeLinecap="round"
		strokeLinejoin="round"
		aria-hidden="true"
	>
		<polyline points="9 18 15 12 9 6" />
	</svg>
);

const AccordionPreview = ( { title, content, postId } ) => {
	const [ isOpen, setIsOpen ] = useState( false );
	const morselId = `morsel-${ postId }`;
	const termId = `morsel-term-${ postId }`;

	return (
		<div className="tidbits-morsel tidbits-morsel--accordion">
			<dt className="tidbits-morsel__term">
				<button
					type="button"
					className="tidbits-morsel__trigger"
					id={ termId }
					aria-expanded={ isOpen }
					aria-controls={ morselId }
					onClick={ () => setIsOpen( ! isOpen ) }
				>
					<span className="tidbits-morsel__title">{ title }</span>
					<ChevronIcon isOpen={ isOpen } />
				</button>
			</dt>
			<dd
				className={ `tidbits-morsel__content${
					isOpen ? ' is-open' : ''
				}` }
				id={ morselId }
				role="region"
				aria-labelledby={ termId }
				aria-hidden={ ! isOpen }
			>
				<div
					className="tidbits-morsel__inner"
					dangerouslySetInnerHTML={ { __html: content } }
				/>
			</dd>
		</div>
	);
};

const StaticPreview = ( { title, content, mode } ) => (
	<div className={ `tidbits-morsel tidbits-morsel--${ mode }` }>
		<dt className="tidbits-morsel__term">{ title }</dt>
		<dd className="tidbits-morsel__content">
			<div
				className="tidbits-morsel__inner"
				dangerouslySetInnerHTML={ { __html: content } }
			/>
		</dd>
	</div>
);

export default function Edit( {
	attributes,
	setAttributes,
	clientId,
	context,
} ) {
	const { postId } = attributes;
	const displayMode = context[ 'tidbits/displayMode' ] || 'accordion';

	// Fetch all tidbit posts for the combobox.
	const { tidbits, isResolving } = useSelect( ( select ) => {
		const query = {
			per_page: 100,
			orderby: 'title',
			order: 'asc',
			status: 'publish',
		};
		return {
			tidbits: select( coreStore ).getEntityRecords(
				'postType',
				'tidbit',
				query
			),
			isResolving: select( coreStore ).isResolving( 'getEntityRecords', [
				'postType',
				'tidbit',
				query,
			] ),
		};
	}, [] );

	// Get postIds used by sibling morsel blocks to prevent duplicates.
	const siblingPostIds = useSelect(
		( select ) => {
			const { getBlockOrder, getBlockAttributes, getBlockRootClientId } =
				select( blockEditorStore );
			const parentClientId = getBlockRootClientId( clientId );

			if ( ! parentClientId ) {
				return [];
			}

			return getBlockOrder( parentClientId )
				.filter( ( id ) => id !== clientId )
				.map( ( id ) => getBlockAttributes( id )?.postId )
				.filter( Boolean );
		},
		[ clientId ]
	);

	// Build combobox options, filtering out posts already used by siblings.
	const options = useMemo( () => {
		if ( ! tidbits ) {
			return [];
		}
		return tidbits
			.filter( ( post ) => ! siblingPostIds.includes( post.id ) )
			.map( ( post ) => ( {
				value: post.id,
				label:
					decodeEntities( post.title.rendered ) ||
					__( '(no title)', 'tidbits' ),
			} ) );
	}, [ tidbits, siblingPostIds ] );

	// Fetch the selected post for preview.
	const selectedPost = useSelect(
		( select ) => {
			if ( ! postId ) {
				return null;
			}
			return select( coreStore ).getEntityRecord(
				'postType',
				'tidbit',
				postId
			);
		},
		[ postId ]
	);

	const onSelectPost = ( value ) => {
		setAttributes( { postId: value ? Number( value ) : 0 } );
	};

	const blockProps = useBlockProps();
	const title =
		decodeEntities( selectedPost?.title?.rendered ) ||
		__( 'Loading…', 'tidbits' );
	const content = selectedPost?.content?.rendered || '';

	return (
		<>
			{ !! postId && (
				<InspectorControls>
					<PanelBody title={ __( 'Tidbit Settings', 'tidbits' ) }>
						<ComboboxControl
							label={ __( 'Change Tidbit', 'tidbits' ) }
							value={ postId }
							options={ options }
							onChange={ onSelectPost }
						/>
					</PanelBody>
				</InspectorControls>
			) }
			<div { ...blockProps }>
				{ ! postId ? (
					<Placeholder
						icon="admin-post"
						label={ __( 'Tidbit', 'tidbits' ) }
						instructions={ __(
							'Search and select a Tidbit post to display.',
							'tidbits'
						) }
					>
						<ComboboxControl
							value={ postId }
							options={ options }
							onChange={ onSelectPost }
						/>
						{ isResolving && (
							<p>{ __( 'Loading tidbits…', 'tidbits' ) }</p>
						) }
					</Placeholder>
				) : (
					<>
						{ displayMode === 'accordion' && (
							<AccordionPreview
								title={ title }
								content={ content }
								postId={ postId }
							/>
						) }
						{ displayMode !== 'accordion' && (
							<StaticPreview
								title={ title }
								content={ content }
								mode={ displayMode }
							/>
						) }
					</>
				) }
			</div>
		</>
	);
}
