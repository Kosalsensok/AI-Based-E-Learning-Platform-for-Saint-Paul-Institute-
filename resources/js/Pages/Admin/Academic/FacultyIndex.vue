<script setup lang="ts">
import { ref } from 'vue'
import { router, useForm } from '@inertiajs/vue3'

import AdminLayout from '@/Layouts/AdminLayout.vue'
import DataTable from 'primevue/datatable'
import Column from 'primevue/column'
import Dialog from 'primevue/dialog'
import Button from 'primevue/button'
import InputText from 'primevue/inputtext'
import Textarea from 'primevue/textarea'
import ToggleButton from 'primevue/togglebutton'
import Toast from 'primevue/toast'
import { useToast } from 'primevue/usetoast'

const props = defineProps<{
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
  description: '',
  is_active: true,
})

const openCreate = () => {
  editMode.value = false
  selected.value = null
  form.reset()
  dialog.value = true
}

const openEdit = (row: any) => {
  editMode.value = true
  selected.value = row
  form.name = row.name
  form.name_kh = row.name_kh || ''
  form.code = row.code || ''
  form.description = row.description || ''
  form.is_active = !!row.is_active
  dialog.value = true
}

const submit = () => {
  if (editMode.value && selected.value) {
    form.put(route('admin.faculties.update', selected.value.id), {
      preserveScroll: true,
      onSuccess: () => {
        dialog.value = false
        toast.add({ severity: 'success', summary: 'Success', detail: 'Faculty updated' })
      },
    })
  } else {
    form.post(route('admin.faculties.store'), {
      preserveScroll: true,
      onSuccess: () => {
        dialog.value = false
        toast.add({ severity: 'success', summary: 'Success', detail: 'Faculty created' })
      },
    })
  }
}

const remove = (row: any) => {
  if (!confirm(`Delete faculty "${row.name}" ?`)) return
  router.delete(route('admin.faculties.destroy', row.id), { preserveScroll: true })
}
</script>

<template>
  <AdminLayout title="Faculties">
    <div class="p-6 space-y-4">
      <Toast />
      <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold">Faculty Management</h1>
        <Button label="+ Create Faculty" icon="pi pi-plus" @click="openCreate" />
      </div>

      <DataTable :value="props.faculties" :rows="10" stripedRows paginator>
        <Column field="name" header="Name" />
        <Column field="name_kh" header="Name (KH)" />
        <Column field="code" header="Code" />
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

      <Dialog v-model:visible="dialog" modal :header="editMode ? 'Edit Faculty' : 'Create Faculty'" :style="{ width: '28rem' }">
        <div class="space-y-4">
          <div>
            <label class="text-sm font-medium">Name *</label>
            <InputText v-model="form.name" class="w-full mt-1" />
            <p v-if="form.errors.name" class="text-sm text-red-500">{{ form.errors.name }}</p>
          </div>

          <div>
            <label class="text-sm font-medium">Name (KH)</label>
            <InputText v-model="form.name_kh" class="w-full mt-1" />
            <p v-if="form.errors.name_kh" class="text-sm text-red-500">{{ form.errors.name_kh }}</p>
          </div>

          <div>
            <label class="text-sm font-medium">Code</label>
            <InputText v-model="form.code" class="w-full mt-1" />
            <p v-if="form.errors.code" class="text-sm text-red-500">{{ form.errors.code }}</p>
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
