import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	InspectorControls,
	store as blockEditorStore,
} from '@wordpress/block-editor';
import { PanelBody, Placeholder, ComboboxControl } from '@wordpress/components';
import { useSelect } from '@wordpress/data';
import { store as coreStore } from '@wordpress/core-data';
import { debounce } from '@wordpress/compose';
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

	const [ filterValue, setFilterValue ] = useState( '' );

	// Fetch tidbit posts matching the current search term.
	const { tidbits, isLoading } = useSelect(
		( select ) => {
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
		[ filterValue ]
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
