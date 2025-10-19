( function () {
	'use strict';

	document.addEventListener( 'DOMContentLoaded', function () {
		const container = document.getElementById(
			'email-redirect-addresses-container'
		);
		const addBtn = document.getElementById( 'add-email-btn' );

		if ( ! container || ! addBtn ) {
			return;
		}

		let fieldIndex = container.querySelectorAll(
			'.email-address-field'
		).length;

		// Add new email field.
		addBtn.addEventListener( 'click', function () {
			const fieldDiv = document.createElement( 'div' );
			fieldDiv.className = 'email-address-field';
			fieldDiv.setAttribute( 'data-index', fieldIndex );

			fieldDiv.innerHTML = `
				<input
					type="email"
					name="email_redirect_addresses[${ fieldIndex }]"
					value=""
					class="regular-text email-address-input"
					placeholder="Enter email address"
				>
				<button type="button" class="button remove-email-btn">
					<span class="dashicons dashicons-trash"></span>
				</button>
			`;

			container.appendChild( fieldDiv );
			fieldIndex++;
			updateRemoveButtons();
		} );

		// Remove email field.
		container.addEventListener( 'click', function ( e ) {
			if (
				e.target.classList.contains( 'remove-email-btn' ) ||
				e.target.closest( '.remove-email-btn' )
			) {
				const fieldDiv = e.target.closest( '.email-address-field' );
				if ( fieldDiv ) {
					fieldDiv.remove();
					updateRemoveButtons();
				}
			}
		} );

		// Update remove button visibility
		function updateRemoveButtons() {
			const fields = container.querySelectorAll( '.email-address-field' );
			const removeBtns =
				container.querySelectorAll( '.remove-email-btn' );

			removeBtns.forEach( ( btn ) => {
				btn.style.display = fields.length > 1 ? 'inline-block' : 'none';
			} );
		}
	} );
} )();
