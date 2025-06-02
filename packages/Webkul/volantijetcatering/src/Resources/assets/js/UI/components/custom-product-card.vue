<template>
    <div


        class="col-12 lg-card-container list-card product-card row"
        v-if="list"
    >
        <div class="product-image">
            <a :title="product.name" :href="`${baseUrl}/${product.slug}`">
                <img
                    :src="product.image || product.product_image"
                    :onerror="`this.src='${this.$root.baseUrl}/vendor/webkul/ui/assets/images/product/large-product-placeholder.png'`"
                />

                <product-quick-view-btn
                    :quick-view-details="product"
                    v-if="!isMobile()"
                ></product-quick-view-btn>
            </a>
        </div>

        <div class="product-information">
            <div>
                <div class="product-name">
                    <a
                        :href="`${baseUrl}/${product.slug}`"
                        :title="product.name"
                        class="unset"
                    >
                        <span class="fs16">{{ product.name }}</span>
                    </a>
                </div>

                <!-- <div class="sticker new" v-if="product.new">
                    {{ product.new }}
                </div>-->

                <!-- <div class="product-price" v-html="product.priceHTML"></div> -->

                <div
                    class="product-rating"
                    v-if="product.totalReviews && product.totalReviews > 0">
                    <star-ratings :ratings="product.avgRating"></star-ratings>
                    <!--  <span>{{ __('products.reviews-count', {'totalReviews': product.totalReviews}) }}</span> -->
                </div>

                <div class="product-rating" v-else>
                    <span class="fs14" v-text="product.firstReviewText"></span>
                </div>

                <vnode-injector
                    :nodes="getDynamicHTML(product.addToCartHtml)"
                ></vnode-injector>
            </div>
        </div>
    </div>

    <!-- here some class changes of class by shyam 01-08- -->

    <div
        class="card grid-card product-card-new product-custom-class col-lg-3 col-md-4 col-6"
        v-else
    >
    <!-- sandeep delete -->
        <!-- <div class="product-content">
            <a
                :href="`${baseUrl}/${product.slug}`"
                :title="product.name"
                class="product-image-container"
            >
                <img
                    loading="lazy"
                    :alt="product.name"
                    :src="product.image || product.product_image"
                    :data-src="product.image || product.product_image"
                    class="card-img-top lzy_img"
                    :onerror="`this.src='${this.$root.baseUrl}/vendor/webkul/ui/assets/images/product/large-product-placeholder.png'`"
                />
                <!-- :src="`${$root.baseUrl}/vendor/webkul/ui/assets/images/product/medium-product-placeholder.png`" /> -->

                <!-- <product-quick-view-btn :quick-view-details="product"></product-quick-view-btn> -->
            <!-- </a>
        </div>  -->
        <div class="card-body">
            <div class="product-name col-12 no-padding custom-product-name">
                <a
                    class="unset"
                    :title="product.name"
                    :href="`${baseUrl}/${product.slug}`"
                >
                    <span class="fs16">{{ product.name }}</span>
                </a>
                <br />
                <p>{{ product.description }}</p>
            </div>

            <div class="sticker new" v-if="product.new">
                {{ product.new }}   
            </div> 

            <!-- <p>{{ product }}</p> -->

            <!-- <div v-html="product.priceHTML"></div>
             <div class="add-to-cart-plus-img">  -->

             <!-- sandeep delete code -->
             <!-- <a :href="`${baseUrl}/${product.slug}`">
                <img
                    class="plus-img"
                    src="/themes/velocity/assets/images/plus.png"
                    alt=""
                />
                
            </a> -->

        
            <!-- sandeep  -->
     
            <!-- {{ product.addToCartHtml }} -->
         
                <!-- <p>{{ product.id }}</p> -->
            <!-- sandeep  -->

          
            
        </div>

             <div class="AddToCartButton">


              <div class="quantityButton">

              <div class="input-group-prepend">
              <button class="btn btn-outline-secondary btn-minus">-</button>
             </div>
            

              <input type="text" name = "quantity-input" value="1" class="QuantityInputButton ml-2">

             <div class="input-group-prepend">
              <button class="btn btn-outline-secondary btn-plus">+</button>
             </div>
           
             </div>



              <div class="AddButton">
             <button class="add_button">Add</button>
            </div>
            


           <div class="Customizationbutton">
           <button class="customization_button">Customization</button>
            </div>


        </div>
        <div class="CategoryInstruction">
            <h3 id = "category_instructions"  @click="toggleInstructions">Special Instructions (optional) +</h3>

            <div id="category_instructions_Div" class="mt-3" style="display: none;" v-show="showInstructions">
            <textarea id="textarea-customize"></textarea>
           </div>

        </div>

     


            <div
                class="product-rating col-12 no-padding"
                v-if="product.totalReviews && product.totalReviews > 0"
            >
                <star-ratings :ratings="product.avgRating"></star-ratings>
                <a
                    class="fs14 unset active-hover"
                    :href="`${$root.baseUrl}/reviews/${product.slug}`"
                >
                    {{
                        __("products.reviews-count", {
                            totalReviews: product.totalReviews,
                        })
                    }}
                </a>
            </div>

       

            <!--  <div class="product-rating col-12 no-padding" v-else>
                <a :href="`${$root.baseUrl}/product/${product.slug}/review`" class="unset">
                        <span class="fs14" v-text="product.firstReviewText"></span>
                </a>
            </div> -->
            <vnode-injector
                :nodes="getDynamicHTML(product.addToCartHtml)"
            ></vnode-injector>
        </div>
      
    </div>
 
