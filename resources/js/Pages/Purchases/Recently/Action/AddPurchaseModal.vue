<template>
    <Modal :show="show" :size="'md'" @close="$emit('close')">
        <template #header>
            <h3 class="text-2xl font-semibold text-gray-900 dark:text-white">
                Add Purchase
            </h3>
        </template>

        <template #body>
            <div class="p-5 max-w-xl mx-auto bg-white rounded-lg border-b border-gray-300 mb-4">
                <div class="flex items-center gap-6 mb-4">
                    <label class="inline-flex items-center gap-2 cursor-pointer">
                        <input type="radio" value="create" v-model="mode" />
                        <span>Create New Purchase</span>
                    </label>

                    <label class="inline-flex items-center gap-2 cursor-pointer">
                        <input type="radio" value="append" v-model="mode" />
                        <span>Add to exist Purchase</span>
                    </label>
                </div>

                <div v-if="mode === 'append'" class="grid grid-cols-12 items-center gap-2 mb-4">
                    <div class="col-span-12">
                        <SingleSelect id="spo-select" v-model="selectedSpo" :options="spoFilter" :optionKey="'value'"
                            :optionLabel="'label'" :placeholder="'Choose SPO ...'" />
                    </div>
                </div>
            </div>
        </template>

        <template #footer>
            <div class="flex justify-end gap-2 px-6 pb-4">
                <button type="button" @click="$emit('close')"
                    class="flex cursor-pointer items-center justify-center bg-gray-500 text-white py-2 px-4 rounded-lg hover:bg-gray-600 transition duration-500 ease-in-out">
                    <i class="fas fa-times mr-1"></i>
                    Cancel
                </button>

                <button type="button" @click="handleSubmit"
                    class="flex cursor-pointer items-center justify-center border bg-white border-blue-600 text-blue-600 py-2 px-4 rounded-lg hover:bg-blue-600 hover:text-white transition duration-500 ease-in-out">
                    Submit
                </button>
            </div>
        </template>
    </Modal>
</template>

<script setup>
import { ref, watch } from "vue";
import Modal from "@/Components/Modal.vue";
import SingleSelect from "@/Components/SingleSelect.vue";
import { router } from "@inertiajs/vue3";
const emit = defineEmits(["close"]);

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    supplierCode: {
        type: String,
        default: () => "",
    },
    spoOptions: {
        type: Array,
        default: () => [],
    },
    selectedPurchase: {
        type: Array,
        default: () => [],
    },
});

const spoFilter = ref([]);
const mode = ref("create");
const selectedSpo = ref("");

watch(
    () => mode.value,
    (val) => {
        if (val === "create") {
            selectedSpo.value = "";
        }
    },
);

watch(
    () => props.show,
    (val) => {
        if (val) {
            mode.value = "create";
            selectedSpo.value = "";
        }
    },
);

watch(
    () => props.supplierCode,
    (val) => {
        if (val) {
            spoFilter.value = props.spoOptions.filter(
                (item) => item.supplier_code == val,
            );
        }
    },
);

const handleSubmit = () => {
    if (mode.value === "append" && !selectedSpo.value) {
        window.notyf.error("Please choose a SPO number.");
        return;
    }

    router.post(
        route("purchase.recently.store"),
        {
            purchases: [
                {
                    mode: mode.value,
                    rows: props.selectedPurchase,
                    spo_id: selectedSpo.value || null,
                    supplier_code: props.selectedPurchase[0].supplier_code,
                },
            ],
        },
        {
            onSuccess: (page) => {
                if (page.props.flash) {
                    const response = page.props.flash.message;
                    window.notyf[response.type](response.messages);
                }
                emit("close");
            },
            onError: (error) => {
                const response = error.error;
                window.notyf[response.type](response.messages);
                emit("close");
            },
        },
    );
};
</script>
