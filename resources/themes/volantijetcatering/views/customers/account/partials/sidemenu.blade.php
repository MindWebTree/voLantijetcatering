<div class="customer-sidebar row no-margin no-padding">
    <div class="account-details col-12">
        @if (auth('customer')->user()->image)
            <div>
                <img style="width:80px;height:80px;border-radius:50%;" src="{{ auth('customer')->user()->image_url }}" alt="{{ auth('customer')->user()->first_name }}"/>
            </div>
        @else
            <div class="customer-name col-12 text-uppercase">
                {{ substr(auth('customer')->user()->first_name, 0, 1) }}
            </div>
        @endif
        <div class="col-12 customer-name-text text-capitalize text-break mt-3">{{ auth('customer')->user()->first_name . ' ' . auth('customer')->user()->last_name}}</div>
        {{-- <div class="customer-email col-12 text-break">{{ auth('customer')->user()->email }}</div> --}}
        <div class="customer-email col-12 text-break">{{ auth('customer')->user()->email }}</div>
    </div>


    @foreach ($menu->items as $menuItem)
    
        <ul type="none" class="navigation">
            {{-- @dd($menu) --}}
            {{-- rearrange menu items --}}
            @php
                $subMenuCollection = [];
                try {
                    $subMenuCollection['profile'] = $menuItem['children']['profile'];
                    $subMenuCollection['orders'] = $menuItem['children']['orders'];
                    // $subMenuCollection['downloadables'] = $menuItem['children']['downloadables']; // commented 13-10-203 (not required)

                    if ((bool) core()->getConfigData('general.content.shop.wishlist_option')) {
                        // $subMenuCollection['wishlist'] = $menuItem['children']['wishlist']; // commented 13-10-203 (not required)
                    }

                    if ((bool) core()->getConfigData('general.content.shop.compare_option')) {
                        // $subMenuCollection['compare'] = $menuItem['children']['compare']; // commented 13-10-203 (not required)
                    }

                    // $subMenuCollection['reviews'] = $menuItem['children']['reviews']; // commented 13-10-203 (not required)
                    // sandeep commnet 
                    // $subMenuCollection['address'] = $menuItem['children']['address'];
                
                    unset(
                        $menuItem['children']['profile'],
                        $menuItem['children']['orders'],
                        $menuItem['children']['downloadables'],
                        $menuItem['children']['wishlist'],
                        $menuItem['children']['compare'],
                        $menuItem['children']['reviews'],
                        $menuItem['children']['address']
                    );

                    foreach ($menuItem['children'] as $key => $remainingChildren) {
                        $subMenuCollection[$key] = $remainingChildren;
                    }
                } catch (\Exception $exception) {
                    $subMenuCollection = $menuItem['children'];
                }
            @endphp

            @foreach ($subMenuCollection as $index => $subMenuItem)
                <li class="{{ $menu->getActive($subMenuItem) }}" title="{{ trans($subMenuItem['name']) }}">
                    <a class="unset fw6 full-width" href="{{ $subMenuItem['url'] }}">
                        <i class="icon {{ $index }} text-down-3"></i>
                        <span>{{ trans($subMenuItem['name']) }}<span>
                        <i class="rango-arrow-right float-right text-down-3"></i>
                    </a>
                </li>   
            @endforeach
        </ul>
    @endforeach
</div>


@push('css')
    <style type="text/css">
        .main-content-wrapper {
            margin-bottom: 0px;
            min-height: 100vh;
        }
    </style>
@endpush