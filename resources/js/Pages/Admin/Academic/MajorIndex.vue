<script setup lang="ts">
import { ref, computed } from 'vue'
import { router, useForm } from '@inertiajs/vue3'

import AdminLayout from '@/Layouts/AdminLayout.vue'
import DataTable from 'primevue/datatable'
import Column from 'primevue/column'
import Dialog from 'primevue/dialog'
import Button from 'primevue/button'
import InputText from 'primevue/inputtext'
import Dropdown from 'primevue/dropdown'
import Textarea from 'primevue/textarea'
import ToggleButton from 'primevue/togglebutton'
import Toast from 'primevue/toast'
import { useToast } from 'primevue/usetoast'

const props = defineProps<{
  majors: Array<any>
  departments: Array<any>
  faculties: Array<any>
}>()

const toast = useToast()

const dialog = ref(false)
const editMode = ref(false)
const selected = ref<any>(null)

const form = useForm({
  name: '',
  name_kh: '',
  code: '',
  department_id: null as number | null,
  description: '',
  is_active: true,
})

// filter departments by faculty
const selectedFacultyId = ref<number | null>(null)

const departmentOptions = computed(() => {
  if (!selectedFacultyId.value) return props.departments
  return props.departments.filter(d => d.faculty?.id === selectedFacultyId.value)
})

const openCreate = () => {
  editMode.value = false
  selected.value = null
  form.reset()
  form.department_id = null
  selectedFacultyId.value = null
  dialog.value = true
}

const openEdit = (row: any) => {
  editMode.value = true
  selected.value = row
  form.name = row.name
  form.name_kh = row.name_kh || ''
  form.code = row.code || ''
  form.department_id = row.department_id
  form.description = row.description || ''
  form.is_active = !!row.is_active
  selectedFacultyId.value = row.department?.faculty?.id ?? null
  dialog.value = true
}

const submit = () => {
  if (editMode.value && selected.value) {
    form.put(route('admin.majors.update', selected.value.id), {
      preserveScroll: true,
      onSuccess: () => {
        dialog.value = false
        toast.add({ severity: 'success', summary: 'Success', detail: 'Major updated' })
      },
    })
  } else {
    form.post(route('admin.majors.store'), {
      preserveScroll: true,
      onSuccess: () => {
        dialog.value = false
        toast.add({ severity: 'success', summary: 'Success', detail: 'Major created' })
      },
    })
  }
}

const remove = (row: any) => {
  if (!confirm(`Delete major "${row.name}" ?`)) return
  router.delete(route('admin.majors.destroy', row.id), { preserveScroll: true })
}
</script>

<template>
  <AdminLayout title="Majors">
    <div class="p-6 space-y-4">
      <Toast />
      <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold">Major Management</h1>
        <Button label="+ Create Major" icon="pi pi-plus" @click="openCreate" />
      </div>

      <DataTable :value="props.majors" :rows="10" stripedRows paginator>
        <Column field="name" header="Major" />
        <Column field="name_kh" header="Major (KH)" />
        <Column field="code" header="Code" />

        <Column header="Department / Faculty">
          <template #body="{ data }">
            <div class="text-sm">
              <div class="font-semibold">{{ data.department?.name }}</div>
              <div class="text-xs text-gray-500">{{ data.department?.faculty?.name }}</div>
            </div>
          </template>
        </Column>

        <Column header="Status">
          <template #body="{ data }">
            <span :class="data.is_active ? 'text-green-600 font-semibold' : 'text-gray-400'">
              {{ data.is_active ? 'Active' : 'Inactive' }}
            </span>
          </template>
        </Column>

        <Column header="Actions" style="min-width: 180px;">
          <template #body="{ data }">
            <div class="flex gap-2">
              <Button icon="pi pi-pencil" severity="info" text rounded @click="openEdit(data)" />
              <Button icon="pi pi-trash" severity="danger" text rounded @click="remove(data)" />
            </div>
          </template>
        </Column>
      </DataTable>

      <Dialog v-model:visible="dialog" modal :header="editMode ? 'Edit Major' : 'Create Major'" :style="{ width: '30rem' }">
        <div class="space-y-4">
          <div>
            <label class="text-sm font-medium">Major Name *</label>
            <InputText v-model="form.name" class="w-full mt-1" />
            <p v-if="form.errors.name" class="text-sm text-red-500">{{ form.errors.name }}</p>
          </div>

          <div>
            <label class="text-sm font-medium">Major Name (KH)</label>
            <InputText v-model="form.name_kh" class="w-full mt-1" />
            <p v-if="form.errors.name_kh" class="text-sm text-red-500">{{ form.errors.name_kh }}</p>
          </div>

          <div>
            <label class="text-sm font-medium">Code</label>
            <InputText v-model="form.code" class="w-full mt-1" />
            <p v-if="form.errors.code" class="text-sm text-red-500">{{ form.errors.code }}</p>
          </div>

          <div>
            <label class="text-sm font-medium">Faculty (Filter Departments)</label>
            <Dropdown
              v-model="selectedFacultyId"
              :options="faculties"
              optionLabel="name"
              optionValue="id"
              placeholder="Select Faculty"
              class="w-full mt-1"
              showClear
            />
          </div>

          <div>
            <label class="text-sm font-medium">Department *</label>
            <Dropdown
              v-model="form.department_id"
              :options="departmentOptions"
              optionLabel="name"
              optionValue="id"
              placeholder="Select Department"
              class="w-full mt-1"
            />
            <p v-if="form.errors.department_id" class="text-sm text-red-500">{{ form.errors.department_id }}</p>
          </div>

          <div>
            <label class="text-sm font-medium">Description</label>
            <Textarea v-model="form.description" rows="3" class="w-full mt-1" />
            <p v-if="form.errors.description" class="text-sm text-red-500">{{ form.errors.description }}</p>
          </div>

          <div class="flex items-center justify-between">
            <span class="text-sm font-medium">Active</span>
            <ToggleButton v-model="form.is_active" onLabel="On" offLabel="Off" />
          </div>

          <div class="flex gap-2 justify-end mt-4">
            <Button type="button" label="Cancel" severity="secondary" outlined @click="dialog = false" />
            <Button :loading="form.processing" label="Save" @click="submit" />
          </div>
        </div>
      </Dialog>
    </div>
  </AdminLayout>
</template>
