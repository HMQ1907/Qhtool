<template>
  <Head>
    <title>{{ pageTitle }}</title>
  </Head>

  <div class="relative min-h-screen overflow-hidden bg-[#050816] text-white">
    <div class="pointer-events-none absolute inset-0">
      <div class="absolute -left-28 top-0 h-72 w-72 rounded-full bg-cyan-500/20 blur-3xl"></div>
      <div class="absolute right-0 top-20 h-80 w-80 rounded-full bg-rose-500/20 blur-3xl"></div>
      <div class="absolute bottom-0 left-1/3 h-80 w-80 rounded-full bg-amber-400/10 blur-3xl"></div>
      <div class="absolute inset-0 bg-[radial-gradient(circle_at_top,rgba(255,255,255,0.08),transparent_30%),linear-gradient(180deg,rgba(255,255,255,0.02),transparent_24%)]"></div>
    </div>

    <template v-if="!isAuthenticated">
      <section class="relative mx-auto max-w-7xl px-4 pb-14 pt-5 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between gap-4 rounded-full border border-white/10 bg-white/5 px-4 py-3 backdrop-blur-xl">
          <div class="flex items-center gap-3">
            <img :src="qhLogoUrl" alt="QH Fashion AI" class="h-11 w-11 rounded-2xl shadow-lg shadow-cyan-500/20" />
            <div>
              <p class="text-sm font-semibold text-white">QH Fashion AI</p>
              <p class="text-xs text-white/60">Tạo ảnh và video thời trang trong vài chục giây</p>
            </div>
          </div>
          <div class="flex items-center gap-2">
            <button
              type="button"
              class="cursor-pointer rounded-full border border-white/10 bg-white/5 px-4 py-2 text-sm font-medium text-white/90 transition hover:bg-white/10"
              @click="scrollToDemo"
            >
              Xem demo
            </button>
            <button
              type="button"
              :disabled="navTarget === 'login'"
              class="cursor-pointer rounded-full bg-gradient-to-r from-rose-500 to-orange-400 px-4 py-2 text-sm font-semibold text-white shadow-lg shadow-rose-500/25 transition hover:scale-[1.02]"
              @click="goToLogin"
            >
              <span v-if="navTarget === 'login'" class="inline-flex items-center gap-2">
                <span class="h-3.5 w-3.5 animate-spin rounded-full border-2 border-white/30 border-t-white"></span>
                Đang mở...
              </span>
              <span v-else>Đăng nhập</span>
            </button>
          </div>
        </div>

        <div class="mt-10 grid gap-8 lg:grid-cols-[1.05fr_0.95fr] lg:items-center">
          <div class="max-w-2xl">
            <div class="inline-flex items-center gap-2 rounded-full border border-cyan-400/20 bg-cyan-400/10 px-4 py-2 text-xs font-semibold uppercase tracking-[0.25em] text-cyan-200">
              Chạy thử ngay
            </div>
            <h1 class="mt-5 text-4xl font-black leading-tight tracking-tight text-white sm:text-5xl lg:text-6xl">
              Tạo ảnh người mẫu mặc sản phẩm của bạn bằng AI trong 30 giây
            </h1>
            <p class="mt-5 max-w-xl text-base leading-7 text-white/70 sm:text-lg">
              Không cần thuê mẫu, không cần studio. Chọn ảnh sản phẩm, chọn người mẫu, chọn background là có ngay ảnh bán hàng sắc nét.
            </p>

            <div class="mt-8 flex flex-wrap gap-3">
              <button
                type="button"
                class="cursor-pointer rounded-full bg-white px-5 py-3 text-sm font-semibold text-slate-950 shadow-xl shadow-white/10 transition hover:-translate-y-0.5"
                @click="goToLogin"
              >
                Dùng thử ngay
              </button>
            </div>

            <div class="mt-8 grid gap-3 sm:grid-cols-3">
              <div class="rounded-3xl border border-white/10 bg-white/5 p-4 backdrop-blur-xl">
                <p class="text-2xl font-black text-white">30s</p>
                <p class="mt-1 text-sm text-white/60">Từ upload đến ảnh hoàn chỉnh</p>
              </div>
              <div class="rounded-3xl border border-white/10 bg-white/5 p-4 backdrop-blur-xl">
                <p class="text-2xl font-black text-white">1 chạm</p>
                <p class="mt-1 text-sm text-white/60">Không cần nhập prompt phức tạp</p>
              </div>
            </div>
          </div>

          <div class="relative">
            <div class="absolute -inset-4 rounded-[32px] bg-gradient-to-br from-cyan-500/20 via-rose-500/20 to-amber-400/10 blur-2xl"></div>
            <div class="relative overflow-hidden rounded-[32px] border border-white/10 bg-[#0b1024]/90 p-4 shadow-2xl shadow-cyan-950/20 backdrop-blur-xl">
              <div class="grid gap-4 sm:grid-cols-[1fr_1.15fr]">
                <div class="rounded-[28px] border border-white/10 bg-white/5 p-4">
                  <p class="text-xs font-semibold uppercase tracking-[0.22em] text-white/50">Ảnh sản phẩm</p>
                  <div class="mt-3 overflow-hidden rounded-[24px] bg-gradient-to-br from-slate-800 to-slate-900 p-4">
                    <img :src="sampleProductImage" alt="Ảnh sản phẩm mẫu" class="w-full rounded-[20px] object-cover shadow-lg" />
                  </div>
                  <p class="mt-3 text-sm font-medium text-white">Áo thun basic</p>
                  <p class="text-xs text-white/55">Mẫu demo để người bán nhìn kết quả ngay</p>
                </div>

                <div class="rounded-[28px] border border-white/10 bg-white/5 p-4">
                  <div class="flex items-center justify-between">
                    <div>
                      <p class="text-xs font-semibold uppercase tracking-[0.22em] text-white/50">Demo trực quan</p>
                      <p class="mt-1 text-sm font-medium text-white">Sẵn 1 mẫu, 1 nền, 1 sản phẩm</p>
                    </div>
                    <span class="rounded-full bg-emerald-400/15 px-3 py-1 text-xs font-semibold text-emerald-200">Miễn phí</span>
                  </div>

                  <div class="mt-4 grid gap-3">
                    <div class="overflow-hidden rounded-[24px] border border-white/10 bg-[#10172f]">
                      <img v-if="demoModel" :src="demoModel.url" alt="Người mẫu mẫu" class="h-56 w-full object-cover" />
                      <div v-else class="flex h-56 items-center justify-center text-sm text-white/40">Chưa có ảnh người mẫu</div>
                    </div>
                    <div class="overflow-hidden rounded-[24px] border border-white/10 bg-[#10172f]">
                      <img v-if="demoBackground" :src="demoBackground.url" alt="Background mẫu" class="h-28 w-full object-cover opacity-95" />
                      <div v-else class="flex h-28 items-center justify-center text-sm text-white/40">Chưa có background</div>
                    </div>
                  </div>

                  <button
                    type="button"
                    class="cursor-pointer mt-4 w-full rounded-2xl bg-gradient-to-r from-rose-500 to-orange-400 px-4 py-3 text-sm font-semibold text-white shadow-lg shadow-rose-500/20 transition hover:scale-[1.01]"
                    @click="runGuestDemo"
                  >
                    Tạo thử miễn phí
                  </button>
                </div>
              </div>

              <div class="mt-4 rounded-[28px] border border-white/10 bg-[#0a1124] p-4">
                <div class="flex items-center justify-between gap-3">
                  <div>
                    <p class="text-sm font-semibold text-white">Demo kết quả</p>
                    <p class="text-xs text-white/55">Hiển thị ngay một ảnh mẫu để khách dễ quyết định</p>
                  </div>
                  <span v-if="guestDemoState === 'loading'" class="rounded-full bg-cyan-400/15 px-3 py-1 text-xs font-semibold text-cyan-200">Đang tạo...</span>
                  <span v-else-if="guestDemoState === 'done'" class="rounded-full bg-emerald-400/15 px-3 py-1 text-xs font-semibold text-emerald-200">Hoàn tất</span>
                </div>

                <div class="mt-4 min-h-[240px] overflow-hidden rounded-[24px] border border-white/10 bg-gradient-to-br from-slate-900 via-slate-950 to-slate-900">
                  <div v-if="guestDemoState === 'idle'" class="grid h-full place-items-center px-8 py-14 text-center text-white/45">
                    <div>
                      <p class="text-base font-medium text-white/70">Ảnh demo sẽ hiện ở đây</p>
                      <p class="mt-2 text-sm">Bấm “Tạo thử miễn phí” để xem trải nghiệm ngay, không cần đăng nhập.</p>
                    </div>
                  </div>

                  <div v-else-if="guestDemoState === 'loading'" class="grid h-full place-items-center px-8 py-14 text-center">
                    <div class="h-16 w-16 animate-spin rounded-full border-4 border-white/10 border-t-cyan-400"></div>
                    <p class="mt-4 text-sm font-medium text-white">Đang tạo ảnh mẫu...</p>
                    <p class="mt-1 text-xs text-white/45">Thường chỉ mất vài giây</p>
                  </div>

                  <div v-else class="relative h-full p-4">
                    <div class="absolute inset-0 bg-[radial-gradient(circle_at_top,rgba(34,211,238,0.18),transparent_30%),radial-gradient(circle_at_bottom_right,rgba(251,113,133,0.18),transparent_28%)]"></div>
                    <div class="relative grid h-full gap-4 lg:grid-cols-[1.05fr_0.95fr]">
                      <div class="overflow-hidden rounded-[22px] border border-white/10 bg-white/5">
                        <img :src="guestDemoBackground" alt="Nền demo" class="h-full min-h-[240px] w-full object-cover" />
                      </div>
                      <div class="grid gap-4">
                        <div class="overflow-hidden rounded-[22px] border border-white/10 bg-white/5 p-3">
                          <img :src="demoModel?.url || sampleProductImage" alt="Người mẫu demo" class="h-56 w-full rounded-[18px] object-cover" />
                        </div>
                        <div class="rounded-[22px] border border-white/10 bg-[#0b132b]/80 p-4">
                          <p class="text-sm font-semibold text-white">Kết quả demo</p>
                          <p class="mt-1 text-xs text-white/55">Ảnh được phối sẵn để người dùng dễ hiểu luồng tạo ảnh.</p>
                          <div class="mt-4 flex items-center justify-between rounded-[18px] border border-white/10 bg-white/5 px-4 py-3">
                            <span class="text-xs text-white/60">Phong cách</span>
                            <span class="text-sm font-semibold text-white">{{ selectedStyleLabel }}</span>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
    </template>

    <template v-else>
      <div class="relative mx-auto max-w-[1640px] px-4 py-4 lg:px-6">
        <div class="grid gap-6 lg:grid-cols-[280px_minmax(0,1fr)]">
          <aside class="h-fit rounded-[30px] border border-white/10 bg-white/5 p-5 shadow-2xl shadow-slate-950/20 backdrop-blur-xl lg:sticky lg:top-4">
            <div class="flex items-center gap-3 border-b border-white/10 pb-5">
              <img :src="qhLogoUrl" alt="QH Fashion AI" class="h-12 w-12 rounded-2xl shadow-lg shadow-cyan-500/20" />
              <div>
                <p class="text-sm font-semibold text-white">QH Fashion AI</p>
                <p class="text-xs text-white/50">Công cụ dành cho shop thời trang</p>
              </div>
            </div>

            <div class="mt-5 space-y-2">
              <button
                v-for="item in sidebarItems"
                :key="item.key"
                type="button"
                class="cursor-pointer flex w-full items-center gap-3 rounded-2xl px-4 py-3 text-left transition"
                :class="activePanel === item.key ? 'bg-white text-slate-950 shadow-lg shadow-white/10' : 'bg-white/5 text-white/80 hover:bg-white/10'"
                @click="activePanel = item.key"
              >
                <span class="text-lg">{{ item.icon }}</span>
                <span class="text-sm font-semibold">{{ item.label }}</span>
              </button>
            </div>
          </aside>

          <main class="relative z-0 space-y-6">
            <section class="relative z-30 rounded-[32px] border border-white/10 bg-white/5 p-4 shadow-2xl shadow-slate-950/20 backdrop-blur-xl sm:p-6">
              <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                <div>
                  <p class="text-xs font-semibold uppercase tracking-[0.26em] text-cyan-200/80">Bảng điều khiển</p>
                  <h2 class="mt-2 text-2xl font-black text-white sm:text-3xl">Tạo nội dung bán hàng bằng AI</h2>
                  <p class="mt-2 max-w-2xl text-sm leading-6 text-white/60">
                    Chọn ảnh, chọn người mẫu, chọn nền và bấm tạo.
                  </p>
                </div>

                <div class="relative flex flex-wrap items-center gap-3">
                  <div class="flex flex-wrap gap-2 rounded-full border border-white/10 bg-white/5 p-2">
                    <button
                      v-for="tab in mainTabs"
                      :key="tab.key"
                      type="button"
                      class="cursor-pointer rounded-full px-4 py-2 text-sm font-semibold transition"
                      :class="activePanel === tab.key ? 'bg-white text-slate-950' : 'text-white/70 hover:text-white'"
                      @click="activePanel = tab.key"
                    >
                      {{ tab.label }}
                    </button>
                  </div>

                  <div ref="profileMenuRef" class="relative z-40">
                    <button
                      ref="profileMenuButtonRef"
                      type="button"
                      :disabled="navTarget !== null"
                      class="cursor-pointer flex items-center gap-3 rounded-full border border-white/10 bg-[#0b1126]/90 px-3 py-2 text-left text-white transition hover:bg-white/10 disabled:cursor-not-allowed disabled:opacity-70"
                      @click.stop="toggleProfileMenu"
                    >
                      <span class="grid h-10 w-10 place-items-center rounded-full bg-gradient-to-br from-cyan-400 via-sky-500 to-indigo-500 text-sm font-black tracking-[0.2em] text-white">
                        {{ userInitials }}
                      </span>
                      <span class="hidden sm:block">
                        <span class="block text-sm font-semibold leading-5">{{ user?.name || 'Người dùng' }}</span>
                        <span class="block text-xs text-white/45">Tài khoản</span>
                      </span>
                      <span class="text-sm text-white/50">⌄</span>
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
                        class="absolute right-0 z-50 mt-3 w-64 overflow-hidden rounded-[24px] border border-white/10 bg-[#08101f] p-2 shadow-2xl shadow-slate-950/40 backdrop-blur-xl"
                      >
                        <div class="px-3 py-3">
                          <p class="text-sm font-semibold text-white">{{ user?.name || 'Người dùng' }}</p>
                          <p class="mt-1 text-xs text-white/45">{{ user?.role === 'admin' ? 'Quản trị viên' : 'Cửa hàng thời trang' }}</p>
                        </div>

                        <button
                          type="button"
                          :disabled="navTarget === 'profile'"
                          class="cursor-pointer flex w-full items-center justify-between rounded-2xl px-3 py-3 text-left text-sm font-semibold text-white transition hover:bg-white/10 disabled:cursor-not-allowed disabled:opacity-60"
                          @click="goToProfile"
                        >
                          <span>Hồ sơ</span>
                          <span v-if="navTarget === 'profile'" class="h-3.5 w-3.5 animate-spin rounded-full border-2 border-white/25 border-t-white"></span>
                          <span v-else class="text-white/35">›</span>
                        </button>

                        <button
                          type="button"
                          :disabled="navTarget === 'logout'"
                          class="cursor-pointer mt-1 flex w-full items-center justify-between rounded-2xl px-3 py-3 text-left text-sm font-semibold text-rose-200 transition hover:bg-rose-500/10 disabled:cursor-not-allowed disabled:opacity-60"
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
              </div>
            </section>

            <section v-if="activePanel === 'image'" class="grid gap-6 xl:grid-cols-[0.95fr_1.05fr]">
              <div class="space-y-5 rounded-[32px] border border-white/10 bg-[#0b1024]/90 p-5 shadow-2xl shadow-slate-950/20 backdrop-blur-xl">
                <div>
                  <p class="text-xs font-semibold uppercase tracking-[0.24em] text-white/45">Bước 1</p>
                  <h3 class="mt-2 text-xl font-black text-white">Tạo ảnh người mẫu</h3>
                  <p class="mt-1 text-sm text-white/55">Tối giản, nhanh, dễ hiểu cho chủ shop.</p>
                </div>

                <div class="rounded-[26px] border border-white/10 bg-white/5 p-4">
                  <label class="mb-3 block text-sm font-semibold text-white">1. Upload ảnh sản phẩm</label>
                  <div
                        class="group relative cursor-pointer overflow-hidden rounded-[24px] border-2 border-dashed transition"
                    :class="isDragging ? 'border-cyan-400 bg-cyan-400/10' : 'border-white/15 bg-[#0c132b]'"
                    @dragover.prevent="isDragging = true"
                    @dragleave.prevent="isDragging = false"
                    @drop.prevent="handleDrop"
                    @click="triggerFilePicker"
                  >
                    <input
                      ref="fileInput"
                      type="file"
                      class="hidden"
                      accept="image/jpeg,image/png,image/webp"
                      @change="handleFileSelect"
                    />

                    <div v-if="!productPreview" class="grid place-items-center px-6 py-10 text-center">
                      <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-white/5 text-white/70 transition group-hover:bg-white/10">⤒</div>
                      <p class="mt-4 text-sm font-medium text-white">Kéo thả hoặc bấm để chọn ảnh</p>
                      <p class="mt-1 text-xs text-white/45">JPG, PNG, WEBP · Tối đa 10MB</p>
                    </div>

                    <div v-else class="relative p-3">
                      <img :src="productPreview" alt="Ảnh sản phẩm đã chọn" class="h-64 w-full rounded-[20px] bg-black/20 object-contain" />
                      <button
                        type="button"
                        class="cursor-pointer absolute right-6 top-6 rounded-full bg-rose-500 px-3 py-1.5 text-xs font-semibold text-white shadow-lg shadow-rose-500/25"
                        @click.stop="clearProduct"
                      >
                        Xóa
                      </button>
                    </div>
                  </div>
                  <p v-if="errors.product_image" class="mt-2 text-xs text-rose-300">{{ errors.product_image }}</p>
                </div>

                <div class="rounded-[26px] border border-white/10 bg-white/5 p-4">
                  <label class="mb-3 block text-sm font-semibold text-white">2. Chọn người mẫu</label>
                  <div v-if="models.length === 0" class="rounded-[20px] border border-dashed border-white/10 p-5 text-center text-sm text-white/45">
                    Chưa có ảnh người mẫu.
                  </div>
                  <div v-else class="grid grid-cols-2 gap-3 sm:grid-cols-3">
                    <button
                      v-for="model in models"
                      :key="model.path"
                      type="button"
                      class="cursor-pointer group relative aspect-[3/4] overflow-hidden rounded-[22px] border border-white/10 transition"
                      :class="selectedModel === model.path ? 'scale-[0.98] ring-2 ring-cyan-400 ring-offset-2 ring-offset-[#0b1024]' : 'hover:-translate-y-0.5 hover:border-white/20'"
                      @click="selectedModel = model.path"
                    >
                      <img :src="model.url" :alt="model.name" class="h-full w-full object-cover" />
                      <div class="absolute inset-0 bg-gradient-to-t from-slate-950/70 via-slate-950/10 to-transparent"></div>
                      <div v-if="selectedModel === model.path" class="absolute right-3 top-3 rounded-full bg-cyan-400 p-1.5 text-xs font-black text-slate-950">✓</div>
                    </button>
                  </div>
                  <p v-if="errors.model_path" class="mt-2 text-xs text-rose-300">{{ errors.model_path }}</p>
                </div>

                <div class="rounded-[26px] border border-white/10 bg-white/5 p-4">
                  <label class="mb-3 block text-sm font-semibold text-white">3. Chọn background</label>
                  <div v-if="backgrounds.length === 0" class="rounded-[20px] border border-dashed border-white/10 p-5 text-center text-sm text-white/45">
                    Chưa có ảnh background.
                  </div>
                  <div v-else class="grid grid-cols-2 gap-3 sm:grid-cols-3">
                    <button
                      v-for="background in backgrounds"
                      :key="background.path"
                      type="button"
                      class="cursor-pointer group relative aspect-[4/3] overflow-hidden rounded-[22px] border border-white/10 transition"
                      :class="selectedBg === background.path ? 'scale-[0.98] ring-2 ring-rose-400 ring-offset-2 ring-offset-[#0b1024]' : 'hover:-translate-y-0.5 hover:border-white/20'"
                      @click="selectedBg = background.path"
                    >
                      <img :src="background.url" :alt="background.name" class="h-full w-full object-cover" />
                      <div class="absolute inset-0 bg-gradient-to-t from-slate-950/75 via-slate-950/5 to-transparent"></div>
                      <div v-if="selectedBg === background.path" class="absolute right-3 top-3 rounded-full bg-rose-400 p-1.5 text-xs font-black text-slate-950">✓</div>
                    </button>
                  </div>
                  <p v-if="errors.bg_path" class="mt-2 text-xs text-rose-300">{{ errors.bg_path }}</p>
                </div>

                <div class="rounded-[26px] border border-white/10 bg-white/5 p-4">
                  <label class="mb-3 block text-sm font-semibold text-white">4. Chọn phong cách</label>
                  <div class="relative">
                    <select
                      v-model="selectedStyle"
                      class="w-full appearance-none rounded-[18px] border border-white/10 bg-[#091022] px-4 py-3 text-sm font-medium text-white outline-none transition focus:border-cyan-400"
                    >
                      <option v-for="option in styleOptions" :key="option.value" :value="option.value" class="bg-slate-950">
                        {{ option.label }}
                      </option>
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-4 grid place-items-center text-white/45">⌄</div>
                  </div>
                </div>

                <div class="rounded-[26px] border border-white/10 bg-white/5 p-4">
                  <div class="mb-3 flex items-center justify-between gap-3">
                    <label class="block text-sm font-semibold text-white">5. Prompt ảnh</label>
                    <button
                      type="button"
                      class="cursor-pointer rounded-full bg-white/10 px-3 py-1.5 text-xs font-semibold text-white transition hover:bg-white/15"
                      @click="applyPromptTemplate('image')"
                    >
                      Gen prompt tự động
                    </button>
                  </div>
                  <textarea
                    v-model="imagePrompt"
                    rows="4"
                    class="w-full rounded-[18px] border border-white/10 bg-[#091022] px-4 py-3 text-sm text-white outline-none transition placeholder:text-white/30 focus:border-cyan-400"
                    placeholder="Nhập prompt nếu bạn muốn tinh chỉnh thêm, hoặc bấm nút tự động để điền sẵn."
                  ></textarea>
                  <p class="mt-2 text-xs leading-5 text-white/45">
                    Có thể để trống. Nếu không nhập, hệ thống sẽ dùng prompt mẫu từ nút tự động hoặc prompt mặc định theo phong cách.
                  </p>
                </div>

                <button
                  type="button"
                  class="cursor-pointer w-full rounded-[22px] bg-gradient-to-r from-cyan-400 via-sky-500 to-indigo-500 px-5 py-4 text-base font-black text-white shadow-2xl shadow-cyan-500/20 transition hover:scale-[1.01] disabled:cursor-not-allowed disabled:opacity-60"
                  :disabled="isGenerating"
                  @click="generateImage"
                >
                  <span v-if="!isGenerating">Tạo ảnh</span>
                  <span v-else>Đang tạo ảnh...</span>
                </button>
              </div>

              <div class="rounded-[32px] border border-white/10 bg-[#0b1024]/90 p-5 shadow-2xl shadow-slate-950/20 backdrop-blur-xl">
                <div class="flex items-center justify-between gap-4 border-b border-white/10 pb-4">
                  <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.24em] text-white/45">Kết quả</p>
                    <h3 class="mt-1 text-xl font-black text-white">Ảnh thành phẩm</h3>
                  </div>
                  <span v-if="currentStatus" class="rounded-full px-3 py-1 text-xs font-semibold" :class="statusBadgeClass">
                    {{ statusLabel }}
                  </span>
                </div>

                <div v-if="!currentStatus" class="grid min-h-[620px] place-items-center rounded-[28px] border border-dashed border-white/10 bg-[radial-gradient(circle_at_top,rgba(255,255,255,0.06),transparent_38%)] p-8 text-center">
                  <div>
                    <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-[28px] bg-white/5 text-3xl">✦</div>
                    <p class="mt-5 text-lg font-semibold text-white">Ảnh kết quả sẽ hiện ở đây</p>
                    <p class="mt-2 text-sm leading-6 text-white/50">Hệ thống sẽ tự lưu trạng thái và cho tải xuống ngay khi hoàn tất.</p>
                  </div>
                </div>

                <div v-else-if="currentStatus === 'pending' || currentStatus === 'processing'" class="grid min-h-[620px] place-items-center rounded-[28px] border border-white/10 bg-white/5 p-8 text-center">
                  <div>
                    <div class="mx-auto h-16 w-16 animate-spin rounded-full border-4 border-white/10 border-t-cyan-400"></div>
                    <p class="mt-5 text-lg font-semibold text-white">Đang tạo ảnh (~20-30s)</p>
                    <p class="mt-2 text-sm text-white/50">Chúng tôi đang ghép sản phẩm, người mẫu và background.</p>
                  </div>
                </div>

                <div v-else-if="currentStatus === 'done' && outputUrl" class="overflow-hidden rounded-[28px] border border-white/10 bg-white/5">
                  <div class="relative overflow-hidden">
                    <img :src="outputUrl" alt="Ảnh đã tạo" class="max-h-[620px] w-full bg-[#060b1a] object-contain" />
                    <div class="pointer-events-none absolute inset-0 bg-gradient-to-t from-slate-950/30 via-transparent to-transparent"></div>
                  </div>
                  <div class="flex flex-col gap-3 border-t border-white/10 p-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                      <p class="text-sm font-semibold text-white">Ảnh đã sẵn sàng</p>
                      <p class="text-xs text-white/45">Tải xuống hoặc chuyển ngay sang tab video.</p>
                    </div>
                    <div class="flex flex-wrap gap-2">
                      <a
                        :href="outputUrl"
                        download="fashion-ai-result.jpg"
                        target="_blank"
                        class="cursor-pointer rounded-full bg-white px-4 py-2 text-sm font-semibold text-slate-950 transition hover:-translate-y-0.5"
                      >
                        Tải xuống
                      </a>
                      <button
                        type="button"
                        class="cursor-pointer rounded-full bg-gradient-to-r from-rose-500 to-orange-400 px-4 py-2 text-sm font-semibold text-white transition hover:-translate-y-0.5"
                        @click="useThisImageForVideo"
                      >
                        Tạo video từ ảnh này
                      </button>
                    </div>
                  </div>
                </div>

                <div v-else class="grid min-h-[620px] place-items-center rounded-[28px] border border-white/10 bg-white/5 p-8 text-center">
                  <div>
                    <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-[24px] bg-rose-500/10 text-3xl text-rose-200">!</div>
                    <p class="mt-5 text-lg font-semibold text-white">Tạo ảnh thất bại</p>
                    <p class="mt-2 text-sm text-white/55">{{ errorMessage || 'Đã có lỗi xảy ra. Vui lòng thử lại.' }}</p>
                    <button
                      type="button"
                      class="cursor-pointer mt-5 rounded-full bg-white/10 px-4 py-2 text-sm font-semibold text-white transition hover:bg-white/15"
                      @click="resetImageResult"
                    >
                      Thử lại
                    </button>
                  </div>
                </div>
              </div>
            </section>

            <section v-else-if="activePanel === 'video'" class="grid gap-6 xl:grid-cols-[0.9fr_1.1fr]">
              <div class="space-y-5 rounded-[32px] border border-white/10 bg-[#0b1024]/90 p-5 shadow-2xl shadow-slate-950/20 backdrop-blur-xl">
                <div>
                  <p class="text-xs font-semibold uppercase tracking-[0.24em] text-white/45">Bước 2</p>
                  <h3 class="mt-2 text-xl font-black text-white">Tạo video từ ảnh đã tạo</h3>
                  <p class="mt-1 text-sm text-white/55">Chọn một ảnh có sẵn rồi chọn kiểu chuyển động.</p>
                </div>

                <div class="rounded-[26px] border border-white/10 bg-white/5 p-4">
                  <label class="mb-3 block text-sm font-semibold text-white">1. Chọn ảnh đã tạo</label>
                  <div v-if="videoSources.length === 0" class="rounded-[20px] border border-dashed border-white/10 p-5 text-center text-sm text-white/45">
                    Chưa có ảnh nào để tạo video. Hãy chuyển sang tab Tạo ảnh trước.
                  </div>
                  <div v-else class="grid grid-cols-2 gap-3 sm:grid-cols-3">
                    <button
                      v-for="source in videoSources"
                      :key="source.id"
                      type="button"
                      class="group relative aspect-[3/4] overflow-hidden rounded-[22px] border border-white/10 transition"
                      :class="selectedVideoSourceId === source.id ? 'scale-[0.98] ring-2 ring-amber-400 ring-offset-2 ring-offset-[#0b1024]' : 'hover:-translate-y-0.5 hover:border-white/20'"
                      @click="selectedVideoSourceId = source.id"
                    >
                      <img :src="source.url" :alt="source.title" class="h-full w-full object-cover" />
                      <div class="absolute inset-0 bg-gradient-to-t from-slate-950/75 via-slate-950/10 to-transparent"></div>
                      <div class="absolute inset-x-0 bottom-0 p-3 text-left">
                        <p class="text-xs font-semibold text-white">{{ source.title }}</p>
                        <p class="text-[11px] text-white/50">{{ source.createdAt }}</p>
                      </div>
                      <div v-if="selectedVideoSourceId === source.id" class="absolute right-3 top-3 rounded-full bg-amber-400 p-1.5 text-xs font-black text-slate-950">✓</div>
                    </button>
                  </div>
                </div>

                <div class="rounded-[26px] border border-white/10 bg-white/5 p-4">
                  <label class="mb-3 block text-sm font-semibold text-white">2. Chọn animation</label>
                  <div class="grid gap-3 sm:grid-cols-3">
                    <button
                      v-for="option in animationOptions"
                      :key="option.value"
                      type="button"
                      class="cursor-pointer rounded-[20px] border px-4 py-4 text-left transition"
                      :class="selectedAnimation === option.value ? 'border-amber-400 bg-amber-400/10 text-white' : 'border-white/10 bg-white/5 text-white/70 hover:border-white/20'"
                      @click="selectedAnimation = option.value"
                    >
                      <p class="text-sm font-semibold">{{ option.label }}</p>
                      <p class="mt-1 text-xs leading-5 text-white/45">{{ option.description }}</p>
                    </button>
                  </div>
                </div>

                <div class="rounded-[26px] border border-white/10 bg-white/5 p-4">
                  <div class="mb-3 flex items-center justify-between gap-3">
                    <label class="block text-sm font-semibold text-white">3. Prompt video</label>
                    <button
                      type="button"
                      class="cursor-pointer rounded-full bg-white/10 px-3 py-1.5 text-xs font-semibold text-white transition hover:bg-white/15"
                      @click="applyPromptTemplate('video')"
                    >
                      Gen prompt tự động
                    </button>
                  </div>
                  <textarea
                    v-model="videoPrompt"
                    rows="4"
                    class="w-full rounded-[18px] border border-white/10 bg-[#091022] px-4 py-3 text-sm text-white outline-none transition placeholder:text-white/30 focus:border-amber-400"
                    placeholder="Nhập prompt cho video nếu muốn, hoặc bấm nút tự động để điền sẵn."
                  ></textarea>
                  <p class="mt-2 text-xs leading-5 text-white/45">
                    Prompt này sẽ dùng cho video sau này; hiện tại vẫn cho phép để trống.
                  </p>
                </div>

                <button
                  type="button"
                  class="cursor-pointer w-full rounded-[22px] bg-gradient-to-r from-amber-400 via-orange-500 to-rose-500 px-5 py-4 text-base font-black text-white shadow-2xl shadow-orange-500/20 transition hover:scale-[1.01] disabled:cursor-not-allowed disabled:opacity-60"
                  :disabled="videoGenerating"
                  @click="generateVideo"
                >
                  <span v-if="!videoGenerating">Tạo video</span>
                  <span v-else>Đang tạo video...</span>
                </button>
              </div>

              <div class="rounded-[32px] border border-white/10 bg-[#0b1024]/90 p-5 shadow-2xl shadow-slate-950/20 backdrop-blur-xl">
                <div class="flex items-center justify-between gap-4 border-b border-white/10 pb-4">
                  <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.24em] text-white/45">Kết quả</p>
                    <h3 class="mt-1 text-xl font-black text-white">Video thành phẩm</h3>
                  </div>
                  <span v-if="videoStatus === 'processing'" class="rounded-full bg-amber-400/15 px-3 py-1 text-xs font-semibold text-amber-200">Đang tạo video (~40-60s)</span>
                  <span v-else-if="videoStatus === 'done'" class="rounded-full bg-emerald-400/15 px-3 py-1 text-xs font-semibold text-emerald-200">Hoàn tất</span>
                </div>

                <div v-if="videoStatus === 'idle'" class="grid min-h-[620px] place-items-center rounded-[28px] border border-dashed border-white/10 bg-[radial-gradient(circle_at_top,rgba(255,255,255,0.06),transparent_38%)] p-8 text-center">
                  <div>
                    <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-[28px] bg-white/5 text-3xl">▶</div>
                    <p class="mt-5 text-lg font-semibold text-white">Video sẽ hiện ở đây</p>
                    <p class="mt-2 text-sm leading-6 text-white/50">Chọn một ảnh đã tạo và bấm “Tạo video”.</p>
                  </div>
                </div>

                <div v-else-if="videoStatus === 'processing'" class="grid min-h-[620px] place-items-center rounded-[28px] border border-white/10 bg-white/5 p-8 text-center">
                  <div>
                    <div class="mx-auto h-16 w-16 animate-spin rounded-full border-4 border-white/10 border-t-amber-400"></div>
                    <p class="mt-5 text-lg font-semibold text-white">Đang tạo video (~40-60s)</p>
                    <p class="mt-2 text-sm text-white/50">Cảnh quay nhẹ, mượt và sẵn sàng đăng bán.</p>
                  </div>
                </div>

                <div v-else class="overflow-hidden rounded-[28px] border border-white/10 bg-white/5">
                  <div class="relative min-h-[620px] overflow-hidden bg-slate-950">
                    <img
                      :src="selectedVideoSource?.url || outputUrl || sampleBackdropImage"
                      alt="Video nền"
                      class="absolute inset-0 h-full w-full object-cover opacity-50"
                    />
                    <div class="absolute inset-0 bg-[radial-gradient(circle_at_top,rgba(255,255,255,0.12),transparent_28%),linear-gradient(180deg,rgba(7,11,26,0.2),rgba(7,11,26,0.88))]"></div>

                    <div class="relative z-10 grid min-h-[620px] place-items-center p-6">
                      <div class="w-full max-w-xl rounded-[30px] border border-white/10 bg-white/5 p-4 shadow-2xl backdrop-blur-xl">
                        <div class="relative overflow-hidden rounded-[24px] border border-white/10 bg-slate-950">
                          <div class="motion-frame">
                            <img
                              :src="selectedVideoSource?.url || outputUrl || sampleBackdropImage"
                              alt="Ảnh nguồn video"
                              class="h-[420px] w-full object-cover"
                            />
                          </div>
                          <div class="absolute inset-0 bg-gradient-to-t from-slate-950/55 via-transparent to-transparent"></div>
                          <div class="absolute inset-x-0 bottom-0 p-5">
                            <p class="text-xs font-semibold uppercase tracking-[0.22em] text-white/55">Video đã tạo</p>
                            <p class="mt-1 text-lg font-black text-white">{{ selectedAnimationLabel }}</p>
                            <p class="mt-2 text-sm text-white/60">Xem trước chuyển động theo phong cách bán hàng.</p>
                          </div>
                        </div>
                        <div class="mt-4 flex flex-wrap items-center justify-between gap-3 rounded-[22px] border border-white/10 bg-slate-950/60 px-4 py-3">
                          <div>
                            <p class="text-sm font-semibold text-white">{{ selectedVideoSource?.title || 'Ảnh nguồn' }}</p>
                            <p class="text-xs text-white/45">{{ selectedVideoSource?.createdAt || 'Mẫu hiện tại' }}</p>
                          </div>
                          <button
                            type="button"
                            class="cursor-pointer rounded-full bg-white px-4 py-2 text-sm font-semibold text-slate-950 transition hover:-translate-y-0.5"
                            @click="activePanel = 'image'"
                          >
                            Tạo ảnh khác
                          </button>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </section>

          </main>
        </div>
      </div>
    </template>
  </div>
