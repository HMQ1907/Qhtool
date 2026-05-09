<template>
    <div class="min-h-screen bg-gray-50 py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto space-y-8">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 font-poppins">{{ campaign.name }}</h1>
                    <p class="mt-2 text-sm text-gray-600">Theo dõi tiến trình sản xuất tự động từng Video trong Campaign.</p>
                </div>
                <a href="/campaigns" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">
                    &larr; Trở lại danh sách
                </a>
            </div>

            <!-- Dashboard Stats -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
                    <p class="text-sm text-gray-500 font-medium">Tổng số Video</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ campaign.videos.length }}</p>
                </div>
                <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
                    <p class="text-sm text-gray-500 font-medium">Hoàn thành</p>
                    <p class="text-2xl font-bold text-green-600 mt-1">{{ completedCount }}</p>
                </div>
                <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
                    <p class="text-sm text-gray-500 font-medium">Video Kéo View</p>
                    <p class="text-2xl font-bold text-blue-600 mt-1">{{ campaign.videos.filter(v => v.video_type === 'monetization').length }}</p>
                </div>
                <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
                    <p class="text-sm text-gray-500 font-medium">Video Giăng Lưới (Aff)</p>
                    <p class="text-2xl font-bold text-purple-600 mt-1">{{ campaign.videos.filter(v => v.video_type === 'affiliate').length }}</p>
                </div>
            </div>

            <!-- Videos List -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="text-lg font-semibold text-gray-900">Danh sách Video</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tiêu đề</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Loại (Type)</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trạng thái Sinh (Pipeline)</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kịch bản (Script)</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Hành động</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="video in campaign.videos" :key="video.id" class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ video.title }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <span v-if="video.video_type === 'affiliate'" class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                                        Chốt Sale (Affiliate)
                                    </span>
                                    <span v-else class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                        Kéo Views (Monetization)
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <div class="flex items-center">
                                        <!-- Simple Status Badge -->
                                        <span :class="[
                                            video.status === 'completed' ? 'bg-green-100 text-green-800' : 
                                            video.status === 'failed' ? 'bg-red-100 text-red-800' : 
                                            'bg-yellow-100 text-yellow-800 animate-pulse',
                                            'px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full uppercase'
                                        ]">
                                            {{ video.status.replace('_', ' ') }}
                                        </span>
                                    </div>
                                    <div v-if="video.error_message" class="text-xs text-red-500 mt-1 max-w-xs truncate" :title="video.error_message">
                                        Lỗi: {{ video.error_message }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate cursor-help" :title="video.script_text">
                                    {{ video.script_text || 'Đang tạo...' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a v-if="video.status === 'completed'" :href="`/${video.final_video_url}`" target="_blank" class="text-indigo-600 hover:text-indigo-900 font-semibold" download>
                                        Tải Video
                                    </a>
                                    <span v-else class="text-gray-300">Chưa tải được</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    campaign: Object,
});

const completedCount = computed(() => {
    return props.campaign.videos.filter(v => v.status === 'completed').length;
});
</script>
