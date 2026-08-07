<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import Button from 'primevue/button'
import InputText from 'primevue/inputtext'
import Textarea from 'primevue/textarea'
import Dropdown from 'primevue/dropdown'
import Toast from 'primevue/toast'
import DataTable from 'primevue/datatable'
import Column from 'primevue/column'
import { useToast } from 'primevue/usetoast'
import dayjs from 'dayjs'

const props = defineProps<{
  notifications: any[]
}>()

const toast = useToast()

const targetOptions = [
  { label: 'All Users', value: 'all' },
  { label: 'Teachers Only', value: 'teachers' },
  { label: 'Students Only', value: 'students' },
]

const form = useForm({
  title: '',
  message: '',
  target: 'all',
})

const sendNotification = () => {
  form.post(route('admin.notifications.send'), {
    preserveScroll: true,
    onSuccess: () => {
      form.reset()
      toast.add({ severity: 'success', summary: 'Sent', detail: 'Notification broadcasted successfully!' })
    }
  })
}

const formatDate = (dateString: string) => {
  if (!dateString) return '-'
  return dayjs(dateString).format('DD MMM YYYY, HH:mm')
}
</script>

<template>
  <AdminLayout title="Notifications">
    <div class="p-6 space-y-6">
      <Toast />
      
      <div>
        <h1 class="text-2xl font-bold">Broadcast Notifications</h1>
        <p class="text-sm text-gray-500">Send system-wide announcements to users via dashboard and email.</p>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Compose form -->
        <div class="lg:col-span-1 bg-white dark:bg-gray-800 rounded-xl shadow p-6 space-y-5 h-fit border border-gray-100 dark:border-gray-700">
          <h2 class="text-lg font-bold border-b pb-2 mb-4">Compose New</h2>
          <div>
            <label class="block text-sm font-medium mb-1">Target Audience *</label>
            <Dropdown
              v-model="form.target"
              :options="targetOptions"
              optionLabel="label"
              optionValue="value"
              class="w-full"
            />
            <p v-if="form.errors.target" class="text-sm text-red-500 mt-1">{{ form.errors.target }}</p>
          </div>

          <div>
            <label class="block text-sm font-medium mb-1">Notification Title *</label>
            <InputText v-model="form.title" class="w-full" placeholder="e.g. System Maintenance" />
            <p v-if="form.errors.title" class="text-sm text-red-500 mt-1">{{ form.errors.title }}</p>
          </div>

          <div>
            <label class="block text-sm font-medium mb-1">Message Content *</label>
            <Textarea v-model="form.message" rows="5" class="w-full" placeholder="Type your message here..." />
            <p v-if="form.errors.message" class="text-sm text-red-500 mt-1">{{ form.errors.message }}</p>
          </div>

          <div class="pt-4 flex justify-end border-t dark:border-gray-700">
            <Button
              label="Send Broadcast"
              icon="pi pi-send"
              :loading="form.processing"
              @click="sendNotification"
              class="w-full"
            />
          </div>
        </div>

        <!-- History -->
        <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-xl shadow p-6 border border-gray-100 dark:border-gray-700">
           <h2 class="text-lg font-bold border-b pb-2 mb-4">Past Broadcasts</h2>
           <DataTable :value="notifications" paginator :rows="10">
              <template #empty>
                <div class="p-4 text-center text-gray-500">No past notifications.</div>
              </template>
              
              <Column field="title" header="Title" sortable>
                <template #body="{ data }">
                  <div class="font-bold">{{ data.title }}</div>
                  <div class="text-xs text-gray-500 line-clamp-1">{{ data.message }}</div>
                </template>
              </Column>
              
              <Column field="target" header="Audience" sortable>
                <template #body="{ data }">
                  <span class="px-2 py-1 bg-gray-100 dark:bg-gray-700 rounded text-xs capitalize">{{ data.target }}</span>
                </template>
              </Column>
              
              <Column field="created_at" header="Sent On" sortable>
                <template #body="{ data }">
                  <span class="text-sm">{{ formatDate(data.created_at) }}</span>
                </template>
              </Column>
           </DataTable>
        </div>

      </div>
    </div>
  </AdminLayout>
</template>
