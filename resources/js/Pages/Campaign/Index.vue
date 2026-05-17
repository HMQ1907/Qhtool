<template>
    <div class="min-h-screen bg-slate-50 px-4 py-6 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-6xl space-y-6">
            <header class="border-b border-slate-200 pb-5">
                <p class="text-sm font-semibold uppercase tracking-wide text-emerald-700">TikTok Shop Affiliate</p>
                <h1 class="mt-1 text-3xl font-bold text-slate-950">Affiliate Video Generator</h1>
                <p class="mt-2 max-w-2xl text-sm text-slate-600">
                    Nhap ten san pham, upload it nhat 3 anh, tool tao mac dinh 1 video review ngan voi voice tieng Viet, subtitle va CTA gio hang.
                </p>
            </header>

            <section class="rounded-lg border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-100 px-6 py-4">
                    <h2 class="text-lg font-semibold text-slate-950">Tao video san pham</h2>
                    <p class="mt-1 text-sm text-slate-500">Ten san pham giup script khong doan bua; anh san pham dung de render video.</p>
                </div>

                <form class="space-y-5 p-6" @submit.prevent="submit">
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <label class="block">
                            <span class="text-sm font-medium text-slate-700">Ten san pham</span>
                            <input v-model="form.product_name" required type="text" class="mt-1 w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500" placeholder="VD: Ke dung do nha bep da nang" />
                        </label>

                        <label class="block">
                            <span class="text-sm font-medium text-slate-700">Do dai video</span>
                            <input v-model="form.duration_seconds" required type="number" min="12" max="35" class="mt-1 w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500" />
                        </label>
                    </div>

                    <label class="block">
                        <span class="text-sm font-medium text-slate-700">Mo ta nhanh <span class="text-slate-400">(khong bat buoc)</span></span>
                        <textarea v-model="form.product_description" rows="3" class="mt-1 w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500" placeholder="Neu muon chinh xac hon, copy 1-2 dong mo ta san pham vao day."></textarea>
                    </label>

                    <label class="block">
                        <span class="text-sm font-medium text-slate-700">Anh san pham (toi thieu 3 anh, nen 4-5 anh)</span>
                        <input required multiple accept="image/*" type="file" class="mt-1 block w-full rounded-md border border-slate-300 text-sm file:mr-4 file:border-0 file:bg-slate-100 file:px-4 file:py-2 file:text-sm file:font-medium file:text-slate-700 hover:file:bg-slate-200" @change="handleImages" />
                        <span class="mt-1 block text-xs text-slate-500">Tool se dung anh nay de render video. Anh cang ro, video cang de ban.</span>
                    </label>

                    <details class="rounded-md border border-slate-200 bg-slate-50 p-4">
                        <summary class="cursor-pointer text-sm font-semibold text-slate-700">Tuy chon nang cao</summary>
                        <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2">
                            <label class="block">
                                <span class="text-sm font-medium text-slate-700">Mode</span>
                                <select v-model="form.generation_mode" class="mt-1 w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                                    <option value="fast_test">Mode 1 - Fast Test</option>
                                    <option value="premium_product">Mode 2 - Premium Product</option>
                                    <option value="winner_scale">Mode 3 - Winner Scale</option>
                                </select>
                            </label>

                            <label class="block">
                                <span class="text-sm font-medium text-slate-700">So video moi lan bam</span>
                                <input v-model="form.total_videos" type="number" min="1" max="20" class="mt-1 w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500" />
                            </label>

                            <label class="block md:col-span-2">
                                <span class="text-sm font-medium text-slate-700">Link TikTok Shop <span class="text-slate-400">(chi de luu lai, khong dua vao video)</span></span>
                                <input v-model="form.product_url" type="url" class="mt-1 w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500" placeholder="https://..." />
                            </label>
                        </div>
                    </details>

                    <div class="flex justify-end">
                        <button type="submit" :disabled="form.processing" class="rounded-md bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-emerald-700 disabled:opacity-50">
                            {{ form.processing ? 'Dang tao...' : 'Tao 1 video affiliate' }}
                        </button>
                    </div>
                </form>
            </section>

            <section class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-100 px-6 py-4">
                    <h2 class="text-lg font-semibold text-slate-950">Video da tao</h2>
                </div>
                <ul class="divide-y divide-slate-100">
                    <li v-for="campaign in campaigns" :key="campaign.id" class="flex items-center justify-between gap-4 p-5 hover:bg-slate-50">
                        <div>
                            <a :href="`/campaigns/${campaign.id}`" class="font-semibold text-emerald-700 hover:text-emerald-800">{{ campaign.name }}</a>
                            <div class="mt-1 flex flex-wrap gap-2 text-xs text-slate-500">
                                <span>{{ campaign.videos_count }} video</span>
                                <span class="rounded-full bg-slate-100 px-2 py-0.5">{{ campaign.status }}</span>
                            </div>
                        </div>
                        <a :href="`/campaigns/${campaign.id}`" class="text-sm font-medium text-slate-600 hover:text-emerald-700">Mo</a>
                    </li>
                    <li v-if="campaigns.length === 0" class="p-6 text-center text-sm text-slate-500">Chua co video nao.</li>
                </ul>
            </section>
        </div>
    </div>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'

defineProps({
    campaigns: Array,
})

const form = useForm({
    product_name: '',
    product_url: '',
    product_description: '',
    generation_mode: 'fast_test',
    total_videos: 1,
    duration_seconds: 20,
    product_images: [],
})

function handleImages(event) {
    form.product_images = Array.from(event.target.files || [])
}

function submit() {
    if (form.product_images.length < 3) {
        form.setError('product_images', 'Can upload it nhat 3 anh san pham.')
        return
    }

    form.post('/campaigns', {
        forceFormData: true,
        preserveScroll: true,
        onSuccess: () => form.reset(),
    })
}
</script>
