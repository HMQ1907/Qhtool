<template>
    <div>
        <table class="w-full text-left text-gray-500 rtl:text-right">
            <thead class="bg-gray-200 text-gray-700 sticky top-0 z-[1]">
                <tr>
                    <th
                        class="px-2 py-2 whitespace-nowrap w-14 hover:bg-primary-color/20 transition duration-300 ease-in-out">
                        <div class="flex gap-2 items-center pl-2">
                            <input type="checkbox"
                                class="size-5 text-primary-color cursor-pointer rounded focus:ring-primary-color"
                                ref="masterCheckbox" :checked="isAllChecked" @change="toggleAll" />
                            <i class="fa-solid fa-caret-down cursor-pointer" data-bs-toggle="dropdown"
                                aria-expanded="false"></i>
                            <ul
                                class="dropdown-menu hidden z-10 bg-white border border-gray-300 rounded-md shadow-lg py-2">
                                <li
                                    class="flex gap-3 items-center hover:bg-primary-color/20 px-2 py-1 cursor-pointer transition-colors duration-300">
                                    <i class="fa-solid fa-trash"></i>
                                    <span class="dropdown-item font-normal"
                                        @click="deleteSPOs(selectedSPOs)">Delete</span>
                                </li>
                            </ul>
                        </div>
                    </th>
                    <th v-for="header in headers" :key="'header_' + header"
                        class="px-4 py-2 whitespace-nowrap min-w-[100px] hover:bg-primary-color/20 transition duration-300 ease-in-out"
                        :class="header.class">
                        {{ header.title }}
                    </th>
                </tr>
            </thead>
            <tbody>
                <template v-if="list.length > 0">
                    <tr v-for="(item, index) in list" :key="'item_' + index"
                        class="border-b border-gray-200 hover:bg-gray-100 transition duration-300 ease-in-out">
                        <td class="px-4 py-2 w-12">
                            <input v-if="item.status == 0" type="checkbox"
                                class="size-5 text-primary-color cursor-pointer rounded focus:ring-primary-color"
                                v-model="selectedSPOs" :value="item" />
                        </td>
                        <td class="px-4 py-2">
                            <span class="px-2 py-1 rounded-md text-sm font-medium" :class="{
                                'bg-blue-100 text-blue-800':
                                    item.status == '1',

                                'bg-red-100 text-red-800':
                                    item.status == '0',
                            }">
                                {{ getStatus(item.status) }}
                            </span>
                        </td>
                        <td class="px-4 py-2">
                            {{ item.purchase_date }}
                        </td>
                        <td class="px-4 py-2 text-blue-500">
                            <Link :href="$route('purchase.spo.detail', item.id)">
                            {{ item.code }}
                            </Link>
                        </td>
                        <td class="px-4 py-2">
                            {{ item.supplier?.data?.name }}
                        </td>
                        <td class="px-4 py-2">
                            {{ getTotalQuantity(item) }}
                        </td>
                        <td class="px-4 py-2">
                            {{ getTotalAmount(item) }}
                        </td>
                        <td class="px-4 py-2">
                            <button @click="
                                handleDownload(item.id, 'pdf', item.code, item.supplier?.data?.name)
                                "
                                class="bg-[#df47d2] hover:bg-[#df47d2]/80 text-white px-4 py-2 rounded text-sm cursor-pointer mr-2">
                                PDF
                            </button>
                            <button @click="
                                handleDownload(item.id, 'xlsx', item.code, item.supplier?.data?.name)
                                "
                                class="bg-[#b5ca56] hover:bg-[#b5ca56]/80 text-white px-4 py-2 rounded text-sm cursor-pointer mr-2">
                                XLSX
                            </button>
                            <button @click="
                                handleDownload(item.id, 'tsv', item.code, item.supplier?.data?.name)
                                "
                                class="bg-[#44a5dd] hover:bg-[#44a5dd]/80 text-white px-4 py-2 rounded text-sm cursor-pointer">
                                TSV
                            </button>
                        </td>
                    </tr>
                </template>
                <template v-else>
                    <tr>
                        <td colspan="8" class="px-4 py-2 text-center border-b border-gray-300">
                            No data available
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>
        <Loading :isLoading="isLoading" />
    </div>