</template>

<script setup>
import { computed, onBeforeUnmount, onMounted, ref, watch } from 'vue'
import { Head, router, usePage } from '@inertiajs/vue3'

const props = defineProps({
  models: { type: Array, default: () => [] },
  backgrounds: { type: Array, default: () => [] },
  generatedImages: { type: Array, default: () => [] },
})

const page = usePage()

const fileInput = ref(null)
const productFile = ref(null)
const productPreview = ref(null)
const isDragging = ref(false)
const selectedModel = ref(props.models[0]?.path ?? null)
const selectedBg = ref(props.backgrounds[0]?.path ?? null)
const selectedStyle = ref('gen-z')

const activePanel = ref('image')
const errors = ref({})
const isGenerating = ref(false)
const currentStatus = ref(null)
const outputUrl = ref(null)
const errorMessage = ref(null)
const pollInterval = ref(null)

const videoStatus = ref('idle')
const videoGenerating = ref(false)
const selectedAnimation = ref('quay-nhe')
const selectedVideoSourceId = ref(null)
const imagePrompt = ref('')
const videoPrompt = ref('')

const guestDemoState = ref('idle')
const guestDemoTimer = ref(null)

const styleOptions = [
  { value: 'gen-z', label: 'Gen Z', description: 'Trẻ, sáng, dễ chạy ads' },
  { value: 'sang-trong', label: 'Sang trọng', description: 'Tôn chất liệu, premium' },
  { value: 'streetwear', label: 'Streetwear', description: 'Năng động, cá tính, phá cách' },
]

