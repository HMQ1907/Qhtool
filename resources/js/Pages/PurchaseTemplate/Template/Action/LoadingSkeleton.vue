<template>
    <div class="space-y-6">
        <div class="space-y-4">
            <div class="flex items-center gap-4">
                <div class="w-32 h-4 bg-gray-200 rounded animate-pulse"></div>
                <div class="flex-1 h-10 bg-gray-200 rounded animate-pulse"></div>
            </div>
            <div class="flex items-start gap-4">
                <div class="w-32 h-4 bg-gray-200 rounded animate-pulse"></div>
                <div class="flex-1 h-20 bg-gray-200 rounded animate-pulse"></div>
            </div>
        </div>

        <div class="relative border border-gray-300 rounded-lg p-6">
            <div class="absolute -top-3 left-4 bg-white px-2">
                <div class="w-16 h-6 bg-gray-200 rounded animate-pulse"></div>
            </div>
            <div class="grid grid-cols-2 gap-6">
                <div class="space-y-4">
                    <div v-for="i in 5" :key="i" class="flex items-center gap-4">
                        <div class="w-32 h-4 bg-gray-200 rounded animate-pulse"></div>
                        <div class="flex-1 h-10 bg-gray-200 rounded animate-pulse"></div>
                    </div>
                </div>
                <div class="space-y-4">
                    <div v-for="i in 5" :key="i" class="flex items-center gap-4">
                        <div class="w-32 h-4 bg-gray-200 rounded animate-pulse"></div>
                        <div class="flex-1 h-10 bg-gray-200 rounded animate-pulse"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="relative border border-gray-300 rounded-lg p-6">
            <div class="absolute -top-3 left-4 bg-white px-2">
                <div class="w-20 h-6 bg-gray-200 rounded animate-pulse"></div>
            </div>
            <div class="grid grid-cols-2 gap-6">
                <div class="space-y-4">
                    <div v-for="i in 4" :key="i" class="flex items-center gap-4">
                        <div class="w-32 h-4 bg-gray-200 rounded animate-pulse"></div>
                        <div class="flex-1 h-10 bg-gray-200 rounded animate-pulse"></div>
                    </div>
                </div>
                <div class="space-y-4">
                    <div v-for="i in 4" :key="i" class="flex items-center gap-4">
                        <div class="w-32 h-4 bg-gray-200 rounded animate-pulse"></div>
                        <div class="flex-1 h-10 bg-gray-200 rounded animate-pulse"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="relative border border-gray-300 rounded-lg p-6">
            <div class="absolute -top-3 left-4 bg-white px-2">
                <div class="w-12 h-6 bg-gray-200 rounded animate-pulse"></div>
            </div>
            <div class="flex gap-4">
                <div class="flex-1 h-64 bg-gray-200 rounded animate-pulse"></div>
                <div class="flex-1 h-64 bg-gray-200 rounded animate-pulse"></div>
            </div>
        </div>

        <div class="relative border border-gray-300 rounded-lg p-6">
            <div class="absolute -top-3 left-4 bg-white px-2">
                <div class="w-16 h-6 bg-gray-200 rounded animate-pulse"></div>
            </div>
            <div class="grid grid-cols-2 gap-6">
                <div class="h-20 bg-gray-200 rounded animate-pulse"></div>
                <div class="space-y-4">
                    <div class="h-10 bg-gray-200 rounded animate-pulse"></div>
                    <div class="h-10 bg-gray-200 rounded animate-pulse"></div>
                </div>
            </div>
        </div>
    </div>
</template>
