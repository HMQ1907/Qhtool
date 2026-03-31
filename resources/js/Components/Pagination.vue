<template>
    <nav aria-label="Page navigation">
        <div class="flex justify-between">
            <div class="flex items-center gap-4">
                <ul class="flex items-center gap-3">
                    <template v-for="(link, key) in list.links">
                        <div
                            v-if="link.url === null"
                            :key="key + '-disabled'"
                            class="flex items-center justify-center h-8 w-8 text-gray-300 cursor-default"
                        >
                            <i
                                v-if="key === 0"
                                class="fa-solid fa-angle-left"
                            ></i>
                            <i
                                v-else-if="key === list.links.length - 1"
                                class="fa-solid fa-angle-right"
                            ></i>
                            <span
                                v-else
                                v-html="link.label"
                                class="text-gray-700"
                            ></span>
                        </div>
                        <div
                            v-else-if="link.url !== null && !link.active"
                            :key="key"
                            :class="'flex items-center justify-center h-8 w-8  rounded cursor-pointer text-gray-700 hover:bg-primary-color/10'"
                            @click="changePageByUrl(link.url)"
                        >
                            <i
                                v-if="key === 0"
                                class="fa-solid fa-angle-left"
                            ></i>
                            <i
                                v-else-if="key === list.links.length - 1"
                                class="fa-solid fa-angle-right"
                            ></i>
                            <span v-else v-html="link.label"></span>
                        </div>
                        <div
                            v-else
                            :key="key + '-active'"
                            :class="'flex items-center justify-center h-10 w-9  rounded bg-primary-color text-white'"
                        >
                            <span v-html="link.label"></span>
                        </div>
                    </template>
                </ul>
                <input
                    type="number"
                    class="border border-gray-300 text-gray-900 rounded block h-8 w-16 py-1 px-2 focus:outline-none focus:ring-0 focus:border-primary-color"
                    :min="1"
                    :max="list.last_page"
                    @change="changePage"
                    v-model="page"
                />
                <div class="flex items-center">
                    <p class="text-gray-700 leading-5">
                        <span>Showing </span>
                        <span class="font-medium">{{
                            $filters.formatNumber(list.from)
                        }}</span>
                        <span> to </span>
                        <span class="font-medium">{{
                            $filters.formatNumber(list.to)
                        }}</span>
                        <span> of </span>
                        <span class="font-medium">{{
                            $filters.formatNumber(list.total)
                        }}</span>
                        <span> results</span>
                    </p>
                </div>
            </div>
            <div class="h-8 border-r-2 border-gray-700 mx-4 mt-1"></div>
            <div class="flex items-center gap-2">
                <p>Records</p>
                <select
                    id="small"
                    class="block w-18 h-8 px-2 py-1 text-gray-900 border border-gray-300 rounded focus:outline-none focus:ring-0 focus:border-primary-color"
                    @change="changePerPage"
                    v-model="perPage"
                >
                    <option value="50" selected>50</option>
                    <option value="100">100</option>
                    <option value="500">500</option>
                </select>
            </div>
        </div>
    </nav>
</template>
<script setup>
import { router } from "@inertiajs/vue3";
import { ref, inject } from "vue";

const props = defineProps(["list"]);
const isLoading = inject("isLoading");

function changePageByUrl(url) {
    const currentParams = route().params;
    const urlParams = new URLSearchParams(url.split("?")[1]);

    const mergedParams = {
        ...currentParams,
        ...Object.fromEntries(urlParams.entries()),
    };

    router.visit(route(route().current(), mergedParams), {
        onBefore: () => {
            isLoading.value = true;
        },
        onFinish: () => {
            isLoading.value = false;
        },
    });
}

const page = ref(props.list.current_page);

function changePage() {
    const params = { ...route().params };
    let newPage = page.value;

    if (newPage > props.list.last_page) {
        newPage = props.list.last_page;
    } else if (newPage < 1) {
        newPage = 1;
    }

    params.page = newPage;

    router.visit(route(route().current(), params), {
        onBefore: () => {
            isLoading.value = true;
        },
        onFinish: () => {
            isLoading.value = false;
        },
    });
}

const perPage = ref(props.list.per_page || 20);

function changePerPage() {
    const params = { ...route().params };
    params.per_page = perPage.value;
    params.page = 1;

    router.visit(route(route().current(), params), {
        onBefore: () => {
            isLoading.value = true;
        },
        onFinish: () => {
            isLoading.value = false;
        },
    });
}
</script>
