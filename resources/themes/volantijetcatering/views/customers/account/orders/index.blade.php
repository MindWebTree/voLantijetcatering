@extends('shop::customers.account.index')

@section('page_title')
    {{-- {{ __('shop::app.customer.account.order.index.page-title') }} --}}
    Orders | Volanti Jet Catering
@endsection

@section('seo')
<meta name="title" content="Orders | Volanti Jet Catering" />
<meta name="description" content="Orders | Volanti Jet Catering" />
<meta name="keywords" content="" />
@stop

@section('page-detail-wrapper')
    <div class="account-head profile-header d-flex justify-content-center mt-3">
        <h3 class="account-heading">
            {{ __('shop::app.customer.account.order.index.title') }}
        </h3>
    </div>

    <div class="account-items-list">
        <div class="account-table-content">
            <order-component></order-component>
             {{-- <datagrid-plus src="{{ route('shop.customer.orders.index') }}"></datagrid-plus> --}}
        </div>
    </div>
@endsection
@push('scripts')
    <script type="text/x-template" id="order-template">
<shimmer-component v-if="isLoading" shimmer-count="4"></shimmer-component>

<template v-else-if="orders.length > 0">
    <div class="container">
        <div class="row">
            <custom-order-card
            :key="index"
            :order="order"
            v-for="(order, index) in orders">
            </custom-order-card>
        </div>
    </div>
</template>
<template v-else>
    <div class='no__order mt-5'>
        <p>No orders available.....<p/>
    </div>
</template>
    </script>


    <script>
        Vue.component('order-component', {
            template: '#order-template',
            data: function() {
                return {
                    'orders': [],
                    'isLoading': true,
                    'paginationHTML': '',
                    'currentScreen': window.innerWidth,
                    'slidesPerPage': 5,
                }
            },

            created: function() {
                this.orderDetail();
                this.setSlidesPerPage(this.currentScreen);
            },

            methods: {
                'orderDetail': function() {
                    this.$http.get(
                            `${this.$root.baseUrl}/customer/account/orders?v=1&channel=default&locale=en${window.location.search}`
                        )
                        .then(response => {
                            this.isLoading = false;
                            this.orders = response.data.records.data;

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
@endpush
