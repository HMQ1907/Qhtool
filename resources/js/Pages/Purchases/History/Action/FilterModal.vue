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
                        SKU
                    </label>
                    <div class="col-span-8 mb-3">
                        <input
                            placeholder="SKU 1, SKU 2, ..."
                            type="text"
                            class="form-input block w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-0 focus:border-primary-color"
                            v-model="form.filter.sku"
                        />
                    </div>
                    <label
                        class="col-span-4 text-gray-700 mb-4"
                        for="supplierSelect"
                    >
                        Identify Code
                    </label>
                    <div class="col-span-8 mb-3">
                        <input
                            placeholder="Identify Code 1, Identify Code 2, ..."
                            type="text"
                            class="form-input block w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-0 focus:border-primary-color"
                            v-model="form.filter.identify_code"
                        />
                    </div>

                    <label
                        class="col-span-4 text-gray-700 mb-4"
                        for="supplierSelect"
                    >
                        SCM Code
                    </label>
                    <div class="col-span-8 mb-3">
                        <input
                            placeholder="SCM Code 1, SCM Code 2, ..."
                            type="text"
                            class="form-input block w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-0 focus:border-primary-color"
                            v-model="form.filter.scm_code"
                        />
                    </div>
                    <label
                        class="col-span-4 text-gray-700 mb-4"
                        for="supplierSelect"
                    >
                        Purchase Code
                    </label>
                    <div class="col-span-8 mb-3">
                        <input
                            placeholder="Purchase Code 1, Purchase Code 2, ..."
                            type="text"
                            class="form-input block w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-0 focus:border-primary-color"
                            v-model="form.filter.purchase_code"
                        />
                    </div>

                    <label
                        class="col-span-4 text-gray-700 mb-4"
                        for="supplierSelect"
                    >
                        Date
                    </label>
                    <div class="col-span-8 mb-3">
                        <div class="flex gap-2">
                            <Datepicker
                                :input-class="'focus:border-primary-color focus:ring-0 outline-none'"
                                :enable-time-picker="false"
                                :class="'z-[9999] '"
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
                                :class="'z-[9999]'"
                                :format="'yyyy-MM-dd'"
                                placeholder="End Date"
                                v-model="form.filter.end_date"
                                auto-apply
                                :teleport="true"
                                :teleport-center="true"
                            />
                        </div>
                    </div>
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
                            :options="props.suppliersName"
                            :placeHolder="'Choose Supplier ...'"
                            v-model="form.filter.supplier_code"
                        />
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
import { onMounted } from "vue";
import { useForm, usePage } from "@inertiajs/vue3";
import Modal from "@/Components/Modal.vue";
import Datepicker from "@vuepic/vue-datepicker";
import "@vuepic/vue-datepicker/dist/main.css";
import MultipleSelect from "@/Components/MultipleSelect.vue";

defineProps(["showFilterModal"]);
const { props } = usePage();
const emit = defineEmits(["close", "update-purchases"]);

const form = useForm({
    filter: {
        sku: null,
        supplier: [],
        identify_code: null,
        scm_code: null,
        purchase_code: null,
        begin_date: null,
        end_date: null,
    },
    per_page: route().params.per_page || 50,
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
    const normalizeCsv = (value) => {
        if (Array.isArray(value)) return value;
        if (value == null) return null;
        return String(value)
            .split(",")
            .map((s) => s.trim())
            .filter((s) => s.length > 0);
    };

    const formattedForm = {
        ...form,
        filter: {
            ...form.filter,
            identify_code: form.filter.identify_code
                ? normalizeCsv(form.filter.identify_code)
                : null,
            begin_date: form.filter.begin_date
                ? formatDate(form.filter.begin_date)
                : null,
            end_date: form.filter.end_date
                ? formatDate(form.filter.end_date)
                : null,
        },
    };

    formattedForm.get(route("purchase.history.index"), {
        onStart: () => {
            closeModal();
        },
        onSuccess: (data) => {
            emit("update-purchases", data.purchases);
        },
    });
};

const formatDate = (date) => {
    if (!date) return null;

    const dateObj = typeof date === "string" ? new Date(date) : date;

    const year = dateObj.getFullYear();
    const month = String(dateObj.getMonth() + 1);
    const day = String(dateObj.getDate());

    return `${year}-${month}-${day}`;
};
</script>

<style>
.dp__input:focus {
    border-color: var(--color-primary-color) !important;
    outline: none !important;
    box-shadow: none !important;
}
</style>