</template>

<script type="text/javascript">

export default {
    props: ["list", "product"],

    data: function () {
        return {
            addToCart: 0,
            addToCartHtml: "",
            // sandeep
            showInstructions: false, // Initially set to false
        };
    },



    methods: {
        isMobile: function () {
            if (
                /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(
                    navigator.userAgent
                )
            ) {
                return true;
            } else {
                return false;
            }
        },

        // sandeep show div
     toggleInstructions: function () {
      this.showInstructions = !this.showInstructions;
    },
    },
};
</script>





//  customization request form



<!-- Modal -->
<div class="modal fade customize_modal" id="exampleModal" tabindex="-1" role="dialog"
aria-labelledby="exampleModalLabel" aria-hidden="true">
<div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel" style="font-weight: 600;">Custom Order Request</h5>
            <button type="button" id="close" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body inquiry_modal_body">
            <!-- sucess mesage  -->
            <div class="inqueryMessage" style="display:none;" id="InqueryMessage">
                <img src="{{ asset('themes/volantijetcatering/assets/images/accept.png') }}" alt=""
                    id="SuccessIcon">
                <h3>Thank you for contacting us.</h3>
                <p> One of our sales rep will get in touch with you soon.
                <p>
            </div>
            <!-- error message -->
            <div id="errorContainer" class="alert alert-danger" style="display: none;">

            </div>

            <form id="inquiryForm" enctype="multipart/form-data">
                @csrf
                {{-- <div class="form-group">
                    <label for="name" class="field_required">Name</label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                        id="name" placeholder="Enter your name" required value="{{ old('name') }}">

                </div>
                <div class="form-group">
                    <label for="email" class="field_required">Email address</label>
                    <input type="email" class="form-control @error('email') is-invalid @enderror"
                        id="email" name="email" placeholder="Enter your email" required
                        value="{{ old('email') }}">
                </div> --}}


                <div class="row">
                    <div class="col-md-6 col-lg-6 col-sm-12">
                        <div class="form-group">
                            <label for="name" class="field_required">Name</label>
                            <input type="text" name="name"
                                class="form-control @error('name') is-invalid @enderror" id="name"
                                placeholder="" required value="{{ old('name') }}">

                        </div>
                    </div>
                    <div class="col-md-6 col-lg-6 col-sm-12">
                        <div class="form-group">
                            <label for="email" class="field_required">Email address</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                id="email" name="email" placeholder="" required
                                value="{{ old('email') }}">
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="phone" class="field_required">Phone number</label>
                    <input type="number" class="form-control @error('mobile_number') is-invalid @enderror"
                        id="phone" name="mobile_number" placeholder="" required
                        value="{{ old('mobile_number') }}">

                </div>
                <div class="form-group">
                    <label for="message" class="field_required">Message</label>
                    <textarea class="form-control @error('message') is-invalid @enderror" id="message" name="message" rows="5"
                        placeholder="" required>{{ old('message') }}</textarea>

                </div>

                <div class="form-group">
                    <label class="field_required" for="uploadfile">Upload Files</label>
                    <input type="file" class="form-control-file @error('uploadfile.*') is-invalid @enderror"
                        id="uploadfile" name="uploadfile[]" multiple required>

                    <div id="fileError" class="text text-danger"></div>
                </div>

                <div class="send__button">
                    <button type="submit" class="sendbutton">Send</button>
                </div>
            </form>
        </div>
    </div>
</div>
</div>