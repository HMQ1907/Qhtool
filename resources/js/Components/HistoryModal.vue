<template>
    <Modal :show="show" :size="'7xl'" @close="$emit('close')">
        <template v-slot:header>
            <h3 class="text-2xl font-semibold text-gray-900 dark:text-white">
                History
            </h3>
        </template>
        <template v-slot:body>
            <ModalLoading :isModalLoading="isModalLoading" />
            <div class="p-4 border-b">
                <Pagination :list="list" @changePageByUrl="changePageByUrl" @changePage="changePage"
                    @changePerPage="changePerPage" class="mb-3" />
                <VTable :list="list" />
                <Pagination :list="list" @changePageByUrl="changePageByUrl" @changePage="changePage"
                    @changePerPage="changePerPage" class="mt-3" />
            </div>
        </template>
        <template v-slot:footer>
            <div class="flex justify-end p-4">
                <button type="button" @click="$emit('close')"
                    class="flex items-center justify-center bg-gray-500 text-white py-2 px-4 rounded-lg hover:bg-gray-600 transition duration-500 ease-in-out cursor-pointer">
                    <i class="fas fa-times mr-1"></i>
                    Cancel
                </button>
            </div>
        </template>
    </Modal>
</template>

<script setup>
import { watch, ref } from "vue";
import axios from "axios";
import Modal from "@/Components/Modal.vue";
import VTable from "./HistoryModal/Table.vue";
import Pagination from "./HistoryModal/Table/Pagination.vue";
import ModalLoading from "./ModalLoading.vue";

const props = defineProps(["show", "page"]);

const list = ref(null);
const isModalLoading = ref(false);

watch(
    () => props.show,
    (newVal) => {
        if (newVal) {
            getHistories(route("history.index", { module: props.page }));
        }
    },
);

const changePageByUrl = (url) => {
    getHistories(url);
};

const changePage = (page) => {
    getHistories(
        route("history.index", {
            page: page,
            per_page: list.value.per_page,
            module: props.page,
        }),
    );
};

const changePerPage = (perPage) => {
    getHistories(
        route("history.index", {
            per_page: perPage,
            module: props.page,
        }),
    );
};

const getHistories = (route) => {
    isModalLoading.value = true;
    axios
        .get(route)
        .then((response) => {
            list.value = response.data.data;
            isModalLoading.value = false;
        })
        .catch((error) => {
            window.notyf.error("A server error occurred. Please contact the dev team to support you.");
            isModalLoading.value = false;
        });
};
</script>
