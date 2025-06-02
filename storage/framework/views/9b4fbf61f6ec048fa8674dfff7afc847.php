<?php
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Facades\Session;
    $guestToken = Session::token();
?>

<?php $__env->startSection('page_title'); ?>
    Menu | Volanti Jet Catering
<?php $__env->stopSection(); ?>

<?php $__env->startSection('seo'); ?>
<?php if(! request()->is('/')): ?>
    <meta name="title" content="Menu | Volanti Jet Catering"/>
    <meta name="description" content="Explore our diverse food menu, packed with flavors to suit every craving. From classic favorites to exciting new dishes, find the perfect meal for any occasion!"/>
    <meta name="keywords" content="Online Food Menu" />
    <link rel="canonical" href="<?php echo e(url()->current()); ?>" />
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content-wrapper'); ?>

<?php 
$customer = auth()->guard('customer')->user();                           
if(Auth::check())
{
$islogin = 1;
$address = Db::table('addresses')->where('address_type','customer')->where('customer_id',$customer->id)->orderBy('created_at', 'desc')->first(); 
// dd($address);			
}
else{
$islogin = 0;
$address = Db::table('addresses')->where('customer_token',$guestToken)->first();
}

?>



    <div class="listing-overlay w-100 d-flex" style="min-height: 210px; align-items: center;">
        <div class="container p-4">
        
            <?php if($address!=''): ?>
            <div class=" listing-banner-contant border-0">
                <h1 class="listing-banner-heading"><?php echo e($address->airport_name); ?></h1>
                <p class="listing-paragraph-1"><?php echo e($address->address1); ?>, </p>
                <p class="listing-paragraph-2"><?php echo e($address->state); ?> <?php echo e($address->postcode); ?>,<?php echo e($address->country); ?></p>
            </div>
           <?php else: ?>
            <div class="listing-banner-choose-contant border-0">
                <h1 class="listing-banner-choose-heading"><a href="<?php echo e(route('shop.home.index')); ?>">Choose Location</a></h1>
            </div>
           <?php endif; ?>
        

            
        </div>
    </div>
    <div class="container category-page mb-5">
        <?php echo e(Breadcrumbs::render('shop.product.parentcat')); ?>

        <div class="row subcategories">
            <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="col-sm-12 col-md-4 col-lg-3">
                    <a href=<?php echo e($category->slug); ?>>
                    <div class="card-block text-center mt-5">
                            <span class="text-center"><?php echo e($category->name); ?></span>
                        </div>
                    </a>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    jQuery(document).ready(function() {
        var categories = <?php echo json_encode($categories, 15, 512) ?>;

        var category_name='';
        jQuery("body").on("keyup", '#category-search',function() {
            var searchTerm = jQuery(this).val().toLowerCase();


            jQuery.each( categories, function( key, value ) {
                return  category_name = value.name;
            });


            var filteredCategories = category_name.filter(function(category) {
                return category.toLowerCase().includes(searchTerm);
            });

            updateCategoryList(filteredCategories);
        });

        function updateCategoryList(filteredCategories) {
            jQuery(".subcategories").empty();

            jQuery.each(filteredCategories, function(index, category) {
                jQuery(".subcategories").append('<div class="col-sm-12 col-md-4 col-lg-3"><div class="card-block text-center mt-5"><a href="' + category.slug + '"><span class="text-center category-name">' + category.name + '</span></a></div></div>');
            });
        }


        // jQuery('body').on('keyup', '#category-search', function() {
        //         // console.log('hfggh');
        //         var name = jQuery(this).val();
        //         // $('#address_update').prop('disabled', false);
        //         $.ajax({
        //             url: "<?php echo e(route('shop.home.index')); ?>",

        //             type: 'GET',
        //             data: {
        //                 'name': name
        //             },
        //             success: function(result) {
        //                 console.log(result);
        //                 jQuery("#address-list").html(result);
        //             }
        //         });

        //     })
    });
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('shop::layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/ubuntu/volantiScottsdale/resources/themes/volantijetcatering/views/products/parentcat.blade.php ENDPATH**/ ?>