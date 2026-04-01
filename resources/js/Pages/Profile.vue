<template>
  <Head>
    <title>Hồ sơ | QH Fashion AI</title>
  </Head>

  <div class="relative min-h-screen overflow-hidden bg-[#050816] text-white">
    <div class="pointer-events-none absolute inset-0">
      <div class="absolute -left-28 top-0 h-72 w-72 rounded-full bg-cyan-500/20 blur-3xl"></div>
      <div class="absolute right-0 top-20 h-80 w-80 rounded-full bg-rose-500/20 blur-3xl"></div>
      <div class="absolute bottom-0 left-1/3 h-80 w-80 rounded-full bg-amber-400/10 blur-3xl"></div>
      <div class="absolute inset-0 bg-[radial-gradient(circle_at_top,rgba(255,255,255,0.08),transparent_30%),linear-gradient(180deg,rgba(255,255,255,0.02),transparent_24%)]"></div>
    </div>

    <div class="relative mx-auto max-w-6xl px-4 py-6 sm:px-6 lg:px-8 lg:py-8">
      <div class="relative z-30 flex items-center justify-between gap-4 rounded-[28px] border border-white/10 bg-white/5 px-4 py-3 backdrop-blur-xl">
        <div>
          <p class="text-xs font-semibold uppercase tracking-[0.26em] text-cyan-200/80">Hồ sơ</p>
          <h1 class="mt-1 text-2xl font-black text-white">Tài khoản & lịch sử</h1>
        </div>
        <div ref="profileMenuRef" class="relative z-40">
          <button
            ref="profileMenuButtonRef"
            type="button"
            :disabled="navTarget !== null"
            class="flex items-center gap-3 rounded-full bg-white px-4 py-2 text-sm font-semibold text-slate-950 transition hover:-translate-y-0.5 disabled:cursor-not-allowed disabled:opacity-70"
            @click.stop="toggleProfileMenu"
          >
            <span class="grid h-8 w-8 place-items-center rounded-full bg-slate-950 text-xs font-black tracking-[0.2em] text-white">
              {{ userInitials }}
            </span>
            <span>Tuỳ chọn</span>
            <span class="text-xs text-slate-500">⌄</span>
          </button>

          <transition
            enter-active-class="transition duration-150 ease-out"
            enter-from-class="scale-95 opacity-0 -translate-y-2"
            enter-to-class="scale-100 opacity-100 translate-y-0"
            leave-active-class="transition duration-120 ease-in"
            leave-from-class="scale-100 opacity-100 translate-y-0"
            leave-to-class="scale-95 opacity-0 -translate-y-2"
          >
            <div
              v-if="profileMenuOpen"
              class="absolute right-0 z-50 mt-3 w-56 overflow-hidden rounded-[24px] border border-white/10 bg-[#08101f] p-2 shadow-2xl shadow-slate-950/40 backdrop-blur-xl"
            >
              <button
                type="button"
                :disabled="navTarget === 'back'"
                class="flex w-full items-center justify-between rounded-2xl px-3 py-3 text-left text-sm font-semibold text-white transition hover:bg-white/10 disabled:cursor-not-allowed disabled:opacity-60"
                @click="goToImageGenerator"
              >
                <span>Tạo ảnh</span>
                <span v-if="navTarget === 'back'" class="h-3.5 w-3.5 animate-spin rounded-full border-2 border-white/25 border-t-white"></span>
                <span v-else class="text-white/35">›</span>
              </button>

              <button
                type="button"
                :disabled="navTarget === 'logout'"
                class="mt-1 flex w-full items-center justify-between rounded-2xl px-3 py-3 text-left text-sm font-semibold text-rose-200 transition hover:bg-rose-500/10 disabled:cursor-not-allowed disabled:opacity-60"
                @click="goToLogout"
              >
                <span>Đăng xuất</span>
                <span v-if="navTarget === 'logout'" class="h-3.5 w-3.5 animate-spin rounded-full border-2 border-rose-200/25 border-t-rose-200"></span>
                <span v-else class="text-rose-200/40">⟶</span>
              </button>
            </div>
          </transition>
        </div>
      </div>

      <div class="relative z-0 mt-6 grid gap-6 lg:grid-cols-[0.95fr_1.05fr]">
        <section class="rounded-[32px] border border-white/10 bg-[#0b1126]/90 p-6 shadow-2xl shadow-slate-950/20 backdrop-blur-xl">
          <div class="flex items-start justify-between gap-4 border-b border-white/10 pb-5">
            <div>
              <p class="text-xs font-semibold uppercase tracking-[0.24em] text-white/45">Tài khoản</p>
              <h2 class="mt-2 text-3xl font-black text-white">{{ user?.name || 'Người dùng' }}</h2>
              <p class="mt-1 text-sm text-white/55">{{ user?.role === 'admin' ? 'Quản trị viên' : 'Cửa hàng thời trang' }}</p>
            </div>
            <span class="rounded-full bg-cyan-400/15 px-3 py-1 text-xs font-semibold text-cyan-200">{{ user?.role === 'admin' ? 'Không giới hạn' : 'Tài khoản thường' }}</span>
          </div>

          <div class="mt-5 grid gap-3 sm:grid-cols-2">
            <div class="rounded-[24px] border border-white/10 bg-white/5 p-4">
              <p class="text-xs uppercase tracking-[0.2em] text-white/45">Ảnh còn lại</p>
              <p class="mt-2 text-3xl font-black text-white">{{ user?.free_images_left ?? 0 }}</p>
              <p class="mt-1 text-sm text-white/45">Lượt tạo ảnh miễn phí</p>
            </div>
            <div class="rounded-[24px] border border-white/10 bg-white/5 p-4">
              <p class="text-xs uppercase tracking-[0.2em] text-white/45">Video miễn phí</p>
              <p class="mt-2 text-3xl font-black text-white">{{ user?.free_videos_left ?? 0 }}</p>
              <p class="mt-1 text-sm text-white/45">Sẽ mở rộng trong giai đoạn sau</p>
            </div>
          </div>

          <div class="mt-5 rounded-[24px] border border-white/10 bg-gradient-to-br from-cyan-500/10 via-slate-900 to-rose-500/10 p-4">
            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-white/45">Ghi chú</p>
            <p class="mt-2 text-sm leading-6 text-white/65">
              Các thông tin quản lý tài khoản được gom về một nơi để trang tạo ảnh luôn gọn. Bạn vẫn có thể xem lại ảnh đã tạo gần nhất ở bên phải.
            </p>
          </div>
        </section>

        <section class="rounded-[32px] border border-white/10 bg-white/5 p-6 shadow-2xl shadow-slate-950/20 backdrop-blur-xl">
          <div class="flex flex-col gap-3 border-b border-white/10 pb-5 sm:flex-row sm:items-end sm:justify-between">
            <div>
              <p class="text-xs font-semibold uppercase tracking-[0.24em] text-white/45">Lịch sử gần đây</p>
              <h2 class="mt-2 text-3xl font-black text-white">Ảnh đã tạo gần nhất</h2>
              <p class="mt-1 text-sm text-white/55">Xem lại, tải xuống hoặc mở để tạo video.</p>
            </div>
            <button
              type="button"
              :disabled="navTarget === 'create'"
              class="rounded-full bg-white/10 px-4 py-2 text-sm font-semibold text-white transition hover:bg-white/15 disabled:cursor-not-allowed disabled:opacity-70"
              @click="goToCreateImage"
            >
              <span v-if="navTarget === 'create'" class="inline-flex items-center gap-2">
                <span class="h-3.5 w-3.5 animate-spin rounded-full border-2 border-white/30 border-t-white"></span>
                Đang mở...
              </span>
              <span v-else>Tạo ảnh mới</span>
            </button>
          </div>

          <div v-if="recentImages.length > 0" class="mt-5 grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
            <article
              v-for="item in recentImages"
              :key="item.id"
              class="overflow-hidden rounded-[28px] border border-white/10 bg-[#0b1024]"
            >
              <img :src="item.url" alt="Ảnh đã tạo" class="h-56 w-full object-cover" />
              <div class="space-y-3 p-4">
                <div>
                  <p class="text-sm font-semibold text-white">{{ item.created_at }}</p>
                  <p class="mt-1 line-clamp-2 text-xs leading-5 text-white/45">{{ item.prompt || 'Ảnh được tạo từ mẫu chọn sẵn.' }}</p>
                </div>
                <div class="flex gap-2">
                  <a :href="item.url" download target="_blank" class="flex-1 rounded-full bg-white px-4 py-2 text-center text-sm font-semibold text-slate-950 transition hover:-translate-y-0.5">
                    Tải
                  </a>
                  <button
                    type="button"
                    :disabled="navTarget === 'video'"
                    class="rounded-full bg-white/10 px-4 py-2 text-sm font-semibold text-white transition hover:bg-white/15 disabled:cursor-not-allowed disabled:opacity-70"
                    @click="goToVideoCreator"
                  >
                    <span v-if="navTarget === 'video'" class="inline-flex items-center gap-2">
                      <span class="h-3.5 w-3.5 animate-spin rounded-full border-2 border-white/30 border-t-white"></span>
                      Đang mở...
                    </span>
                    <span v-else>Tạo video</span>
                  </button>
                </div>
              </div>
            </article>
          </div>

          <div v-else class="mt-5 rounded-[28px] border border-dashed border-white/10 px-6 py-10 text-center text-white/45">
            Chưa có ảnh nào được tạo.
          </div>
        </section>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Head, router, usePage } from '@inertiajs/vue3'
