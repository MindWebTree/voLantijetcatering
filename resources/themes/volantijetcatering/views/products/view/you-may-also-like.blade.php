@if (isset($youMayAlsoLikeIts) && $youMayAlsoLikeItCount = $youMayAlsoLikeIts->count() )
@if($youMayAlsoLikeIts->count()>1)
    <card-list-header
        heading="You May Also Like"
        view-all="false"
        row-class="pt20"
        class="mt-4"
    ></card-list-header>

    <div class="carousel-products vc-full-screen">
        <carousel-component
            slides-per-page="6"
            navigation-enabled="hide"
            pagination-enabled="hide"
            id="you-may-also-like-products-carousel"
            :slides-count="{{ $youMayAlsoLikeItCount }}">
            @foreach ($youMayAlsoLikeIts as $i => $youMayAlsoLikeIt)
                <slide slot="slide-{{ $i }}">
                    @include ('shop::products.list.card', [
                        'product' => $youMayAlsoLikeIt,
                        'addToCartBtnClass' => 'small-padding',
                    ])
                </slide>
            @endforeach
        </carousel-component>
    </div>

    <div class="carousel-products vc-medium-screen">
        <carousel-component
            :slides-count="{{ $youMayAlsoLikeItCount }}"
            slides-per-page="3"
            id="you-may-also-like-products-carousel"
            navigation-enabled="hide"
            pagination-enabled="hide">

            @foreach ($youMayAlsoLikeIts as $i => $youMayAlsoLikeIt)
                <slide slot="slide-{{ $i }}">
                    @include ('shop::products.list.card', [
                        'product' => $youMayAlsoLikeIt,
                        'addToCartBtnClass' => 'small-padding',
                    ])
                </slide>
            @endforeach
        </carousel-component>
    </div>
    <div class="carousel-products vc-small-screen">
        <carousel-component
            :slides-count="{{ $youMayAlsoLikeItCount }}"
            slides-per-page="2"
            id="you-may-also-like-products-carousel"
            navigation-enabled="hide"
            pagination-enabled="hide">

            @foreach ($youMayAlsoLikeIts as $i => $youMayAlsoLikeIt)
                <slide slot="slide-{{ $i }}">
                    @include ('shop::products.list.card', [
                        'product' => $youMayAlsoLikeIt,
                        'addToCartBtnClass' => 'small-padding',
                    ])
                </slide>
            @endforeach
        </carousel-component>
    </div>
@endif
@endif
