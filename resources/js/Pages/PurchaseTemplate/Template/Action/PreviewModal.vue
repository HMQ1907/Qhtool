<template>
    <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center" @click.self="closeModal">
        <div class="w-7xl mx-auto bg-white max-h-[90vh] p-4 relative rounded-lg shadow-2xl">
            <button @click="closeModal" type="button"
                class="absolute cursor-pointer -top-2 -right-2 z-20 w-10 h-10 flex items-center justify-center bg-red-500 hover:bg-red-600 text-white rounded-full shadow-lg transition-all duration-200 hover:scale-110">
                <i class="fas fa-times text-sm"></i>
            </button>

            <div class="border border-gray-200 max-h-[85vh] flex flex-col rounded">
                <div v-if="loading" class="flex items-center justify-center h-96">
                    <div class="text-center">
                        <i class="fa-solid fa-spinner fa-spin text-3xl text-gray-400 mb-3"></i>
                        <p class="text-gray-500 text-lg">Loading preview...</p>
                    </div>
                </div>

                <div v-else-if="pdfFile" class="bg-gray-100 p-2 rounded">
                    <iframe :src="pdfFile" class="w-full h-[85vh] bg-white rounded" frameborder="0"></iframe>
                </div>

                <div v-else-if="excelFile" class="bg-gray-50 p-2 rounded">
                    <iframe :srcdoc="excelFile" class="w-full h-[85vh] bg-white rounded" frameborder="0"></iframe>
                </div>

                <div v-else class="flex items-center justify-center h-96">
                    <div class="text-center">
                        <i class="fa-solid fa-file-pdf text-3xl text-gray-400 mb-3"></i>
                        <p class="text-gray-500 text-lg">
                            No preview available
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, watch } from "vue";
import axios from "axios";

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    templateId: {
        type: [String, Number],
        default: null,
    },
    type: {
        type: String,
        default: "pdf",
    },
    spoId: {
        type: [String, Number],
        default: null,
    },
    language: {
        type: String,
        default: "en",
    },
});

const emit = defineEmits(["close"]);

const loading = ref(false);
const pdfFile = ref("");
const excelFile = ref("");

const closeModal = () => {
    emit("close");
};

const loadPdf = async () => {
    if (!props.templateId) return;
    loading.value = true;
    excelFile.value = "";
    try {
        const response = await axios.get(
            route("purchase.spo.preview", {
                id: props.templateId,
                format: props.type,
                language: props.language,
            }),
        );

        if (response.data && response.data.pdf) {
            pdfFile.value = response.data.pdf;
        }
    } catch (err) {
    } finally {
        loading.value = false;
    }
};

const loadExcel = async () => {
    if (!props.templateId && !props.spoId) return;
    loading.value = true;
    pdfFile.value = "";

    try {
        let response;

        if (props.spoId) {
            response = await axios.get(
                route("purchase.spo.preview", {
                    id: props.templateId,
                    format: "excel",
                    spoId: props.spoId,
                    language: props.language,
                }),
            );
        } else {
            response = await axios.get(
                route("purchase.spo.preview", {
                    id: props.templateId,
                    format: "excel",
                    language: props.language,
                }),
            );
        }
        excelFile.value = response.data.xlsx;
    } catch (err) {
        excelFile.value = "";
    } finally {
        loading.value = false;
    }
};

watch(
    [
        () => props.templateId,
        () => props.spoId,
        () => props.show,
        () => props.type,
    ],
    ([newTemplateId, newSpoId, newShow, newType]) => {
        if (newShow) {
            if (newType == "pdf" && newTemplateId) {
                loadPdf();
            } else if (newType == "excel") {
                if (newSpoId) {
                    loadExcel();
                } else if (newTemplateId) {
                    loadExcel();
                }
            }
        }
    },
    { immediate: true },
);
</script>
