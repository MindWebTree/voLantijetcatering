
<link rel="preload" href="<?php echo e(asset('themes/velocity/assets/fonts/font-rango/rango.ttf') . '?o0evyv'); ?>" as="font" crossorigin="anonymous" />


<link rel="stylesheet" href="<?php echo e(asset('themes/velocity/assets/css/bootstrap.min.css')); ?>" />

<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.7/dist/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>

<?php if(
    core()->getCurrentLocale()
    && core()->getCurrentLocale()->direction === 'rtl'
): ?>
    <link href="<?php echo e(asset('themes/velocity/assets/css/bootstrap-flipped.css')); ?>" rel="stylesheet">
<?php endif; ?>


<link rel="stylesheet" href="<?php echo e(asset(mix('/css/velocity.css', 'themes/volantijetcatering/assets'))); ?>" />


<?php echo $__env->yieldPushContent('css'); ?>


<style>
    <?php echo core()->getConfigData('general.content.custom_scripts.custom_css'); ?>

</style>
<?php /**PATH /home/ubuntu/volantiScottsdale/resources/themes/volantijetcatering/views/layouts/styles.blade.php ENDPATH**/ ?>