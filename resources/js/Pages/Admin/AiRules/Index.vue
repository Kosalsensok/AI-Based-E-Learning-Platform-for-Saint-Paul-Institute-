<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import DataTable from 'primevue/datatable'
import Column from 'primevue/column'
import Button from 'primevue/button'
import InputNumber from 'primevue/inputnumber'
import Toast from 'primevue/toast'
import { useToast } from 'primevue/usetoast'
import dayjs from 'dayjs'

const props = defineProps<{
  recommendations: Array<any>
  activeRules: {
    advance_threshold: number
    review_threshold: number
    remedial_threshold: number
    idle_days: number
  }
}>()

const toast = useToast()

const form = useForm({
  advance_threshold: props.activeRules.advance_threshold,
  review_threshold: props.activeRules.review_threshold,
  remedial_threshold: props.activeRules.remedial_threshold,
  idle_days: props.activeRules.idle_days,
})

const updateRules = () => {
  form.post(route('admin.airules.update'), {
    preserveScroll: true,
    onSuccess: () => {
      toast.add({ severity: 'success', summary: 'Updated', detail: 'AI Rules updated successfully!' })
    }
  })
}

const formatDate = (dateString: string) => {
  if (!dateString) return '-'
  return dayjs(dateString).format('DD MMM YYYY, HH:mm')
}
</script>

<template>
  <AdminLayout title="AI Recommendations">
    <div class="p-6 space-y-6">
      <Toast />
      
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-2xl font-bold">AI Recommendations</h1>
          <p class="text-sm text-gray-500">Configure AI triggers and view generated insights for students.</p>
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Configuration Form -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
          <h2 class="text-lg font-semibold mb-4">Configuration Rules</h2>
          <div class="space-y-5">
            <div>
              <label class="block text-sm font-medium mb-1">Advance Threshold (Score >= %)</label>
              <InputNumber v-model="form.advance_threshold" class="w-full" :min="0" :max="100" />
              <p class="text-xs text-gray-500 mt-1">Triggers 'Ready for next module'</p>
              <p v-if="form.errors.advance_threshold" class="text-sm text-red-500 mt-1">{{ form.errors.advance_threshold }}</p>
            </div>
            
            <div>
              <label class="block text-sm font-medium mb-1">Review Threshold (Score >= %)</label>
              <InputNumber v-model="form.review_threshold" class="w-full" :min="0" :max="100" />
              <p class="text-xs text-gray-500 mt-1">Triggers 'Review current module'</p>
              <p v-if="form.errors.review_threshold" class="text-sm text-red-500 mt-1">{{ form.errors.review_threshold }}</p>
            </div>

            <div>
              <label class="block text-sm font-medium mb-1">Remedial Threshold (Score <= %)</label>
              <InputNumber v-model="form.remedial_threshold" class="w-full" :min="0" :max="100" />
              <p class="text-xs text-gray-500 mt-1">Triggers 'Remedial content suggested'</p>
              <p v-if="form.errors.remedial_threshold" class="text-sm text-red-500 mt-1">{{ form.errors.remedial_threshold }}</p>
            </div>

            <div>
              <label class="block text-sm font-medium mb-1">Max Idle Days</label>
              <InputNumber v-model="form.idle_days" class="w-full" :min="1" :max="30" />
              <p class="text-xs text-gray-500 mt-1">Triggers 'Re-engagement suggested'</p>
              <p v-if="form.errors.idle_days" class="text-sm text-red-500 mt-1">{{ form.errors.idle_days }}</p>
            </div>

            <div class="pt-4 flex justify-end">
              <Button
                label="Save Rules"
                icon="pi pi-save"
                :loading="form.processing"
                @click="updateRules"
              />
            </div>
          </div>
        </div>

        <!-- System Stats Summary (Optional, placeholder for future) -->
        <div class="bg-indigo-50 dark:bg-indigo-900/20 rounded-xl shadow p-6 flex flex-col items-center justify-center text-center">
            <i class="pi pi-sparkles text-indigo-500 text-5xl mb-4"></i>
            <h3 class="text-xl font-bold text-indigo-900 dark:text-indigo-100">AI Engine Active</h3>
            <p class="text-indigo-700 dark:text-indigo-300 mt-2">
              The AI Recommendation system is actively monitoring student progress and generating insights based on the configured rules.
            </p>
            <div class="mt-6 text-sm font-semibold text-indigo-800 dark:text-indigo-200 bg-indigo-100 dark:bg-indigo-900/50 px-4 py-2 rounded-full">
               Total Insights Generated: {{ props.recommendations.length }}
            </div>
        </div>
      </div>

      <!-- Generated Recommendations Table -->
      <div class="mt-8">
        <h2 class="text-lg font-semibold mb-4">Recent AI Insights</h2>
        <DataTable :value="props.recommendations" :rows="10" stripedRows paginator>
          <Column header="Date">
            <template #body="{ data }">
              <span class="text-sm text-gray-500">{{ formatDate(data.created_at) }}</span>
            </template>
          </Column>

          <Column header="Student">
            <template #body="{ data }">
              <div class="font-medium text-gray-700">{{ data.user?.name }}</div>
            </template>
          </Column>

          <Column header="Type">
            <template #body="{ data }">
              <span
                class="px-2.5 py-1 rounded text-xs capitalize font-bold"
                :class="{
                  'bg-emerald-100 text-emerald-800': data.type === 'next_module',
                  'bg-blue-100 text-blue-800': data.type === 'review',
                  'bg-orange-100 text-orange-800': data.type === 'remedial',
                  'bg-red-100 text-red-800': data.type === 'weak_topic',
                  'bg-purple-100 text-purple-800': data.type === 're_engage'
                }"
              >
                {{ data.type.replace('_', ' ') }}
              </span>
            </template>
          </Column>

          <Column field="reason" header="Reason / Detail" />

          <Column header="Status">
            <template #body="{ data }">
              <span v-if="data.is_dismissed" class="text-xs text-gray-400">Dismissed</span>
              <span v-else class="text-xs text-emerald-600 font-semibold">Active</span>
            </template>
          </Column>
        </DataTable>
      </div>

    </div>
  </AdminLayout>
</template>
