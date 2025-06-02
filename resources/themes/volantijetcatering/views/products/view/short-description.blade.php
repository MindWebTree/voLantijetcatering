{!! view_render_event('bagisto.shop.products.view.short_description.before', ['product' => $product]) !!}

<!--  here i comment out the accordian tag to view the simply  -->


    <!-- <accordian :title="'{{ __('shop::app.products.short-description') }}'" :active="true"> -->
        <!-- <div slot="header">
            <h3 class="no-margin display-inbl">
                {{ __('velocity::app.products.short-description') }}
            </h3>

            <i class="rango-arrow"></i>
        </div>

        <div slot="body"> -->
            <P class="full-short-description">
                {!! $product->short_description !!}
            </p>
        <!-- </div> -->
    <!-- </accordian> -->

   

{!! view_render_event('bagisto.shop.products.view.short_description.after', ['product' => $product]) !!}