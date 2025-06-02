<script type="text/javascript" src="<?php echo e(asset(mix('/js/manifest.js', 'themes/velocity/assets'))); ?>"></script>

<script type="text/javascript" src="<?php echo e(asset(mix('/js/velocity-core.js', 'themes/velocity/assets'))); ?>"></script>

<script type="text/javascript" src="<?php echo e(asset(mix('/js/components.js', 'themes/velocity/assets'))); ?>"></script>

<script type="text/javascript">
    (() => {
        /* activate session messages */
        let message = <?php echo json_encode($velocityHelper->getMessage(), 15, 512) ?>;
        if (message.messageType && message.message !== '') {
            window.showAlert(message.messageType, message.messageLabel, message.message);
        }

        /* activate server error messages */
        window.serverErrors = [];
        <?php if(isset($errors)): ?>
            <?php if(count($errors)): ?>
                window.serverErrors = <?php echo json_encode($errors->getMessages(), 15, 512) ?>;
            <?php endif; ?>
        <?php endif; ?>

        /* add translations */
        window._translations = <?php echo json_encode($velocityHelper->jsonTranslations(), 15, 512) ?>;
    })();

    /**
     * Wishist form will dynamically create and execute.
     *
     * @param {!string} action
     * @param {!string} method
     * @param {!string} csrfToken
     */
    function submitWishlistForm(action, method, isConfirm, csrfToken) {
        if (isConfirm && ! confirm('<?php echo e(__('shop::app.checkout.cart.cart-remove-action')); ?>')) return;

        let form = document.createElement('form');
            form.method = 'POST';
            form.action = action;

        let _methodElement = document.createElement('input');
            _methodElement.type = 'hidden';
            _methodElement.name = '_method';
            _methodElement.value = method;
            form.appendChild(_methodElement);

        let _tokenElement = document.createElement('input');
            _tokenElement.type = 'hidden';
            _tokenElement.name ='_token';
            _tokenElement.value = csrfToken;
            form.appendChild(_tokenElement);

        document.body.appendChild(form);
        form.submit();
    }
</script>

<?php echo $__env->yieldPushContent('scripts'); ?>

<script>
    <?php echo core()->getConfigData('general.content.custom_scripts.custom_javascript'); ?>

</script>
<?php /**PATH /home/ubuntu/volantiScottsdale/resources/themes/volantijetcatering/views/layouts/scripts.blade.php ENDPATH**/ ?>