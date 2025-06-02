<?php $toolbarHelper = app('Webkul\Product\Helpers\Toolbar'); ?>



<?php $__env->startSection('page_title'); ?>

<?php echo e(app('request')->input('term') ? app('request')->input('term') . ' | Volanti Jet Catering' : '| Volanti Jet Catering'); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('seo'); ?>
<meta name="title" content="<?php echo e(app('request')->input('term') ? app('request')->input('term') . ' | Volanti Jet Catering' : '| Volanti Jet Catering'); ?>" />
<meta name="description" content="Find your favorite dishes and ingredients with ease! Use our search to explore a world of delicious options tailored to your cravings." />
<meta name="keywords" content="" />
<link rel="canonical" href="<?php echo e(url()->current()); ?>" />

<?php $__env->stopSection(); ?>

<?php $__env->startPush('css'); ?>
    <style type="text/css">
        .category-container {
            min-height: unset;
        }

        .toolbar-wrapper .col-4:first-child {
            display: none !important;
        }

        .toolbar-wrapper .col-4:last-child {
            right: 0;
            position: absolute;
        }

        @media only screen and (max-width: 992px) {
            .main-content-wrapper .vc-header {
                box-shadow: unset;
            }

            .toolbar-wrapper .col-4:last-child {
                left: 175px;
            }

            .toolbar-wrapper .sorter {
                left: 35px;
                position: relative;
            }

            .quick-view-btn-container,
            .rango-zoom-plus,
            .quick-view-in-list {
                display: none;
            }
        }
    </style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content-wrapper'); ?>
    <div class="container category-page-wrapper">
        <search-component></search-component>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
    <script type="text/x-template" id="image-search-result-component-template">
        <div class="image-search-result">
            <div class="searched-image">
                <img :src="searchedImageUrl" alt=""/>
            </div>

            <div class="searched-terms">
                <h3 class="fw6 fs20 mb-4">
                    <?php echo e(__('shop::app.search.analysed-keywords')); ?>

                </h3>

                <div class="term-list">
                    <a v-for="term in searched_terms" :href="'<?php echo e(route('shop.search.index')); ?>?term=' + term.slug">
                        {{ term.name }}
                    </a>
                </div>
            </div>
        </div>
    </script>

    <script type="text/x-template" id="seach-component-template">
        
        <section class="search-container row category-container m-auto">
            
            <?php if(request('image-search')): ?>
                <image-search-result-component></image-search-result-component>
            <?php endif; ?>

             <!-- Sandeep || Display an error message if the search term is less than 3 characters -->

             <?php if(!empty($errorMessage)): ?>
                   <div class="search_error_message text-center">
                    <h2 class="fw6 col-12 text-center" style="font-size:27px">We're Sorry, We Could Not Find  Any Results For "<?php echo e(app('request')->input('term')); ?>"</h2>
                    <span class="col-12"><?php echo e($errorMessage); ?></span>
                    <div class="container category-page mb-5 pt-4">
                        <h3 class="pt-3 m-0 text-center" style="font-size: 24px;">Explore More from Our Menu Collection</h3>
                        <div class="mx-auto my-3" style="width: 60px; height: 2px; background-color: #9a7b4f;"></div>
                        <div class="row subcategories">
                            <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="col-sm-12 col-md-4 col-lg-4">
                                    <a href=<?php echo e($category->slug); ?>>
                                    <div class="card-block text-center mt-5">
                                            <span class="text-center"><?php echo e($category->name); ?></span>
                                        </div>
                                    </a>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                  </div>
             <?php elseif(!$results): ?>
             <div>
                    <h2 class="fw6 col-12 text-center pt-3"  style="font-size:27px">No Results Found For  "<?php echo e(app('request')->input('term')); ?>"</h2>
                    <p class="text-center" style="color: #666;">
                        We couldn't find what you were looking for. Please try another search term or browse our categories.
                    </p>
                    <div class="container category-page mb-5 pt-4">
                        <h3 class="pt-3 m-0 text-center" style="font-size: 24px;">Explore More from Our Menu Collection</h3>
                        <div class="mx-auto my-3" style="width: 60px; height: 2px; background-color: #9a7b4f;"></div>
                        <div class="row subcategories">
                            <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="col-sm-12 col-md-4 col-lg-4">
                                    <a href=<?php echo e($category->slug); ?>>
                                    <div class="card-block text-center mt-5">
                                            <span class="text-center"><?php echo e($category->name); ?></span>
                                        </div>
                                    </a>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                </div>
              <?php else: ?>
                <?php if($results->isEmpty()): ?>
                <div class="no-result-found">$

                    <div>
                    <h2 class="fw6 col-12"><?php echo e(__('shop::app.products.whoops')); ?></h2>
                    <span class="col-12"><?php echo e(__('shop::app.search.no-results')); ?></span>
                    </div>
                </div>

                <?php else: ?>
                
                    
                   

            <div class="container listing-title-section breakfast-image-1 custom-image-background mb-3">
                <img src="<?php echo e(asset('themes/velocity/assets/images/Food-Icon.png')); ?>" class="left-image search-left-image">
    
    
            <div class="centered-word  center-heading" > 
                <h1 style='font-size: 40px;' class='fw-15 f-5 col-12 mb20 p-0 m-auto pl-2'><?php echo e($results->total()); ?> <?php echo e(__('shop::app.search.found-results')); ?> for "<?php echo e(app('request')->input('term')); ?>" </h1>
            </div>
            <img src="<?php echo e(asset('themes/velocity/assets/images/Food-Icon.png')); ?>" class="right-image search-right-image">
    
            </div>
            <?php if(
                $results
                && $results->count()
            ): ?>
                <div class="filters-container col-12" style="
                    margin-top: 20px;
                    padding-left: 0px !important;
                    padding-bottom: 10px !important;
                ">
                    
                </div>

            <?php endif; ?>
                    
                    
                    <div id="search-item-list" class="search-item col-12">
                    <?php $__currentLoopData = $results; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $productFlat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if($toolbarHelper->getCurrentMode() == 'grid'): ?>
                            <?php echo $__env->make('shop::products.list.search-card', [
                                'cardClass' => 'category-product-image-container',
                                'product' => $productFlat->product,
                            ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                        <?php else: ?>
                            <?php echo $__env->make('shop::products.list.search-card', [
                                'list' => true,
                                'product' => $productFlat->product,
                            ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                        <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                    <?php echo $__env->make('ui::datagrid.pagination', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </section>
    </script>

    <script>
        Vue.component('search-component', {
            template: '#seach-component-template',
        });

        Vue.component('image-search-result-component', {
            template: '#image-search-result-component-template',

            data: function() {
                return {
                    searched_terms: [],
                    searchedImageUrl: localStorage.searchedImageUrl,
                }
            },

            created: function() {
                if (localStorage.searched_terms && localStorage.searched_terms != '') {
                    this.searched_terms = localStorage.searched_terms.split('_');

                    this.searched_terms = this.searched_terms.map(term => {
                        return {
                            name: term,
                            slug: term.split(' ').join('+'),
                        }
                    });
                }
            }
        });
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('shop::layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/ubuntu/volantiScottsdale/resources/themes/volantijetcatering/views/search/search.blade.php ENDPATH**/ ?>