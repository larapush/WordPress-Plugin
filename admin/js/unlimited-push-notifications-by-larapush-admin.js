(function( $ ) {
	'use strict';

	/**
	 * All of the code for your admin-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */

	$(function() {
		$('.larapush_send_notification').on('click', function(e) {
			e.preventDefault();
			var data = {
				'action': 'larapush_send_notification',
				'post_id': $(this).data('post-id'),
			}

			var parent = $(this).parent();

			parent.text('Sending...');

			$.post(ajaxurl, data, function(response) {
				console.log(JSON.parse(response));
				if(JSON.parse(response).status == 'success') {
					parent.text('Sent');
				}else{
					parent.text('Some error occured, reload to get error message');
				}
			})
		});
	});
})( jQuery );
