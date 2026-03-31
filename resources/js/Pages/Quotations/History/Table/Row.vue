<template>
    <tr
        class="border-b border-gray-200 hover:bg-primary-color/10 transition transition-duration-300"
    >
        <td class="px-4 py-2">
            {{ row.identify_code }}
        </td>
        <td class="px-4 py-2">
            {{ row.supplier.data.name }}
        </td>
        <td class="px-4 py-2">
            {{ row.sku }}
        </td>
        <td class="px-4 py-2">
            {{ row.product.data.model_number }}
        </td>
        <td class="px-4 py-2">
            {{ row.product.data.name }}
        </td>
        <td class="px-4 py-2 text-gray-400">
            {{ getProductVariant(row) }}
        </td>
        <td class="px-4 py-2">
            {{ row.quantity }}
        </td>
        <td class="px-4 py-2">
            <i
                class="fa-solid"
                :class="getCurrencyIcon(row.supplier.data.currency)"
            ></i>
            {{ $filters.formatNumber(row.unit_price ?? 0) }}
        </td>
        <td class="px-4 py-2">
            {{ row.delivery_days }}
        </td>
    </tr>
</template>

<script setup>
import { getProductVariant, getCurrencyIcon } from "@/Composables/helpers";

defineProps({
    row: {
        type: Object,
        required: true,
    },
});
</script>
