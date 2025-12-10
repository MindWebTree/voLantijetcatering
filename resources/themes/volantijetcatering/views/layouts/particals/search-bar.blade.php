{{-- 22-05-2024 || change center to end  and added style and class "search-bar-container"--}}
{{-- <div class="input-group justify-content-end search-bar-container" style="display: none;"> --}}
<div class="input-group justify-content-end search-bar-container" style="width: 0; overflow: hidden;">
    <form
        method="GET"
        role="search"
        id="search-form"
        action="{{ route('shop.search.index') }}">
        <div
            class="btn-toolbar full-width search-form"
            role="toolbar">
            <searchbar-component>
                <template v-slot:image-search>
                    <image-search-component
                        status="{{core()->getConfigData('general.content.shop.image_search') == '1' ? 'true' : 'false'}}"
                        upload-src="{{ route('shop.image.search.upload') }}"
                        view-src="{{ route('shop.search.index') }}"
                        common-error="{{ __('shop::app.common.error') }}"
                        size-limit-error="{{ __('shop::app.common.image-upload-limit') }}">
                    </image-search-component>
                </template>
            </searchbar-component>
        </div>
    </form>
</div>
