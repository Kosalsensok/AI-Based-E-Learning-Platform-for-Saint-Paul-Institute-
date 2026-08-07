<script setup lang="ts">
import AdminLayout from '@/Layouts/AdminLayout.vue'
import DataTable from 'primevue/datatable'
import Column from 'primevue/column'
import ProgressBar from 'primevue/progressbar'
import dayjs from 'dayjs'

const props = defineProps<{
  progress: Array<any>
}>()

const formatDate = (dateString: string) => {
  if (!dateString) return '-'
  return dayjs(dateString).format('DD MMM YYYY, HH:mm')
}
</script>

<template>
  <AdminLayout title="Progress Tracking">
    <div class="p-6 space-y-4">
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-2xl font-bold">Student Progress</h1>
          <p class="text-sm text-gray-500">System-wide view of student progress across all lessons.</p>
        </div>
      </div>

      <DataTable :value="props.progress" :rows="10" stripedRows paginator>
        <Column header="Last Updated">
          <template #body="{ data }">
            <span class="text-sm text-gray-500">{{ formatDate(data.updated_at) }}</span>
          </template>
        </Column>

        <Column header="Student">
          <template #body="{ data }">
            <div class="font-medium text-gray-700">{{ data.user?.name }}</div>
          </template>
        </Column>

        <Column header="Course">
          <template #body="{ data }">
            <div class="text-sm truncate w-48" :title="data.lesson?.course?.title">
              {{ data.lesson?.course?.title || 'Unknown Course' }}
            </div>
          </template>
        </Column>

        <Column header="Lesson">
          <template #body="{ data }">
            <div class="text-sm text-gray-600 truncate w-48" :title="data.lesson?.title">
              {{ data.lesson?.title }}
            </div>
          </template>
        </Column>

        <Column header="Status">
          <template #body="{ data }">
            <span
              class="px-2.5 py-1 rounded text-xs capitalize font-bold"
              :class="{
                'bg-emerald-100 text-emerald-800': data.status === 'completed',
                'bg-blue-100 text-blue-800': data.status === 'in_progress',
                'bg-gray-100 text-gray-600': data.status === 'not_started'
              }"
            >
              {{ data.status.replace('_', ' ') }}
            </span>
          </template>
        </Column>

      </DataTable>
    </div>
  </AdminLayout>
</template>
