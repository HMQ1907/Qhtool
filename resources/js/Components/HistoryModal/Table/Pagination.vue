<template>
    <nav aria-label="Page navigation" v-if="list">
        <div class="flex justify-between">
            <div class="flex gap-2">
                <ul
                    class="flex items-center -space-x-px h-8 text-sm shadow-md rounded"
                >
                    <template v-for="(link, key) in list.links">
                        <div
                            v-if="link.url === null"
                            :key="key + '-disabled'"
                            class="flex items-center justify-center px-3 h-8 leading-tight text-gray-500 bg-white border border-gray-300 text-opacity-50 first:rounded-s last:rounded-e"
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
                            v-else-if="link.url !== null && !link.active"
                            :key="key"
                            class="flex items-center justify-center px-3 h-8 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-[#F58220]/10 hover:text-[#F58220]/80 first:rounded-s last:rounded-e transition duration-200 ease-in-out cursor-pointer"
                            @click="$emit('changePageByUrl', link.url)"
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
                            class="flex items-center justify-center px-3 h-8 leading-tight text-[#F58220] border border-gray-300 bg-[#F58220]/20"
                        >
                            <span v-html="link.label"></span>
                        </div>
                    </template>
                </ul>
                <input
                    type="number"
                    class="border border-gray-300 text-gray-900 text-sm rounded block w-[65px] py-1 px-2 focus:outline-none focus:ring-0 focus:border-primary-color"
                    :min="1"
                    :max="list.last_page"
                    @change="changePage"
                    v-model="page"
                />
                <div class="flex items-center">
                    <p class="text-sm text-gray-700 leading-5">
                        <span>Showing </span>
                        <span class="font-medium">{{ list.from }}</span>
                        <span> to </span>
                        <span class="font-medium">{{ list.to }}</span>
                        <span> of </span>
                        <span class="font-medium">{{ list.total }}</span>
                        <span> results</span>
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <p>Records</p>
                <select
                    id="small"
                    class="block w-[55px] h-[32px] p-1 text-sm text-gray-900 border border-gray-300 rounded focus:outline-none focus:ring-0 focus:border-primary-color"
                    @change="$emit('changePerPage', $event.target.value)"
                    v-model="perPage"
                >
                    <option value="20" selected>20</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                    <option value="500">500</option>
                </select>
            </div>
        </div>
    </nav>
</template>

<script setup>
import { ref, watch } from "vue";

const props = defineProps(["list"]);

const emit = defineEmits(["changePageByUrl", "changePage", "changePerPage"]);

const page = ref(props.list ? props.list.current_page : 1);
const perPage = ref(props.list ? props.list.per_page : 20);

watch(
    () => props.list,
    (newVal) => {
        page.value = newVal.current_page;
        perPage.value = newVal.per_page;
    },
);

const changePage = () => {
    page.value =
        page.value > props.list.last_page
            ? props.list.last_page
            : page.value < 1
              ? 1
              : page.value;
    emit("changePage", page.value);
};
</script>
