<template>
    <div class="py-6 flex flex-col gap-3">
        <div class="flex items-center gap-2 mx-3">
            <Breadcrumb name="Purchase-SPO" v-show="!hideBreadcrumb" />
            <div class="flex items-center gap-5" v-show="!hideBreadcrumb">
                <SwitchButton v-model="language" @language-changed="setLanguage" />
            </div>
        </div>
        <div class="py-2 flex items-center justify-between">
            <Action />
            <Pagination v-if="SPOPurchases.data && SPOPurchases.data.length > 0" :list="SPOPurchases" />
            <div>
                <ToggleMenuButton @click="hideMenuAndBreadcrumb" />
            </div>
        </div>
        <VTable :list="SPOPurchases.data" class="sticky top-0 z-[1]" :language="language" />
    </div>
</template>

<script setup>
import { ref, inject } from "vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import Layout from "@/Components/Layout.vue";
import VTable from "./SPO/Table.vue";
import Pagination from "@/Components/Pagination.vue";
import Action from "./SPO/Action.vue";
import ToggleMenuButton from "@/Components/Button/ToggleMenu.vue";
import SwitchButton from "@/Components/Button/Switch.vue";

const language = ref('en');

defineOptions({
    layout: Layout,
});

defineProps(["SPOPurchases"]);
const hideHeader = inject("hideHeader");

const hideBreadcrumb = ref(false);

const hideMenuAndBreadcrumb = () => {
    hideBreadcrumb.value = !hideBreadcrumb.value;
    hideHeader.value = !hideHeader.value;
};

const setLanguage = (lang) => {
    language.value = lang;
};

</script>
