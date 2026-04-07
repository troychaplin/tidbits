import { store, getContext } from '@wordpress/interactivity';

store( 'tidbits', {
	actions: {
		toggle() {
			const ctx = getContext();
			ctx.isOpen = ! ctx.isOpen;
		},
	},
} );