import { computed, onBeforeUnmount, onMounted, ref } from 'vue'

const props = defineProps({
  recentImages: { type: Array, default: () => [] },
})

const page = usePage()
const user = computed(() => page.props.user ?? null)
const recentImages = computed(() => props.recentImages ?? [])
const navTarget = ref(null)
const profileMenuOpen = ref(false)
const profileMenuRef = ref(null)
const profileMenuButtonRef = ref(null)

const userInitials = computed(() => {
  const name = user.value?.name || 'User'
  return name
    .split(/\s+/)
    .filter(Boolean)
    .slice(0, 2)
    .map((part) => part.charAt(0))
    .join('')
    .toUpperCase()
})

const closeProfileMenu = () => {
  profileMenuOpen.value = false
}

const toggleProfileMenu = () => {
  if (navTarget.value !== null) {
    return
  }

  profileMenuOpen.value = !profileMenuOpen.value
}

function navigateTo(url, target) {
  closeProfileMenu()
  navTarget.value = target

  router.visit(url, {
    onFinish: () => {
      navTarget.value = null
    },
    onCancel: () => {
      navTarget.value = null
    },
    onError: () => {
      navTarget.value = null
    },
  })
}

function goToImageGenerator() {
  navigateTo('/image-generator', 'back')
}

function goToCreateImage() {
  navigateTo('/image-generator', 'create')
}

function goToVideoCreator() {
  navigateTo('/image-generator?panel=video', 'video')
}

function goToLogout() {
  closeProfileMenu()
  navTarget.value = 'logout'

  router.post('/logout', {}, {
    onFinish: () => {
      navTarget.value = null
    },
    onCancel: () => {
      navTarget.value = null
    },
    onError: () => {
      navTarget.value = null
    },
  })
}

const handleDocumentClick = (event) => {
  if (!profileMenuOpen.value) {
    return
  }

  const menuElement = profileMenuRef.value
  const buttonElement = profileMenuButtonRef.value
  const target = event.target

  if (menuElement?.contains(target) || buttonElement?.contains(target)) {
    return
  }

  profileMenuOpen.value = false
}

onMounted(() => {
  document.addEventListener('click', handleDocumentClick)
})

onBeforeUnmount(() => {
  document.removeEventListener('click', handleDocumentClick)
})
</script>
