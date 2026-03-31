<template>
    <div>
        <div class="flex items-center gap-2 mx-3">
            <FilterButton @click="showFilterModal = true" />
            <ResetButton @click="reset" />
            <div class="h-6 border-r border-gray-300"></div>
            <ExportButton @click="redirectStockPurchaseDownloadPage" />
            <ImportButton @click="uploadFile" />
            <input type="file" class="hidden" ref="fileInput" @change="handleFileChange" />
            <HistoryButton @click="showHistoryModal = true" />
        </div>
        <FilterModal :show="showFilterModal" @close="showFilterModal = false" />
        <HistoryModal :show="showHistoryModal" @close="showHistoryModal = false" :page="'Stock Purchase'" />
    </div>
</template>
<script setup>
import { ref } from "vue";
import { useForm } from "@inertiajs/vue3";
import { resetRoute } from "@/Composables/helpers";
import FilterButton from "@/Components/Button/Filter.vue";
import ResetButton from "@/Components/Button/Reset.vue";
import HistoryButton from "@/Components/Button/History.vue";
import FilterModal from "./Action/FilterModal.vue";
import ExportButton from "@/Components/Button/Export.vue";
import ImportButton from "@/Components/Button/Import.vue";
import HistoryModal from "@/Components/HistoryModal.vue";

const showFilterModal = ref(false);
const showHistoryModal = ref(false);
const fileInput = ref(null);

const form = useForm({
    file: null,
});

const reset = () => {
    resetRoute("stock-purchase.index");
};

const redirectStockPurchaseDownloadPage = () => {
    window.open(route("stock-purchase.export", route().params));
};

const uploadFile = () => {
    fileInput.value.click();
};

const handleFileChange = (e) => {
    const file = e.target.files[0];
    form.file = file;
    form.post(route("stock-purchase.import"), {
        forceFormData: true,
        onSuccess: (response) => {
            window.notyf.success(response.props.flash.message.messages);
        },
        onError: (error) => {
            window.notyf.error(error.error.messages);
        },
        onFinish: () => {
            form.reset("file");
            if (fileInput.value) {
                fileInput.value.value = null;
            }
        },
    });
};
</script>
