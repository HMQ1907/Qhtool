<template>
    <tr class="hover:bg-[#f58220]/50 border-b border-gray-200">
        <td class="px-4 py-2">
            <div v-if="!row.supplier_purchase_order?.id">
                <label class="inline-flex items-center">
                    <input
                        type="checkbox"
                        class="size-5 text-primary-color cursor-pointer rounded focus:ring-primary-color"
                        style="accent-color: #f58220"
                        v-model="isChecked"
                    />
                </label>
            </div>
        </td>
        <td class="p-2">
            {{ row.identify_code }}
        </td>
        <td class="p-2">
            {{ row.sku }}
        </td>
        <td class="p-2">
            {{ row.product?.data.model_number }}
        </td>
        <td class="p-2">
            {{ row.product?.data.name }}
        </td>
        <td class="p-2 text-gray-400">
            {{ getProductVariant(row) }}
        </td>
        <td class="p-2">
            {{ row.quantity }}
        </td>
        <td class="p-2">
            {{ supplier_name }}
        </td>
        <td class="p-2">
            {{ row.orders?.order_scm_code || "" }}
        </td>
        <td class="p-2">
            {{ row.scm_code }}
        </td>
        <td class="p-2">
            <div class="flex">
                <div
                    class="flex items-center px-3 py-1 bg-gray-100 border border-r-0 rounded-l"
                >
                    <span
                        class="text-gray-700 font-medium w-3 flex items-center justify-center"
                    >
                        <i
                            class="fa-solid"
                            :class="
                                getCurrencyIcon(row.supplier?.data.currency)
                            "
                        ></i>
                    </span>
                </div>
                <input
                    type="number"
                    :value="row.unit_price"
                    min="0"
                    @input="
                        $emit('update:unit_price', Number($event.target.value))
                    "
                    :class="{
                        'border-red-500': row.checked && row.unit_price <= 0,
                    }"
                    class="flex-1 w-30 border rounded-r px-2 py-1 focus:outline-none focus:ring-0 focus:border-primary-color"
                />
            </div>
        </td>
        <td class="p-2">
            <input
                type="number"
                v-model.number="row.delivery_days"
                class="w-full rounded px-2 py-1 bg-white focus:outline-none focus:ring-0 focus:border-primary-color"
                :class="{ 'border-red-500': row.checked && !row.delivery_days }"
            />
        </td>
        <td class="p-2 text-blue-500">
            <Link
                :href="
                    $route(
                        'purchase.spo.detail',
                        row.supplier_purchase_order?.id ?? 0,
                    )
                "
            >
                {{ row.supplier_purchase_order?.code }}
            </Link>
        </td>
    </tr>
</template>

<script setup>
import { computed } from "vue";
import { getProductVariant, getCurrencyIcon } from "@/Composables/helpers";
import { Link } from "@inertiajs/vue3";

const props = defineProps({
    row: {
        type: Object,
        required: true,
    },
    supplier_name: {
        type: String,
        required: true,
    },
});

const emit = defineEmits(["checkbox-changed"]);

const isChecked = computed({
    get: () => props.row.checked,
    set: (value) => {
        props.row.checked = value;
        emit("checkbox-changed", value);
    },
});
</script>
