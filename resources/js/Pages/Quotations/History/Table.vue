<template>
    <div class="w-full overflow-x-auto">
        <table class="table-auto w-full text-left text-gray-500 rtl:text-right">
            <thead class="bg-gray-200 text-gray-700 sticky top-0 z-[1]">
                <tr>
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
                    v-for="(row, rowIndex) in props.list.data"
                    :key="rowIndex"
                    :row="row"
                    :class="rowIndex % 2 === 0 ? 'bg-white' : 'bg-gray-50'"
                />
                <tr
                    class="border-b border-gray-200"
                    v-if="!props.list.data || props.list.data.length === 0"
                >
                    <td colspan="11" class="p-2 text-center">
                        No data available
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script setup>
import { ref } from "vue";
import TableRow from "./Table/Row.vue";

const props = defineProps(["list"]);

const headers = ref([
    { title: "ID", class: "px-4 py-2 whitespace-nowrap w-1/10 text-left" },
    {
        title: "SUPPLIER",
        class: "px-4 py-2 whitespace-nowrap  w-1/8 text-left",
    },
    { title: "SKU", class: "px-4  whitespace-nowrap w-1/12 text-left" },
    { title: "PART NO.", class: "px-4  whitespace-nowrap w-1/13 text-left" },
    { title: "DESCRIPTION", class: "px-4  whitespace-nowrap w-1/5 text-left" },
    { title: "VARIANT", class: "px-4  whitespace-nowrap w-1/9 text-left" },
    { title: "QTY", class: "px-4  whitespace-nowrap w-1/13 text-left" },
    { title: "UNIT PRICE", class: "px-4  whitespace-nowrap w-1/13 text-left" },
    {
        title: "DELIVERY DAYS",
        class: "px-4  whitespace-nowrap w-1/13 text-left",
    },
]);
</script>

<style scoped></style>
