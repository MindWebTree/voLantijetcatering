<?php $__env->startSection('page_title'); ?>
    <?php echo e(__('admin::app.sales.orders.title')); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('content-wrapper'); ?>
    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h1><?php echo e(__('admin::app.sales.orders.title')); ?></h1>
            </div>

            <div class="page-action">
                <div class="export-import" @click="showModal('downloadDataGrid')">
                    <i class="export-icon"></i>
                    <span>
                        <?php echo e(__('admin::app.export.export')); ?>

                    </span>
                </div>
                <?php if(auth('admin')->user()->role_id == 1): ?>
                    <a href="<?php echo e(route('custom.add-order')); ?>" class="btn btn-lg btn-primary">
                        Add Order
                    </a>
                <?php endif; ?>

            </div>
        </div>

        <div class="page-content">
            <order-datagrid-plus src="<?php echo e(route('admin.sales.order.index')); ?>"></order-datagrid-plus>
        </div>
    </div>

    <modal id="downloadDataGrid" :is-open="modalIds.downloadDataGrid">
        <h3 slot="header"><?php echo e(__('admin::app.export.download')); ?></h3>
        <div slot="body">
            <export-form></export-form>
        </div>
    </modal>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
    
    <?php if(auth()->guard('admin')->check() && auth()->guard('admin')->user()->role_id == 2): ?>
        <?php echo $__env->make('admin::export.export', [
            'gridName' => app('Webkul\Admin\DataGrids\DeliveryOrdersDataGrid'),
        ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php else: ?>
        <?php echo $__env->make('admin::export.export', ['gridName' => app('Webkul\Admin\DataGrids\OrdersDataGrid')], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php endif; ?>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('admin::layouts.content', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/ubuntu/volantiScottsdale/packages/ACME/paymentProfile/src/Providers/../Resources/views/admin/sales/orders/index.blade.php ENDPATH**/ ?>