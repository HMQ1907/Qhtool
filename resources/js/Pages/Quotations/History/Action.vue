<template>
    <div>
        <div class="flex items-center gap-2 mx-3">
            <FilterButton @click="showFilterModal = true" />
            <ResetButton @click="reset" />
            <div class="h-6 border-r border-gray-300"></div>
            <ExportButton @click="redirectDownloadPage" />
        </div>
        <FilterModal :show="showFilterModal" @close="showFilterModal = false" />
    </div>
</template>
<script setup>
import { ref } from "vue";
import FilterButton from "@/Components/Button/Filter.vue";
import ResetButton from "@/Components/Button/Reset.vue";
import ExportButton from "@/Components/Button/Export.vue";
import FilterModal from "./Action/FilterModal.vue";
import { resetRoute } from "@/Composables/helpers";

const showFilterModal = ref(false);

const reset = () => {
    resetRoute("quotation.history.index");
};

const redirectDownloadPage = () => {
    const filter = route().params.filter;
    if (filter) {
        window.open(route("quotation.history.export", route().params));
    } else {
        Swal.fire({
            title: "If you don't select a filter, the data will be empty",
            icon: "warning",
            confirmButtonText: "Continue",
            confirmButtonColor: "#f58220",
            showCancelButton: true,
            showCloseButton: true,
        }).then((result) => {
            if (result.isConfirmed) {
                window.open(route("quotation.history.export", route().params));
            }
        });
    }
};
</script>
