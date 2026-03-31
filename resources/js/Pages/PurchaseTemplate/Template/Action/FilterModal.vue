<template>
    <Modal :show="showFilterModal" :size="'2xl'" @close="$emit('close')">
        <template v-slot:header>
            <h3 class="text-2xl font-semibold text-gray-900 dark:text-white">
                Filter
            </h3>
        </template>

        <template v-slot:body>
            <div class="p-5 max-w-2xl mx-auto bg-white rounded-lg border-b border-gray-300 mb-4">
                <form class="grid grid-cols-12 gap-1 items-center">
                    <label class="col-span-4 text-gray-700 mb-4" for="supplierSelect">
                        Template Name
                    </label>
                    <div class="col-span-8 mb-3">
                        <input placeholder="Template Name 1, Template Name 2, ..." type="text"
                            class="form-input block w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-0 focus:border-primary-color"
                            v-model="form.filter.name" />
                    </div>
                </form>
            </div>
        </template>

        <template v-slot:footer>
            <div class="col-span-12 flex flex-col items-end gap-2 px-6 pb-4">
                <div class="flex gap-2">
                    <button @click="reset()"
                        class="flex cursor-pointer items-center justify-center border border-gray-300 text-gray-700 py-2 px-4 rounded-lg hover:bg-gray-600 hover:text-white transition duration-500 ease-in-out">
                        <i class="fas fa-undo mr-1"></i>
                        Reset
                    </button>

                    <button @click="filter()"
                        class="flex cursor-pointer items-center justify-center border bg-white border-blue-600 text-blue-600 py-2 px-4 rounded-lg hover:bg-blue-600 hover:text-white transition duration-500 ease-in-out">
                        <i class="fas fa-filter mr-1"></i>
                        Filter
                    </button>
                </div>

                <button type="button" @click="$emit('close')"
                    class="flex cursor-pointer items-center justify-center bg-gray-500 text-white py-2 px-4 rounded-lg hover:bg-gray-600 transition duration-500 ease-in-out">
                    <i class="fas fa-times mr-1"></i>
                    Cancel
                </button>
            </div>
        </template>
    </Modal>
</template>

<script setup>
import { onMounted } from "vue";
import { useForm } from "@inertiajs/vue3";
import Modal from "@/Components/Modal.vue";
defineProps(["showFilterModal", "customers"]);
const emit = defineEmits(["close"]);

const form = useForm({
    filter: {
        name: "",
    },
});

onMounted(() => {
    const routeParams = route().params;

    if (routeParams.filter) {
        Object.assign(form.filter, routeParams.filter);
    }
});

const closeModal = () => {
    emit("close");
};

const reset = () => {
    form.reset();
};

const filter = () => {
    form.get(route("purchase-template.index"), {
        onStart: () => {
            closeModal();
        },
    });
};
</script>
