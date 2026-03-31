<template>
    <div
        class="relative overflow-x-auto shadow-md rounded table-wrp block max-h-[calc(100vh_-_330px)] scroll-table"
        :style="'--scrollbar-track-color: #a855f74C; --scrollbar-thumb-color: #a855f7B2; --scrollbar-thumb-hover-color: #a855f7FF;'"
    >
        <table class="w-full text-left text-gray-500 rtl:text-right">
            <thead
                class="bg-gray-200 uppercase text-gray-700 sticky top-0 z-10"
            >
                <tr>
                    <th></th>
                    <th
                        class="hover:bg-primary-color/20 p-2 transition duration-300 ease-in-out w-[75%]"
                        scope="col"
                    >
                        Operation
                    </th>
                    <th
                        class="hover:bg-primary-color/20 p-2 transition duration-300 ease-in-out w-[15%]"
                        scope="col"
                    >
                        Created By
                    </th>
                    <th
                        class="hover:bg-primary-color/20 p-2 transition duration-300 ease-in-out w-[10%]"
                        scope="col"
                    >
                        Created At
                    </th>
                </tr>
            </thead>
            <tbody class="overflow-y-auto">
                <template v-if="list && list.data.length">
                    <template v-for="(value, key) in list.data">
                        <tr
                            class="border-b border-gray-300 hover:bg-primary-color/10 odd:bg-white even:bg-gray-100 transition duration-300 ease-in-out"
                        >
                            <td>
                                <button
                                    v-if="value.user_activity_import_result"
                                    class="p-2 text-gray-500 hover:text-primary-color/80 transition duration-300 ease-in-out"
                                    @click="toggleCollapse(value.id)"
                                >
                                    <div
                                        class="size-6 p-1 rounded-full bg-gray-200 hover:bg-primary-color/20 transition duration-300 ease-in-out flex items-center justify-center cursor-pointer"
                                    >
                                        <i
                                            class="fa-solid transition-transform duration-300"
                                            :class="
                                                expandedItems[value.id]
                                                    ? 'fa-angle-up'
                                                    : 'fa-angle-down'
                                            "
                                        ></i>
                                    </div>
                                </button>
                            </td>
                            <td
                                class="p-2 align-middle"
                                v-html="value.operation"
                            ></td>
                            <td class="p-2 align-middle">
                                {{ value.created_by.name }}
                            </td>
                            <td class="p-2 align-middle">
                                {{ value.created_at }}
                            </td>
                        </tr>
                        <tr
                            v-if="
                                value.user_activity_import_result &&
                                expandedItems[value.id]
                            "
                            class="border-b border-gray-300"
                        >
                            <td colspan="4" class="py-2 px-10">
                                <div class="flex gap-4">
                                    <div>
                                        Total:
                                        <strong class="text-blue-500">{{
                                            value.user_activity_import_result
                                                .total_records
                                        }}</strong>
                                    </div>
                                    <div>
                                        Success:
                                        <strong class="text-green-500">{{
                                            value.user_activity_import_result
                                                .total_success
                                        }}</strong>
                                    </div>
                                    <div>
                                        Unchanged:
                                        <strong class="text-orange-500">{{
                                            value.user_activity_import_result
                                                .total_unchanged
                                        }}</strong>
                                    </div>
                                    <div>
                                        Failed:
                                        <strong class="text-red-500">{{
                                            value.user_activity_import_result
                                                .total_failed
                                        }}</strong>
                                    </div>
                                </div>
                                <div
                                    v-if="
                                        value.user_activity_import_result
                                            ?.user_activity_import_errors
                                            ?.length
                                    "
                                    class="relative overflow-x-auto table-wrp block max-h-[400px] scroll-table"
                                >
                                    <table
                                        class="w-full text-left text-gray-500 rtl:text-right border border-gray-300"
                                    >
                                        <caption class="caption-top text-sm">
                                            List of Failed Records
                                        </caption>
                                        <thead
                                            class="bg-gray-200 uppercase text-gray-700 sticky top-0 z-1"
                                        >
                                            <tr>
                                                <th
                                                    class="hover:bg-primary-color/20 p-2 transition duration-300 ease-in-out w-[25%]"
                                                    scope="col"
                                                >
                                                    Identifier
                                                </th>
                                                <th
                                                    class="hover:bg-primary-color/20 p-2 transition duration-300 ease-in-out w-[75%]"
                                                    scope="col"
                                                >
                                                    Error Message
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody class="overflow-y-auto">
                                            <template
                                                v-for="(
                                                    error, keyError
                                                ) in value
                                                    .user_activity_import_result
                                                    .user_activity_import_errors"
                                            >
                                                <tr
                                                    class="hover:bg-primary-color/10 odd:bg-white even:bg-gray-100 transition duration-300 ease-in-out"
                                                >
                                                    <td
                                                        class="p-2 align-middle font-semibold"
                                                    >
                                                        {{
                                                            error.identify_name
                                                        }}:
                                                        {{
                                                            error.identify_value
                                                        }}
                                                    </td>
                                                    <td
                                                        class="p-2 align-middle"
                                                    >
                                                        {{
                                                            error.error_message
                                                        }}
                                                    </td>
                                                </tr>
                                            </template>
                                        </tbody>
                                    </table>
                                </div>
                            </td>
                        </tr>
                    </template>
                </template>
                <tr
                    v-else
                    class="hover:bg-primary-color/10 odd:bg-white even:bg-gray-100 transition duration-300 ease-in-out"
                >
                    <td
                        colspan="4"
                        class="p-2 align-middle text-center text-gray-500"
                    >
                        No data available
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script setup>
import { ref } from "vue";

defineProps(["list"]);

const expandedItems = ref({});

const toggleCollapse = (id) => {
    expandedItems.value[id] = !expandedItems.value[id];
};
</script>
