<template>
  <Modal
    :is-visible="isVisible"
    :title="config.title"
    size="sm"
    @close="cancel"
  >
    <div class="text-center">
      <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full mb-4"
           :class="iconClasses">
        <component :is="iconComponent" class="h-6 w-6" />
      </div>
      
      <div class="text-sm text-gray-500 mb-6">
        {{ config.message }}
      </div>
    </div>

    <template #footer>
      <div class="flex justify-end space-x-3">
        <button
          @click="cancel"
          class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
        >
          {{ config.cancelText }}
        </button>
        <button
          @click="confirm"
          :class="[
            'px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white focus:outline-none focus:ring-2 focus:ring-offset-2',
            confirmButtonClasses
          ]"
        >
          {{ config.confirmText }}
        </button>
      </div>
    </template>
  </Modal>
</template>

<script setup>
import { computed } from 'vue'
import { ExclamationTriangleIcon, InformationCircleIcon, CheckCircleIcon } from 'lucide-vue-next'
import Modal from './Modal.vue'

const props = defineProps({
  isVisible: {
    type: Boolean,
    default: false
  },
  config: {
    type: Object,
    default: () => ({
      title: 'Confirm Action',
      message: 'Are you sure you want to continue?',
      confirmText: 'Confirm',
      cancelText: 'Cancel',
      type: 'warning'
    })
  }
})

const emit = defineEmits(['confirm', 'cancel'])

const iconComponent = computed(() => {
  switch (props.config.type) {
    case 'danger':
      return ExclamationTriangleIcon
    case 'success':
      return CheckCircleIcon
    case 'info':
      return InformationCircleIcon
    default:
      return ExclamationTriangleIcon
  }
})

const iconClasses = computed(() => {
  switch (props.config.type) {
    case 'danger':
      return 'bg-red-100 text-red-600'
    case 'success':
      return 'bg-green-100 text-green-600'
    case 'info':
      return 'bg-blue-100 text-blue-600'
    default:
      return 'bg-yellow-100 text-yellow-600'
  }
})

const confirmButtonClasses = computed(() => {
  switch (props.config.type) {
    case 'danger':
      return 'bg-red-600 hover:bg-red-700 focus:ring-red-500'
    case 'success':
      return 'bg-green-600 hover:bg-green-700 focus:ring-green-500'
    case 'info':
      return 'bg-blue-600 hover:bg-blue-700 focus:ring-blue-500'
    default:
      return 'bg-yellow-600 hover:bg-yellow-700 focus:ring-yellow-500'
  }
})

const confirm = () => {
  emit('confirm')
}

const cancel = () => {
  emit('cancel')
}
</script>