<!--  include search fields  -->
<?php
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
$guestToken = Session::token();
?>
<!-- include search fields  -->
<?php $toolbarHelper = app('Webkul\Product\Helpers\Toolbar'); ?>
<?php $productRepository = app('Webkul\Product\Repositories\ProductRepository'); ?>

<?php $__env->startSection('page_title'); ?>
<?php echo e(trim($category->meta_title) != "" ? $category->meta_title : $category->name); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('seo'); ?>
<meta name="title" content="<?php echo e(trim($category->meta_title) != "" ? $category->meta_title : $category->name); ?>" />
<meta name="description" content="<?php echo e($category->meta_description); ?>" />
<meta name="keywords" content="<?php echo e($category->meta_keywords); ?>" />
<link rel="canonical" href="<?php echo e(url()->current()); ?>" />
<?php if(core()->getConfigData('catalog.rich_snippets.categories.enable')): ?>
<script type="application/ld+json">
    {
        !!app('Webkul\Product\Helpers\SEO') - > getCategoryJsonLd($category) !!
    }

</script>
<?php endif; ?>
<?php $__env->stopSection(); ?>
<?php $__env->startPush('css'); ?>
<style type="text/css">
    .product-price span:first-child,
    .product-price span:last-child {
        font-size: 18px;
        font-weight: 600;
    }

    @media only screen and (max-width: 992px) {
        .main-content-wrapper .vc-header {
            box-shadow: unset;
        }
    }

</style>
<?php $__env->stopPush(); ?>
<?php
$isProductsDisplayMode = in_array(
$category->display_mode, [
null,
'products_only',
'products_and_description'
]
);
$isDescriptionDisplayMode = in_array(
$category->display_mode, [
null,
'description_only',
'products_and_description'
]
);

