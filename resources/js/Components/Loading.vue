<template>
    <div
        class="fixed z-50 flex items-center justify-center overflow-hidden w-full h-screen top-0 bg-white/50"
        :class="[isLoading ? 'block' : 'hidden']"
    >
        <div class="flex items-center">
            <img :src="'/images/icons/cat-loading.gif'" alt="loading" />
        </div>
    </div>
</template>
<script setup>
defineProps(["isLoading"]);
</script>
