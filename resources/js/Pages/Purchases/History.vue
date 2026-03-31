<template>
    <div class="py-6 flex flex-col gap-3">
        <Breadcrumb name="Purchase-History" v-show="!hideBreadcrumb" />
        <div class="py-2 flex items-center justify-between">
            <Action />
            <Pagination
                v-if="purchases.data && purchases.data.length > 0"
                :list="purchases"
            />
            <div>
                <ToggleMenuButton @click="hideMenuAndBreadcrumb" />
            </div>
        </div>
        <VTable :list="purchases" class="sticky top-0 z-[1]" />
    </div>
</template>

<script setup>
import { ref, inject } from "vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import Layout from "@/Components/Layout.vue";
import VTable from "./History/Table.vue";
import Pagination from "@/Components/Pagination.vue";
import Action from "./History/Action.vue";
import ToggleMenuButton from "@/Components/Button/ToggleMenu.vue";

defineOptions({
    layout: Layout,
});

defineProps(["purchases"]);

const hideHeader = inject("hideHeader");

const hideBreadcrumb = ref(false);

const hideMenuAndBreadcrumb = () => {
    hideBreadcrumb.value = !hideBreadcrumb.value;
    hideHeader.value = !hideHeader.value;
};
</script>
