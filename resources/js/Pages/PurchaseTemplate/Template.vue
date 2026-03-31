<template>
    <div class="py-6 flex flex-col gap-3">
        <div class="flex items-center justify-between">
            <Breadcrumb name="Purchase Template-Template" v-show="!hideBreadcrumb" />
            <div class="flex items-center gap-5 mr-3" v-show="!hideBreadcrumb">
                <SwitchButton v-model="language" />
            </div>
        </div>
        <div class="py-2 flex items-center justify-between">
            <Action ref="actionRef" />
            <Pagination :list="props.purchaseTemplates" />
            <div>
                <ToggleMenuButton @click="hideMenuAndBreadcrumb" />
            </div>
        </div>
        <VTable :list="props.purchaseTemplates.data" :language="language" />
    </div>
</template>

<script setup>
import { ref, inject, provide } from "vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import Action from "./Template/Action.vue";
import Layout from "@/Components/Layout.vue";
import Pagination from "@/Components/Pagination.vue";
import VTable from "./Template/Table.vue";
import ToggleMenuButton from "@/Components/Button/ToggleMenu.vue";
import SwitchButton from "@/Components/Button/Switch.vue";

defineOptions({
    layout: Layout,
});

const props = defineProps({
    purchaseTemplates: {
        type: Object,
        required: true,
    },
});

const hideHeader = inject("hideHeader");
const hideBreadcrumb = ref(false);
const actionRef = ref(null);
const language = ref('en');

provide("actionRef", actionRef);

const hideMenuAndBreadcrumb = () => {
    hideBreadcrumb.value = !hideBreadcrumb.value;
    hideHeader.value = !hideHeader.value;
};
</script>
