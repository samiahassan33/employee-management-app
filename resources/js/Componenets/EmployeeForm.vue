<template>
  <form @submit.prevent="handleSubmit" class="space-y-6">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <!-- Name Field -->
      <div>
        <label for="name" class="block text-sm font-medium text-gray-700">
          Full Name *
        </label>
        <input
          id="name"
          v-model="form.name"
          type="text"
          required
          :class="[
            'mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500',
            hasError('name') ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : ''
          ]"
          placeholder="Enter full name"
        />
        <p v-if="hasError('name')" class="mt-1 text-sm text-red-600">
          {{ getError('name') }}
        </p>
      </div>

      <!-- Email Field -->
      <div>
        <label for="email" class="block text-sm font-medium text-gray-700">
          Email Address *
        </label>
        <input
          id="email"
          v-model="form.email"
          type="email"
          required
          :class="[
            'mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500',
            hasError('email') ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : ''
          ]"
          placeholder="employee@company.com"
        />
        <p v-if="hasError('email')" class="mt-1 text-sm text-red-600">
          {{ getError('email') }}
        </p>
      </div>

      <!-- Phone Field -->
      <div>
        <label for="phone" class="block text-sm font-medium text-gray-700">
          Phone Number *
        </label>
        <input
          id="phone"
          v-model="form.phone"
          type="tel"
          required
          :class="[
            'mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500',
            hasError('phone') ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : ''
          ]"
          placeholder="+1 (555) 123-4567"
        />
        <p v-if="hasError('phone')" class="mt-1 text-sm text-red-600">
          {{ getError('phone') }}
        </p>
      </div>

      <!-- Department Field -->
      <div>
        <label for="department_id" class="block text-sm font-medium text-gray-700">
          Department *
        </label>
        <select
          id="department_id"
          v-model="form.department_id"
          required
          :class="[
            'mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500',
            hasError('department_id') ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : ''
          ]"
        >
          <option value="">Select Department</option>
          <option
            v-for="department in departments"
            :key="department.id"
            :value="department.id"
          >
            {{ department.name }} ({{ department.code }})
          </option>
        </select>
        <p v-if="hasError('department_id')" class="mt-1 text-sm text-red-600">
          {{ getError('department_id') }}
        </p>
      </div>

      <!-- Joining Date Field -->
      <div class="md:col-span-2">
        <label for="joining_date" class="block text-sm font-medium text-gray-700">
          Joining Date *
        </label>
        <input
          id="joining_date"
          v-model="form.joining_date"
          type="date"
          required
          :max="today"
          :class="[
            'mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500',
            hasError('joining_date') ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : ''
          ]"
        />
        <p v-if="hasError('joining_date')" class="mt-1 text-sm text-red-600">
          {{ getError('joining_date') }}
        </p>
      </div>
    </div>

    <!-- Form Actions -->
    <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
      <button
        type="button"
        @click="$emit('cancel')"
        class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
      >
        Cancel
      </button>
      <button
        type="submit"
        :disabled="isSubmitting"
        class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50"
      >
        <div v-if="isSubmitting" class="animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2"></div>
        {{ isEditing ? 'Update Employee' : 'Create Employee' }}
      </button>
    </div>
  </form>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue'
import { useFormValidation } from '../composables/useFormValidation'
import { useDepartments } from '../composables/useDepartments'

const props = defineProps({
  employee: {
    type: Object,
    default: null
  },
  isSubmitting: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['submit', 'cancel'])

const { departments, fetchAllDepartments } = useDepartments()
const { errors, hasError, getError, validate, setServerErrors, clearErrors } = useFormValidation()

const form = ref({
  name: '',
  email: '',
  phone: '',
  department_id: '',
  joining_date: ''
})

const isEditing = computed(() => !!props.employee)
const today = computed(() => new Date().toISOString().split('T')[0])

const validationRules = {
  name: [
    { required: true, message: 'Name is required' },
    { minLength: 2, message: 'Name must be at least 2 characters' }
  ],
  email: [
    { required: true, message: 'Email is required' },
    { email: true, message: 'Please enter a valid email address' }
  ],
  phone: [
    { required: true, message: 'Phone number is required' },
    { pattern: /^[\+]?[\d\s\-\(\)]+$/, message: 'Please enter a valid phone number' }
  ],
  department_id: [
    { required: true, message: 'Department is required' }
  ],
  joining_date: [
    { required: true, message: 'Joining date is required' }
  ]
}

const handleSubmit = () => {
  clearErrors()
  
  if (validate(form.value, validationRules)) {
    emit('submit', { ...form.value })
  }
}

const setServerErrors = (serverErrors) => {
  setServerErrors(serverErrors)
}

// Watch for employee prop changes (for editing)
watch(() => props.employee, (employee) => {
  if (employee) {
    form.value = {
      name: employee.name || '',
      email: employee.email || '',
      phone: employee.phone || '',
      department_id: employee.department_id || '',
      joining_date: employee.joining_date || ''
    }
  } else {
    // Reset form for new employee
    form.value = {
      name: '',
      email: '',
      phone: '',
      department_id: '',
      joining_date: ''
    }
  }
  clearErrors()
}, { immediate: true })

onMounted(async () => {
  await fetchAllDepartments()
})

defineExpose({
  setServerErrors
})
</script>
