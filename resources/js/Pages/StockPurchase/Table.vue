<template>
    <table class="w-full text-left text-gray-500 rtl:text-right">
        <thead class="bg-gray-200 text-gray-700 sticky top-0 z-[1]">
            <tr>
                <th v-for="header in headers" :key="'header_' + header" :class="header.class"
                    class="px-4 py-2 whitespace-nowrap min-w-[100px] hover:bg-primary-color/20 transition duration-300 ease-in-out">
                    {{ header.title }}
                </th>
            </tr>
        </thead>
        <tbody>
            <template v-if="list.length > 0">
                <template v-for="(item, index) in list" :key="'item_' + index">
                    <tr class="border-b border-gray-200 hover:bg-primary-color/10 transition duration-300 ease-in-out">
                        <td class="px-4 py-2">
                            <span data-bs-toggle="collapse" aria-expanded="false" :aria-controls="'collapseStockPurchase' + item.id
                                " :href="'#collapseStockPurchase' + item.id">
                                <i
                                    class="fa-solid fa-circle-plus text-green-500 bg-white cursor-pointer hover:text-green-600"></i>
                                <i
                                    class="fa-solid fa-circle-minus text-red-500 bg-white cursor-pointer hover:text-red-600"></i>
                            </span>
                        </td>
                        <td class="px-4 py-2">
                            {{ item.key }}
                        </td>
                        <td class="px-4 py-2">
                            <button
                                class="flex cursor-pointer items-center gap-2 p-2 rounded text-white transition duration-300 ease-in-out w-5/6 justify-center"
                                :class="item.is_purchased
                                        ? 'bg-green-600 pointer-events-none'
                                        : 'bg-primary-color hover:bg-[#cc6b1a] cursor-pointer'
                                    " @click="onPurchase(item.id)">
                                <i :class="item.is_purchased
                                        ? 'fa-regular fa-circle-check'
                                        : 'fas fa-shopping-cart'
                                    "></i>
                                {{
                                    item.is_purchased ? "Purchased" : "Purchase"
                                }}
                            </button>
                        </td>
                    </tr>
                    <tr v-if="item.purchases.length > 0" class="collapse border-b border-gray-300"
                        :id="'collapseStockPurchase' + item.id">
                        <td colspan="5" class="p-4">
                            <table class="w-full text-left text-gray-500 rtl:text-right border border-gray-300">
                                <thead class="bg-gray-200 uppercase text-gray-700 sticky top-0 z-1">
                                    <tr>
                                        <th v-for="subHeader in subHeaders" :key="'subHeader_' + subHeader"
                                            class="px-4 py-2 whitespace-nowrap min-w-[100px] hover:bg-primary-color/20 transition duration-300 ease-in-out">
                                            {{ subHeader.title }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="overflow-y-auto">
                                    <template v-for="(
purchase, index
                                        ) in item.purchases" :key="'subItem_' + index">
                                        <tr
                                            class="border-b border-gray-200 hover:bg-primary-color/10 transition duration-300 ease-in-out">
                                            <td class="px-4 py-2 w-1/8">
                                                {{
                                                    purchase.supplier
                                                        ? purchase.supplier.data
                                                            .name
                                                        : purchase.supplier_code
                                                }}
                                            </td>
                                            <td class="px-4 py-2 w-1/2">
                                                <div class="flex gap-3">
                                                    <img :src="getImageUrl(
                                                        purchase.product
                                                            ?.data
                                                            ?.product_image,
                                                    )
                                                        " :alt="purchase.product
                                                                ?.data?.name
                                                            "
                                                        class="size-17 object-contain rounded border border-gray-300 p-1" />
                                                    <div class="flex flex-col">
                                                        <span class="font-bold">
                                                            {{
                                                                purchase.product
                                                                    ?.data?.name
                                                            }}
                                                        </span>
                                                        <span class="text-gray-500">
                                                            {{
                                                                purchase.product
                                                                    ?.data
                                                                    ?.brand_name
                                                            }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-4 py-2 w-1/4">
                                                {{ purchase.sku }}
                                            </td>
                                            <td class="px-4 py-2 w-1/4">
                                                {{ purchase.quantity }}
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                </template>
            </template>
            <tr class="border-b border-gray-200" v-else>
                <td colspan="5" class="p-2 text-center">No data available</td>
            </tr>
        </tbody>
    </table>
</template>
<script setup>
import { ref } from "vue";
import { router } from "@inertiajs/vue3";
import { getImageUrl } from "@/Composables/helpers";

defineProps(["list"]);

const headers = ref([
    { title: "", class: "w-[4%]" },
    { title: "Key" },
    { title: "Action", class: "w-[10%]" },
]);

const subHeaders = ref([
    { title: "Supplier" },
    { title: "Name" },
    { title: "Sku" },
    { title: "Quantity" },
]);

const onPurchase = (id) => {
    Swal.fire({
        title: "Are you sure?",
        html: `Do you want purchase this order?`,
        icon: "question",
        showCancelButton: true,
        cancelButtonText: "No, cancel!",
        confirmButtonText: "Yes, please.",
    }).then((result) => {
        if (result.isConfirmed) {
            router.post(
                route("stock-purchase.purchase", id),
                {},
                {
                    onSuccess: (response) => {
                        window.notyf.success(response.props.flash.message.messages);
                    },
                    onError: (error) => {
                        if (error.error?.type) {
                            window.notyf.error(error.error.messages);
                        }
                    },
                },
            );
        }
    });
};
</script>
<style scoped>
.collapse.show {
    visibility: visible !important;
}

span[aria-expanded="true"] .fa-circle-plus {
    display: none;
}

span[aria-expanded="false"] .fa-circle-minus {
    display: none;
}
</style>
