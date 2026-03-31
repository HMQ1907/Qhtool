<template>
    <div class="py-6 flex flex-col gap-3">
        <Breadcrumb
            name="Purchase Template - Template Mapping"
            v-show="!hideBreadcrumb"
        />
        <div class="py-2 flex items-center justify-between">
            <Action />
            <Pagination :list="suppliers" />
            <div>
                <ToggleMenuButton @click="hideMenuAndBreadcrumb" />
            </div>
        </div>
        <VTable :list="suppliers.data" :templates="templates" />
    </div>
</template>

<script setup>
import { ref, inject } from "vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import Layout from "@/Components/Layout.vue";
import Pagination from "@/Components/Pagination.vue";
import VTable from "./TemplateMapping/Table.vue";
import ToggleMenuButton from "@/Components/Button/ToggleMenu.vue";
import Action from "./TemplateMapping/Action.vue";

defineOptions({
    layout: Layout,
});

defineProps({
    suppliers: {
        type: Object,
        required: true,
    },
    templates: {
        type: Object,
        required: true,
    },
});

const hideHeader = inject("hideHeader");
const hideBreadcrumb = ref(false);
const hideMenuAndBreadcrumb = () => {
    hideBreadcrumb.value = !hideBreadcrumb.value;
    hideHeader.value = !hideHeader.value;
};
</script>
