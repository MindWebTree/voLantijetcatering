
<template>
    <div class="col-lg-4 col-md-6 col-sm-12 order-grid">
        <div class="order_no_order_detail p-0 col-12 d-flex">
        <p class="order-no mt-5 mb-2 col-8 p-0">Order Number : {{ order.id }}</p>
        <!-- <div class="row order-btn py-2"> -->
                <a
                    class="col-4 mt-5 mb-2 p-0"
                    :title="order.id"
                    :href="`${order.view_url}`"
                    style="text-align: end;color: black; text-decoration: underline;"
                    >View Detail</a

                >
                <!-- <button type="button" class="btn btn-rate">Rate Order</button> -->
            <!-- </div> -->
        </div>
        <div class="card order-card border-0">
            <div class="row card-body">
                <div class="col-9 airport-add">
                    <h6>{{ order.airport_name }}</h6>
                    <p class="mt-2">{{ order.address1 }}</p>
                   
                    <!-- <p class="mt-2">{{ order }}</p> -->    
                </div>
                <div class="col-3 p-0">
                    <div class="status m-auto orderDeatil" v-html="order.status"></div>
               </div>
               <div class="order-price" v-if="!isStatusPending">
                <p>
                    ${{ 
                        (
                            parseFloat(order.grand_total.replace(/[$,]/g, '').trim()) + 
                            (order.Handling_charges ? parseFloat(order.Handling_charges) : 0)
                        ).toFixed(2) 
                    }}
                </p>
            </div>
                <div class="order-price" v-if="isStatusPending">
                    <span>N/A</span>
                </div>
            </div>

            <div class="col-12 order-product">
                <p>{{ formatDate(order.created_at) }}</p>
            </div>
           
        </div>
    </div>
</template>

<script type="text/javascript">
export default {
    props: ["list", "order"],
    data() {
        return {
            order: {
                status: `{{order.status}}`,
            },
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
  
        //sandeep add Method to format date in m-d-Y h:i:s A format
        formatDate(date) {
        const d = new Date(date);
        const pad = (num) => String(num).padStart(2, '0');
        const hours = d.getHours();
        const ampm = hours >= 12 ? 'PM' : 'AM';
        return `${pad(d.getMonth() + 1)}-${pad(d.getDate())}-${d.getFullYear()} ${pad(hours > 12 ? hours - 12 : hours)}:${pad(d.getMinutes())}:${pad(d.getSeconds())} ${ampm}`;
            },
        },
    computed: {
        isStatusPending() {
            const tempElement = document.createElement("div");
            tempElement.innerHTML = this.order.status;
            return tempElement.textContent.toLowerCase().includes("pending");
        },
        // sandeep add code for usa date formate
        formattedOrderDate() {
            return this.formatDate(this.order.created_at);
        }
    },
};
</script>
