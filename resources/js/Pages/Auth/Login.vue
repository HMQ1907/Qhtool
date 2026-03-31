<template>
  <div class="min-h-screen bg-gray-950 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
      <h2 class="mt-6 text-center text-3xl font-extrabold text-white">
        Đăng nhập vào hệ thống
      </h2>
      <p class="mt-2 text-center text-sm text-gray-400">
        QH Fashion AI Tool
      </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
      <div class="bg-gray-900 py-8 px-4 shadow sm:rounded-lg sm:px-10 border border-gray-800">
        <form class="space-y-6" @submit.prevent="submit">
          <div>
            <label for="email" class="block text-sm font-medium text-gray-300">
              Email
            </label>
            <div class="mt-1">
              <input id="email" name="email" type="email" autocomplete="email" required
                v-model="form.email"
                class="appearance-none block w-full px-3 py-2 border border-gray-700 bg-gray-800 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-violet-500 focus:border-violet-500 sm:text-sm text-white" />
            </div>
            <p v-if="form.errors.email" class="mt-2 text-sm text-red-500">{{ form.errors.email }}</p>
          </div>

          <div>
            <label for="password" class="block text-sm font-medium text-gray-300">
              Mật khẩu
            </label>
            <div class="mt-1">
              <input id="password" name="password" type="password" autocomplete="current-password" required
                v-model="form.password"
                class="appearance-none block w-full px-3 py-2 border border-gray-700 bg-gray-800 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-violet-500 focus:border-violet-500 sm:text-sm text-white" />
            </div>
            <p v-if="form.errors.password" class="mt-2 text-sm text-red-500">{{ form.errors.password }}</p>
          </div>

          <div>
            <button type="submit" :disabled="form.processing"
              class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-violet-600 hover:bg-violet-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-violet-500"
              :class="{ 'opacity-25': form.processing }">
              Đăng nhập
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3';

const form = useForm({
  email: '',
  password: '',
  remember: false,
});

const submit = () => {
  form.post('/login', {
    onFinish: () => form.reset('password'),
  });
};
</script>
