<template>
  <div v-if="show" class="fixed bottom-4 right-4 z-50">
    <div :class="[
      'p-4 rounded-lg shadow-lg max-w-sm',
      type === 'water_low' || type === 'water_not_available' || type === 'watering_failed' 
        ? 'bg-red-100 border-red-500 text-red-700'
        : 'bg-green-100 border-green-500 text-green-700'
    ]">
      <div class="flex items-center">
        <div class="flex-shrink-0">
          <svg v-if="type === 'water_low' || type === 'water_not_available' || type === 'watering_failed'" 
               class="h-6 w-6 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                  d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
          </svg>
          <svg v-else class="h-6 w-6 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                  d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
        </div>
        <div class="ml-3">
          <p class="text-sm font-medium">{{ message }}</p>
        </div>
        <div class="ml-auto pl-3">
          <div class="-mx-1.5 -my-1.5">
            <button @click="close" class="inline-flex rounded-md p-1.5 focus:outline-none focus:ring-2 focus:ring-offset-2">
              <span class="sr-only">Dismiss</span>
              <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
              </svg>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';

const props = defineProps({
  type: {
    type: String,
    required: true
  },
  message: {
    type: String,
    required: true
  }
});

const show = ref(true);

const close = () => {
  show.value = false;
};

// Auto-close after 5 seconds
onMounted(() => {
  setTimeout(() => {
    close();
  }, 5000);
});
</script> 