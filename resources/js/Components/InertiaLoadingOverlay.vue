<template>
  <div
    class="fixed inset-0 z-[9999] flex items-center justify-center bg-slate-950/45 backdrop-blur-[2px]"
    :class="isLoading ? 'pointer-events-auto opacity-100' : 'pointer-events-none opacity-0'"
    aria-live="polite"
    aria-busy="isLoading"
  >
    <div class="flex flex-col items-center gap-4 rounded-[28px] border border-white/10 bg-[#08101f]/90 px-8 py-7 shadow-2xl shadow-slate-950/40">
      <img :src="loadingGifUrl" alt="loading" class="h-20 w-20 object-contain" />
      <p class="text-sm font-semibold tracking-[0.18em] text-white/80 uppercase">Đang tải...</p>
    </div>
  </div>
</template>

<script setup>
const loadingGifUrl = `${import.meta.env.BASE_URL}images/icons/cat-loading.gif`

defineProps({
  isLoading: {
    type: Boolean,
    default: false,
  },
})
</script>