<template>
    <div class="min-h-screen bg-slate-50 px-4 py-6 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-7xl space-y-6">
            <header class="flex items-center justify-between border-b border-slate-200 pb-5">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-wide text-emerald-700">Affiliate batch</p>
                    <h1 class="mt-1 text-3xl font-bold text-slate-950">{{ campaign.name }}</h1>
                    <p class="mt-2 text-sm text-slate-600">{{ completedCount }}/{{ campaign.videos.length }} video hoan thanh.</p>
                </div>
                <a href="/campaigns" class="rounded-md border border-slate-300 px-3 py-2 text-sm font-medium text-slate-700 hover:bg-white">Quay lai</a>
            </header>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
                <Metric label="Tong video" :value="campaign.videos.length" />
                <Metric label="Hoan thanh" :value="completedCount" tone="green" />
                <Metric label="Dang chay" :value="runningCount" tone="amber" />
                <Metric label="Loi" :value="failedCount" tone="red" />
            </div>

            <section class="grid grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-3">
                <article v-for="video in campaign.videos" :key="video.id" class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
                    <div class="aspect-[9/16] bg-slate-950">
                        <video
                            v-if="video.status === 'completed' && video.final_video_url"
                            :src="resolveVideoUrl(video.final_video_url)"
                            controls
                            class="h-full w-full object-contain"
                        ></video>
                        <div v-else class="flex h-full items-center justify-center p-6 text-center text-sm text-slate-400">
                            {{ video.status === 'failed' ? 'Render loi' : 'Dang xu ly...' }}
                        </div>
                    </div>
                    <div class="space-y-3 p-4">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <h2 class="font-semibold text-slate-950">{{ video.title }}</h2>
                                <p class="mt-1 text-xs text-slate-500">{{ video.generation_mode }} · {{ video.duration_seconds }}s</p>
                            </div>
                            <span :class="statusClass(video.status)" class="rounded-full px-2 py-1 text-xs font-semibold uppercase">{{ video.status }}</span>
                        </div>

                        <p class="line-clamp-4 text-sm text-slate-600">{{ video.script_text || 'Dang tao script...' }}</p>
                        <p v-if="video.caption" class="line-clamp-3 text-xs text-slate-500">{{ video.caption }}</p>
                        <p v-if="video.error_message" class="line-clamp-3 text-xs text-red-600">{{ video.error_message }}</p>

                        <div class="flex gap-2">
                            <a
                                v-if="video.final_video_url"
                                :href="resolveVideoUrl(video.final_video_url)"
                                target="_blank"
                                class="rounded-md bg-emerald-600 px-3 py-2 text-sm font-semibold text-white hover:bg-emerald-700"
                            >
                                Mo video
                            </a>
                            <a
                                v-if="video.product_url"
                                :href="video.product_url"
                                target="_blank"
                                class="rounded-md border border-slate-300 px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50"
                            >
                                Link SP
                            </a>
                        </div>
                    </div>
                </article>
            </section>
        </div>
    </div>
</template>

<script setup>
import { computed, h } from 'vue'

const props = defineProps({
    campaign: Object,
})

const Metric = (props) => h('div', {
    class: 'rounded-lg border border-slate-200 bg-white p-4 shadow-sm',
}, [
    h('p', { class: 'text-sm font-medium text-slate-500' }, props.label),
    h('p', {
        class: [
            'mt-1 text-2xl font-bold',
            props.tone === 'green' ? 'text-green-600' : props.tone === 'amber' ? 'text-amber-600' : props.tone === 'red' ? 'text-red-600' : 'text-slate-950',
        ],
    }, props.value),
])

const completedCount = computed(() => props.campaign.videos.filter((video) => video.status === 'completed').length)
const runningCount = computed(() => props.campaign.videos.filter((video) => !['completed', 'failed'].includes(video.status)).length)
const failedCount = computed(() => props.campaign.videos.filter((video) => video.status === 'failed').length)

function resolveVideoUrl(url) {
    if (!url) return '#'
    if (/^https?:\/\//i.test(url)) return url
    return `/${String(url).replace(/^\/+/, '')}`
}

function statusClass(status) {
    if (status === 'completed') return 'bg-green-100 text-green-800'
    if (status === 'failed') return 'bg-red-100 text-red-800'
    return 'bg-amber-100 text-amber-800'
}
</script>
