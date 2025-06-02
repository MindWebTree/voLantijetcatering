


    <?php
        $defaultVariant = $product->getTypeInstance()->getDefaultVariant();
        // dd($defaultVariant);
        $config = app('Webkul\Product\Helpers\ConfigurableOption')->getConfigurationConfig($product);
// dd($product);
        $galleryImages = product_image()->getGalleryImages($product);

        // dd($galleryImages);
        // dd($config['options']);
    ?>

    <div class=attributes">
        <input type="hidden" value="<?php echo e($defaultVariant->id ?? ''); ?>" id="selected_configurable_option" name="selected_configurable_option"/>

        <?php $__currentLoopData = $config['attributes']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $attribute): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="attribute control-group <?php echo e($errors->has('super_attribute[' . $attribute['id'] . ']') ? 'has-error' : ''); ?>">
                <b class="required fs-4"><?php echo e($attribute['label']); ?></b>

                <span id="redioErrorMessage_<?php echo e($product->id); ?>" class="Redio_Error d-flex" style="color: red"></span>

                <?php if(empty($attribute['swatch_type']) || $attribute['swatch_type'] == 'dropdown'): ?>
                    <span class="custom-form">
                        <select
                            class="control styled-select"
                            name="super_attribute[<?php echo e($attribute['id']); ?>]"
                            id="attribute_<?php echo e($attribute['id']); ?>"
                            @change="configure(attribute, $event.target.value)">
                            
                            <option value=""><?php echo e(__('shop::app.products.select-above-options')); ?></option>
                            <?php $__currentLoopData = $attribute['options']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($option['id']); ?>" <?php echo e($option['id'] == $defaultVariant->{$attribute['code']} ? 'selected' : ''); ?>>
                                    <?php echo e($option['label']); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>

                        <div class="select-icon-container">
                            <span class="select-icon rango-arrow-down"></span>
                        </div>
                    </span>
                <?php else: ?>
                    <span class="swatch-container" id="swatch_container_ProductId_<?php echo e($product->id); ?>">

                        <?php $__currentLoopData = $attribute['options']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <label class="swatch single-product-page-buttons" for="attribute_<?php echo e($attribute['id']); ?>_option_<?php echo e($option['id']); ?>_<?php echo e($product->id); ?>">
                            <input
                                type="radio"
                                class="product_variant"
                                value="<?php echo e($option['id']); ?>"
                                name="super_attribute[<?php echo e($attribute['id']); ?>]"
                                id="attribute_<?php echo e($attribute['id']); ?>_option_<?php echo e($option['id']); ?>_<?php echo e($product->id); ?>"
                                attr="<?php echo e($option['products'][0]); ?>"
                                hidden
                            >
                            <div class="single-product-page-button-group-1">
                                <?php if($attribute['swatch_type'] == 'color'): ?>
                                    <span style="background: <?php echo e($option['swatch_value']); ?>"></span>
                                <?php elseif($attribute['swatch_type'] == 'image'): ?>
                                    <img src="<?php echo e($option['swatch_value']); ?>" title="<?php echo e($option['label']); ?>" alt="" />
                                <?php elseif($attribute['swatch_type'] == 'text'): ?>
                                    <span class="btn-secondary span px-2"><?php echo e($option['label']); ?></span>
                                <?php endif; ?>
                            </div>
                        </label>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                    </span>
                <?php endif; ?>

                <?php if(!count($attribute['options'])): ?>
                    <span class="no-options"><?php echo e(__('shop::app.products.select-above-options')); ?></span>
                <?php endif; ?>

                <?php if($errors->has('super_attribute[' . $attribute['id'] . ']')): ?>
                    <span class="control-error"><?php echo e($errors->first('super_attribute[' . $attribute['id'] . ']')); ?></span>
                <?php endif; ?>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
    <label style="color: #f84661" class="special_instruction">Special Instructions(optional)</label>
    <div id="category_instructions_Div" class="mt-1 w-100">
        <textarea id="textarea-customize" name="special_instruction" class="p-2" placeholder="Add your special instructions here..."></textarea>          
    </div>

<?php /**PATH /home/ubuntu/volantiScottsdale/resources/themes/volantijetcatering/views/products/single-product-info.blade.php ENDPATH**/ ?>