<template>
    <ul class="dropdown-menu z-10 hidden rounded-md py-2 bg-white shadow-lg cursor-pointer border border-gray-200">
        <li v-for="(item, index) in menu" :key="'sub_menu_' + index"
            class="px-3 py-1.5 transition-colors duration-300 relative" :class="[
                checkActiveMenu(item, $route())
                    ? 'bg-primary-color text-white menu-active font-bold'
                    : 'hover:bg-primary-color/20 text-gray-900 font-normal has-[.menu-active]:!bg-primary-color has-[.menu-active]:!text-white',
            ]">
            <template v-if="item.children.length === 0">
                <span v-if="$route().current() === item.name" class="flex gap-2 items-center">
                    <i v-if="item.icon" :class="item.icon"></i>
                    <img v-if="item.image" :src="item.image" alt="menu-icon" class="size-5 w-auto" />
                    <span>{{ item.title }}</span>
                </span>
                <Link v-else :href="$route(item.name, item.query)"
                    class="flex gap-2 items-center after:absolute after:inset-0">
                <i v-if="item.icon" :class="item.icon"></i>
                <img v-if="item.image" :src="item.image" alt="menu-icon" class="size-5 w-auto" />
                <span>{{ item.title }}</span>
                </Link>
            </template>
            <div v-else>
                <div class="flex gap-2 items-center dropdown relative" data-bs-toggle="dropdown" aria-expanded="false">
                    <i :class="item.icon"></i>
                    <span>{{ item.title }}</span>
                    <i class="fa-solid fa-caret-right"></i>
                </div>
                <SubMenu :menu="item.children" />
            </div>
        </li>
    </ul>
</template>
<script setup>
import { Link } from "@inertiajs/vue3";
import { checkActiveMenu } from "@/Composables/helpers.js";

const props = defineProps(["menu"]);
</script>

<style scoped>
.dropdown-menu {
    top: 12px !important;
    left: -8px !important;
}
</style>
