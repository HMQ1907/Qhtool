<template>
    <Modal :show="show" :size="'7xl'" @close="$emit('close')">
        <template v-slot:header>
            <h3 class="text-2xl font-semibold text-gray-900">
                {{ title }}Purchase Template
            </h3>
        </template>

        <template v-slot:body>
            <div class="p-6 max-w-7xl mx-auto bg-white">
                <div v-if="isLoading">
                    <LoadingSkeleton :is-loading="isLoading" />
                </div>

                <div v-else>
                    <form @submit.prevent="submit" class="space-y-6">
                        <div class="space-y-4 pl-6">
                            <div class="flex items-center gap-4">
                                <label class="text-sm font-medium text-gray-700 w-32 flex-shrink-0">Template Name
                                    <span class="text-red-500">*</span></label>
                                <input type="text" v-model="form.name"
                                    class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    placeholder="Enter template name" />
                            </div>
                            <div>
                                <span class="text-red-500 ml-36" v-if="form.errors.name">{{ form.errors.name }}</span>
                            </div>
                            <div class="flex items-start gap-4">
                                <label class="text-sm font-medium text-gray-700 w-32 flex-shrink-0 pt-2">Memo</label>
                                <textarea v-model="form.memo" rows="3"
                                    class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    placeholder="Enter memo"></textarea>
                                <span class="text-red-500 text-xs" v-if="form.errors.memo">{{ form.errors.memo }}</span>
                            </div>
                        </div>

                        <div class="relative border border-gray-300 rounded-lg p-6">
                            <h4 class="absolute -top-3 left-4 bg-white px-2 text-lg font-semibold text-gray-800">
                                Webike
                            </h4>
                            <div class="grid grid-cols-2 gap-6">
                                <template v-for="column in webikeFieldColumns" :key="column">
                                    <div class="space-y-4">
                                        <div class="flex items-center gap-4" v-for="field in column" :key="field.key">
                                            <label class="text-sm font-medium text-gray-700 w-32 flex-shrink-0">
                                                {{ field.label }}
                                            </label>

                                            <div v-if="field.key === 'webike_logo'" class="flex-1">
                                                <input id="webike_logo_file" type="file" accept="image/png, image/jpeg"
                                                    @change="handleWebikeLogoChange" class="hidden" />
                                                <label for="webike_logo_file" :class="[
                                                    'inline-flex cursor-pointer hover:border-primary-color items-center gap-2 px-4 py-2 rounded-full border border-gray-300 bg-white text-gray-700 shadow-sm',
                                                ]">
                                                    <span
                                                        class="w-6 h-6 flex items-center justify-center rounded-full bg-primary-color text-white">
                                                        <i class="fas fa-plus text-xs"></i>
                                                    </span>
                                                    <span>Choose File</span>
                                                </label>
                                                <span v-if="webikeLogoName"
                                                    class="inline-flex items-center gap-2 ml-3 text-xs text-gray-600">
                                                    <i class="far fa-file-image"></i>
                                                    <span class="truncate max-w-[200px]">{{ webikeLogoName }}</span>
                                                </span>
                                            </div>

                                            <input v-else-if="
                                                field.key ===
                                                'webike_country'
                                            " readonly disabled :placeholder="field.placeholder" type="text"
                                                v-model="form[field.key]"
                                                class="flex-1 px-3 py-2 border bg-gray-500/20 rounded-md" />

                                            <SingleSelect v-else-if="
                                                [
                                                    'webike_state_province',
                                                    'webike_city_municipality',
                                                    'webike_district_sub_district',
                                                ].includes(field.key)
                                            " class="flex-1" v-model="form[field.key]" :options="getOptions(field.key)"
                                                option-label="label" option-key="value"
                                                :placeholder="field.placeholder" />

                                            <input v-else :placeholder="field.placeholder" type="text"
                                                v-model="form[field.key]"
                                                class="flex-1 px-3 py-2 border border-gray-300 rounded-md" />

                                            <span class="text-red-500 text-xs" v-if="form.errors[field.key]">
                                                {{ form.errors[field.key] }}
                                            </span>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <div class="relative border border-gray-300 rounded-lg p-6">
                            <h4 class="absolute -top-3 left-4 bg-white px-2 text-lg font-semibold text-gray-800">
                                Ship To
                            </h4>
                            <div class="grid grid-cols-2 gap-6">
                                <template v-for="column in shipToFieldColumns" :key="column">
                                    <div class="space-y-4">
                                        <div class="flex items-center gap-4" v-for="field in column" :key="field.key">
                                            <label class="text-sm font-medium text-gray-700 w-32 flex-shrink-0">
                                                {{ field.label }}
                                            </label>

                                            <SingleSelect v-if="
                                                [
                                                    'ship_to_country',
                                                    'ship_to_state_province',
                                                    'ship_to_city_municipality',
                                                    'ship_to_district_sub_district',
                                                ].includes(field.key) &&
                                                (field.key === 'ship_to_country' || isThailand)" class="flex-1"
                                                v-model="form[field.key]" :options="getOptions(field.key)"
                                                option-label="label" option-key="value"
                                                :placeholder="field.placeholder" />

                                            <input v-else :placeholder="field.placeholder" type="text"
                                                v-model="form[field.key]"
                                                class="flex-1 px-3 py-2 border border-gray-300 rounded-md" />

                                            <span class="text-red-500 text-xs" v-if="form.errors[field.key]">
                                                {{ form.errors[field.key] }}
                                            </span>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <div class="relative border border-gray-300 rounded-lg p-6">
                            <h4 class="absolute -top-3 left-4 bg-white px-2 text-lg font-semibold text-gray-800">
                                Items
                            </h4>
                            <div class="flex items-center justify-center gap-2">
                                <div class="w-100">
                                    <div class="h-90 border border-gray-300 rounded p-4 overflow-y-auto bg-white select-none"
                                        @drop="handleDrop($event, 'available')" @dragover.prevent @dragenter.prevent>
                                        <div v-for="(item, index) in availableItems" :key="index" :draggable="true"
                                            @dragstart="handleDragStart($event, item, 'available', index)"
                                            @dragend="handleDragEnd"
                                            class="p-3 mb-2 bg-white border border-gray-200 rounded cursor-grab active:cursor-grabbing hover:bg-gray-50 select-none relative z-[1] transition-all duration-200 item-row">
                                            <div
                                                class="flex items-center justify-between pointer-events-none item-content">
                                                <span class="text-sm font-medium text-gray-700">{{ item.title }}</span>
                                                <i class="fas fa-grip-vertical text-xs text-gray-400"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex flex-col items-center justify-center gap-2 h-90 px-16">
                                    <button type="button" @click="moveAllRight"
                                        class="w-32 h-10 text-lg cursor-pointer flex items-center justify-center bg-white text-orange-500 rounded-lg border border-orange-500 hover:bg-orange-500 hover:text-white">
                                        <i class="fas fa-angle-double-right"></i>
                                    </button>
                                    <button type="button" @click="moveAllLeft"
                                        class="w-32 h-10 text-lg flex items-center cursor-pointer justify-center bg-white text-orange-500 rounded-lg border border-orange-500 hover:bg-orange-500 hover:text-white">
                                        <i class="fas fa-angle-double-left"></i>
                                    </button>
                                </div>

                                <div class="w-100">
                                    <div class="h-90 border border-gray-300 rounded p-4 overflow-y-auto bg-white select-none"
                                        @drop="handleDrop($event, 'selected')" @dragover.prevent @dragenter.prevent>
                                        <div v-for="(item, index) in selectedItems" :key="item.mapping"
                                            :draggable="true"
                                            @dragstart="handleDragStart($event, item, 'selected', index)"
                                            @dragend="handleDragEnd($event)" @dragleave="handleDragLeave($event)"
                                            @dragover="handleDragOver($event, index, item)"
                                            @drop="handleReorderDrop($event, index)" :class="{
                                                'bg-white cursor-grab active:cursor-grabbing hover:bg-gray-50': true,
                                                'border-orange-500 bg-orange-50 shadow-md':
                                                    dragOverIndex === index &&
                                                    draggedItem,
                                            }"
                                            class="p-3 mb-2 border border-gray-200 rounded relative z-[1] select-none transition-all duration-200 item-row">
                                            <div
                                                class="flex items-center justify-between pointer-events-none item-content">
                                                <div class="flex items-center gap-2">
                                                    <span class="text-sm font-medium text-gray-700">
                                                        {{ item.title }}
                                                    </span>
                                                </div>
                                                <div class="flex items-center gap-1 text-gray-400">
                                                    <i v-if="!isMandatoryItem(item)
                                                    " class="fas fa-grip-vertical text-xs"></i>
                                                    <i v-else class="fas fa-lock text-xs text-red-500"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <span class="text-red-500 text-xs" v-if="form.errors.items">
                                {{ form.errors.items }}
                            </span>
                        </div>

                        <div class="relative border border-gray-300 rounded-lg p-6">
                            <h4 class="absolute -top-3 left-4 bg-white px-2 text-lg font-semibold text-gray-800">
                                Footer
                            </h4>
                            <div class="grid grid-cols-2 gap-6">
                                <div>
                                    <div class="flex items-start gap-4">
                                        <label class="text-sm font-medium text-gray-700 w-32 flex-shrink-0 pt-2">Note
                                        </label>
                                        <textarea v-model="form.note" rows="5"
                                            class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                                    </div>
                                </div>
                                <div class="space-y-4">
                                    <div class="flex items-center gap-4">
                                        <label class="text-sm font-medium text-gray-700 w-32 flex-shrink-0">Display
                                            Total Amount</label>
                                        <input type="checkbox" v-model="form.is_display_total_amount"
                                            class="size-5 text-primary-color cursor-pointer rounded focus:ring-primary-color" />
                                    </div>
                                    <div class="flex items-center gap-4">
                                        <label class="text-sm font-medium text-gray-700 w-32 flex-shrink-0">
                                            Display Webike Address</label>
                                        <input type="checkbox" v-model="form.is_display_webike_address"
                                            class="size-5 text-primary-color cursor-pointer rounded focus:ring-primary-color" />
                                    </div>
                                    <div class="flex items-center gap-4">
                                        <label class="text-sm font-medium text-gray-700 w-32 flex-shrink-0">Authorized
                                            Signature:</label>
                                        <input id="authorized_signature_file" type="file" accept="image/png, image/jpeg"
                                            @change="handleAuthorizedSignatureChange" class="hidden" />
                                        <label for="authorized_signature_file" :class="[
                                            'inline-flex items-center gap-2 px-4 py-2 rounded-full border cursor-pointer hover:border-primary-color border-gray-300 bg-white text-gray-700 shadow-sm',
                                        ]">
                                            <span
                                                class="w-6 h-6 flex items-center justify-center rounded-full bg-primary-color text-white">
                                                <i class="fas fa-plus text-xs"></i>
                                            </span>
                                            <span>Choose File</span>
                                        </label>
                                        <span v-if="authorizedSignatureName"
                                            class="inline-flex items-center gap-2 ml-3 text-xs text-gray-600">
                                            <i class="far fa-file-image"></i>
                                            <span class="truncate max-w-[200px]">
                                                {{ authorizedSignatureName }}
                                            </span>
                                        </span>
                                        <span class="text-red-500" v-if="form.errors.authorized_signature_url">
                                            {{ form.errors.authorized_signature_url }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </template>

        <template v-slot:footer>
            <div class="flex justify-end gap-3 px-6 pb-4">
                <button type="button" @click="$emit('close')"
                    class="cursor-pointer border border-gray-300 text-gray-700 py-2 px-4 rounded-lg hover:bg-gray-600 hover:text-white">
                    <i class="fas fa-times"></i>
                    Cancel
                </button>
                <button type="button" @click="submit" :disabled="form.processing"
                    class="cursor-pointer bg-primary-color text-white py-2 px-4 rounded-lg hover:bg-primary-color/80">
                    <i class="fa-solid fa-circle-check"></i> Submit
                </button>
            </div>
        </template>
    </Modal>
</template>

<script setup>
import { ref, watch, computed, nextTick } from "vue";
import { useForm, usePage } from "@inertiajs/vue3";
import Modal from "@/Components/Modal.vue";
import LoadingSkeleton from "./LoadingSkeleton.vue";
import SingleSelect from "@/Components/SingleSelect.vue";
import axios from "axios";

import {
    webikeFields,
    shipToFields,
    defaultItems,
    mandatoryItems,
} from "@/Composables/filedPurchaseTemplate";

const emit = defineEmits(["close"]);

const props = defineProps({
    show: { type: Boolean, default: false },
    mode: { type: String, default: "add" },
    templateId: { type: Number },
});

const countries = computed(() => usePage().props.countries);
const locations = computed(() => usePage().props.locations);
const isLoading = ref(false);
const webikeLogoName = ref(null);
const authorizedSignatureName = ref(null);

const initializeItems = () => {
    const mandatoryMappings = mandatoryItems.map((item) => item.mapping);
    const available = defaultItems.filter(
        (item) => !mandatoryMappings.includes(item.mapping),
    );
    const selected = [...mandatoryItems];

    return { available, selected };
};

const { available: initialAvailable, selected: initialSelected } = initializeItems();
const availableItems = ref([...initialAvailable]);
const selectedItems = ref([...initialSelected]);
const isLoadingData = ref(false);

const title = computed(() => {
    if (props.mode === "add") {
        return "Add New ";
    } else if (props.mode === "edit") {
        return "Edit ";
    }
});

const form = useForm({
    name: "",
    memo: "",
    webike_logo_url: "Webike_Thailand.jpg",
    webike_name: "",
    webike_street_address: "",
    webike_district_sub_district: "",
    webike_city_municipality: "",
    webike_state_province: "",
    webike_postal_code_zip_code: "",
    webike_country: "Thailand",
    webike_phone_number: "",
    webike_tax_id: "",
    ship_to_name: "",
    ship_to_street_address: "",
    ship_to_district_sub_district: "",
    ship_to_city_municipality: "",
    ship_to_state_province: "",
    ship_to_postal_code_zip_code: "",
    ship_to_country: "",
    ship_to_phone_number: "",
    items: [],
    is_display_total_amount: false,
    is_display_webike_address: false,
    note: "1. Please notify us immediately if you are unable to ship as specified.\n2. Ship date will confirm again by phone.",
    authorized_signature_url: null,
});

const dragOverIndex = ref(-1);
let draggedItem = null;

const isMandatoryItem = (item) =>
    mandatoryItems.some((m) => m.mapping === item.mapping);

const findIndexByMapping = (list, mapping) =>
    list.findIndex((it) => it.mapping === mapping);

const resetDrag = () => {
    dragOverIndex.value = -1;
    draggedItem = null;
};

const handleDragStart = (event, item, source = "available") => {
    const itemElement = event.currentTarget;

    if (!itemElement || !itemElement.classList.contains("item-row")) {
        event.preventDefault();
        return;
    }

    const allItems = document.querySelectorAll(".item-row.dragging");
    allItems.forEach((el) => el.classList.remove("dragging"));

    draggedItem = {
        mapping: item.mapping,
        source,
        item,
        element: itemElement,
    };

    requestAnimationFrame(() => {
        itemElement.classList.add("dragging");
    });

    event.dataTransfer.effectAllowed = "move";
    event.dataTransfer.setData("text/plain", item.mapping ?? "");
};

const handleDrop = (event, targetList) => {
    if (!draggedItem) return;
    event.preventDefault();
    event.stopPropagation();

    const { mapping, source, item } = draggedItem;

    if (source === targetList) {
        resetDrag();
        return;
    }

    if (targetList === "available" && source === "selected") {
        if (isMandatoryItem(item)) {
            resetDrag();
            return;
        }
        const srcIndex = findIndexByMapping(selectedItems.value, mapping);
        if (srcIndex > -1) {
            const removed = selectedItems.value[srcIndex];
            selectedItems.value = selectedItems.value.filter(
                (_, idx) => idx !== srcIndex,
            );
            availableItems.value = [...availableItems.value, removed];
        }
    } else if (targetList === "selected" && source === "available") {
        const srcIndex = findIndexByMapping(availableItems.value, mapping);
        if (srcIndex > -1) {
            const moved = availableItems.value[srcIndex];
            availableItems.value = availableItems.value.filter(
                (_, idx) => idx !== srcIndex,
            );
            selectedItems.value = [...selectedItems.value, moved];
        }
    }
    resetDrag();
};

const handleDragOver = (event, index) => {
    if (!draggedItem) return;

    event.preventDefault();
    event.stopPropagation();
    if (dragOverIndex.value !== index) dragOverIndex.value = index;
};

const handleReorderDrop = (event, targetIndex) => {
    if (!draggedItem) return;
    event.preventDefault();
    event.stopPropagation();

    if (draggedItem.source === "selected") {
        const mapping = draggedItem.mapping;
        const sourceIndex = findIndexByMapping(selectedItems.value, mapping);

        if (
            sourceIndex === -1 ||
            targetIndex === -1 ||
            sourceIndex === targetIndex
        ) {
            resetDrag();
            return;
        }

        const arr = [...selectedItems.value];
        const [moved] = arr.splice(sourceIndex, 1);
        arr.splice(targetIndex, 0, moved);
        selectedItems.value = arr;
    } else if (draggedItem.source === "available") {
        const sourceIndex = findIndexByMapping(availableItems.value, draggedItem.mapping);

        if (sourceIndex === -1 || targetIndex === -1) {
            resetDrag();
            return;
        }

        const moved = availableItems.value[sourceIndex];
        const updatedSelected = [...selectedItems.value];
        updatedSelected.splice(targetIndex, 0, moved);
        selectedItems.value = updatedSelected;
        availableItems.value = availableItems.value.filter((_, idx) => idx !== sourceIndex);
    }

    resetDrag();
};

const handleDragEnd = (event) => {
    const allItems = document.querySelectorAll(".item-row.dragging");
    allItems.forEach((el) => el.classList.remove("dragging"));

    if (event && event.target) {
        event.target.classList.remove("dragging");
    }

    if (draggedItem && draggedItem.element) {
        draggedItem.element.classList.remove("dragging");
    }

    resetDrag();
};

const handleDragLeave = (event) => {
    if (!event.currentTarget.contains(event.relatedTarget)) {
        dragOverIndex.value = -1;
    }
};

const moveAllRight = () => {
    if (availableItems.value.length === 0) return;
    selectedItems.value.push(...availableItems.value);
    availableItems.value = [];
};

const moveAllLeft = () => {
    const nonMandatory = selectedItems.value.filter(
        (it) => !isMandatoryItem(it),
    );
    if (nonMandatory.length > 0) availableItems.value.push(...nonMandatory);
    selectedItems.value = selectedItems.value.filter((it) =>
        isMandatoryItem(it),
    );
};

const handleWebikeLogoChange = (event) => {
    const file = event.target.files[0];
    if (file) {
        form.webike_logo_url = file;
        webikeLogoName.value = file.name;
    }
};

const handleAuthorizedSignatureChange = (event) => {
    const file = event.target.files[0];
    if (file) {
        form.authorized_signature_url = file;
        authorizedSignatureName.value = file.name;
    }
};

const countryOptions = computed(() =>
    (countries.value || []).map((name) => ({ label: name, value: name })),
);

const isThailand = computed(
    () => (form.ship_to_country || "").toLowerCase() == "thailand",
);

const provinceOptions = computed(() => {
    const provinces = [];
    (locations.value || []).forEach((l) => {
        if (l.province && !provinces.includes(l.province)) {
            provinces.push(l.province);
        }
    });
    return provinces.map((p) => ({ label: p, value: p }));
});

const districtOptions = computed(() => {
    if (!form.ship_to_state_province) return [];
    const districts = [];
    (locations.value || [])
        .filter((l) => l.province == form.ship_to_state_province)
        .forEach((l) => {
            if (l.district && !districts.includes(l.district)) {
                districts.push(l.district);
            }
        });
    return districts.map((d) => ({ label: d, value: d }));
});

const subdistrictOptions = computed(() => {
    if (!form.ship_to_state_province || !form.ship_to_city_municipality)
        return [];
    return (locations.value || [])
        .filter(
            (l) =>
                l.province === form.ship_to_state_province &&
                l.district === form.ship_to_city_municipality,
        )
        .map((l) => ({ label: l.subdistrict, value: l.subdistrict }));
});

const webikeDistrictOptions = computed(() => {
    if (!form.webike_state_province) return [];
    const districts = [];
    (locations.value || [])
        .filter((l) => l.province == form.webike_state_province)
        .forEach((l) => {
            if (l.district && !districts.includes(l.district)) {
                districts.push(l.district);
            }
        });
    return districts.map((d) => ({ label: d, value: d }));
});

const webikeSubdistrictOptions = computed(() => {
    if (!form.webike_state_province || !form.webike_city_municipality)
        return [];
    return (locations.value || [])
        .filter(
            (l) =>
                l.province === form.webike_state_province &&
                l.district === form.webike_city_municipality,
        )
        .map((l) => ({ label: l.subdistrict, value: l.subdistrict }));
});

const findPostalCode = (province, district, subdistrict) => {
    if (!province || !district || !subdistrict) return null;

    const location = (locations.value || []).find(l =>
        l.province === province &&
        l.district === district &&
        l.subdistrict === subdistrict
    );

    return location?.post_code || null;
};

const getOptions = (fieldKey) => {
    switch (fieldKey) {
        case "webike_state_province":
            return provinceOptions.value;
        case "webike_city_municipality":
            return webikeDistrictOptions.value;
        case "webike_district_sub_district":
            return webikeSubdistrictOptions.value;
        case "ship_to_country":
            return countryOptions.value;
        case "ship_to_state_province":
            return provinceOptions.value;
        case "ship_to_city_municipality":
            return districtOptions.value;
        case "ship_to_district_sub_district":
            return subdistrictOptions.value;
        default:
            return [];
    }
};

const webikeFieldColumns = computed(() => {
    return [webikeFields.slice(0, 5), webikeFields.slice(5)];
});

const shipToFieldColumns = computed(() => {
    return [shipToFields.slice(0, 4), shipToFields.slice(4)];
});

watch(
    () => form.ship_to_country,
    () => {
        if (isLoadingData.value) return;
        form.ship_to_state_province = "";
        form.ship_to_city_municipality = "";
        form.ship_to_district_sub_district = "";
    },
);

watch(
    () => form.ship_to_state_province,
    () => {
        if (isLoadingData.value) return;
        if (!isThailand.value) return;
        form.ship_to_city_municipality = "";
        form.ship_to_district_sub_district = "";
        form.ship_to_postal_code_zip_code = "";
    },
);

watch(
    () => form.ship_to_city_municipality,
    () => {
        if (isLoadingData.value) return;
        if (!isThailand.value) return;
        form.ship_to_district_sub_district = "";
        form.ship_to_postal_code_zip_code = "";
    },
);

watch(
    () => form.webike_state_province,
    () => {
        if (isLoadingData.value) return;
        form.webike_city_municipality = "";
        form.webike_district_sub_district = "";
        form.webike_postal_code_zip_code = "";
    },
);

watch(
    () => form.webike_city_municipality,
    () => {
        if (isLoadingData.value) return;
        form.webike_district_sub_district = "";
        form.webike_postal_code_zip_code = "";
    },
);

watch(
    () => form.ship_to_district_sub_district,
    () => {
        if (isLoadingData.value) return;
        if (!isThailand.value) return;

        const postalCode = findPostalCode(
            form.ship_to_state_province,
            form.ship_to_city_municipality,
            form.ship_to_district_sub_district
        );

        if (postalCode) {
            form.ship_to_postal_code_zip_code = postalCode;
        }
    },
);

watch(
    () => form.webike_district_sub_district,
    () => {
        if (isLoadingData.value) return;

        const postalCode = findPostalCode(
            form.webike_state_province,
            form.webike_city_municipality,
            form.webike_district_sub_district
        );

        if (postalCode) {
            form.webike_postal_code_zip_code = postalCode;
        }
    },
);


const fetchTemplateData = async (templateId) => {
    try {
        isLoading.value = true;
        isLoadingData.value = true;

        const response = await axios.put(
            route("purchase-template.edit", templateId),
        );
        const templateData = response.data.data;

        Object.keys(form.data()).forEach((key) => {
            form[key] = templateData[key];
        });

        await nextTick();

        if (templateData.webike_logo_url) {
            const url = templateData.webike_logo_url || "";
            webikeLogoName.value =
                url.split("/").pop()?.split("?")[0] || "webike_logo";
        }

        if (templateData.authorized_signature_url) {
            const url = templateData.authorized_signature_url || "";
            authorizedSignatureName.value =
                url.split("/").pop()?.split("?")[0] || "authorized_signature";
        }

        const { available, selected } = initializeItems();
        availableItems.value = [...available];
        selectedItems.value = [...selected];

        if (templateData.items) {
            const savedItems = templateData.items;

            if (Array.isArray(savedItems) && savedItems.length > 0) {
                const savedMappings = savedItems.map((item) => item.mapping);

                availableItems.value = availableItems.value.filter(
                    (item) => !savedMappings.includes(item.mapping),
                );

                selectedItems.value = [...savedItems];
                form.items = [...savedItems];
            } else {
                selectedItems.value = [...mandatoryItems];
                form.items = [...mandatoryItems];
            }
        } else {
            selectedItems.value = [...mandatoryItems];
            form.items = [...mandatoryItems];
        }
    } catch (error) {
        emit("close");
        window.notyf.error("A server error occurred. Please contact the dev team to support you.");
    } finally {
        isLoading.value = false;
        isLoadingData.value = false;
    }
};

const submit = () => {
    if (props.mode == "add") {
        form.post(route("purchase-template.store"), {
            forceFormData: true,
            onSuccess: (response) => {
                form.reset();
                webikeLogoName.value = null;
                authorizedSignatureName.value = null;
                emit("close");
                Swal.fire({
                    icon: "success",
                    title: response.props.flash.message.title,
                    text: response.props.flash.message.messages,
                });
            },
        });
    } else if (props.mode == "edit") {
        form.post(route("purchase-template.update", props.templateId), {
            preserveScroll: true,
            onSuccess: (response) => {
                form.reset();
                webikeLogoName.value = null;
                authorizedSignatureName.value = null;
                emit("close");
                Swal.fire({
                    icon: "success",
                    title: response.props.flash.message.title,
                    text: response.props.flash.message.messages,
                });
            },
        });
    }
};

watch(
    selectedItems,
    (val) => {
        form.items = [...val];
    },
    { deep: true },
);

watch(
    () => props.templateId,
    (newId) => {
        form.clearErrors();
        if (props.mode == "edit" && newId) {
            fetchTemplateData(newId);
        }
        if (props.mode == "add") {
            form.reset();
            const { available, selected } = initializeItems();
            availableItems.value = [...available];
            selectedItems.value = [...selected];
            form.items = [...selected];
            webikeLogoName.value = 'Webike_Thailand.jpg';
            authorizedSignatureName.value = null;
        }
    },
    { immediate: false },
);
</script>

<style scoped>
.item-row[draggable="false"] {
    -webkit-user-drag: none !important;
    -moz-user-drag: none !important;
    -ms-user-drag: none !important;
}

.item-row[draggable="false"] * {
    -webkit-user-drag: none !important;
}

.item-row[draggable="true"]:active {
    cursor: grabbing;
}

.item-row.dragging {
    opacity: 0.4 !important;
    transform: scale(0.95) !important;
    z-index: 999;
}
</style>
