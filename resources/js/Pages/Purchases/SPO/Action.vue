<template>
    <div>
        <div class="flex items-center gap-2 mx-3">
            <FilterButton @click="showFilterModal = true" />
            <ResetButton @click="reset" />
            <div class="h-6 border-r border-gray-300"></div>
            <HistoryButton @click="showHistoryModal = true" />
        </div>
        <FilterModal :show="showFilterModal" @close="showFilterModal = false" />
        <HistoryModal
            :show="showHistoryModal"
            @close="showHistoryModal = false"
            page="SPO Purchases"
        />
    </div>
</template>
<script setup>
import { ref } from "vue";
import FilterButton from "@/Components/Button/Filter.vue";
import ResetButton from "@/Components/Button/Reset.vue";
import HistoryButton from "@/Components/Button/History.vue";
import FilterModal from "./Action/FilterModal.vue";
import HistoryModal from "@/Components/HistoryModal.vue";
import { resetRoute } from "@/Composables/helpers";

const showFilterModal = ref(false);
const showHistoryModal = ref(false);

const reset = () => {
    resetRoute("purchase.spo.index");
};
</script>
