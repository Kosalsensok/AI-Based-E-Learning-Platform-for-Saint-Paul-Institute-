<script setup lang="ts">
import AdminLayout from '@/Layouts/AdminLayout.vue'
import Button from 'primevue/button'
import Card from 'primevue/card'

</script>

<template>
  <AdminLayout title="System Reports">
    <div class="p-6 space-y-6">
      <div>
        <h1 class="text-2xl font-bold">System Reports & Exports</h1>
        <p class="text-sm text-gray-500">Generate analytics and download system data.</p>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        
        <Card>
          <template #title>Financial Report</template>
          <template #content>
            <p class="text-sm text-gray-600 mb-4">Export all approved payments and revenue.</p>
            <a :href="route('admin.reports.financials')" class="p-button p-component p-button-outlined w-full flex justify-center text-primary border-primary">
              <span class="pi pi-download mr-2 mt-1"></span>
              <span class="font-bold">Export CSV</span>
            </a>
          </template>
        </Card>

        <Card>
          <template #title>Enrollment Data</template>
          <template #content>
            <p class="text-sm text-gray-600 mb-4">Download a complete list of students and enrollments.</p>
            <a :href="route('admin.reports.enrollments')" class="p-button p-component p-button-success p-button-outlined w-full flex justify-center text-green-600 border-green-600">
              <span class="pi pi-file-excel mr-2 mt-1"></span>
              <span class="font-bold">Export CSV</span>
            </a>
          </template>
        </Card>

        <Card>
          <template #title>System Logs</template>
          <template #content>
            <p class="text-sm text-gray-600 mb-4">Export raw system logs for auditing and security review.</p>
            <Button label="Download ZIP" icon="pi pi-file-zip" severity="secondary" outlined class="w-full" />
          </template>
        </Card>

      </div>
    </div>
  </AdminLayout>
</template>
