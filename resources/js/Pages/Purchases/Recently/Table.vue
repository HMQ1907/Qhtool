<template>
    <div class="p-4">
        <div
            v-for="(item, index) in tables"
            v-if="tables.length > 0"
            :key="item.supplier_name + index"
            class="rounded-lg shadow-lg mb-6 border-b border-l border-r border-gray-200"
        >
            <div class="p-4 border-gray-200">
                <h2 class="text-2xl font-bold text-black">
                    To: {{ item.supplier_name }}
                </h2>
            </div>

            <div class="px-4">
                <div class="border border-gray-300 overflow-hidden rounded-sm">
                    <table
                        class="bg-white w-full text-left text-gray-500 rtl:text-right"
                    >
                        <thead
                            class="bg-gray-200 text-gray-700 sticky top-0 z-[1]"
                        >
                            <tr>
                                <th
                                    class="px-4 py-2 w-[100px] hover:bg-primary-color/20 transition duration-300 ease-in-out"
                                >
                                    <input
                                        ref="headerCheckboxes"
                                        type="checkbox"
                                        @change="toggleAllCheckboxes(index)"
                                        :checked="areAllChecked(index)"
                                        style="accent-color: #e09859"
                                        class="size-5 text-primary-color cursor-pointer rounded focus:ring-primary-color"
                                    />
                                </th>
                                <th
                                    v-for="header in headers"
                                    :key="'header_' + header.title"
                                    :class="header.class"
                                >
                                    {{ header.title }}
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <table-row
                                v-for="(row, rowIndex) in item.rows"
                                :key="row.identify_code"
                                :supplier_name="item.supplier_name"
                                :row="row"
                                @checkbox-changed="updateIndeterminateState"
                            />

                            <tr v-if="!item.rows || item.rows.length === 0">
                                <td colspan="10" class="p-2 text-center">
                                    No data available
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="p-4 flex items-start">
                <button
                    @click="openAddPurchase(index)"
                    class="bg-[#f58220] hover:bg-[#f58220]/80 text-white px-4 py-2 rounded text-sm cursor-pointer h-[38px]"
                >
                    Add Purchase
                </button>
            </div>
        </div>
        <div v-else>
            <div class="rounded-lg shadow-md border-gray-200">
                <div class="p-4">
                    <div class="border border-gray-300 overflow-hidden">
                        <table
                            class="bg-white w-full text-left text-gray-500 rtl:text-right"
                        >
                            <thead
                                class="bg-gray-200 text-gray-700 sticky top-0 z-[1]"
                            >
                                <tr>
                                    <th
                                        class="px-4 py-2 whitespace-nowrap w-[100px] hover:bg-primary-color/20 transition duration-300 ease-in-out"
                                    >
                                        <input
                                            type="checkbox"
                                            class="size-5 text-primary-color cursor-pointer rounded focus:ring-primary-color"
                                            style="accent-color: #e09859"
                                            disabled
                                        />
                                    </th>
                                    <th
                                        v-for="header in headers"
                                        :key="'header_' + header.title"
                                        class="px-4 py-2 whitespace-nowrap min-w-[100px] text-left"
                                    >
                                        {{ header.title }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td
                                        colspan="11"
                                        class="p-4 text-center border-t border-gray-300"
                                    >
                                        No data available
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <AddPurchaseModal
            :selectedPurchase="selectedPurchase"
            :show="showAddModal"
            :supplierCode="supplierCode"
            :spoOptions="props.spoOptions"
            @close="showAddModal = false"
        />
    </div>
</template>

<script setup>
import { ref, watch, nextTick } from "vue";
import TableRow from "./Table/Row.vue";
import AddPurchaseModal from "./Action/AddPurchaseModal.vue";

const props = defineProps({
    list: {
        type: Array,
        required: true,
    },
    spoOptions: {
        type: Array,
        required: true,
    },
});

const tables = ref([]);
const selectedPurchase = ref([]);
const supplierCode = ref("");
const headerCheckboxes = ref([]);

const initTables = () => {
    tables.value = props.list.map((item) => ({
        ...item,
        purchase_code: "",
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

const showAddModal = ref(false);
const currentTableIndex = ref(null);

const openAddPurchase = (tableIndex) => {
    const selectedRows = getSelectedRows(tableIndex);
    if (selectedRows.length === 0) {
        showError("Please check at least one item");
        return;
    }

    if (selectedRows.some((row) => !row.delivery_days)) {
        showError("Delivery date is required");
        return;
    }
    selectedPurchase.value = selectedRows;
    currentTableIndex.value = tableIndex;
    supplierCode.value = selectedRows[0].supplier_code;
    showAddModal.value = true;
};

const isEligible = (row) => !row.supplier_purchase_order?.id;

const areAllChecked = (tableIndex) => {
    const eligibleRows = tables.value[tableIndex].rows.filter(isEligible);
    if (eligibleRows.length === 0) return false;
    return eligibleRows.every((row) => row.checked);
};

const areSomeChecked = (tableIndex) => {
    const eligibleRows = tables.value[tableIndex].rows.filter(isEligible);
    const checkedCount = eligibleRows.filter((row) => row.checked).length;
    return checkedCount > 0 && checkedCount < eligibleRows.length;
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
        if (isEligible(row)) {
            row.checked = allChecked;
        }
    });
    updateIndeterminateState();
};

const getSelectedRows = (tableIndex) => {
    return tables.value[tableIndex].rows.filter(
        (row) => row.checked && isEligible(row),
    );
};

const showError = (message) => {
    Swal.fire({
        title: "Warning!",
        html: message,
        icon: "error",
        confirmButtonText: "OK",
    });
};

const headers = ref([
    { title: "ID", class: "p-2  w-1/11 text-left" },
    { title: "SKU", class: "p-2  w-1/15 text-left" },
    { title: "PART NO.", class: "p-2  w-1/12 text-left" },
    { title: "DESCRIPTION", class: "p-2  w-1/7 text-left" },
    { title: "VARIANT", class: "p-2  w-1/10 text-left" },
    { title: "QTY", class: "p-2  w-1/16 text-left" },
    { title: "SUPPLIER", class: "p-2  w-1/12 text-left" },
    { title: "SCM CODE", class: "p-2  w-1/12 text-left" },
    { title: "ORDER NO.", class: "p-2  w-1/8 text-left" },
    { title: "UNIT PRICE", class: "p-2  w-1/16 text-left" },
    { title: "DELIVERY DAYS", class: "p-2  w-1/10 text-left" },
    { title: "SPO", class: "p-2  min-w-[150px] text-left" },
]);
</script>
