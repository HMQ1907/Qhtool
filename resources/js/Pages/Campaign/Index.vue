<template>
    <div class="min-h-screen bg-gray-50 py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto space-y-8">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 font-poppins">Stoicism & Psychology Generator</h1>
                </div>
            </div>

            <!-- Create Campaign Form -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="text-lg font-semibold text-gray-900">Tạo Chiến Dịch Mới (Campaign)</h3>
                </div>
                <div class="p-6">
                    <form @submit.prevent="submit" class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Name -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Tên chiến dịch</label>
                                <input v-model="form.name" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="VD: Triết lý Cuộc sống Phần 1" required />
                            </div>

                            <!-- Total Videos -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Số lượng Video cần tạo</label>
                                <input v-model="form.total_videos" type="number" min="1" max="50" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required />
                            </div>

                            <!-- Affiliate Ratio -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Tỉ lệ chèn Affiliate (%)</label>
                                <div class="mt-1 flex items-center gap-4">
                                    <input v-model="form.affiliate_ratio" type="range" min="0" max="100" class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer" />
                                    <span class="text-sm font-semibold text-indigo-600 w-12">{{ form.affiliate_ratio }}%</span>
                                </div>
                                <p class="mt-1 text-xs text-gray-500">Ví dụ: Tạo 10 video, tỉ lệ 30% -> 7 video kéo views, 3 video gắn CTA Bio.</p>
                            </div>

                            <!-- Affiliate Link -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Link Affiliate (Tuỳ chọn)</label>
                                <input v-model="form.affiliate_link" type="url" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="https://..." />
                            </div>
                        </div>

                        <div class="flex justify-end pt-4">
                            <button type="submit" :disabled="form.processing" class="inline-flex justify-center py-2 px-6 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 transition-colors">
                                <span v-if="form.processing">Đang thiết lập...</span>
                                <span v-else>🚀 Khởi tạo Chiến Dịch</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Campaign List -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="text-lg font-semibold text-gray-900">Chiến dịch đã tạo</h3>
                </div>
                <ul role="list" class="divide-y divide-gray-100">
                    <li v-for="campaign in campaigns" :key="campaign.id" class="p-6 hover:bg-gray-50 transition-colors">
                        <div class="flex items-center justify-between">
                            <div class="flex-1 min-w-0">
                                <a :href="`/campaigns/${campaign.id}`" class="focus:outline-none">
                                    <p class="text-sm font-semibold text-indigo-600 truncate">{{ campaign.name }}</p>
                                    <p class="sm:flex sm:items-center mt-1">
                                        <span class="text-sm text-gray-500 mr-4">Ngách: {{ campaign.niche }}</span>
                                        <span class="text-sm text-gray-500 mr-4">Số lượng: {{ campaign.total_videos }} videos</span>
                                        <span class="text-sm px-2 py-0.5 rounded-full bg-blue-100 text-blue-800 font-medium">Trạng thái: {{ campaign.status }}</span>
                                    </p>
                                </a>
                            </div>
                            <div>
                                <a :href="`/campaigns/${campaign.id}`" class="text-gray-400 hover:text-indigo-600">
                                    Xem chi tiết &rarr;
                                </a>
                            </div>
                        </div>
                    </li>
                    <li v-if="campaigns.length === 0" class="p-6 text-center text-gray-500 text-sm">
                        Chưa có chiến dịch nào được tạo.
                    </li>
                </ul>
            </div>
        </div>
    </div>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3';

defineProps({
    campaigns: Array,
});

const form = useForm({
    name: '',
    total_videos: 10,
    affiliate_ratio: 30,
    affiliate_link: '',
});

const submit = () => {
    form.post('/campaigns', {
        preserveScroll: true,
        onSuccess: () => form.reset(),
    });
};
</script>
