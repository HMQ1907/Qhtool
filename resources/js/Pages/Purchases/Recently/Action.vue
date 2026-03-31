<template>
    <div>
        <div class="flex items-center gap-2 mx-3">
            <FilterButton @click="showFilterModal = true" />
            <ResetButton @click="reset" />
            <div class="h-6 border-r border-gray-300"></div>
            <ExportButton @click="redirectDownloadPage" />
            <HistoryButton @click="showHistoryModal = true" />
        </div>
        <FilterModal
            :show="showFilterModal"
            @close="showFilterModal = false"
            :customers="props.customers"
        />
        <HistoryModal
            :show="showHistoryModal"
            @close="showHistoryModal = false"
            page="Purchases"
        />
    </div>
</template>
<script setup>
import { ref } from "vue";
import FilterButton from "@/Components/Button/Filter.vue";
import ResetButton from "@/Components/Button/Reset.vue";
import ExportButton from "@/Components/Button/Export.vue";
import HistoryButton from "@/Components/Button/History.vue";
import FilterModal from "./Action/FilterModal.vue";
import HistoryModal from "@/Components/HistoryModal.vue";
import { resetRoute } from "@/Composables/helpers";

const props = defineProps(["customers"]);

const showFilterModal = ref(false);
const showHistoryModal = ref(false);

const reset = () => {
    resetRoute("purchase.recently.index");
};

const redirectDownloadPage = () => {
    window.open(route("purchase.recently.export", route().params));
};
</script>
