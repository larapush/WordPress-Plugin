<div class="larapush_cover">
  <amp-web-push-widget visibility="unsubscribed" layout="fixed" width="300" height="50"><button on="tap:amp-web-push.subscribe" class="larapush-btn"><?php echo esc_html(
      $amp_button_text
  ); ?></button></amp-web-push-widget>
<?php if ($amp_unsubscribe_button) { ?>
  <amp-web-push-widget visibility="subscribed" layout="fixed" width="300" height="50"><button on="tap:amp-web-push.unsubscribe" class="larapush-btn-disabled">Unsubscribe from Push</button></amp-web-push-widget>
<?php } ?>
</div>