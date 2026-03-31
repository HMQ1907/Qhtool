<template>
    <div
        class="min-h-screen bg-gray-50 flex flex-col items-center justify-center p-5"
    >
        <div
            class="max-w-4xl w-full bg-white rounded-lg shadow-lg overflow-hidden"
        >
            <div class="flex flex-col md:flex-row">
                <div
                    class="bg-[#f58220]/10 p-8 flex items-center justify-center md:w-1/2"
                >
                    <div class="text-center">
                        <h1 class="text-9xl font-bold text-[#f58220]">404</h1>
                        <p class="text-2xl font-semibold text-gray-700 mt-4">
                            Page Not Found
                        </p>
                    </div>
                </div>

                <div class="p-8 md:w-1/2 flex flex-col justify-center">
                    <div class="mb-8">
                        <h2 class="text-2xl font-bold text-gray-800 mb-4">
                            Oops! We couldn't find that page.
                        </h2>
                        <p class="text-gray-600">
                            The page you are looking for might have been
                            removed, had its name changed, or is temporarily
                            unavailable.
                        </p>
                    </div>

                    <div class="space-y-4">
                        <Link
                            href="/"
                            class="block w-full bg-[#f58220] hover:bg-[#f58220]/90 text-white font-medium py-3 px-4 rounded text-center transition duration-200"
                        >
                            <i class="fa-solid fa-home mr-2"></i> Go to Homepage
                        </Link>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-8 text-center text-gray-500 text-sm">
            <p>If you believe this is an error, please contact support.</p>
        </div>
    </div>
</template>

<script setup>
import { Link } from "@inertiajs/vue3";
import Layout from "@/Components/Layout.vue";

defineOptions({
    layout: Layout,
});
</script>
