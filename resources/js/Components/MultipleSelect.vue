<template>
    <div
        class="relative w-full max-w-md"
        v-click-outside="() => (dropdownOpen = false)"
    >
        <div
            class="flex items-center flex-wrap gap-2 min-h-[2.4rem] w-full border border-gray-300 rounded-md px-3 py-[5px] text-sm cursor-pointer focus-within:ring focus-within:ring-primary-color"
            @click="toggleDropdown"
            tabindex="0"
        >
            <!-- Tags -->
            <template v-if="selectedKeys.length">
                <span
                    v-for="(key, index) in selectedKeys"
                    :key="key"
                    class="flex items-center px-2 py-0.5 bg-primary-color/20 text-primary-color rounded-md border border-primary-color/50"
                >
                    {{ getLabelByKey(key) }}
                    <button
                        @click.stop="removeByIndex(index)"
                        class="ml-1 text-primary-color/70 hover:text-primary-color"
                    >
                        &times;
                    </button>
                </span>
            </template>

            <!-- Placeholder -->
            <span v-else class="text-base">{{ placeHolder }}</span>

            <!-- Dropdown Arrow -->
            <div class="ml-auto text-gray-500">
                <svg
                    class="w-4 h-4"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="3"
                        d="M19 9l-7 7-7-7"
                    />
                </svg>
            </div>
        </div>

        <!-- Dropdown Menu -->
        <div
            v-show="dropdownOpen"
            class="absolute z-50 mt-1 w-full bg-white border border-gray-300 rounded-md shadow-lg"
            :style="{ 'max-height': '300px', 'overflow-y': 'auto' }"
        >
            <div class="flex flex-col max-h-64">
                <!-- Search Input -->
                <div
                    class="p-2 border-b border-gray-300 shrink-0 bg-white sticky top-0 z-10"
                >
                    <input
                        v-model="searchTerm"
                        type="text"
                        placeholder="Search..."
                        class="w-full px-2 py-1 text-sm rounded-md border-gray-300 focus:outline-none focus:ring-0 focus:ring-primary-color"
                        @click.stop
                    />
                </div>

                <!-- Option List -->
                <div class="overflow-auto flex-grow">
                    <div v-if="filteredOptions.length">
                        <div
                            v-for="option in filteredOptions"
                            :key="getKey(option)"
                            @click="toggle(getKey(option))"
                            :class="[
                                'flex justify-between items-center px-4 py-2 text-sm cursor-pointer hover:bg-primary-color/5',
                                isSelected(getKey(option))
                                    ? 'bg-primary-color/10 text-primary-color/80 font-medium'
                                    : '',
                            ]"
                        >
                            <span>{{ getLabel(option) }}</span>
                            <span
                                v-if="isSelected(getKey(option))"
                                class="text-xs text-primary-color/60 italic"
                                >Selected</span
                            >
                        </div>
                    </div>
                    <div v-else class="px-4 py-2 text-gray-400 text-sm italic">
                        Not found...
                    </div>
                </div>

                <!-- Warning -->
                <div
                    v-if="limitReached"
                    class="px-4 py-2 text-red-600 text-xs bg-red-50 border-t border-red-200 shrink-0 sticky bottom-0"
                >
                    Maximum of {{ props.maxSelected }} options selected. First
                    remove a selected option to select another.
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, watch, computed } from "vue";

const props = defineProps({
    modelValue: {
        type: Array,
        default: () => [],
    },
    options: {
        type: Array,
        default: () => [],
    },
    placeHolder: {
        type: String,
        default: "Placeholder",
    },
    maxSelected: {
        type: Number,
        default: 99,
    },
    optionKey: {
        type: String,
        default: "key",
    },
    optionLabel: {
        type: String,
        default: "name",
    },
});
const emit = defineEmits(["update:modelValue"]);

const selectedKeys = ref([...props.modelValue]);
const dropdownOpen = ref(false);
const searchTerm = ref("");

watch(
    () => props.modelValue,
    (val) => {
        selectedKeys.value = Array.isArray(val) ? [...val] : [];
    },
);

const toggleDropdown = () => {
    dropdownOpen.value = !dropdownOpen.value;
};

const isSelected = (key) => selectedKeys.value.includes(key);

const toggle = (key) => {
    const index = selectedKeys.value.indexOf(key);
    if (index === -1) {
        if (selectedKeys.value.length >= props.maxSelected) return;
        selectedKeys.value.push(key);
    } else {
        selectedKeys.value.splice(index, 1);
    }
    emit("update:modelValue", selectedKeys.value);
    searchTerm.value = "";
};

const removeByIndex = (index) => {
    selectedKeys.value.splice(index, 1);
    emit("update:modelValue", selectedKeys.value);
};

const filteredOptions = computed(() =>
    props.options.filter((opt) =>
        opt[props.optionLabel]
            .toLowerCase()
            .includes(searchTerm.value.trim().toLowerCase())
    )
);

const limitReached = computed(
    () => selectedKeys.value.length >= props.maxSelected,
);

const getKey = (option) => {
    return (
        option?.[props.optionKey] ??
        option?.code ??
        option?.id ??
        String(option)
    );
};

const getLabel = (option) => {
    return option?.[props.optionLabel] ?? String(getKey(option));
};

const getLabelByKey = (key) => {
    const found = props.options.find((opt) => getKey(opt) === key);
    return found ? getLabel(found) : String(key);
};
</script>