</template>

<script setup>
import { ref, computed, watch, onMounted } from "vue";
import { router } from "@inertiajs/vue3";
import { Link } from "@inertiajs/vue3";
import Loading from "@/Components/Loading.vue";
const props = defineProps(["list", "language"]);

const selectedSPOs = ref([]);
const masterCheckbox = ref(null);
const isLoading = ref(false);

const headers = ref([
    {
        title: "Status",
        class: "px-4 py-2 whitespace-nowrap w-1/8 text-left",
    },
    {
        title: "Purchase Date",
        class: "px-4 py-2 whitespace-nowrap w-1/7 text-left",
    },
    {
        title: "SPO Code",
        class: "px-4 py-2 whitespace-nowrap w-1/6 text-left",
    },
    {
        title: "Supplier",
        class: "px-4 py-2 whitespace-nowrap w-1/7 text-left",
    },
    {
        title: "Total Quantity",
        class: "px-4 py-2 whitespace-nowrap w-1/8 text-left",
    },
    {
        title: "Total Amount",
        class: "px-4 py-2 whitespace-nowrap w-1/8 text-left",
    },
    {
        title: "Download",
        class: "px-4 py-2 whitespace-nowrap w-1/3 text-left",
    },
]);

const isAllChecked = computed(
    () =>
        selectedSPOs.value.length > 0 &&
        selectedSPOs.value.length ===
        props.list.filter((item) => item.status == 0).length &&
        props.list.filter((item) => item.status == 0).length > 0,
);
const isSomeChecked = computed(
    () =>
        selectedSPOs.value.length > 0 &&
        !isAllChecked.value &&
        props.list.filter((item) => item.status == 0).length > 0,
);

watch([isAllChecked, isSomeChecked], () => {
    if (masterCheckbox.value) {
        masterCheckbox.value.indeterminate = isSomeChecked.value;
    }
});

onMounted(() => {
    if (masterCheckbox.value) {
        masterCheckbox.value.indeterminate = isSomeChecked.value;
    }
});

const toggleAll = (e) => {
    if (e.target.checked) {
        selectedSPOs.value = [...props.list.filter((item) => item.status == 0)];
    } else {
        selectedSPOs.value = [];
    }
};

const deleteSPOs = (spoItems) => {
    if (spoItems.length > 0) {
        Swal.fire({
            title: "Are you sure?",
            html: `Do you want to delete these SPOs?`,
            icon: "warning",
            showCancelButton: true,
            cancelButtonText: "No, cancel!",
            confirmButtonText: "Yes, please.",
        }).then((result) => {
            if (result.isConfirmed) {
                router.visit(route("purchase.spo.destroy"), {
                    method: "delete",
                    data: {
                        spoIds: spoItems.map((item) => item.id),
                    },
                    onSuccess: () => {
                        Swal.fire({
                            icon: "success",
                            title: "Success!",
                            text: "SPOs deleted successfully.",
                        });
                    },
                });
            }
        });
    } else {
        Swal.fire({
            icon: "error",
            title: "Warning!",
            text: "Please select at least one SPO to delete.",
        });
    }
};

const getStatus = (item) => {
    return item == 0 ? "Pending" : "Approved";
};

const getTotalQuantity = (item) => {
    return item.purchases.reduce((acc, curr) => acc + curr.quantity, 0);
};

const getTotalAmount = (item) => {
    let total = item.purchases.reduce(
        (acc, curr) => acc + curr.quantity * curr.unit_price,
        0,
    );
    return new Intl.NumberFormat().format(total);
};

const handleDownload = async (id, format, code, supplierName) => {
    try {
        isLoading.value = true;
        const response = await fetch(
            route("purchase.spo.download", { id, format, language: props.language }),
        );
        const blob = await response.blob();
        const url = window.URL.createObjectURL(blob);
        const link = document.createElement("a");
        link.href = url;
        link.download = `${code}_${supplierName}.${format}`;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        window.URL.revokeObjectURL(url);
        window.notyf.success("Downloaded successfully.");
    } catch (error) {
        window.notyf.error("Download failed.");
    } finally {
        isLoading.value = false;
    }
};
</script>
