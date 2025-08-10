<!-- pages/Employees.vue -->
<template>
  <div class="space-y-6">
    <div class="flex justify-between items-center">
      <h1 class="text-2xl font-bold text-gray-900">Employees</h1>
    </div>

    <data-table
      :items="employees.data"
      :columns="columns"
      :pagination="employees.meta"
      :can-create="authStore.hasPermission('employees.create')"
      @create="showCreateModal"
      @search="handleSearch"
      @sort="handleSort"
      @page-changed="handlePageChange"
    >
      <template #actions="{ item }">
        <div class="flex space-x-2">
          <button
            v-if="authStore.hasPermission('employees.edit')"
            @click="editEmployee(item)"
            class="text-blue-600 hover:text-blue-900"
          >
            Edit
          </button>
          <button
            v-if="authStore.hasPermission('employees.delete')"
            @click="deleteEmployee(item)"
            class="text-red-600 hover:text-red-900"
          >
            Delete
          </button>
        </div>
      </template>
    </data-table>

    <!-- Employee Form Modal -->
    <employee-form-modal
      v-if="showModal"
      :employee="selectedEmployee"
      @close="closeModal"
      @saved="handleEmployeeSaved"
    />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useAuthStore } from '../stores/auth'
import { useEmployeeStore } from '../stores/employees'
import DataTable from '../components/DataTable.vue'
import EmployeeFormModal from '../components/EmployeeFormModal.vue'

const authStore = useAuthStore()
const employeeStore = useEmployeeStore()

const employees = ref({ data: [], meta: {} })
const showModal = ref(false)
const selectedEmployee = ref(null)

const columns = [
  { key: 'name', label: 'Name' },
  { key: 'email', label: 'Email' },
  { key: 'phone', label: 'Phone' },
  { key: 'department.name', label: 'Department' },
  { key: 'joining_date', label: 'Joining Date' },
  { key: 'actions', label: 'Actions' }
]

onMounted(async () => {
  await loadEmployees()
})

const loadEmployees = async (params = {}) => {
  employees.value = await employeeStore.fetchEmployees(params)
}

const handleSearch = (search) => {
  loadEmployees({ search })
}

const handleSort = ({ field, direction }) => {
  loadEmployees({ sort: field, order: direction })
}

const handlePageChange = (page) => {
  loadEmployees({ page })
}
</script>