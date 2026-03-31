<template>
    <Modal :show="showFilterModal" :size="'2xl'" @close="$emit('close')">
        <template v-slot:header>
            <h3 class="text-2xl font-semibold text-gray-900 dark:text-white">
                Filter
            </h3>
        </template>

        <template v-slot:body>
            <div
                class="p-5 max-w-2xl mx-auto bg-white rounded-lg border-b border-gray-300 mb-4"
            >
                <form class="grid grid-cols-12 gap-1 items-center">
                    <label
                        class="col-span-4 text-gray-700 mb-4"
                        for="supplierSelect"
                    >
                        Supplier Name
                    </label>
                    <div class="col-span-8 mb-3">
                        <MultipleSelect
                            class="z-10"
                            id="supplier-id-select"
                            :options="usePage().props.suppliersName"
                            :placeHolder="'Choose Supplier ...'"
                            v-model="form.filter.supplier_code"
                        />
                    </div>
                    <label
                        class="col-span-4 text-gray-700 mb-4"
                        for="supplierSelect"
                    >
                        SPO Code
                    </label>
                    <div class="col-span-8 mb-3">
                        <input
                            placeholder="SPO Code 1, SPO Code 2, ..."
                            type="text"
                            class="form-input block w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-0 focus:border-primary-color"
                            v-model="form.filter.code"
                        />
                    </div>
                    <label
                        class="col-span-4 text-gray-700 mb-4"
                        for="supplierSelect"
                    >
                        Status
                    </label>
                    <div class="col-span-8 mb-3">
                        <SingleSelect
                            v-model="form.filter.status"
                            :options="spoStatus"
                            :optionKey="'value'"
                            :optionLabel="'label'"
                            :placeholder="'Choose Status ...'"
                        />
                    </div>

                    <label
                        class="col-span-4 text-gray-700 mb-4"
                        for="supplierSelect"
                    >
                        Purchase Date
                    </label>
                    <div class="col-span-8 mb-3">
                        <div class="flex gap-2">
                            <Datepicker
                                :input-class="'focus:border-primary-color focus:ring-0 outline-none'"
                                :enable-time-picker="false"
                                class="z-[9]"
                                placeholder="Begin Date"
                                :format="'yyyy-MM-dd'"
                                v-model="form.filter.begin_date"
                                auto-apply
                                :teleport="true"
                                :teleport-center="true"
                            />
                            <Datepicker
                                :input-class="'focus:border-primary-color focus:ring-0 outline-none'"
                                :enable-time-picker="false"
                                teleport="#app"
                                class="z-[9]"
                                :format="'yyyy-MM-dd'"
                                placeholder="End Date"
                                v-model="form.filter.end_date"
                                auto-apply
                                :teleport="true"
                                :teleport-center="true"
                            />
                        </div>
                    </div>
                </form>
            </div>
        </template>

        <template v-slot:footer>
            <div class="col-span-12 flex flex-col items-end gap-2 px-6 pb-4">
                <div class="flex gap-2">
                    <button
                        @click="reset()"
                        class="flex cursor-pointer items-center justify-center border border-gray-300 text-gray-700 py-2 px-4 rounded-lg hover:bg-gray-600 hover:text-white transition duration-500 ease-in-out"
                    >
                        <i class="fas fa-undo mr-1"></i>
                        Reset
                    </button>

                    <button
                        @click="filter()"
                        class="flex cursor-pointer items-center justify-center border bg-white border-blue-600 text-blue-600 py-2 px-4 rounded-lg hover:bg-blue-600 hover:text-white transition duration-500 ease-in-out"
                    >
                        <i class="fas fa-filter mr-1"></i>
                        Filter
                    </button>
                </div>

                <button
                    type="button"
                    @click="$emit('close')"
                    class="flex cursor-pointer items-center justify-center bg-gray-500 text-white py-2 px-4 rounded-lg hover:bg-gray-600 transition duration-500 ease-in-out"
                >
                    <i class="fas fa-times mr-1"></i>
                    Cancel
                </button>
            </div>
        </template>
    </Modal>
</template>

<script setup>
import { onMounted, ref } from "vue";
import { useForm, usePage } from "@inertiajs/vue3";
import Modal from "@/Components/Modal.vue";
import MultipleSelect from "@/Components/MultipleSelect.vue";
import SingleSelect from "@/Components/SingleSelect.vue";
import Datepicker from "@vuepic/vue-datepicker";
import "@vuepic/vue-datepicker/dist/main.css";

const emit = defineEmits(["close"]);
defineProps(["showFilterModal"]);

const form = useForm({
    filter: {
        supplier_code: [],
        status: "",
        code: "",
        begin_date: "",
        end_date: "",
    },
});

const spoStatus = ref([
    {
        label: "Pending",
        value: "0",
    },
    {
        label: "Approved",
        value: "1",
    },
]);

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
    form.get(route("purchase.spo.index"), {
        onStart: () => {
            closeModal();
        },
    });
};
</script>
