<script async custom-element="amp-web-push" src="https://cdn.ampproject.org/v0/amp-web-push-0.1.js"></script>
<amp-web-push id="amp-web-push" layout="nodisplay" helper-iframe-url="<?= get_site_url(
    blog_id
) ?>/helper-frame.html" permission-dialog-url="https://<?= get_site_url(
    blog_id
) ?>/permission-dialog.html" service-worker-url="https://<?= get_site_url(
    blog_id
) ?>/firebase-messaging-sw.js"></amp-web-push>
<style amp-custom>
  .larapush-btn {
    height: 50px;
    width: 300px;
    font-family: Arial, Helvetica, sans-serif !important;
    font-weight: 700;
    line-height: 1.2em;
    font-size: 1em;
    color: #fff;
    display: inline-block;
    padding: .75em 1.75em;
    text-decoration: none;
    text-align: center;
    border-radius: 3px;
    background-image: <?php echo esc_html($amp_button_color); ?>;
    border: none;
    cursor: pointer;
  }

  amp-web-push-widget{
    box-shadow: 0 15px 35px -5px rgb(0 0 0 / 25%)
  }

  .larapush-btn-disabled {
    height: 50px;
    width: 300px;
    font-family: Arial, Helvetica, sans-serif !important;
    font-weight: 700;
    line-height: 1.2em;
    font-size: 1em;
    color: rgb(100, 100, 100);
    display: inline-block;
    padding: .75em 1.75em;
    text-decoration: none;
    text-align: center;
    border-radius: 3px;
    background: rgb(210, 210, 210);
    border: none;
    cursor: pointer;
    box-shadow: 0 15px 35px -5px rgb(0 0 0 / 25%);
  }

  div.larapush_cover {
    text-align: center;
  }
</style>