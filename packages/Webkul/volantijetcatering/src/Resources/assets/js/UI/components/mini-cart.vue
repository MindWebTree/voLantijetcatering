<template>
  <div :class="`dropdown ${cartItems.length > 0 ? '' : 'disable-active'}`">
    <div
      class="dropdown-toggle btn btn-link"
      id="mini-cart"
      :class="{ 'cursor-not-allowed': !cartItems.length }"
    >
      <!-- Tanish || added ternary check on class -->
      <div :class="cartCount >= 1 ? 'mini-cart-content' : 'mini-cart-content'">
        <!-- <i class="material-icons-outlined ">shopping_cart</i> -->

        <i class="material-icons text-down-3"
          ><img
            class="shopping-bag-img"
            src="/themes/volantijetcatering/assets/images/shopping-bag.png"
        /></i>
        <div class="badge-container">
          <!-- Tanish || added ternary check item and items on v-text -->

          <span
            class="badge bg-dark "
            v-text="cartCount > 1 ? cartCount : cartCount"
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
      :class="{ hide: shouldHideCart }"
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
        <div class="mini-cart-container" id="mini-cart-duplicate-container">
          <div
            class="row small-card-container col-12 pt-2 pb-2 " style ="border-bottom: 1px solid rgb(222, 226, 230);"
            :key="index"
            v-for="(item, index) in cartItems"
          >
          <!-- sandeep delete image code -->
            <!-- <div class="col-3 product-image-container mr15 border-0">
              <span class="remove-item" @click="removeProduct(item.id)">
                <span class="rango-close"></span>
              </span>

  
              <a class="unset" :href="`${$root.baseUrl}/${item.url_key}`">
                <div
                  class="product-image"
                  :style="`background-image: url(${item.images.medium_image_url});`"
                ></div>
              </a>
            </div> -->
            <div class="col-8 no-padding card-body align-vertical-top" style="padding-right: 10px !important">
              <div class="no-padding">
                <div class="fs16 text-nowrap fw6 product-name pb-1" v-html="item.name"></div>

                <div class="row mini-cart-instruction d-block" style="font-size: 11px;">
                  <!-- sandeep delete div -->
                <!-- <div class="row mini-cart-instruction"> -->
                  <div class="pb-1" v-if="item.additional.attributes != undefined">
                    Preference:
                    <template v-for="attribute in item.additional.attributes">
                      <span v-if="attribute.option_label">
                        {{ attribute.option_label }}
                      </span>
                    </template>
                  </div>

                  <div
                    v-if="
                      item.additional.special_instruction !== undefined &&
                      item.additional.special_instruction !== ''
                    "
                    >Special Instruction <br /><div style="background-color: #f2f2f3;padding:10px">{{ item.additional.special_instruction }}</div></div
                  >
                </div>
              <!-- </div> -->
            </div>
              </div>
  

            <div class="fs14 card-current-price fw6 col-4 mt-2 p-0 text-right">
                  <div class="display-inbl pb-2" style="display: flex !important;">
                    <!-- <img
                      src="/themes/volantijetcatering/assets/images/close.png"
                      alt="close Icon"
                      width="6"
                      height="6"
                      class="bin-icon-image"
                    /> -->
                    <!-- <span class="quantityValue ml-1 fs13">{{ item.quantity }}</span> -->
                    <!-- <span class="remove-item" @click="removeProduct(item.id)"> -->
                    <!-- <span class="group__input__field d-flex justify-content-between">
                      <button class="border-0 editMinusBtn" style="width: 16px;" data-item-id="{{ item.id }}">-</button>
                      <input type="number" class="text-center w-50 border-0 bg-light p-1 editQuantityInput" value="{{ item.quantity }}" data-item-id="{{ item.id }}">
                      <button class="border-0 editPlusBtn" style="width: 16px;" data-item-id="{{ item.id }}">+</button>
                    </span> -->
                    <span class="group__input__field d-flex justify-content-between">
                    <button
                      class="border-0 editMinusBtn"
                      style="width: 16px;"
                      :data-item-id="item.id"
                    >-</button>
                    <input
                      type="text"
                      class="text-center w-50 border-0 bg-light p-1 editQuantityInput"
                      :value="item.quantity"
                      :data-item-id="item.id"
                    >
                    <button
                      class="border-0 editPlusBtn"
                      style="width: 16px;"
                      :data-item-id="item.id"
                    >+</button>
                  </span>
        
                      <span class="bin_icon m-auto">
                        <span class="bin-btn-ring ml-2"></span>
                      <span class="bin-icon">
                        <img src="/themes/volantijetcatering/assets/images/bin-mini-cart.png" alt="Bin Icon" width="18" height="18" class="bin-icon-image ml-2" @click="removeProduct(item.id,$event)">
                      </span>
                      </span>


                      <!-- </span> -->
                  </div>
                  <!-- <button
                      class="border-0 text-end w-auto UpdateQuantityButton ml-2"
                      :data-item-id="item.id"
                    ><img src="/themes/volantijetcatering/assets/images/save.png" alt="Save" style="width: 18px; height: 18px; background: #fff; pointer-events: none;"></button> -->
                 
                    <button
                  class="border-0 text-end w-auto UpdateQuantityButton ml-2"
                  :data-item-id="item.id"
                >
                  <img src="/themes/volantijetcatering/assets/images/save.png" alt="Save" style="width: 18px; height: 18px; background: #fff; pointer-events: none;">
                </button>
                  <!-- <span class="card-total-price fw6">
                                        {{
                                            isTaxInclusive == "1"
                                                ? item.base_total_with_tax
                                                : item.base_total
                                        }}
                                    </span> -->
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

        <div class="modal-footer custom-modal-footer bg-white">
          <a
            class="col text-left fs16 link-color remove-decoration"
            :href="viewCartRoute"
          >
            <span v-html="viewCartText"></span>
          </a>

          <div class="col text-right no-padding">
            <a :href="checkoutRoute" @click="checkMinimumOrder($event)">
              <button type="button" class="theme-btn fs16 fw6 checkout__button">
                <img src="/../themes/volantijetcatering/assets/images/lock.png" alt="" />
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

  computed: {
    shouldHideCart() {
      const urlParams = new URLSearchParams(window.location.search);
      return this.cartItems.length === 0 && !urlParams.has("summaryedit");
    },
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

            for (const [idx, item] of response.data.mini_cart.cart_items.entries()) {
              this.cartCount = this.cartCount + parseInt(item.quantity);
            }

            this.cartInformation = response.data.mini_cart.cart_details;

            // sandeep add code for open mini cart 
            this.openMiniCart();

          } else {
            this.cartCount = 0;
          }
        })
        .catch((exception) => {
          console.log(this.__("error.something_went_wrong"));
        });
    },
    
    openMiniCart() {
      let cartModal = document.getElementById("cart-modal-content");
      if (cartModal) {
        cartModal.classList.remove("hide");
        cartModal.classList.add("slide-cart-modal");
      }
  },
    removeProduct: function (productId,event) {
      let $clickedElement = $(event.currentTarget);

    $clickedElement.closest('.bin-icon').css('visibility', 'hidden');
    $clickedElement.closest('.display-inbl').find(".bin-btn-ring").show();

      this.$http
        .delete(`${this.$root.baseUrl}/cart/remove/${productId}`)
        .then((response) => {
          this.cartItems = this.cartItems.filter((item) => item.id != productId);
          this.$root.miniCartKey++;

          window.showAlert(
            `alert-${response.data.status}`,
            response.data.label,
            response.data.message
          );

          $clickedElement.closest('.display-inbl').find(".bin-btn-ring").hide();
          $clickedElement.closest('.bin-icon').css('visibility', 'visible');

          if (!this.cartItems.length && this.isCheckoutPage()) {
            window.location.href = this.checkoutRoute;
          }
        })
        .catch((exception) => {
          console.log(this.__("error.something_went_wrong"));

          $clickedElement.closest('.display-inbl').find(".bin-btn-ring").hide();
          $clickedElement.closest('.bin-icon').css('visibility', 'visible');
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
