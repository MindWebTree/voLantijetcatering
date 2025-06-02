<template>
  <div :class="`quantity control-group mb-1 ${hasError ? 'has-error' : ''}`">
    <!-- <label
            class="required"
            for="quantity-changer"
            v-text="quantityText"
        ></label>-->

    <div class="quantityButton">
      <div class="input-group-prepend">
      <button type="button" class="btn btn-minus" @click="decreaseQty()">
        <!-- <img src="/themes/volantijetcatering/assets/images/remove.png"/> -->
        -
      </button>
    </div>

      <input
        ref="quantityChanger"
        name="quantity"
        :model="qty"
        class="QuantityInputButton"
        id="quantity-changer"
        v-validate="validations"
        :data-vv-as="`&quot;${quantityText}&quot;`"
        @keyup="setQty($event)"
      />

      <div class="input-group-prepend">
      <button type="button" class="btn btn-plus" @click="increaseQty()">
        <!-- <img src="/themes/volantijetcatering/assets/images/add.png"/> -->
        +
      </button>
    </div>
  </div>
 

    <!-- <span class="control-error" v-if="errors.has(controlName)">{{
      errors.first(controlName)
    }}</span> -->
    <!-- SANDEEP -->
    
    <span class="control-error" v-if="hasError">
      {{ errorMessage }}
    </span>

  </div>
</template>

<script>
export default {
  template: "#quantity-changer-template",

  // inject: ["$validator"],

  props: {
    controlName: {
      type: String,
      default: "quantity",
    },

    quantity: {
      type: [Number, String],
      default: 1,
    },

    quantityText: {
      type: String,
      default: "Quantity",
    },

    minQuantity: {
      type: [Number, String],
      default: 1,
    },

    validations: {
      type: String,
      default: "required|numeric|min_value:1",
    },

    productId: {
      type: [Number, String],
      required: true
    },
    quantityId: {
      type: String,
      required: true
    }

    

  },

  data: function () {
    return {
      qty: this.quantity,
        hasError: false,
        errorMessage: ''
    };
  },

  // mounted: function () {
  //   this.$refs.quantityChanger.value =
  //     this.qty > this.minQuantity ? this.qty : this.minQuantity;
  // },


  mounted() {
    this.$refs.quantityChanger.value =
      this.qty > this.minQuantity ? this.qty : this.minQuantity;
  },


  // watch: {
  //   qty: function (val) {
  //     this.$refs.quantityChanger.value = !isNaN(parseFloat(val)) ? val : 0;

  //     this.qty = !isNaN(parseFloat(val)) ? this.qty : 0;

  //     // this.$emit("onQtyUpdated", this.qty);

  //     this.$emit("onQtyUpdated", { productId: this.productId, qty: this.qty });

  //     this.$validator.validate();
  //   },
  // },


  watch: {
    qty(val) {
      this.$refs.quantityChanger.value = !isNaN(parseFloat(val)) ? val : 0;
      this.qty = !isNaN(parseFloat(val)) ? this.qty : 0;
      this.$emit("onQtyUpdated", { productId: this.productId, qty: this.qty });

      this.validate();
    },
  },
  

  methods: {
    setQty: function ({ target }) {
      this.qty = parseInt(target.value);
    },

    decreaseQty: function () {
      if (this.qty > this.minQuantity) this.qty = parseInt(this.qty) - 1;
    },

    increaseQty: function () {
      this.qty = parseInt(this.qty) + 1;
    },

  validate() {
      this.$validator.validate().then(success => {
        if (!success) {
          this.hasError = true;
          this.errorMessage = this.$validator.errors.first(this.controlName);
        } else {
          this.hasError = false;
          this.errorMessage = '';
        }
      });
    },
  },
};

</script>
