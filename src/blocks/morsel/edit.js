import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	InspectorControls,
	store as blockEditorStore,
} from '@wordpress/block-editor';
import {
	PanelBody,
	Placeholder,
	ComboboxControl,
	Disabled,
} from '@wordpress/components';
import { useSelect } from '@wordpress/data';
import { store as coreStore } from '@wordpress/core-data';
import { debounce } from '@wordpress/compose';
import { useMemo, useState, useContext } from '@wordpress/element';
import { decodeEntities } from '@wordpress/html-entities';

// Canned content for the block/inserter preview, where no real Tidbit posts
// exist to fetch. Only rendered inside a disabled preview context.
const PREVIEW_TIDBITS = [
	{
		title: __( 'HyperText Markup Language', 'tidbits' ),
		content:
			'<p>The standard language for structuring the content of web pages.</p>',
	},
	{
		title: __( 'Cascading Style Sheets', 'tidbits' ),
		content:
			'<p>Describes how a document looks — its colours, layout, and typography.</p>',
	},
	{
		title: __( 'Application Programming Interface', 'tidbits' ),
		content:
			'<p>A set of rules that lets two pieces of software talk to each other.</p>',
	},
];

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
	const panelId = `morsel-${ postId }-panel`;
	const toggleId = `morsel-${ postId }-toggle`;

	return (
		<div className="tidbits-morsel tidbits-morsel--accordion">
			<h3 className="tidbits-morsel__heading">
				<button
					type="button"
					className="tidbits-morsel__trigger"
					id={ toggleId }
					aria-expanded={ isOpen }
					aria-controls={ panelId }
					onClick={ () => setIsOpen( ! isOpen ) }
				>
					<span className="tidbits-morsel__title">{ title }</span>
					<ChevronIcon isOpen={ isOpen } />
				</button>
			</h3>
			<div
				className={ `tidbits-morsel__panel${
					isOpen ? ' is-open' : ''
				}` }
				id={ panelId }
				aria-labelledby={ toggleId }
				inert={ ! isOpen ? '' : undefined }
			>
				<div
					className="tidbits-morsel__inner"
					dangerouslySetInnerHTML={ { __html: content } }
				/>
			</div>
		</div>
	);
};

const StaticPreview = ( { title, content, mode } ) => (
	<div className={ `tidbits-morsel tidbits-morsel--${ mode }` }>
		<dt className="tidbits-morsel__term">{ title }</dt>
		{ /* Content sits directly in the <dd>, mirroring render.php, so the
		   `.tidbits-morsel__content > *` margin resets apply and the spacing
		   around dividers matches the front end (no extra inner wrapper). */ }
		<dd
			className="tidbits-morsel__content"
			dangerouslySetInnerHTML={ { __html: content } }
		/>
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

	// BlockPreview (used by the inserter) renders blocks inside <Disabled>, so
	// this flags the preview context where canned sample content is shown
	// instead of fetched posts or the "select a Tidbit" placeholder.
	const isPreview = useContext( Disabled.Context );

	const [ filterValue, setFilterValue ] = useState( '' );

	// Fetch tidbit posts matching the current search term. Skipped in preview
	// to avoid REST requests while rendering inserter thumbnails.
	const { tidbits, isLoading } = useSelect(
		( select ) => {
			if ( isPreview ) {
				return { tidbits: [], isLoading: false };
			}
			const query = {
				per_page: 20,
				orderby: 'title',
				order: 'asc',
				status: 'publish',
				_fields: 'id,title',
			};
			if ( filterValue ) {
				query.search = filterValue;
				query.search_columns = [ 'post_title' ];
			}
			return {
				tidbits: select( coreStore ).getEntityRecords(
					'postType',
					'tidbit',
					query
				),
				isLoading: select( coreStore ).isResolving(
					'getEntityRecords',
					[ 'postType', 'tidbit', query ]
				),
			};
		},
		[ filterValue, isPreview ]
	);

	// Position of this morsel among its siblings, used to vary the sample
	// content across rows in the preview.
	const blockIndex = useSelect(
		( select ) => select( blockEditorStore ).getBlockIndex( clientId ),
		[ clientId ]
	);

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

	// Build combobox options from search results, filtering out sibling
	// duplicates and ensuring the currently-selected post is always present.
	const options = useMemo( () => {
		const fetched = ( tidbits ?? [] )
			.filter( ( post ) => ! siblingPostIds.includes( post.id ) )
			.map( ( post ) => ( {
				value: post.id,
				label:
					decodeEntities( post.title.rendered ) ||
					__( '(no title)', 'tidbits' ),
			} ) );

		if (
			selectedPost &&
			! fetched.some( ( option ) => option.value === selectedPost.id )
		) {
			fetched.unshift( {
				value: selectedPost.id,
				label:
					decodeEntities( selectedPost.title?.rendered ) ||
					__( '(no title)', 'tidbits' ),
			} );
		}

		return fetched;
	}, [ tidbits, siblingPostIds, selectedPost ] );

	const onSelectPost = ( value ) => {
		setAttributes( { postId: value ? Number( value ) : 0 } );
	};

	const blockProps = useBlockProps();
	const sample =
		PREVIEW_TIDBITS[ Math.max( 0, blockIndex ?? 0 ) % PREVIEW_TIDBITS.length ];
	const title = isPreview
		? sample.title
		: decodeEntities( selectedPost?.title?.rendered ) ||
		  __( 'Loading…', 'tidbits' );
	const content = isPreview
		? sample.content
		: selectedPost?.content?.rendered || '';

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
							onFilterValueChange={ debounce(
								setFilterValue,
								300
							) }
							isLoading={ isLoading }
						/>
					</PanelBody>
				</InspectorControls>
			) }
			<div { ...blockProps }>
				{ ! postId && ! isPreview ? (
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
							onFilterValueChange={ debounce(
								setFilterValue,
								300
							) }
							isLoading={ isLoading }
						/>
					</Placeholder>
				) : (
					<>
						{ displayMode === 'accordion' && (
							<AccordionPreview
								title={ title }
								content={ content }
								postId={ postId || clientId }
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
