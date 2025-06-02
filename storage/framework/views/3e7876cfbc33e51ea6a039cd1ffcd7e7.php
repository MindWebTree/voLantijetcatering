

<?php $__env->startSection('page_title'); ?>
Error logs | Volanti Jet Catering
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content-wrapper'); ?>

<div class="container my-5 text-center">

    <h2 class="text-center mb-4">Error Logs Entries</h2>
    <?php if($logs->isNotEmpty()): ?>
    <div class="table-responsive">
    <table class="table table-bordered table-striped table-hover">
        <thead class="thead-dark">
            <tr>
                <th scope="col" class="text-center">#</th>
                <th scope="col" class="text-center">Customer ID</th>
                <th scope="col" class="text-center">Message Type</th>
                <th scope="col" class="text-center">Message</th>
                <th scope="col" class="text-center">URL</th>
                <th scope="col" class="text-center">Created At</th>
                <th scope="col" class="text-center">Updated At</th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $logs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <th scope="row" class="text-center"><?php echo e($log->id); ?></th>
                <td class="text-center" style="max-width: 200px;"><?php echo e($log->customer_id ?? 'N/A'); ?></td>
                <td class="text-center" style="max-width: 200px;"><?php echo e($log->message_type ?? 'N/A'); ?></td>
                <td class="text-center" style="max-width: 400px;max-height:200px;overflow:auto;"><?php echo e($log->message ?? 'N/A'); ?></td>
                <td class="text-center" style="max-width: 300px;max-height:200px;overflow:auto;"><?php echo e($log->page_url ?? 'N/A'); ?></td>
                <td class="text-center" style="max-width: 200px;"><?php echo e($log->created_at); ?></td>
                <td class="text-center" style="max-width: 200px;"><?php echo e($log->updated_at); ?></td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
    </div>
    <div class="d-flex justify-content-center mt-4">
        <nav aria-label="Page navigation example">
            <ul class="pagination flex-wrap w-100" style="gap:5px;">
                
                <li class="page-item <?php echo e($logs->onFirstPage() ? 'disabled' : ''); ?>">
                    <a class="page-link" href="<?php echo e($logs->previousPageUrl()); ?>" aria-label="Previous">
                        <span aria-hidden="true">&laquo;</span>
                    </a>
                </li>
    
                
                <?php
                    $currentPage = $logs->currentPage();
                    $start = max(1, $currentPage - 2); // Start from 2 pages before current page
                    $end = min($logs->lastPage(), $currentPage + 2); // End at 2 pages after current page
                ?>
                
                <?php if($start > 1): ?>
                    <li class="page-item"><a class="page-link" href="<?php echo e($logs->url(1)); ?>">1</a></li>
                    <?php if($start > 2): ?> <li class="page-item disabled"><span class="page-link">...</span></li> <?php endif; ?>
                <?php endif; ?>
                
                <?php for($i = $start; $i <= $end; $i++): ?>
                    <li class="page-item <?php echo e($logs->currentPage() == $i ? 'active' : ''); ?>" style="<?php echo e($logs->currentPage() == $i ? 'color: white !important; border-bottom: none !important;' : ''); ?>">
                        <a class="page-link" href="<?php echo e($logs->url($i)); ?>"><?php echo e($i); ?></a>
                    </li>
                <?php endfor; ?>

                <?php if($end < $logs->lastPage()): ?>
                    <?php if($end < $logs->lastPage() - 1): ?> <li class="page-item disabled"><span class="page-link">...</span></li> <?php endif; ?>
                    <li class="page-item"><a class="page-link" href="<?php echo e($logs->url($logs->lastPage())); ?>"><?php echo e($logs->lastPage()); ?></a></li>
                <?php endif; ?>
    
                
                <li class="page-item <?php echo e($logs->hasMorePages() ? '' : 'disabled'); ?>">
                    <a class="page-link" href="<?php echo e($logs->nextPageUrl()); ?>" aria-label="Next">
                        <span aria-hidden="true">&raquo;</span>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
    <?php else: ?>
    <div>No error logs found at the moment.</div>
    <?php endif; ?>
    
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('shop::layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/ubuntu/volantiScottsdale/packages/Webkul/Shop/src/Providers/../Resources/views/logs/error-log.blade.php ENDPATH**/ ?>