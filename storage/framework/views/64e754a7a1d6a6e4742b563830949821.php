<div class="col-lg-12 col-md-12 col-sm-12 footer-ct-content">
	<div class="row">

        <?php if($velocityMetaData): ?>
            <?php echo Blade::render($velocityMetaData->footer_middle_content); ?>

        <?php else: ?>
            <div class="col-lg-6 col-md-12 col-sm-12 no-padding">
                <ul type="none">
                    <li>
                        <a href="<?php echo e(url('/about-us/company-profile')); ?>">
                            <?php echo e(__('velocity::app.admin.meta-data.footer-middle.about-us')); ?>

                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(url('/about-us/company-profile')); ?>">
                            <?php echo e(__('velocity::app.admin.meta-data.footer-middle.customer-service')); ?>

                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(url('/about-us/company-profile')); ?>">
                            <?php echo e(__('velocity::app.admin.meta-data.footer-middle.whats-new')); ?>

                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(url('/about-us/company-profile')); ?>">
                            <?php echo e(__('velocity::app.admin.meta-data.footer-middle.contact-us')); ?>

                        </a>
                    </li>
                </ul>
            </div>
            <div class="col-lg-6 col-md-12 col-sm-12 no-padding">
                <ul type="none">
                    <li>
                        <a href="<?php echo e(url('/about-us/company-profile')); ?>">
                            <?php echo e(__('velocity::app.admin.meta-data.footer-middle.order-and-returns')); ?>

                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(url('/about-us/company-profile')); ?>">
                            <?php echo e(__('velocity::app.admin.meta-data.footer-middle.payment-policy')); ?>

                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(url('/about-us/company-profile')); ?>">
                            <?php echo e(__('velocity::app.admin.meta-data.footer-middle.shipping-policy')); ?>

                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(url('/about-us/company-profile')); ?>">
                            <?php echo e(__('velocity::app.admin.meta-data.footer-middle.privacy-and-cookies-policy')); ?>

                        </a>
                    </li>
                </ul>
            </div>
        <?php endif; ?>
	</div>
</div><?php /**PATH /home/ubuntu/volantiScottsdale/resources/themes/volantijetcatering/views/layouts/footer/footer-links/footer-middle.blade.php ENDPATH**/ ?>