<?php
    $youMayAlsoLikeIt = $youMayAlsoLikeIts;
?>

 @if ($youMayAlsoLikeIt->count())
    <div class="attached-products-wrapper">

        <div class="title">
            
            <span class="border-bottom"></span>
        </div>

        <div class="product-grid-4">

            @foreach ($youMayAlsoLikeIt as $youMayAlsoLike)

                @include ('shop::products.list.card', ['product' => $youMayAlsoLike])

            @endforeach

        </div>

    </div>
@endif 