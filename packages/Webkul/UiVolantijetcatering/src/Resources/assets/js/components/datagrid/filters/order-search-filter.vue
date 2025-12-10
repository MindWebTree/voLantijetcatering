<template>
    <div class="search-filter order-search-filter">
        <div class="icon-wrapper order__search__icon">
            <span
                class="icon search-icon search-btn"
                v-on:click="search(searchValue)"
            ></span>
        </div>
        <input
            type="search"
            id="search-field"
            class="control order__search"
            :placeholder="placeholder"
            v-model="searchValue"
            v-on:keyup.enter="search(searchValue)"
        />

        <!-- <input
            type="text"
            id="search-field"
            class="control  "
            :placeholder="placeholder"
            v-model="searchValue"
            v-on:keyup.enter="search(searchValue)"
            inputmode="numeric"
            oninput="this.value = this.value.replace(/[^0-9]/g, '')"
        /> -->

    </div>
</template>

<script>
export default {
    props: ['translations'],

    data() {
        return {
            searchValue: '',
            placeholder: 'Search...'
        };
    },

    methods: {
        search(searchValue) {
            this.$emit('onFilter', { data: { searchValue } });
        }
    }
};
</script>
