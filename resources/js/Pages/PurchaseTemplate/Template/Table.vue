<template>
    <div>
        <table class="w-full text-left text-gray-500 rtl:text-right">
            <thead class="bg-gray-200 text-gray-700 sticky top-0 z-[1]">
                <tr>
                    <th
                        class="px-2 py-2 whitespace-nowrap w-14 hover:bg-primary-color/20 transition duration-300 ease-in-out">
                        <div class="flex gap-2 items-center pl-2 relative">
                            <input type="checkbox"
                                class="size-5 text-primary-color cursor-pointer rounded focus:ring-primary-color"
                                ref="masterCheckbox" :checked="isAllChecked" @change="toggleAll" />
                            <div class="relative">
                                <i class="fa-solid fa-caret-down cursor-pointer" @click="toggleMasterDropdown"></i>
                                <ul v-show="showMasterDropdown"
                                    class="absolute top-6 left-0 z-10 bg-white border border-gray-300 rounded-md shadow-lg py-2 min-w-[120px]">
                                    <li class="flex gap-3 items-center hover:bg-primary-color/20 px-2 py-1 cursor-pointer transition-colors duration-300"
                                        @click="deleteTemplate(selectedTemplates.map((item) => item.id))">
                                        <i class="fa-solid fa-trash"></i>
                                        <span class="font-normal">Delete</span>
                                    </li>
                                </ul>
                            </div>
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
                            <input type="checkbox"
                                class="size-5 text-primary-color cursor-pointer rounded focus:ring-primary-color"
                                v-model="selectedTemplates" :value="item" />
                        </td>
                        <td class="px-4 py-2">
                            {{ item.id }}
                        </td>
                        <td class="px-4 py-2">
                            {{ item.name }}
                        </td>
                        <td class="px-4 py-2">
                            {{ item.memo }}
                        </td>
                        <td class="px-4 py-2">
                            {{ item.updated_by?.name || "N/A" }}
                        </td>
                        <td class="px-4 py-2">
                            {{ item.updated_at || "" }}
                        </td>
                        <td class="px-4 py-2">
                            <div class="flex items-center gap-2">
                                <div class="relative">
                                    <PreviewButton @click="togglePreviewDropdown(index)" />
                                    <ul v-show="showPreviewDropdown === index"
                                        class="absolute top-6 left-0 z-10 bg-white border border-gray-300 rounded-md shadow-lg py-2 min-w-[80px]">
                                        <li class="hover:bg-primary-color/20 px-2 py-1 cursor-pointer transition-colors duration-300"
                                            @click="preview('preview', item.id, 'excel', props.language)">
                                            <span>XLSX</span>
                                        </li>
                                        <li class="hover:bg-primary-color/20 px-2 py-1 cursor-pointer transition-colors duration-300"
                                            @click="preview('preview', item.id, 'pdf', props.language)">
                                            <span>PDF</span>
                                        </li>
                                    </ul>
                                </div>
                                <EditButton @click="openModal('edit', item.id)" />
                            </div>
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
    </div>
</template>

<script setup>
import { ref, computed, watch, onMounted, inject, onUnmounted } from "vue";
import { router } from "@inertiajs/vue3";
import PreviewButton from "@/Components/Button/Preview.vue";
import EditButton from "@/Components/Button/Edit.vue";

const props = defineProps(["list", "language"]);
const selectedTemplates = ref([]);
const masterCheckbox = ref(null);
const showMasterDropdown = ref(false);
const showPreviewDropdown = ref(null);

const actionRef = inject("actionRef");

const openModal = (mode, id) => {
    if (actionRef && actionRef.value) {
        actionRef.value.openModal(mode, id, props.language);
    }
};

const preview = (mode, id, type, language) => {
    if (actionRef && actionRef.value) {
        actionRef.value.preview(mode, id, type, language);
    }
};

const headers = ref([
    { title: "ID", class: "px-4 py-2 whitespace-nowrap w-1/18 text-left" },
    {
        title: "Template Name",
        class: "px-4 py-2 whitespace-nowrap w-1/3 text-left",
    },
    { title: "Memo", class: "px-4 py-2 whitespace-nowrap w-1/4 text-left" },
    {
        title: "Updated By",
        class: "px-4 py-2 whitespace-nowrap w-1/5 text-left",
    },
    {
        title: "Updated At",
        class: "px-4 py-2 whitespace-nowrap w-1/5 text-left",
    },
    {
        title: "Action",
        class: "px-4 py-2 whitespace-nowrap w-24 text-left pl-7",
    },
]);

const isAllChecked = computed(
    () =>
        selectedTemplates.value.length > 0 &&
        selectedTemplates.value.length === props.list.length,
);
const isSomeChecked = computed(
    () => selectedTemplates.value.length > 0 && !isAllChecked.value,
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
        selectedTemplates.value = [...props.list];
    } else {
        selectedTemplates.value = [];
    }
};

const toggleMasterDropdown = () => {
    showMasterDropdown.value = !showMasterDropdown.value;
    showPreviewDropdown.value = null;
};

const togglePreviewDropdown = (index) => {
    showPreviewDropdown.value = showPreviewDropdown.value === index ? null : index;
    showMasterDropdown.value = false;
};

const closeAllDropdowns = () => {
    showMasterDropdown.value = false;
    showPreviewDropdown.value = null;
};

const deleteTemplate = (ids) => {
    closeAllDropdowns();
    if (selectedTemplates.value.length > 0) {
        Swal.fire({
            title: "Are you sure?",
            html: `Do you want to delete these templates?`,
            icon: "warning",
            showCancelButton: true,
            cancelButtonText: "No, cancel!",
            confirmButtonText: "Yes, please.",
        }).then((result) => {
            if (result.isConfirmed) {
                router.delete(route("purchase-template.destroy"), {
                    data: {
                        ids: ids,
                    },
                    onSuccess: (response) => {
                        const result = response.props.flash.message;
                        Swal.fire({
                            icon: result.type,
                            title: result.title,
                            html: result.messages,
                        });
                    },
                });
            }
        });
    } else {
        Swal.fire({
            icon: "error",
            title: "Warning!",
            text: "Please select at least one template to delete.",
        });
    }
};

const handleClickOutside = (event) => {
    if (!event.target.closest('.relative')) {
        closeAllDropdowns();
    }
};

onMounted(() => {
    document.addEventListener('click', handleClickOutside);
});

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside);
});
</script>