const animationOptions = [
  { value: 'quay-nhe', label: 'Quay nhẹ', description: 'Tạo cảm giác sản phẩm có chiều sâu' },
  { value: 'zoom', label: 'Zoom', description: 'Đẩy mạnh chi tiết và cảm giác chuyển động' },
  { value: 'catwalk', label: 'Catwalk', description: 'Phong cách trình diễn thời trang' },
]

const sidebarItems = [
  { key: 'image', icon: '✦', label: 'Tạo ảnh' },
  { key: 'video', icon: '▶', label: 'Tạo video' },
]

const mainTabs = [
  { key: 'image', label: 'Tạo ảnh' },
  { key: 'video', label: 'Tạo video' },
]

const stylePromptMap = {
  'gen-z': 'Fashion campaign, trendy, clean lighting, social commerce ready',
  'sang-trong': 'Luxury fashion campaign, elegant lighting, premium editorial style',
  streetwear: 'Streetwear fashion campaign, bold, urban, high-contrast lighting',
}

const demoModel = computed(() => props.models[0] ?? null)
const demoBackground = computed(() => props.backgrounds[0] ?? null)
const selectedStyleLabel = computed(() => styleOptions.find((item) => item.value === selectedStyle.value)?.label ?? 'Gen Z')
const selectedAnimationLabel = computed(() => animationOptions.find((item) => item.value === selectedAnimation.value)?.label ?? 'Quay nhẹ')
const pageTitle = computed(() => (isAuthenticated.value ? 'QH Fashion AI - Tạo ảnh và video' : 'QH Fashion AI - Tạo ảnh người mẫu bằng AI'))

