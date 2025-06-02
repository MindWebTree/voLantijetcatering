{{-- @extends('shop::layouts.master') --}}


    @php
        $defaultVariant = $product->getTypeInstance()->getDefaultVariant();
        // dd($defaultVariant);
        $config = app('Webkul\Product\Helpers\ConfigurableOption')->getConfigurationConfig($product);
// dd($product);
        $galleryImages = product_image()->getGalleryImages($product);

        // dd($galleryImages);
        // dd($config['options']);
    @endphp

    <div class=attributes">
        <input type="hidden" value="{{ $defaultVariant->id ?? '' }}" id="selected_configurable_option" name="selected_configurable_option"/>

        @foreach ($config['attributes'] as $attribute)
            <div class="attribute control-group {{ $errors->has('super_attribute[' . $attribute['id'] . ']') ? 'has-error' : '' }}">
                <b class="required fs-4">{{ $attribute['label'] }}</b>

                <span id="redioErrorMessage_{{ $product->id }}" class="Redio_Error d-flex" style="color: red"></span>

                @if (empty($attribute['swatch_type']) || $attribute['swatch_type'] == 'dropdown')
                    <span class="custom-form">
                        <select
                            class="control styled-select"
                            name="super_attribute[{{ $attribute['id'] }}]"
                            id="attribute_{{ $attribute['id'] }}"
                            @change="configure(attribute, $event.target.value)">
                            
                            <option value="">{{ __('shop::app.products.select-above-options') }}</option>
                            @foreach ($attribute['options'] as $option)
                                <option value="{{ $option['id'] }}" {{ $option['id'] == $defaultVariant->{$attribute['code']} ? 'selected' : '' }}>
                                    {{ $option['label'] }}
                                </option>
                            @endforeach
                        </select>

                        <div class="select-icon-container">
                            <span class="select-icon rango-arrow-down"></span>
                        </div>
                    </span>
                @else
                    <span class="swatch-container" id="swatch_container_ProductId_{{ $product->id }}">

                        @foreach ($attribute['options'] as $option)
                        <label class="swatch single-product-page-buttons" for="attribute_{{ $attribute['id'] }}_option_{{ $option['id'] }}_{{ $product->id }}">
                            <input
                                type="radio"
                                class="product_variant"
                                value="{{ $option['id'] }}"
                                name="super_attribute[{{ $attribute['id'] }}]"
                                id="attribute_{{ $attribute['id'] }}_option_{{ $option['id'] }}_{{ $product->id }}"
                                attr="{{ $option['products'][0] }}"
                                hidden
                            >
                            <div class="single-product-page-button-group-1">
                                @if ($attribute['swatch_type'] == 'color')
                                    <span style="background: {{ $option['swatch_value'] }}"></span>
                                @elseif ($attribute['swatch_type'] == 'image')
                                    <img src="{{ $option['swatch_value'] }}" title="{{ $option['label'] }}" alt="" />
                                @elseif ($attribute['swatch_type'] == 'text')
                                    <span class="btn-secondary span px-2">{{ $option['label'] }}</span>
                                @endif
                            </div>
                        </label>
                    @endforeach

                    </span>
                @endif

                @if (!count($attribute['options']))
                    <span class="no-options">{{ __('shop::app.products.select-above-options') }}</span>
                @endif

                @if ($errors->has('super_attribute[' . $attribute['id'] . ']'))
                    <span class="control-error">{{ $errors->first('super_attribute[' . $attribute['id'] . ']') }}</span>
                @endif
            </div>
        @endforeach
    </div>
    <label style="color: #f84661" class="special_instruction">Special Instructions(optional)</label>
    <div id="category_instructions_Div" class="mt-1 w-100">
        <textarea id="textarea-customize" name="special_instruction" class="p-2" placeholder="Add your special instructions here..."></textarea>          
    </div>

