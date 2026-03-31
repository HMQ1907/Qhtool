<template>
    <div class="relative flex gap-4 items-center">
        <button
            type="button"
            class="flex items-center gap-2 text-sm font-medium text-gray-700 hover:text-gray-900 focus:outline-none"
            :class="{
                'animate-bounce duration-100': isAppDropdownOpen,
            }"
            @click="toggleAppDropdown"
        >
            <div class="flex items-center gap-2 cursor-pointer">
                <img
                    :src="'/images/svg/app.svg'"
                    alt="user-menu"
                    class="w-10"
                />
            </div>
        </button>
        <div
            v-if="isAppDropdownOpen"
            class="absolute right-30 z-50 mt-43 min-w-72 min-h-24 rounded-md bg-white ring-1 ring-black ring-opacity-5 focus:outline-none border border-slate-200 p-[12px]"
            @click.stop
        >
            <div class="flex justify-center gap-1">
                <div
                    v-for="(item, index) in appDropdown"
                    :key="index"
                    class="hover:bg-primary-color/30 w-20 h-20 rounded-md flex items-center justify-center transition-colors duration-100 mx-2"
                    :class="{
                        'bg-primary-color/30': item.active,
                    }"
                >
                    <a :href="item.url" target="_blank">
                        <img
                            class="size-13"
                            :src="item.image"
                            alt="user-menu"
                        />
                    </a>
                </div>
            </div>
        </div>
        <button
            type="button "
            class="flex items-center gap-2 cursor-pointer"
            @click="toggleUserMenu"
        >
            <span class="me-2">{{ user?.name }}</span>
            <img
                class="size-10 rounded border border-slate-300"
                :src="avatarUrl"
                alt="avatar"
            />
            <i
                class="fa-solid fa-caret-down"
                :class="{
                    'rotate-180': isUserMenuOpen,
                }"
            ></i>
        </button>

        <div
            v-if="isUserMenuOpen"
            class="absolute right-0 z-50 mt-56 min-w-72 origin-top-right divide-y divide-gray-200 rounded-md !border !border-slate-300 bg-white py-3 shadow-lg focus:outline-none"
        >
            <div class="flex gap-2 px-2 pb-3">
                <img
                    :src="avatarUrl"
                    class="size-12 rounded border border-slate-300"
                    alt="avatar"
                />
                <div class="flex flex-col">
                    <span>{{ user?.name }}</span>
                    <span class="text-sm">
                        {{ user?.email }}
                    </span>
                </div>
            </div>
            <div>
                <a
                    target="_blank"
                    class="block p-2 transition duration-300"
                    :class="[
                        'hover:bg-primary-color/20',
                        $route().current('iam.change_password')
                            ? '!bg-primary-color !text-white'
                            : '',
                    ]"
                    :href="
                        zero_url +
                        '/change-password?redirect=' +
                        usePage().props.pms_url
                    "
                >
                    Change Password
                </a>
            </div>
            <div class="px-2 pt-2">
                <button
                    type="button"
                    class="relative flex w-full items-baseline justify-center gap-2 rounded bg-red-500 p-1 text-sm text-white focus:outline-none cursor-pointer transition duration-300 hover:bg-red-600"
                    @click="logout"
                >
                    <span>Logout</span>
                    <i class="fas fa-sign-out-alt"></i>
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from "vue";
import { usePage } from "@inertiajs/vue3";

const isAppDropdownOpen = ref(false);

const isUserMenuOpen = ref(false);

const props = defineProps({
    user: {
        type: Object,
        required: true,
    },
    zeroUrl: {
        type: String,
        required: false,
    },
    zero_url: {
        type: String,
        required: true,
    },
});

const logout = () => {
    window.location.href = props.zero_url + "/logout";
};

const avatarUrl = ref("/images/avatar.jpg");

const appDropdown = ref(usePage().props.apps ?? []);

const toggleAppDropdown = (event) => {
    event.stopPropagation();
    isAppDropdownOpen.value = !isAppDropdownOpen.value;
    if (isAppDropdownOpen.value) {
        isUserMenuOpen.value = false;
    }
};

const toggleUserMenu = (event) => {
    event.stopPropagation();
    isUserMenuOpen.value = !isUserMenuOpen.value;
    if (isUserMenuOpen.value) {
        isAppDropdownOpen.value = false;
    }
};

const handleClickOutside = (event) => {
    const dropdown = document.querySelector(".dropdown");
    if (dropdown && !dropdown.contains(event.target)) {
        isAppDropdownOpen.value = false;
        isUserMenuOpen.value = false;
    }
};

onMounted(() => {
    document.addEventListener("click", handleClickOutside);
});

onUnmounted(() => {
    document.removeEventListener("click", handleClickOutside);
});
</script>