const user = computed(() => page.props.user ?? null)
const isAuthenticated = computed(() => Boolean(user.value))
const promptTemplate = computed(() => String(page.props.promptImage || '').trim())
const defaultImagePrompt = computed(() => stylePromptMap[selectedStyle.value] || stylePromptMap['gen-z'])
const navTarget = ref(null)
const profileMenuOpen = ref(false)
const profileMenuRef = ref(null)
const profileMenuButtonRef = ref(null)
const qhLogoUrl = `${import.meta.env.BASE_URL}images/icons/qh-fashion-logo.svg`

const sampleProductImage = computed(() => `data:image/svg+xml;charset=UTF-8,${encodeURIComponent(`
  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 800 1000">
    <defs>
      <linearGradient id="g1" x1="0" x2="1" y1="0" y2="1">
        <stop offset="0%" stop-color="#ffffff"/>
        <stop offset="100%" stop-color="#dfe7ff"/>
      </linearGradient>
      <linearGradient id="g2" x1="0" x2="1" y1="0" y2="1">
        <stop offset="0%" stop-color="#f3f6ff"/>
        <stop offset="100%" stop-color="#c8d4ff"/>
      </linearGradient>
    </defs>
    <rect width="800" height="1000" rx="48" fill="url(#g2)"/>
    <ellipse cx="400" cy="300" rx="180" ry="120" fill="#9fb5ff" opacity="0.25"/>
    <rect x="260" y="180" width="280" height="520" rx="120" fill="url(#g1)"/>
    <path d="M320 210c30 40 60 60 80 60s50-20 80-60l70 32-40 94c-16 38-24 80-24 123v241H234V459c0-43-8-85-24-123l-40-94 70-32c30 40 60 60 80 60z" fill="#101828" opacity="0.92"/>
    <path d="M355 286h90l15 52h-120z" fill="#2d3748" opacity="0.75"/>
    <rect x="260" y="700" width="280" height="120" rx="36" fill="#ffffff" opacity="0.9"/>
    <text x="400" y="770" font-family="Arial, sans-serif" font-size="40" font-weight="700" text-anchor="middle" fill="#0f172a">Áo thun basic</text>
  </svg>
`)}`)

