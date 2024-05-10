(function ($) {
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

  $(function () {
    async function sendNotificationByLaraPush(id) {
      var formData = new FormData();
      formData.append('action', 'larapush_send_notification');
      formData.append('post_id', id);

      try {
        var ajaxurl = adminAjax.ajaxurl;
        const response = await fetch(ajaxurl, {
          method: 'POST',
          body: formData,
        });
        const responseData = await response.json();
        if (responseData.status === 'success') {
          return 'Sent';
        } else {
          return 'Some error occurred, reload to get error message';
        }
      } catch (error) {
        console.error(error);
        return 'An error occurred while sending the notification';
      }
    }

    $('.send-notification-button').on('click', async function (e) {
      e.preventDefault();

      // get all children divs
      let postId = $(this).children('div').data().postId;
      // #larapush-send-notification-btn
      let messageBtn = $(this).find('#larapush-send-notification-btn');
      messageBtn.text('Sending...');
      let notificationSentResult = await sendNotificationByLaraPush(postId);
      messageBtn.text(notificationSentResult);
    });

    $('.larapush_send_notification').on('click', async function (e) {
      e.preventDefault();

      let postId = $(this).data('post-id');
      let parent = $(this).parent();
      parent.text('Sending...');
      let notificationSentResult = await sendNotificationByLaraPush(postId);
      console.log(notificationSentResult);
      parent.text(notificationSentResult);
    });
  });
})(jQuery);
