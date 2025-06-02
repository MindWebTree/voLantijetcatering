<template>
    <div class="shimmer-card-container">
        <div class="shimmer-cards">
            <div
                class="shimmer-card"
                v-for="count in shimmerCountInt"
                :key="count"
            >
                <div class="shimmer-wrapper">
                    <div class="shimmer-product-image animate"></div>
                    <div class="comment animate"></div>
                    <div class="comment animate"></div>
                    <div class="comment animate"></div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        'shimmerCount': {
            default: 6,
        },
    },

    data: function () {
        return {
            shimmerCountInt: parseInt(this.shimmerCount),
        }
    }
}
</script>

<style>
    .shimmer-card-container {
        width: 100%;
        /*overflow-x: auto;
        white-space: nowrap;*/
    }

    .shimmer-cards {
        /*display: flex;*/
        justify-content: flex-start;
        width: 100%;
    }

    .shimmer-card {
        flex: 0 0 auto;
        margin: 0 10px;
        padding: 30px 40px;
        border: 2px solid #fff;
        box-shadow: 0 3px 6px rgba(0, 0, 0, 0.16), 0 3px 6px rgba(0, 0, 0, 0.23);
        /* width: 300px;*/ /* Adjust width as needed */
        width: 100%;
        height: 200px;
        margin: 0 0 35px 0;
    }

    .shimmer-product-image {
        width: 100%;
        height: 70px;
    }

    .comment {
        height: 10px;
        background: #777;
        margin-top: 20px;
    }

    .shimmer-wrapper {
        width: 0px;
        animation: fullView 0.5s forwards linear;
    }

    @keyframes fullView {
        100% {
            width: 100%;
        }
    }

    .animate {
        animation: shimmer 2s infinite;
        background: linear-gradient(to right, #eff1f3 4%, #e2e2e2 25%, #eff1f3 36%);
        background-size: 1000px 100%;
    }

    @keyframes shimmer {
        0% {
            background-position: -1000px 0;
        }
        100% {
            background-position: 1000px 0;
        }
    }
</style>
