<!-- LaraPush Push Notification Integration -->
<script src="<?php echo esc_url($script_url); ?>"></script>
<?php if ($additional_js_code != ''): ?>
<script>
    var additionalJsCode = <?php echo wp_json_encode($additional_js_code); ?>;
    eval(additionalJsCode);
</script>
<?php endif; ?>
<!-- /.LaraPush Push Notification Integration -->
