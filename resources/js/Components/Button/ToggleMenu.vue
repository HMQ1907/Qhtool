<template>
    <button
        class="hover:bg-primary-color/20 size-10 rounded-full flex items-center justify-center cursor-pointer"
        @click="toggleHiddenMenu"
    >
        <i
            class="pt-0.5 text-xl"
            :class="
                state.isHiddenMenu
                    ? 'fa-solid fa-angles-down'
                    : 'fa-solid fa-angles-up'
            "
        ></i>
    </button>
</template>

<script setup>
import { state, toggleHiddenMenu } from "@/Composables/helpers";
</script>
