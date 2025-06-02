<?php $reviewHelper = app('Webkul\Product\Helpers\Review'); ?>
<?php $toolbarHelper = app('Webkul\Product\Helpers\Toolbar'); ?>

<?php
    use Jenssegers\Agent\Agent;


    $list = $toolbarHelper->getCurrentMode() == 'list' ? true : false;

    $productBaseImage = product_image()->getProductBaseImage($product);

    $totalReviews = $reviewHelper->getTotalReviews($product);

    $avgRatings = ceil($reviewHelper->getAverageRating($product));

    $agent = new Agent();
    $isDesktop = $agent->isDesktop();
    $isTablet = $agent->isTablet();


?>

<?php echo view_render_event('bagisto.shop.products.list.card.before', ['product' => $product]); ?>

<?php if(!empty($list)): ?>

    <div class="col-12 lg-card-container list-card product-card row">
        <div class="product-image">
            <a title="<?php echo e($product->name); ?>" href="<?php echo e($product->url_key); ?>">
                <img src="<?php echo e($productBaseImage['medium_image_url']); ?>"
                    :onerror="`this.src='${this.$root.baseUrl}/vendor/webkul/ui/assets/images/product/large-product-placeholder.png'`"
                    alt=""/>

                <div class="quick-view-in-list">
                    <product-quick-view-btn
                        :quick-view-details="<?php echo e(json_encode($velocityHelper->formatProduct($product))); ?>"></product-quick-view-btn>
                </div>
            </a>
        </div>
        <div class="product-information">
            <div>
                <div class="product-name">
                    <a href="<?php echo e($product->url_key); ?>" title="<?php echo e($product->name); ?>"
                        class="unset">

                        <span class="fs16"><?php echo e($product->name); ?></span>
                    </a>

                    <?php if(!empty($additionalAttributes)): ?>
                        <?php if(isset($item->additional['attributes'])): ?>
                            <div class="item-options">

                                <?php $__currentLoopData = $item->additional['attributes']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $attribute): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <b><?php echo e($attribute['attribute_name']); ?> : </b><?php echo e($attribute['option_label']); ?></br>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>

                <div class="product-price">
                    <?php echo $__env->make('shop::products.price', ['product' => $product], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                </div>

                <?php if($totalReviews): ?>
                    <div class="product-rating">
                        <star-ratings ratings="<?php echo e($avgRatings); ?>"></star-ratings>

                        <span><?php echo e($totalReviews); ?> Ratings</span>
                    </div>
                <?php endif; ?>

                <div class="cart-wish-wrap mt5">
                    <?php echo $__env->make('shop::products.add-to-cart', [
                        'addWishlistClass' => 'pl10',
                        'product' => $product,
                        'addToCartBtnClass' => 'medium-padding',
                        'showCompare' => (bool) core()->getConfigData('general.content.shop.compare_option'),
                    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                </div>
            </div>
        </div>
    </div>
<?php else: ?>
    
    
        <div class="card grid-card product-card-new search-product-card col-12 m-0">

            <div class="row my-4 ml-0 <?php if(($isDesktop || $isTablet) || (!$isDesktop && !$isTablet && $product['type'] != 'simple')): ?> align-items-center <?php endif; ?>">
                
                
            
                    
                    
                    
    
                    
                    
                
            
    
            <div class="card-body col-10 p-md-0 p-lg-0 mt-lg-3 mt-md-3">
                <div class="product-name no-padding search-product-name w-100">
                        <span class="fs16 my-1"><?php echo e($product->name); ?></span>
                        
                 
                    <br>
                    <p class="text-left"><?php echo e($product->description); ?></p>
                    
                    <?php if($isDesktop || $isTablet): ?>
                    <?php if($product->isSaleable() && $product['type'] == 'simple'): ?>
                    <a id="category_instructions" data-toggle="collapse" class="m-0"
                    href="#category_instructions_Div<?php echo e($product['id']); ?>" role="button" aria-expanded="true"
                    aria-controls="category_instructions_Div">Special Instructions
                    (optional)
                    <span class="toggle-icon">-</span></a>
                <div class="collapse multi-collapse category_instructions_Div mt-3 mb-3 show in" id="category_instructions_Div<?php echo e($product['id']); ?>">
                    <div id="category_instructions_Div" class="">
                        <textarea id="textarea-customize" name="special_instruction" class="p-2"  placeholder="Add your special instructions here..."></textarea>          
                    </div>
                </div>
                    <?php endif; ?>
                    <?php endif; ?>
                </div>
                
                
                    
                
    
                
    
                
    
                
            </div>




            <div class="AddToCartButton col-2 my-4 p-md-0 p-lg-0" id="AddToCartButton_searchpage">
                <input type="hidden" name="product_id" value="<?php echo e($product['id']); ?>" id="ProductId">
                <?php if($product->isSaleable()): ?>
                    <?php if($product['type'] == 'simple'): ?>
                    <quantity-changer
                    :product-id="<?php echo e($product['id']); ?>"
                    :quantity-id="'quantity_' + <?php echo e($product['id']); ?>"
                    quantity-text="<?php echo e(__('shop::app.products.quantity')); ?>">
                  </quantity-changer>
                  
                     <span id="quantityError_<?php echo e($product['id']); ?>_<?php echo e($product['category_id']); ?>" class="text-danger" style="color: red"></span>

                        <div class="AddButton text-center mt-2">
                            <button type="submit" class="add_button" id="AddToCartButton" data="<?php echo e($product['type']); ?>" attr="<?php echo e($product['category_id']); ?>">Add</button>
                            <span id="successMessage_<?php echo e($product['id']); ?>_<?php echo e($product['category_id']); ?>" class="text-success successMessage"></span>
                        </div>
                    <?php else: ?>
                        <div class="configurable_product">
                            <div class="AddButton text-center">
                                <input type="hidden" id="slug" value="<?php echo e($product['url_key']); ?>">
                                <button type="button" data-toggle="modal" data-target="#exampleModal<?php echo e($product['id']); ?>_<?php echo e($product['category_id']); ?>" class="OptionsAddButton"
                                    id="AddToCartButtonpopup">Add</button>
                                <span class="customisable">Customisable</span>
                                <br>
                                
                                <span id="successMessage_<?php echo e($product['id']); ?>_<?php echo e($product['category_id']); ?>" class="text-success successMessage" style="display: none;"></span>
                                
                            </div>
                            <!-- Modal -->
                            <div class="modal custom_modal fade p-0" id="exampleModal<?php echo e($product['id']); ?>_<?php echo e($product['category_id']); ?>" data="<?php echo e($product['id']); ?>" tabindex="-1" role="dialog"
                                aria-labelledby="exampleModalLabel" aria-hidden="true">
                                <div class="modal-dialog  modal-dialog-centered " role="document">
                                    <div class="modal-content pb-3">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalLabel">Add To Cart</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <span class="fs16 ProductName" id = "ProductName"><?php echo e($product['name']); ?></span>
                                            <br />
                                            <p class="description"><?php echo e($product['description']); ?></p>
                                            <quantity-changer
                                            :product-id="<?php echo e($product['id']); ?>"
                                            :quantity-id="'quantity_' + <?php echo e($product['id']); ?>"
                                            quantity-text="<?php echo e(__('shop::app.products.quantity')); ?>">
                                          </quantity-changer>
                                          
                                            
                                            <span id="quantityError_<?php echo e($product['id']); ?>_<?php echo e($product['category_id']); ?>" class="text-danger" style="color: red"></span>
                                            <div class="variant__option"></div>
                                        </div>

                                        
                                            <button type="submit" class="add_button OptionsAddButton m-auto" data="<?php echo e($product['type']); ?>" id="Add_Button_Popop" attr="<?php echo e($product['category_id']); ?>">Add</button>
                                            

                                    </div>
                                </div> 
                            </div>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="AddButton text-center">
                        <button type="submit" class="stockoutButton" disabled>Out of stock</button>
                    </div>
                <?php endif; ?>

            </div>
            <?php if(!$isDesktop && !$isTablet): ?>
            <?php if($product->isSaleable() && $product['type'] == 'simple'): ?>
            <div class="category-instructions-div col-12 p-md-0 p-lg-0">
            <a id="category_instructions" data-toggle="collapse" class="m-0"
            href="#category_instructions_Div<?php echo e($product['id']); ?>" role="button" aria-expanded="true"
            aria-controls="category_instructions_Div">Special Instructions
            (optional)
            <span class="toggle-icon">-</span></a>
        <div class="collapse multi-collapse category_instructions_Div mt-3 mb-3 show in" id="category_instructions_Div<?php echo e($product['id']); ?>">
            <div id="category_instructions_Div" class="">
                <textarea id="textarea-customize" name="special_instruction" class="p-2"  placeholder="Add your special instructions here..."></textarea>          
            </div>
        </div>
        </div>
            <?php endif; ?>
            <?php endif; ?>
        </div>
        </div>
    
<?php endif; ?>

<?php echo view_render_event('bagisto.shop.products.list.card.after', ['product' => $product]); ?>

<?php /**PATH /home/ubuntu/volantiScottsdale/resources/themes/volantijetcatering/views/products/list/search-card.blade.php ENDPATH**/ ?>