?>
<?php $__env->startSection('content-wrapper'); ?>
<category-component></category-component>
<?php $__env->stopSection(); ?>
<?php $__env->startPush('scripts'); ?>
<script type="text/x-template" id="category-template">
    <section class="row col-12 velocity-divide-page category-page-wrapper">  
   <div class="listing-overlay w-100 d-flex" style="min-height:190px; align-items:center">
                   <div class="container ">
                   <?php 
                   $customer = auth()->guard('customer')->user();                           
                     
                       if(Auth::check())
                       {
                           $islogin = 1;
                           $address = Db::table('addresses')->where('customer_id',$customer->id)->orderBy('created_at', 'desc')->first(); 			
                       }
                       else{
                           $islogin = 0;
                           $address = Db::table('addresses')->where('customer_token',$guestToken)->first();
                       }
                
                    ?>
                    <?php if($address!=''): ?>
                    
                       <div class=" listing-banner-contant py-3">
                           <h2 class="listing-banner-heading"><?php echo e($address->airport_name); ?></h2>
                           <p class="listing-paragraph-1"><?php echo e($address->address1); ?>, </p>
                           <p class="listing-paragraph-2"><?php echo e($address->state); ?> <?php echo e($address->postcode); ?>,<?php echo e($address->country); ?></p>        
                       </div>
                       <?php else: ?>
                       <div class="listing-banner-choose-contant py-3">
                        <h1 class="listing-banner-choose-heading"><a href="<?php echo e(route('shop.home.index')); ?>">Choose Location</a></h1>
                    </div>
                    <?php endif; ?>
                       
                       
                   </div>
               </div>

       <?php echo view_render_event('bagisto.shop.productOrCategory.index.before', ['category' => $category]); ?>

   
       <?php if(in_array($category->display_mode, [null, 'products_only', 'products_and_description'])): ?>
           <?php echo $__env->make('shop::products.list.layered-navigation', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
       <?php endif; ?>
       <div class="col-12 no-padding">
                   <div class="hero-image">
                       <?php if(!is_null($category->category_banner)): ?>
                       <!-- here i change the img class logo to image banner -->
                           <img class="image banner" src="<?php echo e($category->banner_url); ?>" alt="" width="100%" height="350px" />
                       <?php endif; ?>
                   </div>
               </div>
   
        <!-- i change this div class" name category-container "right to container  -->
   
        <div class="container col-lg-9 col-12 mb-3" id="ProductsContainer" style="padding:0;">
           <?php echo e(Breadcrumbs::render('shop.productOrCategory.index',$category)); ?>

       <?php                 
        if(count($childCategory)>0){                       
        ?>
       <div class=" listing-menu-section">
       
   </div>
   <?php   
}
    ?>   
           <div class="row remove-padding-margin">
               <div class="pl0 col-12">
                   <!-- <h2 class="fw6 mb10"><?php echo e($category->name); ?></h2> -->
   
                   <!-- <?php if($isDescriptionDisplayMode): ?>
                       <?php if($category->description): ?>
                           <div class="category-description">
                               <?php echo $category->description; ?>

                           </div>
                       <?php endif; ?>
                   <?php endif; ?> -->
               </div>
               </div>
   
     
               <div class="search-product ml-3 ml-md-3 ml-lg-3 mr-3 mr-md-3 mr-lg-0">
                <input id="tnb-google-search-input" class="search_product" type="text" placeholder="Search for dishes" name = "product_search">
            </div> 


                   <div class="search_not_found_message pt-5"></div>
               <div class='col-md-12' id="products_header">


                   <div class='carousel-products sub-category'>
                       <!-- <carousel-component
                           :slides-per-page="slidesPerPage"
                           pagination-enabled="hide"
                           :slides-count="<?php echo e(count($childCategory)); ?>">
   
                           
                       </carousel-component> -->
                        <?php
                            if(count($childCategory)>0){
                        ?>
                        <?php
                            $category_ids = $category->id;
                        ?>
                        <?php if(count($category->products)): ?>
                         <div class="category-block" >
                           
                        <?php if($category->display_mode == 'description_only'): ?>
                            style="width: 100%"
                        <?php endif; ?>

                        
                        
                            <h1  id="categoryheading" class="mt-4"> <?php echo e($category->name); ?></h1>   
                        
                        
                        <div class="container listing-title-section breakfast-image-1 custom-image-background d-none" id="<?php echo e($category->slug); ?>">
                           
                            
        
            
                
                
        
                </div>
                <div class="row column-title">

                </div> 

                <?php endif; ?>

                

                <?php
                $products = collect($getCategorydetail['products']);
                
                $categories = collect($getCategorydetail['product_category']);
                $desiredCategoryId = $category->id; // Keep as object property access
            
                // Filter categories based on the desired category ID
                $filteredCategories = $categories->filter(function ($cat) use ($desiredCategoryId) {
                    return $cat->category_id == $desiredCategoryId; // Use object property access
                });
            
                // Extract product IDs from the filtered categories
                $filteredProductIds = $filteredCategories->pluck('product_id');
            
                // Filter products based on the product IDs from the filtered categories
                $categoryproducts = $products->filter(function ($product) use ($filteredProductIds) {
                    return $filteredProductIds->contains($product['id']); // Use object property access
                
                });
                
            ?>

                        <shimmer-component v-if="isLoading" shimmer-count="4"></shimmer-component>
                        <template v-else-if="products.length > 0">
                            <?php if($toolbarHelper->getCurrentMode() == 'grid'): ?>    
                                <div class="row col-12 remove-padding-margin custom-product-card ">
                                    
                                    

                                    
                                    <?php echo $__env->make('shop::products.category-products.category-product-list',['categoryproducts'=>$categoryproducts,'cate_id' => $category->id], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>   
                            

                                </div>
                            <?php else: ?>
                                <div class="product-list">
                                    <custom-product-card
                                        list=true
                                        :key="index"
                                        :product="product"
                                        v-for="(product, index) in products">
                                    </custom-product-card>
                                </div>
                            <?php endif; ?>
        
                            <?php echo view_render_event('bagisto.shop.productOrCategory.index.pagination.before', ['category' => $category]); ?>

        
                                <div class="bottom-toolbar" v-html="paginationHTML"></div>
        
                            <?php echo view_render_event('bagisto.shop.productOrCategory.index.pagination.after', ['category' => $category]); ?>

                        </template> 

                    
            
                       <?php $__currentLoopData = $childCategory; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $childSubCategory): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                      
                 
                     
                       <?php
                        $category_ids .= ','.$childSubCategory->id;
                       ?>
                       <?php if(count($childSubCategory->products)>0): ?>

                       
                       
   
            
                <h1 class ="childCategoryheading mt-4"> <?php echo e($childSubCategory->name); ?></h1>   
            
        
   
</div>
</div>
   <div class=" row column-title">
    

   </div>                            
   <?php endif; ?>

               
                
                <?php

                $desiredCategoryId = $childSubCategory->id; 

                // Filter products based on the desired category ID
                $filteredProducts = $categories->filter(function ($category) use ($desiredCategoryId) {
                    return $category->category_id == $desiredCategoryId;
                });

                $filteredProductIds = $filteredProducts->pluck('product_id');
        
                // Filter products based on the product IDs from the filtered categories
                $categoryproducts = $products->filter(function ($product) use ($filteredProductIds) {
                    return $filteredProductIds->contains($product['id']);
                });

            ?>
           <div class="child_category" id="child-category">
           <div class="category-block"
                   <?php if($category->display_mode == 'description_only'): ?>
                       style="width: 100%"
                   <?php endif; ?>>
   
                   <shimmer-component v-if="isLoading" shimmer-count="4"></shimmer-component>

                <template v-else-if="products.length > 0">
                    <?php if($toolbarHelper->getCurrentMode() == 'grid'): ?>    
                        <div class="row col-12 remove-padding-margin custom-product-card">

                            
                            

                            
                            <?php echo $__env->make('shop::products.category-products.category-product-list',['categoryproducts'=>$categoryproducts,'cate_id' => $childSubCategory->id], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>    
                            <?php if(!empty($getCategorydetail['paginationHTML'])): ?>
                            <div class="pagination-wrapper">
                                <?php echo $getCategorydetail['paginationHTML']; ?>

                            </div>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <div class="product-list">
                            <custom-product-card
                                list=true
                                :key="index"
                                :product="product"
                                v-for="(product, index) in products">
                            </custom-product-card>
                        </div>
                    <?php endif; ?>

                    <?php echo view_render_event('bagisto.shop.productOrCategory.index.pagination.before', ['category' => $category]); ?>


                        <div class="bottom-toolbar" v-html="paginationHTML"></div>
                    <?php echo view_render_event('bagisto.shop.productOrCategory.index.pagination.after', ['category' => $category]); ?>

                </template>
                
                
                    <div class="product-list empty" v-else>
                        <h2><?php echo e(__('shop::app.products.whoops')); ?></h2>
                        <p><?php echo e(__('shop::app.products.empty')); ?></p>
                    </div>
                    </div>
                    </div>   
                    <?php $__currentLoopData = $childSubCategory; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $thirdChildSubCategory): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                     
                 
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    
                <?php $__env->startPush('scripts'); ?>                        
            <script>

   Vue.component('category-component', {
       template: '#category-template',
   
       data: function () {
           return {
               'products': [],
               'isLoading': true,
               'paginationHTML': '',
               'currentScreen': window.innerWidth,
               'slidesPerPage': 5,
           }
       },
   
       created: function () {                 
           this.getCategoryProducts();
           this.setSlidesPerPage(this.currentScreen);
       },
   
           methods: {                    
            'getCategoryProducts': function() {
                        this.$http.get(
                                `${this.$root.baseUrl}/category-products/<?php echo e($category_ids); ?>${window.location.search}`
                            )
                            .then(response => {
                                
                                this.isLoading = false;
                                this.products = response.data.products;
                                // this.paginationHTML = response.data.paginationHTML;
                                this.product_category = response.data
                                    .product_category; //category-id and product-id
                            })
                            .catch(error => {
                                this.isLoading = false;
                                
                            })
                    },
   
           setSlidesPerPage: function (width) {
               
               if (width >= 1200) {
                   this.slidesPerPage = 5;
               } else if (width < 1200 && width >= 626) {
                   this.slidesPerPage = 3;
               } else if (width < 626 && width >= 400) {
                   this.slidesPerPage = 2;
               } else {
                   this.slidesPerPage = 1;
               }
           }
       }
   })
   
   
</script>
<?php $__env->stopPush(); ?>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php
}else{
?>

<!-- work when only product show-->
<?php if($isProductsDisplayMode): ?>




                        
                        <h1  id="categoryheading" class="mt-4"> <?php echo e($category->name); ?></h1>  
                        
                        


<div class="row column-title">

</div>
<div class="category-block" <?php if($category->display_mode == 'description_only'): ?>
    style="width: 100%"
    <?php endif; ?>>

    <shimmer-component v-if="isLoading" shimmer-count="4"></shimmer-component>

    <template v-else-if="products.length > 0">
        <?php if($toolbarHelper->getCurrentMode() == 'grid'): ?>

        <div class="row col-12 remove-padding-margin custom-product-card">
            
            

            
            <?php echo $__env->make('shop::products.category-products.single-category-products',['cate_id' => $category->id], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        </div>
        <?php else: ?>
        <div class="product-list">
            
            <?php echo $__env->make('shop::products.category-products.single-category-products',['cate_id' => $category->id], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        </div>
        <?php endif; ?>

        <?php echo view_render_event('bagisto.shop.productOrCategory.index.pagination.before', ['category' => $category]); ?>


        <div class="bottom-toolbar" v-html="paginationHTML"></div>

        <?php echo view_render_event('bagisto.shop.productOrCategory.index.pagination.after', ['category' => $category]); ?>

    </template>



    <div class="product-list empty" v-else>
        <h2><?php echo e(__('shop::app.products.whoops')); ?></h2>
        <p><?php echo e(__('shop::app.products.empty')); ?></p>
    </div>
</div>
<?php endif; ?>
<?php $__env->startPush('scripts'); ?>
<script>
    
    Vue.component('category-component', {
        template: '#category-template'
        , data: function() {
            return {
                'products': []
                , 'isLoading': true
                , 'paginationHTML': ''
                , 'currentScreen': window.innerWidth
                , 'slidesPerPage': 5
            , }
        },

        created: function() {
            this.getCategoryProducts();
            this.setSlidesPerPage(this.currentScreen);
        },

        methods: {
            'getCategoryProducts': function() {
                this.$http.get(`${this.$root.baseUrl}/category-products/<?php echo e($category->id); ?>?limit=48${window.location.search}`)
                    .then(response => {
                       this.isLoading = false;
                        this.products = response.data.products;

                        // this.paginationHTML = response.data.paginationHTML;
                    })
                    .catch(error => {
                        this.isLoading = false;
                    })
            },

            setSlidesPerPage: function(width) {

                if (width >= 1200) {
                    this.slidesPerPage = 5;
                } else if (width < 1200 && width >= 626) {
                    this.slidesPerPage = 3;
                } else if (width < 626 && width >= 400) {
                    this.slidesPerPage = 2;
                } else {
                    this.slidesPerPage = 1;
                }
            }
        }
    })

</script>
<?php $__env->stopPush(); ?>
<?php

}
?>
</div>
</div>
<?php if($isProductsDisplayMode): ?>
<div class="filters-container">
    <template v-if="products.length >= 0">
        
    </template>
</div>
</div>
<?php endif; ?>
<?php echo view_render_event('bagisto.shop.productOrCategory.index.after', ['category' => $category]); ?>

</section>
</script>

<?php $__env->stopPush(); ?>



<?php $__env->startPush('scripts'); ?>

<?php $__env->stopPush(); ?>

<?php echo $__env->make('shop::layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/ubuntu/volantiScottsdale/resources/themes/volantijetcatering/views/products/index.blade.php ENDPATH**/ ?>