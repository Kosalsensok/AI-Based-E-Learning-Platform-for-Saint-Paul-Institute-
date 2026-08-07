<script setup lang="ts">
import AdminLayout from '@/Layouts/AdminLayout.vue'
import DataTable from 'primevue/datatable'
import Column from 'primevue/column'

const props = defineProps<{
  liveClasses: Array<any>
  upcomingQuizzes: Array<any>
}>()
</script>

<template>
  <AdminLayout title="Global Calendar">
    <div class="p-6 space-y-8">
      
      <div>
        <h1 class="text-2xl font-bold">Academic Calendar & Events</h1>
        <p class="text-sm text-gray-500">Overview of all active instructor-led classes and upcoming assessments.</p>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- Live Classes -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
          <div class="p-4 border-b bg-blue-50 dark:bg-blue-900/20">
            <h2 class="text-lg font-semibold text-blue-800 dark:text-blue-200">
              <i class="pi pi-video mr-2"></i> Live Classes (Instructor-Led)
            </h2>
          </div>
          <DataTable :value="props.liveClasses" :rows="10" stripedRows paginator>
            <Column field="title" header="Course" />
            
            <Column header="Teacher">
              <template #body="{ data }">
                <span class="text-gray-700">{{ data.teacher?.name || 'Unknown' }}</span>
              </template>
            </Column>

            <Column header="Status">
              <template #body="{ data }">
                <span class="text-xs bg-emerald-100 text-emerald-800 px-2 py-1 rounded font-bold capitalize">
                  {{ data.status }}
                </span>
              </template>
            </Column>
          </DataTable>
        </div>

        <!-- Upcoming Quizzes -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
          <div class="p-4 border-b bg-purple-50 dark:bg-purple-900/20">
            <h2 class="text-lg font-semibold text-purple-800 dark:text-purple-200">
              <i class="pi pi-calendar mr-2"></i> Active Quizzes
            </h2>
          </div>
          <DataTable :value="props.upcomingQuizzes" :rows="10" stripedRows paginator>
            <Column field="title" header="Quiz" />
            
            <Column header="Course">
              <template #body="{ data }">
                <span class="text-gray-700 text-sm truncate block w-40" :title="data.course?.title">
                  {{ data.course?.title || '-' }}
                </span>
              </template>
            </Column>

            <Column header="Questions">
              <template #body="{ data }">
                <span class="font-bold text-gray-600">{{ data.questions_count ?? 0 }}</span>
              </template>
            </Column>
          </DataTable>
        </div>

      </div>

    </div>
  </AdminLayout>
</template>
