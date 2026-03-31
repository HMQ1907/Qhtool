<template>
    <header class="relative z-10">
        <nav class="w-full bg-white shadow-md">
            <div class="mx-auto py-2 px-3">
                <div class="relative flex items-center justify-between">
                    <div class="flex gap-6 items-center">
                        <a class="flex flex-shrink-0 items-center">
                            <img class="size-10 w-auto" :src="'/images/icons/PMS128.ico'" alt="PMS Logo" />
                        </a>
                        <ul class="flex gap-4">
                            <template v-for="(item, index) in menu" :key="'menu_' + index">
                                <li class="dropdown p-2 rounded-lg transition-colors duration-300 relative" :class="[
                                    checkActiveMenu(item, $route())
                                        ? 'bg-primary-color text-white menu-active font-bold'
                                        : 'hover:bg-primary-color/20 text-gray-900 has-[.menu-active]:!bg-primary-color has-[.menu-active]:!text-white has-[.menu-active]:font-bold',
                                ]">
                                    <template v-if="item.children.length === 0">
                                        <span v-if="$route().current() === item.name" class="flex gap-2 items-center">
                                            <i v-if="item.icon" :class="item.icon"></i>
                                            <img v-if="item.image" :src="item.image" alt="menu-icon"
                                                class="size-5 w-auto" />
                                            <span>{{ item.title }}</span>
                                        </span>
                                        <Link v-else :href="$route(item.name, item.query)"
                                            class="flex gap-2 items-center after:absolute after:inset-0">
                                        <i v-if="item.icon" :class="item.icon"></i>
                                        <img v-if="item.image" :src="item.image" alt="menu-icon"
                                            class="size-5 w-auto" />
                                        <span>{{ item.title }}</span>
                                        </Link>
                                    </template>
                                    <div v-else>
                                        <div class="flex gap-2 items-center relative cursor-pointer"
                                            data-bs-toggle="dropdown" aria-expanded="false">
                                            <i v-if="item.icon" :class="item.icon"></i>
                                            <img v-else :src="checkActiveMenu(
                                                item,
                                                $route(),
                                            )
                                                ? item.image.replace(
                                                    '-black',
                                                    '-white',
                                                )
                                                : item.image
                                                " alt="menu-icon" class="size-5 w-auto" />
                                            <span>{{ item.title }}</span>
                                            <i class="fa-solid fa-caret-down"></i>
                                        </div>
                                        <SubMenu :menu="item.children" />
                                    </div>
                                </li>
                            </template>
                        </ul>
                    </div>
                    <UserMenu :user="usePage().props.user" :zeroUrl="usePage().props.zms_url"
                        :zero_url="usePage().props.zero_url" />
                </div>
            </div>
        </nav>
    </header>
</template>
<script setup>
import { Link, usePage } from "@inertiajs/vue3";
import UserMenu from "./Header/UserMenu.vue";
import SubMenu from "./Header/SubMenu.vue";
import { checkActiveMenu } from "@/Composables/helpers.js";

const props = defineProps(["menu"]);
</script>
