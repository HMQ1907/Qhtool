<template>
  <Head>
    <title>Profile | Stoicism & Psychology Generator</title>
  </Head>

  <div class="min-h-screen bg-gray-50 px-4 py-8 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-4xl space-y-6">
      <div class="flex items-center justify-between">
        <div>
          <p class="text-sm font-medium text-indigo-600">Profile</p>
          <h1 class="mt-1 text-3xl font-bold text-gray-900">{{ user?.name || 'User' }}</h1>
          <p class="mt-1 text-sm text-gray-500">{{ user?.email }}</p>
        </div>

        <a href="/campaigns" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">
          Back to campaigns
        </a>
      </div>

      <section class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
        <dl class="grid gap-4 sm:grid-cols-2">
          <div>
            <dt class="text-sm font-medium text-gray-500">Role</dt>
            <dd class="mt-1 text-lg font-semibold text-gray-900">{{ user?.role === 'admin' ? 'Admin' : 'User' }}</dd>
          </div>
          <div>
            <dt class="text-sm font-medium text-gray-500">Account</dt>
            <dd class="mt-1 text-lg font-semibold text-gray-900">{{ user?.email }}</dd>
          </div>
        </dl>
      </section>

      <div class="flex justify-end">
        <button
          type="button"
          :disabled="loggingOut"
          class="rounded-md bg-gray-900 px-4 py-2 text-sm font-semibold text-white hover:bg-gray-800 disabled:opacity-60"
          @click="logout"
        >
          {{ loggingOut ? 'Logging out...' : 'Logout' }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Head, router, usePage } from '@inertiajs/vue3'
import { computed, ref } from 'vue'

const page = usePage()
const user = computed(() => page.props.user ?? null)
const loggingOut = ref(false)

function logout() {
  loggingOut.value = true

  router.post('/logout', {}, {
    onFinish: () => {
      loggingOut.value = false
    },
  })
}
</script>
