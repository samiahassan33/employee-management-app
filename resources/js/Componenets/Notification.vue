<template>
  <Teleport to="body">
    <div class="fixed inset-0 z-50 flex items-start justify-end p-6 pointer-events-none">
      <div class="w-full max-w-sm space-y-4">
        <TransitionGroup
          enter-active-class="transform transition duration-300 ease-out"
          enter-from-class="translate-x-full opacity-0"
          enter-to-class="translate-x-0 opacity-100"
          leave-active-class="transform transition duration-200 ease-in"
          leave-from-class="translate-x-0 opacity-100"
          leave-to-class="translate-x-full opacity-0"
        >
          <div
            v-for="notification in notifications"
            :key="notification.id"
            :class="[
              'relative bg-white rounded-lg shadow-lg border-l-4 p-4 pointer-events-auto',
              borderClasses[notification.type]
            ]"
          >
            <div class="flex">
              <div class="flex-shrink-0">
                <component 
                  :is="getIcon(notification.type)" 
                  :class="['h-5 w-5', iconClasses[notification.type]]"
                />
              </div>
              <div class="ml-3 w-0 flex-1">
                <p class="text-sm font-medium text-gray-900">
                  {{ notification.title }}
                </p>
                <p class="mt-1 text-sm text-gray-500">
                  {{ notification.message }}
                </p>
              </div>
              <div class="ml-4 flex-shrink-0 flex">
                <button
                  @click="$emit('remove', notification.id)"
                  class="rounded-md inline-flex text-gray-400 hover:text-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                >
                  <XIcon class="h-5 w-5" />
                </button>
              </div>
            </div>
          </div>
        </TransitionGroup>
      </div>
    </div>
  </Teleport>
</template>

<script setup>
import { 
  CheckCircleIcon, 
  ExclamationTriangleIcon, 
  XCircleIcon, 
  InformationCircleIcon,
  XIcon 
} from 'lucide-vue-next'

defineProps({
  notifications: {
    type: Array,
    default: () => []
  }
})

defineEmits(['remove'])

const borderClasses = {
  success: 'border-green-400',
  error: 'border-red-400',
  warning: 'border-yellow-400',
  info: 'border-blue-400'
}

const iconClasses = {
  success: 'text-green-400',
  error: 'text-red-400',
  warning: 'text-yellow-400',
  info: 'text-blue-400'
}

const getIcon = (type) => {
  switch (type) {
    case 'success':
      return CheckCircleIcon
    case 'error':
      return XCircleIcon
    case 'warning':
      return ExclamationTriangleIcon
    case 'info':
      return InformationCircleIcon
    default:
      return InformationCircleIcon
  }
}
</script>