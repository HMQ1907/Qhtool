<template>
    <div class="relative w-full" ref="root">
        <button
            type="button"
            class="w-full flex items-center justify-between border border-gray-300 rounded-md px-3 py-2 text-sm hover:border-gray-400 focus:outline-none focus:ring-0 bg-white"
            @click="toggle"
        >
            <span :class="{ 'text-gray-500': !selectedLabel }">
                {{ selectedLabel || placeholder }}
            </span>
            <i class="fa-solid fa-caret-down"></i>
        </button>

        <div
            v-if="open"
            class="absolute z-50 mt-1 w-full bg-white border border-gray-300 rounded-md shadow-lg max-h-64 overflow-auto"
        >
            <div class="p-2 sticky top-0 bg-white border-b border-gray-200">
                <input
                    v-model="searchQuery"
                    type="text"
                    :placeholder="searchPlaceholder"
                    class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-0"
                />
            </div>
            <div
                v-for="opt in filteredOptions"
                :key="getValue(opt)"
                @click="select(getValue(opt))"
                :class="[
                    'px-3 py-2 text-sm text-gray-700 cursor-pointer hover:bg-primary-color/15',
                    modelValue == getValue(opt)
                        ? 'bg-primary-color/10 text-primary-color/80 font-medium'
                        : '',
                ]"
            >
                {{ getLabel(opt) }}
            </div>
            <div
                v-if="filteredOptions.length === 0"
                class="px-3 py-2 text-sm text-gray-400"
            >
                No results
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from "vue";

const props = defineProps({
    modelValue: {
        type: [String, Number, null],
        default: "",
    },
    options: {
        type: Array,
        default: () => [],
    },
    optionKey: {
        type: String,
        default: "value",
    },
    optionLabel: {
        type: String,
        default: "label",
    },
    placeholder: {
        type: String,
        default: "Select...",
    },
    searchable: {
        type: Boolean,
        default: true,
    },
    searchPlaceholder: {
        type: String,
        default: "Type to search...",
    },
});

const emit = defineEmits(["update:modelValue", "change"]);

const open = ref(false);
const root = ref(null);
const searchQuery = ref("");

const getValue = (opt) => opt?.[props.optionKey];
const getLabel = (opt) => opt?.[props.optionLabel] ?? String(getValue(opt));

const selectedLabel = computed(() => {
    const found = props.options.find((o) => getValue(o) == props.modelValue);
    return found ? getLabel(found) : "";
});

const toggle = () => (open.value = !open.value);

const select = (value) => {
    emit("update:modelValue", value);
    emit("change", value);
    open.value = false;
};

const filteredOptions = computed(() => {
    if (!props.searchable) return props.options;
    const q = searchQuery.value.trim().toLowerCase();
    if (!q) return props.options;
    return props.options.filter((opt) =>
        String(getLabel(opt)).toLowerCase().includes(q),
    );
});

const onClickOutside = (e) => {
    if (!root.value) return;
    if (!root.value.contains(e.target)) {
        open.value = false;
    }
};

onMounted(() => {
    window.addEventListener("click", onClickOutside);
});

onBeforeUnmount(() => {
    window.removeEventListener("click", onClickOutside);
});
</script>
