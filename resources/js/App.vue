<template>
  <div id="app">
    <!-- Guest Layout (Login page) -->
    <router-view v-if="isGuestRoute" />

    <!-- Authenticated Layout -->
    <div v-else class="min-h-screen bg-gray-50">
      <!-- Navigation -->
      <Navbar />

      <!-- Sidebar -->
      <Sidebar :is-open="sidebarOpen" @close="sidebarOpen = false" />

      <!-- Main Content -->
      <div :class="['transition-all duration-300 ease-in-out', sidebarOpen ? 'lg:ml-64' : 'lg:ml-20']">
        <!-- Mobile header -->
        <div class="lg:hidden bg-white shadow-sm border-b border-gray-200 px-4 py-2">
          <button
            @click="sidebarOpen = !sidebarOpen"
            class="p-2 rounded-md text-gray-600 hover:text-gray-900 hover:bg-gray-100"
          >
            <MenuIcon class="h-6 w-6" />
          </button>
        </div>

        <!-- Page Content -->
        <main class="p-6">
          <router-view />
        </main>
      </div>
    </div>

    <!-- Global Notifications -->
    <Notification
      :notifications="notifications"
      @remove="removeNotification"
    />

    <!-- Global Loading -->
    <div v-if="isGlobalLoading" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg p-6 flex items-center space-x-3">
        <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-600"></div>
        <span class="text-gray-900">Loading...</span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { MenuIcon } from 'lucide-vue-next'
import { useAuth } from './composables/useAuth'
import { useNotifications } from './composables/useNotifications'
</Script>

import Navbar