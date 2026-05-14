<template>
    <div class="min-h-screen bg-gray-50 px-4 py-8 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-7xl space-y-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="font-poppins text-3xl font-bold text-gray-900">{{ campaign.name }}</h1>
                    <p class="mt-2 text-sm text-gray-600">Theo doi tien trinh san xuat tung video trong campaign.</p>
                </div>
                <a href="/campaigns" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">
                    &larr; Tro lai danh sach
                </a>
            </div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
                <div class="rounded-xl border border-gray-100 bg-white p-4 shadow-sm">
                    <p class="text-sm font-medium text-gray-500">Tong so video</p>
                    <p class="mt-1 text-2xl font-bold text-gray-900">{{ campaign.videos.length }}</p>
                </div>
                <div class="rounded-xl border border-gray-100 bg-white p-4 shadow-sm">
                    <p class="text-sm font-medium text-gray-500">Hoan thanh</p>
                    <p class="mt-1 text-2xl font-bold text-green-600">{{ completedCount }}</p>
                </div>
                <div class="rounded-xl border border-gray-100 bg-white p-4 shadow-sm">
                    <p class="text-sm font-medium text-gray-500">Keo view</p>
                    <p class="mt-1 text-2xl font-bold text-blue-600">{{ monetizationCount }}</p>
                </div>
                <div class="rounded-xl border border-gray-100 bg-white p-4 shadow-sm">
                    <p class="text-sm font-medium text-gray-500">Affiliate</p>
                    <p class="mt-1 text-2xl font-bold text-purple-600">{{ affiliateCount }}</p>
                </div>
            </div>

            <div class="overflow-hidden rounded-xl border border-gray-100 bg-white shadow-sm">
                <div class="border-b border-gray-100 bg-gray-50/50 px-6 py-5">
                    <h3 class="text-lg font-semibold text-gray-900">Danh sach video</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Tieu de</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Loai</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Trang thai</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Script / caption</th>
                                <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">Hanh dong</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            <tr v-for="video in campaign.videos" :key="video.id" class="hover:bg-gray-50">
                                <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900">
                                    {{ video.title }}
                                    <div class="mt-1 text-xs font-normal text-gray-500">
                                        {{ video.duration_seconds || 30 }}s · {{ video.aspect_ratio || '9:16' }} · {{ video.quality || '720p' }}
                                    </div>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm">
                                    <span
                                        v-if="video.video_type === 'affiliate'"
                                        class="inline-flex rounded-full bg-purple-100 px-2 py-1 text-xs font-semibold leading-5 text-purple-800"
                                    >
                                        Affiliate
                                    </span>
                                    <span
                                        v-else
                                        class="inline-flex rounded-full bg-blue-100 px-2 py-1 text-xs font-semibold leading-5 text-blue-800"
                                    >
                                        Monetization
                                    </span>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm">
                                    <span
                                        :class="[
                                            video.status === 'completed' ? 'bg-green-100 text-green-800' :
                                            video.status === 'failed' ? 'bg-red-100 text-red-800' :
                                            'animate-pulse bg-yellow-100 text-yellow-800',
                                            'inline-flex rounded-full px-2 py-1 text-xs font-semibold uppercase leading-5'
                                        ]"
                                    >
                                        {{ String(video.status).replace('_', ' ') }}
                                    </span>
                                    <div v-if="video.external_task_id" class="mt-1 max-w-xs truncate text-xs text-gray-400" :title="video.external_task_id">
                                        Task: {{ video.external_task_id }}
                                    </div>
                                    <div v-if="video.external_url_expires_at" class="mt-1 text-xs text-amber-600">
                                        Cloud URL expires: {{ video.external_url_expires_at }}
                                    </div>
                                    <div v-if="video.error_message" class="mt-1 max-w-xs truncate text-xs text-red-500" :title="video.error_message">
                                        Loi: {{ video.error_message }}
                                    </div>
                                </td>
                                <td class="max-w-xs px-6 py-4 text-sm text-gray-500">
                                    <div class="truncate" :title="video.script_text">{{ video.script_text || 'Dang tao...' }}</div>
                                    <div v-if="video.caption" class="mt-1 truncate text-xs text-gray-400" :title="video.caption">
                                        {{ video.caption }}
                                    </div>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-right text-sm font-medium">
                                    <a
                                        v-if="video.status === 'completed' && video.final_video_url"
                                        :href="resolveVideoUrl(video.final_video_url)"
                                        target="_blank"
                                        class="text-indigo-600 hover:text-indigo-900"
                                    >
                                        Mo video
                                    </a>
                                    <span v-else class="text-gray-300">Chua co</span>
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
import { computed } from 'vue'

const props = defineProps({
    campaign: Object,
})

const completedCount = computed(() => props.campaign.videos.filter((video) => video.status === 'completed').length)
const monetizationCount = computed(() => props.campaign.videos.filter((video) => video.video_type === 'monetization').length)
const affiliateCount = computed(() => props.campaign.videos.filter((video) => video.video_type === 'affiliate').length)

function resolveVideoUrl(url) {
    if (!url) {
        return '#'
    }

    if (/^https?:\/\//i.test(url)) {
        return url
    }

    return `/${String(url).replace(/^\/+/, '')}`
}
</script>
