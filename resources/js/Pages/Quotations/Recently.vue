<template>
    <div class="py-6 flex flex-col gap-3">
        <Breadcrumb name="Quotation-Recently" v-show="!hideBreadcrumb" />
        <div class="py-2 flex items-center justify-between">
            <Action />
            <Pagination :list="props.quotations" />
            <div>
                <ToggleMenuButton @click="hideMenuAndBreadcrumb" />
            </div>
        </div>

        <VTable :list="formattedQuotations" />
    </div>
</template>

<script setup>
import { ref, inject, computed } from "vue";
import Layout from "@/Components/Layout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import VTable from "./Recently/Table.vue";
import Action from "./Recently/Action.vue";
import ToggleMenuButton from "@/Components/Button/ToggleMenu.vue";
import Pagination from "@/Components/Pagination.vue";

defineOptions({
    layout: Layout,
});

const props = defineProps(["quotations"]);

const formattedQuotations = computed(() => {
    return Object.entries(props.quotations.data).map(
        ([_, quotations]) => ({
            supplier_name: quotations[0].supplier.data.name,
            rows: quotations,
        }),
        { deep: true },
    );
});

const hideHeader = inject("hideHeader");
const hideBreadcrumb = ref(false);

const hideMenuAndBreadcrumb = () => {
    hideBreadcrumb.value = !hideBreadcrumb.value;
    hideHeader.value = !hideHeader.value;
};
</script>
