<template>
    <div :class="`dropdown ${cartItems.length > 0 ? '' : 'disable-active'}`">
        <div
            class="dropdown-toggle btn btn-link"
            id="mini-cart"
            :class="{ 'cursor-not-allowed': !cartItems.length }"
        >
            <!-- Tanish || added ternary check on class -->
            <div
                :class="
                    cartCount >= 1
                        ? 'mini-cart-content'
                        : 'mini-cart-content'
                "
            >
                <!-- <i class="material-icons-outlined ">shopping_cart</i> -->

                <i class="material-icons text-down-3"
                    ><img class="shopping-bag-img"
                        src="/themes/volantijetcatering/assets/images/shopping-bag.png"
                /></i>
                <div class="badge-container">
                    <!-- Tanish || added ternary check item and items on v-text -->
                   
                    <span
                        class="badge bg-dark"
                        v-text="
                            cartCount > 1
                                ? cartCount
                                : cartCount
                        "
                        v-if="cartCount != 0"
                    >
                    </span>
                </div>
                <!-- commenting this section by Shyam on 25-07-23-->
                <!-- <span class="fs18 fw6 cart-text" v-text="cartText"></span>-->
            </div>

            <!--  <div class="down-arrow-container">
                <span class="rango-arrow-down"></span>
            </div> -->
        </div>

        <div
            id="cart-modal-content"
            class="modal-content dropdown-list sensitive-modal cart-modal-content cart__modal"
            :class="{ hide: !cartItems.length }"
        >
            <!-- Tanish || custom element for mini-cart header start -->
            <div class="min-cart-items">
                <div class="row mini-cart-header">
                    <div class="col-12 mini-cart-header">
                        <p>Your Order</p>
                        <img
                            id="close-btn"
                            src="/themes/volantijetcatering/assets/images/close-btn.png"
                            alt=""
                        />
                    </div>
                </div>
                <hr />
                <!-- Tanish || custom element for mini-cart header end -->
                <div class="mini-cart-container">
                    <div
                        class="row small-card-container"
                        :key="index"
                        v-for="(item, index) in cartItems"
                    >
                        <div
                            class="col-3 product-image-container mr15 border-0"
                        >
                            <span
                                class="remove-item"
                                @click="removeProduct(item.id)"
                            >
                                <span class="rango-close"></span>
                            </span>

                            <a
                                class="unset"
                                :href="`${$root.baseUrl}/${item.url_key}`"
                            >
                                <div
                                    class="product-image"
                                    :style="`background-image: url(${item.images.medium_image_url});`"
                                ></div>
                            </a>
                        </div>
                        <div
                            class="col-9 no-padding card-body align-vertical-top"
                        >
                            <div class="no-padding">
                                <div
                                    class="fs16 text-nowrap fw6 product-name"
                                    v-html="item.name"
                                ></div>

                                <div class="fs14 card-current-price fw6">
                                    <div class="display-inbl">
                                        <label class="fw5">{{
                                            __("checkout.qty")
                                        }}</label>
                                        <input
                                            type="text"
                                            disabled
                                            :value="item.quantity"
                                            class="ml5"
                                        />
                                    </div>
                                    <!-- <span class="card-total-price fw6">
                                        {{
                                            isTaxInclusive == "1"
                                                ? item.base_total_with_tax
                                                : item.base_total
                                        }}
                                    </span> -->
                                </div>
                            
                            </div>
                            <div class="row mini-cart-instruction">

                                <span v-if="item.additional.attributes != undefined"><strong>Preference: </strong>{{ item.additional.attributes.options.option_label }} </span>

                                <span v-if="item.additional.special_instruction !== undefined &&  item.additional.special_instruction !== ''"
                                    ><strong>Special Instruction: </strong
                                    >{{
                                        item.additional.special_instruction
                                    }}</span
                                >
                               
                        
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mini-cart-footer">
                <!-- <div class="modal-footer custom-modal-footer">
                    <h5 class="col-6 text-left fw6">
                        {{ subtotalText }}
                    </h5>

                    <h5 class="col-6 text-right fw6 no-padding">
                        {{
                            isTaxInclusive == "1"
                                ? cartInformation.base_grand_total
                                : cartInformation.base_sub_total
                        }}
                    </h5>
                </div> -->

                <div class="modal-footer custom-modal-footer">
                    <a
                        class="col text-left fs16 link-color remove-decoration"
                        :href="viewCartRoute"
                    >
                        <span v-html="viewCartText"></span>
                    </a>

                    <div class="col text-right no-padding">
                        <a
                            :href="checkoutRoute"
                            @click="checkMinimumOrder($event)"
                        >
                            <button type="button" class="theme-btn fs16 fw6">
                                {{ checkoutText }}
                            </button>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style lang="scss">
.hide {
    display: none !important;
}
</style>

<script>
export default {
    props: [
        "isTaxInclusive",
        "viewCartRoute",
        "checkoutRoute",
        "checkMinimumOrderRoute",
        "cartText",
        "viewCartText",
        "checkoutText",
        "subtotalText",
    ],

    data: function () {
        return {
            cartItems: [],
            cartInformation: [],
            cartCount: 0,
        };
    },

    mounted: function () {
        this.getMiniCartDetails();
    },

    watch: {
        "$root.miniCartKey": function () {
            this.getMiniCartDetails();
        },
    },

    methods: {
        getMiniCartDetails: function () {
            this.$http
                .get(`${this.$root.baseUrl}/mini-cart`)
                .then((response) => {
                    if (response.data.status) {
               
                        this.cartItems = response.data.mini_cart.cart_items;

                        this.cartCount = 0;

                        for (const [
                            idx,
                            item,
                        ] of response.data.mini_cart.cart_items.entries()) {
                         
                            this.cartCount = this.cartCount+parseInt(item.quantity);                       
                        }

                        this.cartInformation =
                            response.data.mini_cart.cart_details;
                    } else {
                        this.cartCount = 0;
                    }
                })
                .catch((exception) => {
                    console.log(this.__("error.something_went_wrong"));
                });
        },

        removeProduct: function (productId) {
            this.$http
                .delete(`${this.$root.baseUrl}/cart/remove/${productId}`)
                .then((response) => {
                    this.cartItems = this.cartItems.filter(
                        (item) => item.id != productId
                    );
                    this.$root.miniCartKey++;

                    window.showAlert(
                        `alert-${response.data.status}`,
                        response.data.label,
                        response.data.message
                    );

                    if (!this.cartItems.length && this.isCheckoutPage()) {
                        window.location.href = this.checkoutRoute;
                    }
                })
                .catch((exception) => {
                    console.log(this.__("error.something_went_wrong"));
                });
        },

        isCheckoutPage() {
            return window.location.href.includes("checkout");
        },

        checkMinimumOrder: function (e) {
            e.preventDefault();

            this.$http.post(this.checkMinimumOrderRoute).then(({ data }) => {
              
                if (!data.status) {
                
                    window.showAlert(`alert-warning`, "Warning", data.message);
                } else {
             
                    window.location.href = this.checkoutRoute;
                }
            });
        },
    },
};
</script>