const guestDemoBackground = computed(() => demoBackground.value?.url ?? sampleProductImage.value)

const generatedImages = computed(() => props.generatedImages ?? [])

const recentImagesWithCurrent = computed(() => {
  const items = generatedImages.value.map((item) => ({
    id: item.id,
    url: item.url,
    title: 'Ảnh đã tạo',
    createdAt: item.created_at || 'Vừa xong',
  }))

  if (outputUrl.value) {
    items.unshift({
      id: 'current-image',
      url: outputUrl.value,
      title: 'Ảnh vừa tạo',
      createdAt: 'Ngay bây giờ',
    })
  }

  return items
})

const videoSources = computed(() => recentImagesWithCurrent.value)
const selectedVideoSource = computed(() => videoSources.value.find((item) => item.id === selectedVideoSourceId.value) ?? null)

const statusBadgeClass = computed(() => {
  const map = {
    pending: 'bg-amber-400/15 text-amber-200',
    processing: 'bg-sky-400/15 text-sky-200',
    done: 'bg-emerald-400/15 text-emerald-200',
    failed: 'bg-rose-400/15 text-rose-200',
  }

  return map[currentStatus.value] ?? 'bg-white/10 text-white/70'
})

const statusLabel = computed(() => {
  const map = {
    pending: 'Đang chờ',
    processing: 'Đang xử lý',
    done: 'Hoàn thành',
    failed: 'Thất bại',
  }

  return map[currentStatus.value] ?? 'Sẵn sàng'
})

