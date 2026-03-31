<template>
    <main>
        <VHead />
        <Header :menu v-show="!hideHeader" />
        <main
            class="min-h-[calc(100vh_-_120px)] grid bg-[url(../../public/images/BG.png)]"
        >
            <slot />
        </main>
        <Footer />
        <Loading :isLoading />
    </main>
</template>

<script setup>
import { ref, provide, onMounted } from "vue";
import { router } from "@inertiajs/vue3";
import VHead from "./Head.vue";
import Header from "./Header.vue";
import Footer from "./Footer.vue";
import Loading from "./Loading.vue";

const isLoading = ref(false);
const hideHeader = ref(false);
defineProps(["menu"]);
provide("isLoading", isLoading);
provide("hideHeader", hideHeader);
onMounted(() => {
    router.on("start", () => {
        isLoading.value = true;
    });
    router.on("finish", () => {
        isLoading.value = false;
    });
    router.on("error", () => {
        isLoading.value = false;
    });
});
</script>
