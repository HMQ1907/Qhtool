<template>
    <div class="py-6 flex flex-col gap-3">
        <Breadcrumb :name="'Export'" />
        <div class="flex justify-center">
            <div class="flex flex-col justify-center">
                <div
                    class="h-[70vh] w-[70vw] bg-white rounded-lg border border-gray-200"
                >
                    <div v-if="!isError" class="flex flex-col gap-4">
                        <div class="text-center text-3xl font-bold mt-4">
                            <p>Our data is downloading ...</p>
                            <p>Please do not close tab during this process.</p>
                            <p>
                                Next download request will start after this
                                process is completed.
                            </p>
                        </div>
                        <div class="flex justify-center">
                            <img
                                class="size-96"
                                :src="'/images/export-cat-loading.gif'"
                                alt="Export Cat Loading"
                            />
                        </div>
                        <div class="flex justify-center">
                            <div
                                class="w-[50%] bg-gray-200 rounded-full dark:bg-gray-700 overflow-hidden"
                            >
                                <div
                                    class="bg-primary-color text-sm text-white text-center pt-0.5 leading-none rounded-full transition-all duration-500 overflow-hidden"
                                    :style="'width: ' + process + '%'"
                                >
                                    {{ process }}%
                                </div>
                            </div>
                        </div>
                    </div>
                    <div v-else class="flex flex-col gap-4">
                        <div
                            class="text-center text-3xl font-bold mt-4 text-red-500"
                        >
                            <p>Download process is error ...</p>
                            <p>
                                Please refresh page (F5) or contact Admin for
                                support.
                            </p>
                        </div>
                        <div class="flex justify-center">
                            <img
                                class="size-96"
                                :src="'/images/885f340cd15cc57e192b56ccb5546d27.gif'"
                                alt="Error Cat Loading"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script setup>
import { onUnmounted, ref } from "vue";
import axios from "axios";
import Layout from "@/Components/Layout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";

defineOptions({
    layout: Layout,
});

const props = defineProps(["filePath", "totalRecords"]);

const isError = ref(false);
const polling = ref(true);
const process = ref(0);

onUnmounted(() => {
    polling.value = false;
});

const poll = async () => {
    if (!polling.value) return;
    try {
        await axios
            .get(
                route("check-file-exist", {
                    filePath: props.filePath,
                    total: props.totalRecords,
                }),
            )
            .then((response) => {
                if (response.data.data.is_exists) {
                    process.value = 100;
                    polling.value = false;
                    window.location.replace(
                        route("download-file", {
                            filePath: response.data.data.file_path,
                        }),
                    );
                    setTimeout(() => {
                        window.close();
                    }, 3000);
                } else if (response.data.data.process) {
                    process.value = response.data.data.process;
                }
            })
            .catch((error) => {
                isError.value = true;
                console.log(error);
            });
    } catch (error) {
        console.log(error);
    } finally {
        setTimeout(poll, 5000);
    }
};

setTimeout(poll, 1000);
</script>
