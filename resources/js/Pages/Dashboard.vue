<template>
    <div>
        <div class="flex justify-between items-center">
        </div>
    </div>
</template>
<script setup>
import Layout from "@/Components/Layout.vue";

defineOptions({
    layout: Layout,
});
const users = defineProps(["users"]);
</script>
