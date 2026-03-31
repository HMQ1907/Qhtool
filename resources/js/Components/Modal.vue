<template>
    <transition name="modal">
        <div
            class="relative z-50"
            aria-labelledby="modal-title"
            aria-modal="true"
            v-show="props.show"
        >
            <div
                class="fixed inset-0 bg-gray-500 opacity-75"
                aria-hidden="true"
            ></div>
            <div class="fixed inset-0 z-50 w-screen overflow-y-auto">
                <div
                    class="flex min-h-full items-end justify-center text-center sm:items-center"
                >
                    <div
                        class="relative transform rounded-lg bg-white text-left shadow-xl sm:my-8"
                        :class="'w-' + props.size"
                    >
                        <div>
                            <div
                                class="flex items-center justify-between border-b border-gray-200 rounded-t-lg dark:border-gray-600 p-4"
                            >
                                <slot name="header"></slot>
                                <button
                                    type="button"
                                    class="cursor-pointer size-10 rounded-full text-gray-400 transition duration-500 ease-in-out hover:bg-gray-200 hover:text-gray-900"
                                    @click="$emit('close')"
                                >
                                    <i
                                        class="fa-solid fa-xmark align-middle text-lg"
                                    ></i>
                                </button>
                            </div>
                            <div class="">
                                <slot name="body"></slot>
                            </div>
                            <div>
                                <slot name="footer"></slot>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </transition>
</template>
<script setup>
import { watch } from "vue";

const props = defineProps(["show", "size"]);

watch(
    () => props.show,
    (newValue) => {
        if (newValue) {
            document.body.style.overflow = "hidden";
        } else {
            document.body.style.overflow = "";
        }
    },
);
</script>
<style scoped>
.modal-enter-active,
.modal-leave-active {
    transition:
        opacity 0.3s ease,
        transform 0.3s ease;
}

.modal-enter-from,
.modal-leave-to {
    opacity: 0;
}
</style>
