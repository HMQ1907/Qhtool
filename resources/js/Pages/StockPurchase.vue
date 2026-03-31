<template>
    <div class="py-6 flex flex-col gap-3">
        <Breadcrumb name="Stock Purchase" v-show="!hideBreadcrumb" />
        <div class="py-2 flex items-center justify-between">
            <Action />
            <Pagination :list="stockPurchases" />
            <div>
                <ToggleMenuButton @click="hideMenuAndBreadcrumb" />
            </div>
        </div>
        <VTable :list="stockPurchases.data" class="sticky top-0 z-[1]" />
    </div>
</template>
<script setup>
import { inject, ref } from "vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import Layout from "@/Components/Layout.vue";
import ToggleMenuButton from "@/Components/Button/ToggleMenu.vue";
import Action from "./StockPurchase/Action.vue";
import VTable from "./StockPurchase/Table.vue";
import Pagination from "@/Components/Pagination.vue";

defineOptions({
    layout: Layout,
});

defineProps(["stockPurchases"]);

const hideHeader = inject("hideHeader");
const hideBreadcrumb = ref(false);

const hideMenuAndBreadcrumb = () => {
    hideBreadcrumb.value = !hideBreadcrumb.value;
    hideHeader.value = !hideHeader.value;
};
</script>
