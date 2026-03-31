<template>
    <div class="py-6 flex flex-col gap-3">
        <Breadcrumb name="Purchase-Recently" v-show="!hideBreadcrumb" />
        <div class="py-2 flex items-center justify-between">
            <Action :customers="props.customers" />
            <Pagination :list="props.purchases" />
            <div>
                <ToggleMenuButton @click="hideMenuAndBreadcrumb" />
            </div>
        </div>
        <VTable :list="formattedPurchases" :spoOptions="formattedSpoOptions" />
    </div>
</template>

<script setup>
import { computed, ref, inject } from "vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import Action from "./Recently/Action.vue";
import Layout from "@/Components/Layout.vue";
import Pagination from "@/Components/Pagination.vue";
import VTable from "./Recently/Table.vue";
import ToggleMenuButton from "@/Components/Button/ToggleMenu.vue";

defineOptions({
    layout: Layout,
});

const props = defineProps({
    purchases: {
        type: Object,
        required: true,
    },
    customers: {
        type: Array,
        required: true,
    },
    spo: {
        type: Array,
        required: true,
    },
});

const formattedPurchases = computed(() => {
    return Object.entries(props.purchases.data).map(([_, rows]) => ({
        supplier_name: rows[0].supplier?.data?.name,
        rows,
    }));
});

const formattedSpoOptions = computed(() => {
    return props.spo.map((item) => ({
        supplier_code: item.supplier_code,
        label: item.code,
        value: item.id,
    }));
});

const hideHeader = inject("hideHeader");
const hideBreadcrumb = ref(false);
const hideMenuAndBreadcrumb = () => {
    hideBreadcrumb.value = !hideBreadcrumb.value;
    hideHeader.value = !hideHeader.value;
};
</script>