const demoSectionRef = ref(null)

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

const triggerFilePicker = () => {
  fileInput.value?.click()
}

const cleanLabel = (label) => String(label).replace(/[_-]/g, ' ')

const closeProfileMenu = () => {
  profileMenuOpen.value = false
}

const toggleProfileMenu = () => {
  if (navTarget.value !== null) {
    return
  }

  profileMenuOpen.value = !profileMenuOpen.value
}

const goToLogin = () => {
  closeProfileMenu()
  navTarget.value = 'login'

  router.visit('/login', {
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

function goToProfile() {
  closeProfileMenu()
  navTarget.value = 'profile'

  router.visit('/profile', {
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

const scrollToDemo = () => {
  demoSectionRef.value?.scrollIntoView({ behavior: 'smooth', block: 'start' })
}

function handleFileSelect(event) {
  const file = event.target.files?.[0]
  if (file) {
    setProductFile(file)
  }
}

function handleDrop(event) {
  isDragging.value = false
  const file = event.dataTransfer.files?.[0]
  if (file && file.type.startsWith('image/')) {
    setProductFile(file)
  }
}

function setProductFile(file) {
  if (file.size > 10 * 1024 * 1024) {
    errors.value.product_image = 'Ảnh không được vượt quá 10MB.'
    return
  }

  if (productPreview.value) {
    URL.revokeObjectURL(productPreview.value)
  }

  errors.value.product_image = null
  productFile.value = file
  productPreview.value = URL.createObjectURL(file)
}

function applyPromptTemplate(target) {
  const template = promptTemplate.value || defaultImagePrompt.value

  if (target === 'video') {
    videoPrompt.value = template
    return
  }

  imagePrompt.value = template
}

function clearProduct() {
  productFile.value = null
  if (productPreview.value) {
    URL.revokeObjectURL(productPreview.value)
  }
  productPreview.value = null
  if (fileInput.value) {
    fileInput.value.value = ''
  }
}

function validateImageForm() {
  errors.value = {}

  let hasError = false

  if (!productFile.value) {
    errors.value.product_image = 'Vui lòng upload ảnh sản phẩm.'
    hasError = true
  }

  if (!selectedModel.value) {
    errors.value.model_path = 'Vui lòng chọn người mẫu.'
    hasError = true
  }

  if (!selectedBg.value) {
    errors.value.bg_path = 'Vui lòng chọn background.'
    hasError = true
  }

  return !hasError
}

async function generateImage() {
  if (!validateImageForm()) {
    return
  }

  const formData = new FormData()
  formData.append('product_image', productFile.value)
  formData.append('model_path', selectedModel.value)
  formData.append('bg_path', selectedBg.value)
  formData.append('prompt', imagePrompt.value.trim() || promptTemplate.value || defaultImagePrompt.value)
  formData.append('_token', document.querySelector('meta[name="csrf-token"]')?.content || '')

  try {
    isGenerating.value = true
    currentStatus.value = 'pending'
    outputUrl.value = null
    errorMessage.value = null

    const response = await fetch('/image-generator', {
      method: 'POST',
      body: formData,
    })

    if (!response.ok) {
      const data = await response.json().catch(() => ({}))

      if (response.status === 422 && data.errors) {
        errors.value = {}
        for (const [key, messages] of Object.entries(data.errors)) {
          errors.value[key] = messages[0]
        }
        currentStatus.value = null
        isGenerating.value = false
        return
      }

      throw new Error(data.message || 'Đã có lỗi xảy ra.')
    }

    const result = await response.json()
    startPolling(result.id)
  } catch (error) {
    currentStatus.value = 'failed'
    errorMessage.value = error.message
    isGenerating.value = false
  }
}

function startPolling(id) {
  clearPolling()

  pollInterval.value = window.setInterval(async () => {
    try {
      const response = await fetch(`/image-generator/${id}/status`)
      const data = await response.json()

      currentStatus.value = data.status

      if (data.status === 'done') {
        outputUrl.value = data.output_url
        isGenerating.value = false
        clearPolling()
      } else if (data.status === 'failed') {
        errorMessage.value = data.error_message
        isGenerating.value = false
        clearPolling()
      }
    } catch (error) {
      console.warn('[Poll] Lỗi tạm thời:', error.message)
    }
  }, 3000)
}

function clearPolling() {
  if (pollInterval.value) {
    clearInterval(pollInterval.value)
    pollInterval.value = null
  }
}

function resetImageResult() {
  clearPolling()
  currentStatus.value = null
  outputUrl.value = null
  errorMessage.value = null
  isGenerating.value = false
}

function useThisImageForVideo() {
  if (!outputUrl.value) {
    return
  }

  activePanel.value = 'video'
  selectedVideoSourceId.value = 'current-image'
  videoStatus.value = 'idle'
}

function useHistoryImageForVideo(item) {
  activePanel.value = 'video'
  selectedVideoSourceId.value = item.id
  videoStatus.value = 'idle'
}

function generateVideo() {
  if (!selectedVideoSource.value) {
    return
  }

  videoGenerating.value = true
  videoStatus.value = 'processing'

  if (!videoPrompt.value.trim()) {
    videoPrompt.value = promptTemplate.value || defaultImagePrompt.value
  }

  window.setTimeout(() => {
    videoGenerating.value = false
    videoStatus.value = 'done'
  }, 2400)
}

function runGuestDemo() {
  clearTimeout(guestDemoTimer.value)
  guestDemoState.value = 'loading'

  guestDemoTimer.value = window.setTimeout(() => {
    guestDemoState.value = 'done'
  }, 1800)
}

watch(
  videoSources,
  (sources) => {
    if (!selectedVideoSourceId.value && sources.length > 0) {
      selectedVideoSourceId.value = sources[0].id
    }
  },
  { immediate: true },
)

watch(
  () => activePanel.value,
  (panel) => {
    if (panel === 'video' && !selectedVideoSourceId.value && videoSources.value.length > 0) {
      selectedVideoSourceId.value = videoSources.value[0].id
    }
  },
)

watch(
  () => props.models,
  (models) => {
    if (!selectedModel.value && models.length > 0) {
      selectedModel.value = models[0].path
    }
  },
  { immediate: true },
)

watch(
  () => props.backgrounds,
  (backgrounds) => {
    if (!selectedBg.value && backgrounds.length > 0) {
      selectedBg.value = backgrounds[0].path
    }
  },
  { immediate: true },
)

watch(
  selectedStyle,
  () => {
    if (!imagePrompt.value.trim()) {
      return
    }

    if (imagePrompt.value === defaultImagePrompt.value) {
      imagePrompt.value = defaultImagePrompt.value
    }
  },
)

onBeforeUnmount(() => {
  clearPolling()
  clearTimeout(guestDemoTimer.value)
  if (productPreview.value) {
    URL.revokeObjectURL(productPreview.value)
  }
})
</script>

<style scoped>
.motion-frame {
  animation: motion-sway 5.5s ease-in-out infinite;
  transform-origin: center center;
}

@keyframes motion-sway {
  0%,
  100% {
    transform: scale(1) translateY(0);
  }
  50% {
    transform: scale(1.03) translateY(-4px);
  }
}
</style>