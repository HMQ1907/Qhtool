<template>
    <div>
        <table class="w-full text-left text-gray-500 rtl:text-right">
            <thead class="bg-gray-200 text-gray-700 sticky top-0 z-[1]">
                <tr>
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
                        <td class="px-4 py-2">
                            {{ item.code }}
                        </td>
                        <td class="px-4 py-2">
                            {{ item.data.name }}
                        </td>
                        <td class="px-4 py-2">
                            <SingleSelect :options="templates" option-key="id" option-label="name"
                                v-model="item.purchase_template_id" :placeholder="'Select Template...'"
                                @change="(val) => mappingTemplate(item, val)" />
                        </td>
                        <td class="px-4 py-2">
                            {{ item.updated_by.name }}
                        </td>
                        <td class="px-4 py-2">
                            {{ item.updated_at || "" }}
                        </td>
                    </tr>
                </template>
                <template v-else>
                    <tr>
                        <td colspan="7" class="px-4 py-2 text-center border-b border-gray-300">
                            No data available
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>
        <Loading :is-loading="isLoading" />
    </div>
</template>

<script setup>
import { ref } from "vue";
import axios from "axios";
import SingleSelect from "@/Components/SingleSelect.vue";
import Loading from "@/Components/Loading.vue";
import { router } from "@inertiajs/vue3";

defineProps(["list", "templates"]);

const isLoading = ref(false);
const headers = ref([
    {
        title: "Supplier Code",
        class: "px-4 py-2 whitespace-nowrap w-1/6 text-left",
    },
    {
        title: "Supplier Name",
        class: "px-4 py-2 whitespace-nowrap w-1/5 text-left",
    },
    {
        title: "Template Name",
        class: "px-4 py-2 whitespace-nowrap w-1/4 text-left",
    },
    {
        title: "Updated By",
        class: "px-4 py-2 whitespace-nowrap w-1/5 text-left",
    },
    {
        title: "Updated At",
        class: "px-4 py-2 whitespace-nowrap w-1/6 text-left",
    },
]);

const mappingTemplate = async (item, value) => {
    try {
        isLoading.value = true;
        const { data } = await axios.post(
            route("purchase-template.mapping.update", { code: item.code }),
            { purchase_template_id: value },
        );

        if (data?.status == "success") {
            window.notyf.success(data?.data + " template mapping successfully.");
            router.reload();
        } else if (data?.status == "error") {
            window.notyf.error("This supplier has pending SPO.");
        }
    } catch (e) {
        window.notyf.error("A server error occurred. Please contact the dev team to support you.");
    } finally {
        isLoading.value = false;
    }
};
</script>
