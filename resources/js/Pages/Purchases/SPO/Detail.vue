<template>
    <div class="py-6 flex flex-col gap-3">
        <div class="flex items-center justify-between">
            <Breadcrumb name="Purchase Detail" />
            <div class="flex items-center gap-5 mr-3">
                <SwitchButton v-model="language" />
            </div>
        </div>
        <div class="bg-white border border-gray-200 p-6 m-3">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">
                Information
            </h2>
            <div class="grid grid-cols-2 gap-8">
                <div class="space-y-4">
                    <div class="flex items-center gap-2">
                        <label class="text-gray-700">SPO Code:</label>
                        <p class="text-gray-900 font-semibold">
                            {{ SPODetail?.code || "-" }}
                        </p>
                    </div>
                    <div class="flex items-center gap-2">
                        <label class="text-gray-700">Purchase Date:</label>
                        <p class="text-gray-900 font-semibold">
                            {{ SPODetail?.purchase_date || "-" }}
                        </p>
                    </div>
                    <div class="flex items-center gap-2">
                        <label class="text-gray-700">Total Quantity:</label>
                        <p class="text-gray-900 font-semibold">
                            {{ getTotalQuantity(SPODetail) }}
                        </p>
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="flex items-center gap-2">
                        <label class="text-gray-700">Status:</label>
                        <span class="inline-flex px-2 py-1 text-xs rounded-md font-semibold" :class="{
                            'bg-red-100 text-red-800':
                                SPODetail?.status == 0,
                            'bg-green-100 text-green-800':
                                SPODetail?.status == 1,
                        }">
                            {{ getStatus(SPODetail?.status) }}
                        </span>
                    </div>
                    <div class="flex items-center gap-2">
                        <label class="text-gray-700">Supplier:</label>
                        <p class="text-gray-900 font-semibold">
                            {{ SPODetail?.supplier?.data?.name || "-" }}
                        </p>
                    </div>
                    <div class="flex items-center gap-2">
                        <label class="text-gray-700">Total Amount:</label>
                        <p class="text-gray-900 font-semibold">
                            {{ getTotalAmount(SPODetail) }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white border border-gray-200 p-4 m-3">
            <div class="border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">List Items</h2>
            </div>
            <div class="overflow-x-auto rounded-lg">
                <table
                    class="table-auto w-full text-left text-gray-500 rtl:text-right border border-gray-200 rounded-lg">
                    <thead class="bg-gray-200 text-gray-700 sticky top-0 z-[1]">
                        <tr>
                            <th v-for="header in headers" :key="'header_' + header.title" :class="header.class">
                                {{ header.title }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <template v-if="SPODetail?.purchases?.length > 0">
                            <tr v-for="(item, index) in SPODetail.purchases" :key="'item_' + index"
                                class="hover:bg-gray-50">
                                <td class="px-4 py-4 text-sm text-gray-900">
                                    {{ item.identify_code ?? "" }}
                                </td>
                                <td class="px-4 py-4">
                                    <img :src="getImageUrl(
                                        item.product?.data
                                            ?.product_image,
                                    ) || '-'
                                        " class="size-17 object-contain rounded border border-gray-300 p-1" />
                                </td>
                                <td class="px-4 py-4 text-sm text-gray-900">
                                    {{ item.sku ?? "" }}
                                </td>
                                <td class="px-4 py-4 text-sm text-gray-900">
                                    {{ item.product?.data?.model_number ?? "" }}
                                </td>
                                <td class="px-4 py-4 text-sm text-gray-900">
                                    {{ item.product?.data?.name ?? "" }}
                                </td>
                                <td class="px-4 py-4 text-sm text-gray-900">
                                    {{ getProductVariant(item) }}
                                </td>
                                <td class="px-4 py-4 text-sm text-gray-900">
                                    {{ item.quantity || 0 }}
                                </td>
                                <td class="px-4 py-4 text-sm text-gray-900">
                                    {{ SPODetail?.supplier?.data?.name ?? "" }}
                                </td>
                                <td class="px-4 py-4 text-sm text-gray-900">
                                    {{ item.orders?.order_scm_code ?? "" }}
                                </td>
                                <td class="px-4 py-4 text-sm text-gray-900">
                                    {{ item.scm_code ?? "" }}
                                </td>
                                <td class="px-4 py-4 text-sm text-gray-900">
                                    {{ formatPrice(item.unit_price) }}
                                </td>
                                <td class="px-4 py-4 text-sm text-gray-900">
                                    {{ item.delivery_days ?? "" }}
                                </td>
                                <td v-if="SPODetail.status == 0" class="px-4 py-4 text-sm">
                                    <button @click="deleteItem(item.id)"
                                        class="px-4 py-2 cursor-pointer text-white bg-red-600 border border-transparent rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                        Delete
                                    </button>
                                </td>
                            </tr>
                        </template>
                        <template v-else>
                            <tr>
                                <td colspan="13" class="px-4 py-8 text-center text-gray-500">
                                    No items available
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="flex items-center justify-between p-3">
            <button @click="goBack"
                class="px-4 py-2 cursor-pointer text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Back
            </button>

            <div class="flex items-center gap-3">
                <button @click="previewSPO"
                    class="px-4 py-2 cursor-pointer text-white bg-gray-500 rounded-md hover:bg-gray-600 transition-all duration-300">
                    Preview
                </button>
                <button v-if="SPODetail.status == 0" @click="deleteSPO"
                    class="px-4 py-2 cursor-pointer text-white bg-red-500 rounded-md hover:bg-red-600 transition-all duration-300">
                    Delete
                </button>

                <div class="relative">
                    <button @click="toggleDownloadDropdown"
                        class="px-4 py-2 cursor-pointer text-white bg-blue-500 rounded-md hover:bg-blue-600 flex items-center gap-2 transition-all duration-300">
                        File Download
                        <i class="fa-solid fa-caret-down"></i>
                    </button>

                    <div v-show="showDownloadDropdown"
                        class="absolute right-0 mt-2 w-32 bg-white border border-gray-200 rounded-md shadow-lg z-99">
                        <button @click="
                            handleDownload(
                                SPODetail.id,
                                'pdf',
                                SPODetail.code,
                                SPODetail.supplier?.data?.name
                            )
                            "
                            class="block w-full px-4 py-2 cursor-pointer text-sm text-gray-700 hover:bg-gray-100 text-left">
                            PDF
                        </button>
                        <button @click="
                            handleDownload(
                                SPODetail.id,
                                'xlsx',
                                SPODetail.code,
                                SPODetail.supplier?.data?.name
                            )
                            "
                            class="block w-full px-4 py-2 cursor-pointer text-sm text-gray-700 hover:bg-gray-100 text-left">
                            XLSX
                        </button>
                        <button @click="
                            handleDownload(
                                SPODetail.id,
                                'tsv',
                                SPODetail.code,
                                SPODetail.supplier?.data?.name
                            )
                            "
                            class="block w-full px-4 py-2 cursor-pointer text-sm text-gray-700 hover:bg-gray-100 text-left">
                            TSV
                        </button>
                    </div>
                </div>

                <button v-if="SPODetail.status == 0" @click="approveSPO"
                    class="px-4 py-2 text-white cursor-pointer bg-green-500 rounded-md hover:bg-green-600 transition-all duration-300">
                    Approve
                </button>
            </div>
        </div>
        <Loading :isLoading="isLoading" />
        <PreviewModal :realPurchase="realPurchase" :show="showPreviewModal" :templateId="previewSPOId"
            :spoId="previewSPOId" :type="previewType" :language="language" @close="closePreviewModal" />
    </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from "vue";
import { router } from "@inertiajs/vue3";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import Layout from "@/Components/Layout.vue";
import { getImageUrl } from "@/Composables/helpers";
import { getProductVariant } from "@/Composables/helpers";
import Loading from "@/Components/Loading.vue";
import PreviewModal from "@/Pages/PurchaseTemplate/Template/Action/PreviewModal.vue";
import SwitchButton from "@/Components/Button/Switch.vue";

defineOptions({
    layout: Layout,
});

const props = defineProps(["SPODetail"]);
const isLoading = ref(false);
const showPreviewModal = ref(false);
const previewSPOId = ref(null);
const previewType = ref("pdf");
const realPurchase = ref(true);
const language = ref('en');

const previewSPO = () => {
    previewSPOId.value = props.SPODetail.id;
    previewType.value = "excel";
    showPreviewModal.value = true;
    realPurchase.value = true;
    language.value = language.value;
};

const closePreviewModal = () => {
    showPreviewModal.value = false;
    previewSPOId.value = null;
};

const headers = ref([
    {
        title: "ID",
        class: "px-4 py-2 whitespace-nowrap w-1/8 text-left",
    },
    {
        title: "IMAGE",
        class: "px-4 py-2 whitespace-nowrap w-1/7 text-left",
    },
    {
        title: "SKU",
        class: "px-4 py-2 whitespace-nowrap w-1/6 text-left",
    },
    {
        title: "PART NO.",
        class: "px-4 py-2 whitespace-nowrap w-1/7 text-left",
    },
    {
        title: "DESCRIPTION",
        class: "px-4 py-2 whitespace-nowrap w-1/8 text-left",
    },
    {
        title: "VARIANT",
        class: "px-4 py-2 whitespace-nowrap w-1/8 text-left",
    },
    {
        title: "QTY",
        class: "px-4 py-2 whitespace-nowrap w-1/3 text-left",
    },
    {
        title: "SUPPLIER",
        class: "px-4 py-2 whitespace-nowrap w-1/8 text-left",
    },
    {
        title: "SCM CODE",
        class: "px-4 py-2 whitespace-nowrap w-1/8 text-left",
    },
    {
        title: "ORDER NO.",
        class: "px-4 py-2 whitespace-nowrap w-1/8 text-left",
    },
    {
        title: "UNIT PRICE",
        class: "px-4 py-2 whitespace-nowrap w-1/8 text-left",
    },
    {
        title: "DELIVERY DAYS",
        class: "px-4 py-2 whitespace-nowrap w-1/8 text-left",
    },
]);

onMounted(() => {
    if (props.SPODetail.status == 0) {
        headers.value.push({
            title: "ACTION",
            class: "px-4 py-2 whitespace-nowrap w-1/8 text-left",
        });
    }
});

const showDownloadDropdown = ref(false);

const getStatus = (status) => {
    return status == 0 ? "Pending" : "Approved";
};

const getTotalQuantity = (spo) => {
    if (!spo?.purchases) return 0;
    return spo.purchases.reduce((acc, curr) => acc + (curr.quantity || 0), 0);
};

const getTotalAmount = (spo) => {
    if (!spo?.purchases) return 0;
    const total = spo.purchases.reduce(
        (acc, curr) => acc + (curr.quantity || 0) * (curr.unit_price || 0),
        0,
    );
    return new Intl.NumberFormat().format(total);
};

const toggleDownloadDropdown = () => {
    showDownloadDropdown.value = !showDownloadDropdown.value;
};

const handleDownload = async (id, format, code, supplierName) => {
    try {
        isLoading.value = true;
        const response = await fetch(
            route("purchase.spo.download", { id, format, language: language.value }),
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
        window.open(route("purchase.spo.download", { id, format, language: language.value }));
        window.notyf.error("Download failed.");
    } finally {
        isLoading.value = false;
    }
};

const formatPrice = (price) => {
    if (!price) return "-";
    return new Intl.NumberFormat().format(price);
};

const goBack = () => {
    router.visit(route("purchase.spo.index"));
};

const deleteItem = (itemId) => {
    if (props.SPODetail?.purchases?.length <= 1) {
        Swal.fire({
            title: "Warning!",
            text: "You cannot delete the last item.",
            icon: "warning",
        });
        return;
    }

    Swal.fire({
        title: "Are you sure?",
        text: "Do you want to delete this item?",
        icon: "warning",
        showCancelButton: true,
        cancelButtonText: "No, cancel!",
        confirmButtonText: "Yes, please.",
    }).then((result) => {
        if (result.isConfirmed) {
            router.visit(route("purchase.spo.destroy.item", itemId), {
                method: "delete",
                onSuccess: (page) => {
                    window.notyf.success(page.props.flash.message.messages);
                },
                onError: (error) => {
                    console.log(error);
                    window.notyf.error("A server error occurred. Please contact the dev team to support you.");
                },
            });
        }
    });
};

const deleteSPO = () => {
    Swal.fire({
        title: "Are you sure?",
        text: "Do you want to delete this SPO?",
        icon: "warning",
        showCancelButton: true,
        cancelButtonText: "No, cancel!",
        confirmButtonText: "Yes, please.",
    }).then((result) => {
        if (result.isConfirmed) {
            router.visit(route("purchase.spo.destroy"), {
                method: "delete",
                data: {
                    spoIds: [props.SPODetail.id],
                },
                onSuccess: (page) => {
                    window.notyf.success(page.props.flash.message.messages);
                },
                onError: (error) => {
                    window.notyf.error("A server error occurred. Please contact the dev team to support you.");
                },
            });
        }
    });
};

const approveSPO = () => {
    Swal.fire({
        title: "Are you sure?",
        text: "Do you want to approve this SPO?",
        icon: "warning",
        showCancelButton: true,
        cancelButtonText: "No, cancel!",
        confirmButtonText: "Yes, please.",
    }).then((result) => {
        if (result.isConfirmed) {
            router.visit(route("purchase.spo.approve", props.SPODetail.id), {
                method: "put",
                onSuccess: (page) => {
                    window.notyf.success(page.props.flash.message.messages);
                },
                onError: (error) => {
                    window.notyf.error("A server error occurred. Please contact the dev team to support you.");
                },
            });
        }
    });
};

const handleClickOutside = (event) => {
    if (!event.target.closest(".relative")) {
        showDownloadDropdown.value = false;
    }
};

onMounted(() => {
    document.addEventListener("click", handleClickOutside);
});

onUnmounted(() => {
    document.removeEventListener("click", handleClickOutside);
});
</script>
