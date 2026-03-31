<template>
    <div>
        <div class="flex items-center gap-2 mx-3">
            <FilterButton @click="showFilterModal = true" />
            <ResetButton @click="reset" />
            <div class="h-6 border-r border-gray-300"></div>
            <CreateButton @click="openModal('add')" />
            <HistoryButton @click="showHistoryModal = true" />
        </div>
        <FilterModal :show="showFilterModal" @close="showFilterModal = false" />
        <HistoryModal :show="showHistoryModal" @close="showHistoryModal = false" page="PurchaseTemplates" />
        <EditAddNewPurchaseTemplateModal :show="showEditModal" :mode="modalMode" :templateId="templateId"
            @close="closeModal" />
        <PreviewModal :show="showPreviewModal" :templateId="previewTemplateId" :type="previewType" :language="language"
            @close="closePreviewModal" />
    </div>
</template>
<script setup>
import { ref } from "vue";
import { resetRoute } from "@/Composables/helpers";
import FilterButton from "@/Components/Button/Filter.vue";
import ResetButton from "@/Components/Button/Reset.vue";
import HistoryButton from "@/Components/Button/History.vue";
import FilterModal from "./Action/FilterModal.vue";
import HistoryModal from "@/Components/HistoryModal.vue";
import CreateButton from "@/Components/Button/Create.vue";
import EditAddNewPurchaseTemplateModal from "./Action/EditAddNewPurchaseTemplateModal.vue";
import PreviewModal from "./Action/PreviewModal.vue";


const showFilterModal = ref(false);
const showHistoryModal = ref(false);
const showEditModal = ref(false);
const showPreviewModal = ref(false);
const modalMode = ref("add");
const templateId = ref(null);
const previewTemplateId = ref(null);
const previewType = ref('pdf');
const language = ref('en');

const reset = () => {
    resetRoute("purchase-template.index");
};

const openModal = (mode, id, lang) => {
    modalMode.value = mode;
    templateId.value = id;
    showEditModal.value = true;
    language.value = lang;
};

const closeModal = () => {
    showEditModal.value = false;
    templateId.value = null;
};

const preview = (mode, id, type = 'pdf', lang = language.value) => {
    previewType.value = type || 'pdf';
    previewTemplateId.value = id;
    showPreviewModal.value = true;
    language.value = lang;
};

const closePreviewModal = () => {
    showPreviewModal.value = false;
    previewTemplateId.value = null;
};

defineExpose({
    openModal,
    preview,
});
</script>
