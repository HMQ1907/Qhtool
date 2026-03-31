<template>
    <div class="p-4">
        <div v-for="(item, index) in tables" v-if="tables.length > 0" :key="item.supplier_name + index"
            class="rounded-lg shadow-lg mb-6 border-b border-l border-r border-gray-200">
            <div class="p-4 border-gray-200">
                <h2 class="text-2xl font-bold text-black">
                    To: {{ item.supplier_name }}
                </h2>
            </div>

            <div class="px-4">
                <div class="border border-gray-300 overflow-hidden rounded-sm">
                    <table class="bg-white w-full text-left text-gray-500 rtl:text-right">
                        <thead class="bg-gray-200 text-gray-700 sticky top-0 z-[1]">
                            <tr>
                                <th
                                    class="px-4 py-2 whitespace-nowrap w-[100px] hover:bg-primary-color/20 transition duration-300 ease-in-out">
                                    <input ref="headerCheckboxes" type="checkbox" @change="toggleAllCheckboxes(index)"
                                        :checked="areAllChecked(index)"
                                        class="size-5 text-primary-color cursor-pointer rounded focus:ring-primary-color"
                                        style="accent-color: #e09859" />
                                </th>
                                <th v-for="header in headers" :key="'header_' + header.title" :class="header.class">
                                    {{ header.title }}
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <table-row v-for="row in item.rows" :key="row.identify_code" :row="row"
                                @checkbox-changed="updateIndeterminateState" />
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="p-4 border-gray-200">
                <button @click="confirm(index)"
                    class="bg-[#f58220] hover:bg-[#f58220]/80 text-white px-4 py-2 rounded text-sm cursor-pointer">
                    Submit
                </button>
            </div>
        </div>

        <div v-else>
            <div class="rounded-lg shadow-md border-gray-200">
                <div class="p-4">
                    <div class="border border-gray-300 overflow-hidden">
                        <table class="bg-white w-full text-left text-gray-500 rtl:text-right">
                            <thead class="bg-gray-200 text-gray-700 sticky top-0 z-[1]">
                                <tr>
                                    <th
                                        class="px-4 py-2 whitespace-nowrap w-[100px] hover:bg-primary-color/20 transition duration-300 ease-in-out">
                                        <input type="checkbox"
                                            class="size-5 text-primary-color cursor-pointer rounded focus:ring-primary-color"
                                            style="accent-color: #e09859" disabled />
                                    </th>
                                    <th v-for="header in headers" :key="'header_' + header.title"
                                        class="px-4 py-2 whitespace-nowrap min-w-[100px] text-left">
                                        {{ header.title }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="10" class="p-4 text-center border-t border-gray-300">
                                        No data available
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, watch, nextTick } from "vue";
import { useForm } from "@inertiajs/vue3";
import TableRow from "./Table/Row.vue";

const props = defineProps({
    list: {
        type: Array,
        required: true,
    },
});

const headers = ref([
    { title: "ID", class: "p-2 w-1/11 text-left" },
    { title: "ORDER NO.", class: "p-2 w-1/8 text-left" },
    { title: "SKU", class: "p-2  w-1/13 text-left" },
    {
        title: "PART NO.",
        class: "p-2  w-1/10 text-left",
    },
    { title: "DESCRIPTION", class: "p-2 w-1/6 text-left" },
    {
        title: "VARIANT",
        class: "p-2  w-1/6 text-left",
    },

    { title: "QTY", class: "p-2 w-1/12 text-left" },
    {
        title: "UNIT PRICE",
        class: "p-2  w-1/10 text-left",
    },
    {
        title: "DELIVERY DAYS",
        class: "p-2  w-1/10 text-left",
    },
]);

const tables = ref([]);

const headerCheckboxes = ref([]);

const initTables = () => {
    tables.value = props.list.map((item) => ({
        ...item,
        rows: item.rows.map((row) => ({
            ...row,
            checked: false,
        })),
    }));
};

initTables();

watch(
    () => props.list,
    () => {
        initTables();
        updateIndeterminateState();
    },
    { deep: true },
);

const form = useForm({
    quotations: [],
});

const areAllChecked = (tableIndex) => {
    return tables.value[tableIndex].rows.every((row) => row.checked);
};

const areSomeChecked = (tableIndex) => {
    const checkedCount = tables.value[tableIndex].rows.filter(
        (row) => row.checked,
    ).length;
    return (
        checkedCount > 0 && checkedCount < tables.value[tableIndex].rows.length
    );
};

const updateIndeterminateState = () => {
    nextTick(() => {
        if (headerCheckboxes.value) {
            headerCheckboxes.value.forEach((checkbox, index) => {
                if (checkbox) {
                    checkbox.indeterminate = areSomeChecked(index);
                }
            });
        }
    });
};

const toggleAllCheckboxes = (tableIndex) => {
    const allChecked = !areAllChecked(tableIndex);
    tables.value[tableIndex].rows.forEach((row) => {
        row.checked = allChecked;
    });
    updateIndeterminateState();
};

const getSelectedRows = (tableIndex) => {
    return tables.value[tableIndex].rows.filter((row) => row.checked);
};

const showError = (message) => {
    Swal.fire({
        title: "Warning!",
        html: message,
        icon: "error",
        confirmButtonText: "OK",
    });
};

const confirm = (tableIndex) => {
    const selectedRows = getSelectedRows(tableIndex);

    if (selectedRows.length === 0) {
        showError("Please check at least one item");
        return;
    }

    if (selectedRows.some((row) => !row.delivery_days)) {
        showError("Delivery date is required");
        return;
    }

    const totalCount = tables.value[tableIndex].rows.length;
    const checkedCount = selectedRows.length;

    Swal.fire({
        title: "Are you sure?",
        html: `Total: ${totalCount}<br>Checked: ${checkedCount}<br>Do you want to continue?`,
        icon: "question",
        showCancelButton: true,
        cancelButtonText: "No, cancel!",
        confirmButtonText: "Yes, please.",
    }).then((result) => {
        if (result.isConfirmed) {
            submitForm(tableIndex, selectedRows);
        }
    });
};

const submitForm = (tableIndex, selectedRows) => {
    form.quotations = [
        {
            supplier_name: tables.value[tableIndex].supplier_name,
            rows: selectedRows.map((row) => ({
                id: row.id,
                identify_code: row.identify_code,
                scm_code: row.scm_code,
                quantity: row.quantity,
                unit_price: row.unit_price,
                delivery_days: row.delivery_days,
            })),
        },
    ];

    form.post(route("quotation.recently.store"), {
        onSuccess: (page) => {
            if (page.props.flash) {
                window.notyf.success(page.props.flash.message.messages);
            }
            form.reset();
        },
        onError: (error) => {
            window.notyf.error("A server error occurred. Please contact the dev team to support you.");
        },
    });
};
</script>
