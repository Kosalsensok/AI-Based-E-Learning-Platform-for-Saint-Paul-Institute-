<script setup lang="ts">
import { ref } from 'vue'
import { router, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import DataTable from 'primevue/datatable'
import Column from 'primevue/column'
import Button from 'primevue/button'
import Dialog from 'primevue/dialog'
import InputText from 'primevue/inputtext'
import Textarea from 'primevue/textarea'
import InputNumber from 'primevue/inputnumber'
import Dropdown from 'primevue/dropdown'
import ToggleButton from 'primevue/togglebutton'
import Toast from 'primevue/toast'
import { useToast } from 'primevue/usetoast'

const props = defineProps<{
  courses: Array<any>
  teachers: Array<any>
  majors: Array<any>
}>()

const toast = useToast()

const dialog = ref(false)
const editMode = ref(false)
const selectedCourse = ref<any>(null)

const learningModeOptions = [
  { label: 'Self Paced', value: 'self_paced' },
  { label: 'Instructor Led', value: 'instructor_led' },
  { label: 'Hybrid', value: 'hybrid' }
]

const statusOptions = [
  { label: 'Draft', value: 'draft' },
  { label: 'Pending Approval', value: 'pending_approval' },
  { label: 'Published', value: 'published' },
  { label: 'Rejected', value: 'rejected' }
]

const form = useForm({
  title: '',
  code: '',
  description: '',
  teacher_id: null as number | null,
  major_id: null as number | null,
  learning_mode: 'self_paced',
  is_paid: false,
  price: 0,
  status: 'published'
})

const openCreate = () => {
  editMode.value = false
  selectedCourse.value = null
  form.reset()
  if (props.teachers.length > 0) {
    form.teacher_id = props.teachers[0].id
  }
  dialog.value = true
}

const openEdit = (course: any) => {
  editMode.value = true
  selectedCourse.value = course
  form.title = course.title
  form.code = course.code || ''
  form.description = course.description || ''
  form.teacher_id = course.teacher_id
  form.major_id = course.major_id
  form.learning_mode = course.learning_mode || 'self_paced'
  form.is_paid = !!course.is_paid
  form.price = Number(course.price) || 0
  form.status = course.status
  dialog.value = true
}

const submitForm = () => {
  if (editMode.value && selectedCourse.value) {
    form.put(route('admin.courses.update', selectedCourse.value.id), {
      preserveScroll: true,
      onSuccess: () => {
        dialog.value = false
        toast.add({ severity: 'success', summary: 'Success', detail: 'Course updated successfully' })
      }
    })
  } else {
    form.post(route('admin.courses.store'), {
      preserveScroll: true,
      onSuccess: () => {
        dialog.value = false
        toast.add({ severity: 'success', summary: 'Success', detail: 'Course created successfully' })
      }
    })
  }
}

const approveCourse = (course: any) => {
  if (!confirm(`Are you sure you want to approve "${course.title}"?`)) return
  router.post(route('admin.courses.approve', course.id), {}, {
    preserveScroll: true,
    onSuccess: () => toast.add({ severity: 'success', summary: 'Success', detail: 'Course Approved' })
  })
}

const rejectCourse = (course: any) => {
  if (!confirm(`Are you sure you want to reject "${course.title}"?`)) return
  router.post(route('admin.courses.reject', course.id), {}, {
    preserveScroll: true,
    onSuccess: () => toast.add({ severity: 'warn', summary: 'Success', detail: 'Course Rejected' })
  })
}

const deleteCourse = (course: any) => {
  if (!confirm(`Are you sure you want to permanently delete "${course.title}"?`)) return
  router.delete(route('admin.courses.destroy', course.id), {
    preserveScroll: true,
    onSuccess: () => toast.add({ severity: 'success', summary: 'Success', detail: 'Course Deleted' })
  })
}
</script>

<template>
  <AdminLayout title="Course Approvals & Management">
    <div class="p-6 space-y-4">
      <Toast />
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-2xl font-bold">Course Management</h1>
          <p class="text-sm text-gray-500">Create, edit, approve, or delete system courses.</p>
        </div>
        <Button label="+ Add New Course" icon="pi pi-plus" severity="primary" @click="openCreate" />
      </div>

      <DataTable :value="props.courses" :rows="10" stripedRows paginator>
        <Column field="title" header="Course Title" sortable />
        
        <Column header="Teacher">
          <template #body="{ data }">
            <span class="font-medium text-gray-700">{{ data.teacher?.name || 'Unknown' }}</span>
          </template>
        </Column>
        
        <Column header="Major">
          <template #body="{ data }">
            <span class="text-gray-600">{{ data.major?.name || 'General' }}</span>
          </template>
        </Column>
        
        <Column header="Type / Price">
          <template #body="{ data }">
            <span v-if="data.is_paid" class="font-semibold text-emerald-600">${{ data.price }}</span>
            <span v-else class="text-xs bg-gray-100 text-gray-600 px-2 py-0.5 rounded font-bold">FREE</span>
          </template>
        </Column>

        <Column header="Status">
          <template #body="{ data }">
            <span
              class="px-2.5 py-1 rounded text-xs capitalize font-bold"
              :class="{
                'bg-yellow-100 text-yellow-800': data.status === 'pending_approval',
                'bg-emerald-100 text-emerald-800': data.status === 'published',
                'bg-gray-100 text-gray-600': data.status === 'draft',
                'bg-red-100 text-red-800': data.status === 'rejected'
              }"
            >
              {{ data.status.replace('_', ' ') }}
            </span>
          </template>
        </Column>

        <Column header="Actions" style="min-width: 180px;">
          <template #body="{ data }">
            <div class="flex gap-1 items-center">
              <Button icon="pi pi-pencil" severity="info" text rounded @click="openEdit(data)" tooltip="Edit" />
              
              <Button v-if="data.status === 'pending_approval'" icon="pi pi-check" severity="success" text rounded @click="approveCourse(data)" tooltip="Approve" />
              <Button v-if="data.status === 'pending_approval'" icon="pi pi-times" severity="warning" text rounded @click="rejectCourse(data)" tooltip="Reject" />

              <Button v-if="data.status === 'published'" icon="pi pi-ban" severity="warning" text rounded @click="rejectCourse(data)" tooltip="Unpublish" />
              <Button v-if="data.status === 'rejected'" icon="pi pi-check" severity="success" text rounded @click="approveCourse(data)" tooltip="Approve" />

              <Button icon="pi pi-trash" severity="danger" text rounded @click="deleteCourse(data)" tooltip="Delete" />
            </div>
          </template>
        </Column>
      </DataTable>

      <!-- Create / Edit Dialog -->
      <Dialog v-model:visible="dialog" modal :header="editMode ? 'Edit Course' : 'Create Course'" :style="{ width: '32rem' }">
        <form @submit.prevent="submitForm" class="space-y-4 pt-2">
          <div>
            <label class="block text-sm font-medium mb-1">Course Title *</label>
            <InputText v-model="form.title" class="w-full" placeholder="e.g. Web Development 101" required />
            <p v-if="form.errors.title" class="text-xs text-red-500 mt-1">{{ form.errors.title }}</p>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium mb-1">Course Code</label>
              <InputText v-model="form.code" class="w-full" placeholder="e.g. CS-101" />
            </div>
            <div>
              <label class="block text-sm font-medium mb-1">Learning Mode</label>
              <Dropdown v-model="form.learning_mode" :options="learningModeOptions" optionLabel="label" optionValue="value" class="w-full" />
            </div>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium mb-1">Teacher *</label>
              <Dropdown v-model="form.teacher_id" :options="props.teachers" optionLabel="name" optionValue="id" placeholder="Select Teacher" class="w-full" required />
              <p v-if="form.errors.teacher_id" class="text-xs text-red-500 mt-1">{{ form.errors.teacher_id }}</p>
            </div>
            <div>
              <label class="block text-sm font-medium mb-1">Major</label>
              <Dropdown v-model="form.major_id" :options="props.majors" optionLabel="name" optionValue="id" placeholder="Select Major (Optional)" class="w-full" />
            </div>
          </div>

          <div>
            <label class="block text-sm font-medium mb-1">Description</label>
            <Textarea v-model="form.description" rows="3" class="w-full" placeholder="Course outline & summary..." />
          </div>

          <div class="grid grid-cols-2 gap-4 items-center bg-gray-50 dark:bg-gray-700/50 p-3 rounded-lg border">
            <div class="flex items-center justify-between">
              <span class="text-sm font-medium">Paid Course?</span>
              <ToggleButton v-model="form.is_paid" onLabel="Paid" offLabel="Free" />
            </div>
            <div v-if="form.is_paid">
              <label class="block text-xs font-medium mb-1">Price ($)</label>
              <InputNumber v-model="form.price" mode="currency" currency="USD" locale="en-US" class="w-full" />
            </div>
          </div>

          <div>
            <label class="block text-sm font-medium mb-1">Status</label>
            <Dropdown v-model="form.status" :options="statusOptions" optionLabel="label" optionValue="value" class="w-full" />
          </div>

          <div class="flex justify-end gap-2 pt-4 border-t dark:border-gray-700">
            <Button type="button" label="Cancel" severity="secondary" outlined @click="dialog = false" />
            <Button type="submit" :label="editMode ? 'Update Course' : 'Create Course'" icon="pi pi-check" :loading="form.processing" />
          </div>
        </form>
      </Dialog>
    </div>
  </AdminLayout>
</template>
