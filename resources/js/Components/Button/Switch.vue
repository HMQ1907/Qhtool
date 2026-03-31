<template>
    <div class="relative inline-flex items-center">
        <button @click="setLanguage('en')" :class="[
            'px-4 py-2 text-sm rounded-l-lg border cursor-pointer',
            language === 'en'
                ? 'bg-primary-color text-white border-primary-color'
                : 'bg-white text-gray-600 border-gray-300 hover:bg-gray-50'
        ]">
            EN
        </button>

        <button @click="setLanguage('th')" :class="[
            'px-4 py-2 text-sm rounded-r-lg border-l-0 border cursor-pointer',
            language === 'th'
                ? 'bg-primary-color text-white border-primary-color'
                : 'bg-white text-gray-600 border-gray-300 hover:bg-gray-50'
        ]">
            TH
        </button>
    </div>
</template>

<script setup>
import { ref } from 'vue'

const props = defineProps({
    modelValue: {
        type: String,
        default: 'en'
    }
})

const emit = defineEmits(['update:modelValue', 'language-changed'])

const language = ref(props.modelValue || 'en')

const setLanguage = (lang) => {
    language.value = lang
    emit('update:modelValue', lang)
    emit('language-changed', lang)
}
</script>
