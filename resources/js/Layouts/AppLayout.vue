<template>
  <div class="min-h-screen bg-gray-100">
    <nav class="bg-white shadow-sm">
      <!-- ... existing nav code ... -->
    </nav>

    <main>
      <slot />
    </main>

    <!-- Notifications -->
    <div v-for="(notification, index) in notifications" :key="index">
      <Notification
        :type="notification.type"
        :message="notification.message"
        @close="removeNotification(index)"
      />
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { router } from '@inertiajs/vue3';
import Notification from '@/Components/Notification.vue';
import Echo from 'laravel-echo';

const notifications = ref([]);

const addNotification = (type, message) => {
  notifications.value.push({ type, message });
};

const removeNotification = (index) => {
  notifications.value.splice(index, 1);
};

let echo = null;

onMounted(() => {
  // Initialize Laravel Echo
  echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
    forceTLS: true
  });

  // Listen for sensor notifications
  echo.private(`App.Models.User.${window.auth.user.id}`)
    .listen('SensorNotification', (e) => {
      addNotification(e.type, e.message);
    });
});

onUnmounted(() => {
  if (echo) {
    echo.disconnect();
  }
});
</script> 