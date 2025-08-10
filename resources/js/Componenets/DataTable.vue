// components/DataTable.vue
<template>
  <div class="bg-white shadow-sm rounded-lg border border-gray-200">
    <!-- Header with Search and Actions -->
    <div class="p-6 border-b border-gray-200">
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-4 sm:space-y-0">
        <div class="flex-1 max-w-md">
          <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <SearchIcon class="h-5 w-5 text-gray-400" />
            </div>
            <input
              v-model="searchTerm"
              type="text"
              :placeholder="searchPlaceholder"
              class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
              @input="onSearch"
            />
          </div>
        </div>

        <div class="flex items-center space-x-3">
          <slot name="filters"></slot>
          
          <button
            v-if="canCreate"
            @click="$emit('create')"
            class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
          >
            <PlusIcon class="h-4 w-4 mr-2" />
            {{ createButtonText }}
          </button>

          <button
            v-if="canExport"
            @click="$emit('export')"
            class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
          >
            <DownloadIcon class="h-4 w-4 mr-2" />
            Export
          </button>
        </div>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="isLoading" class="p-6 text-center">
      <div class="inline-flex items-center">
        <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-600 mr-3"></div>
        Loading...
      </div>
    </div>

    <!-- Empty State -->
    <div v-else-if="!items || items.length === 0" class="p-12 text-center">
      <div class="text-gray-500">
        <slot name="empty-state">
          <div class="text>
             <div class="text-lg font-medium mb-2">No {{ resourceName }} found</div>
          <p class="text-sm">{{ emptyMessage }}</p>
        </slot>
      </div>
    </div>

    <!-- Data Table -->
    <div v-else class="overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th
              v-for="column in columns"
              :key="column.key"
              :class="[
                'px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider',
                column.sortable ? 'cursor-pointer hover:bg-gray-100' : ''
              ]"
              @click="column.sortable && sort(column.key)"
            >
              <div class="flex items-center space-x-1">
                <span>{{ column.label }}</span>
                <div v-if="column.sortable" class="flex flex-col">
                  <ChevronUpIcon 
                    :class="[
                      'h-3 w-3',
                      sortField === column.key && sortDirection === 'asc' 
                        ? 'text-blue-600' 
                        : 'text-gray-300'
                    ]" 
                  />
                  <ChevronDownIcon 
                    :class="[
                      'h-3 w-3 -mt-1',
                      sortField === column.key && sortDirection === 'desc' 
                        ? 'text-blue-600' 
                        : 'text-gray-300'
                    ]" 
                  />
                </div>
              </div>
            </th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr
            v-for="item in items"
            :key="item.id"
            class="hover:bg-gray-50 transition-colors duration-150"
          >
            <td
              v-for="column in columns"
              :key="column.key"
              class="px-6 py-4 whitespace-nowrap text-sm"
            >
              <slot :name="`cell-${column.key}`" :item="item" :value="getNestedValue(item, column.key)">
                <span :class="column.class || 'text-gray-900'">
                  {{ getNestedValue(item, column.key) }}
                </span>
              </slot>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <div v-if="pagination && pagination.total > 0" class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
      <div class="flex items-center justify-between">
        <div class="flex-1 flex justify-between sm:hidden">
          <button
            :disabled="!pagination.prev_page_url"
            @click="$emit('page-changed', pagination.current_page - 1)"
            class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50"
          >
            Previous
          </button>
          <button
            :disabled="!pagination.next_page_url"
            @click="$emit('page-changed', pagination.current_page + 1)"
            class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50"
          >
            Next
          </button>
        </div>
        
        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
          <div>
            <p class="text-sm text-gray-700">
              Showing
              <span class="font-medium">{{ pagination.from || 0 }}</span>
              to
              <span class="font-medium">{{ pagination.to || 0 }}</span>
              of
              <span class="font-medium">{{ pagination.total }}</span>
              results
            </p>
          </div>
          
          <div>
            <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
              <button
                :disabled="!pagination.prev_page_url"
                @click="$emit('page-changed', pagination.current_page - 1)"
                class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50"
              >
                <ChevronLeftIcon class="h-5 w-5" />
              </button>
              
              <button
                v-for="page in visiblePages"
                :key="page"
                @click="$emit('page-changed', page)"
                :class="[
                  'relative inline-flex items-center px-4 py-2 border text-sm font-medium',
                  page === pagination.current_page
                    ? 'z-10 bg-blue-50 border-blue-500 text-blue-600'
                    : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50'
                ]"
              >
                {{ page }}
              </button>
              
              <button
                :disabled="!pagination.next_page_url"
                @click="$emit('page-changed', pagination.current_page + 1)"
                class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50"
              >
                <ChevronRightIcon class="h-5 w-5" />
              </button>
            </nav>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script setup>
import { ref, computed } from 'vue'
import { 
  SearchIcon, 
  PlusIcon, 
  DownloadIcon,
  ChevronUpIcon,
  ChevronDownIcon,
  ChevronLeftIcon,
  ChevronRightIcon
} from 'lucide-vue-next'

const props = defineProps({
  items: {
    type: Array,
    default: () => []
  },
  columns: {
    type: Array,
    required: true
  },
  pagination: {
    type: Object,
    default: () => ({})
  },
  isLoading: {
    type: Boolean,
    default: false
  },
  canCreate: {
    type: Boolean,
    default: false
  },
  canExport: {
    type: Boolean,
    default: false
  },
  createButtonText: {
    type: String,
    default: 'Add New'
  },
  searchPlaceholder: {
    type: String,
    default: 'Search...'
  },
  resourceName: {
    type: String,
    default: 'items'
  },
  emptyMessage: {
    type: String,
    default: 'Get started by creating a new item.'
  }
})

const emit = defineEmits([
  'create',
  'search', 
  'sort',
  'page-changed',
  'export'
])

const searchTerm = ref('')
const sortField = ref('')
const sortDirection = ref('asc')

const visiblePages = computed(() => {
  const current = props.pagination.current_page || 1
  const last = props.pagination.last_page || 1
  const delta = 2
  
  const range = []
  const rangeWithDots = []
  
  for (let i = Math.max(2, current - delta); i <= Math.min(last - 1, current + delta); i++) {
    range.push(i)
  }
  
  if (current - delta > 2) {
    rangeWithDots.push(1, '...')
  } else {
    rangeWithDots.push(1)
  }
  
  rangeWithDots.push(...range)
  
  if (current + delta < last - 1) {
    rangeWithDots.push('...', last)
  } else if (last > 1) {
    rangeWithDots.push(last)
  }
  
  return rangeWithDots.filter(page => page !== 1 || last > 1)
})

const getNestedValue = (obj, path) => {
  return path.split('.').reduce((o, p) => o?.[p], obj) || ''
}

const onSearch = () => {
  emit('search', searchTerm.value)
}

const sort = (field) => {
  if (sortField.value === field) {
    sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc'
  } else {
    sortField.value = field
    sortDirection.value = 'asc'
  }
  emit('sort', { field, direction: sortDirection.value })
}
</script>