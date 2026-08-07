<script setup lang="ts">
import { ref } from 'vue'
import { router, useForm, Link } from '@inertiajs/vue3'
import TeacherLayout from '@/Layouts/TeacherLayout.vue'
import Button from 'primevue/button'
import Dialog from 'primevue/dialog'
import InputText from 'primevue/inputtext'
import Toast from 'primevue/toast'
import { useToast } from 'primevue/usetoast'
import Accordion from 'primevue/accordion'
import AccordionTab from 'primevue/accordiontab'
import Dropdown from 'primevue/dropdown'
import Textarea from 'primevue/textarea'

const props = defineProps<{
  course: any
}>()

const toast = useToast()

// Module Dialog
const showModuleDialog = ref(false)
const moduleEditMode = ref(false)
const selectedModule = ref<any>(null)

const moduleForm = useForm({
  title: ''
})

const openNewModule = () => {
  moduleEditMode.value = false
  moduleForm.reset()
  showModuleDialog.value = true
}

const openEditModule = (mod: any) => {
  moduleEditMode.value = true
  selectedModule.value = mod
  moduleForm.title = mod.title
  showModuleDialog.value = true
}

const submitModule = () => {
  if (moduleEditMode.value && selectedModule.value) {
    moduleForm.put(route('teacher.modules.update', selectedModule.value.id), {
      onSuccess: () => {
        showModuleDialog.value = false
        toast.add({ severity: 'success', summary: 'Success', detail: 'Module Updated' })
      }
    })
  } else {
    moduleForm.post(route('teacher.courses.modules.store', props.course.id), {
      onSuccess: () => {
        showModuleDialog.value = false
        toast.add({ severity: 'success', summary: 'Success', detail: 'Module Created' })
      }
    })
  }
}

const deleteModule = (mod: any) => {
  if (confirm(`Delete module ${mod.title}? This will delete all its lessons too.`)) {
    router.delete(route('teacher.modules.destroy', mod.id), {
      onSuccess: () => toast.add({ severity: 'success', summary: 'Deleted', detail: 'Module deleted' })
    })
  }
}

// Lesson Dialog
const showLessonDialog = ref(false)
const lessonEditMode = ref(false)
const selectedLesson = ref<any>(null)
const activeModuleForNewLesson = ref<any>(null)

const lessonForm = useForm({
  title: '',
  type: 'video',
  content: '',
  video_url: '',
  is_free: false,
})

const lessonTypes = [
  { label: 'Video', value: 'video' },
  { label: 'Reading', value: 'reading' }
]

const openNewLesson = (mod: any) => {
  lessonEditMode.value = false
  activeModuleForNewLesson.value = mod
  lessonForm.reset()
  showLessonDialog.value = true
}

const openEditLesson = (lesson: any) => {
  lessonEditMode.value = true
  selectedLesson.value = lesson
  lessonForm.title = lesson.title
  lessonForm.type = lesson.type
  lessonForm.content = lesson.content || ''
  lessonForm.video_url = lesson.video_url || ''
  lessonForm.is_free = lesson.is_free
  showLessonDialog.value = true
}

const submitLesson = () => {
  if (lessonEditMode.value && selectedLesson.value) {
    lessonForm.put(route('teacher.lessons.update', selectedLesson.value.id), {
      onSuccess: () => {
        showLessonDialog.value = false
        toast.add({ severity: 'success', summary: 'Success', detail: 'Lesson Updated' })
      }
    })
  } else {
    lessonForm.post(route('teacher.modules.lessons.store', activeModuleForNewLesson.value.id), {
      onSuccess: () => {
        showLessonDialog.value = false
        toast.add({ severity: 'success', summary: 'Success', detail: 'Lesson Created' })
      }
    })
  }
}

const deleteLesson = (lesson: any) => {
  if (confirm(`Delete lesson ${lesson.title}?`)) {
    router.delete(route('teacher.lessons.destroy', lesson.id), {
      onSuccess: () => toast.add({ severity: 'success', summary: 'Deleted', detail: 'Lesson deleted' })
    })
  }
}
</script>

<template>
  <TeacherLayout :title="`Curriculum: ${course.title}`">
    <div class="p-6 space-y-6">
      <Toast />
      
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
          <Link :href="route('teacher.courses.index')">
            <Button icon="pi pi-arrow-left" text rounded />
          </Link>
          <div>
            <h1 class="text-2xl font-bold">Curriculum</h1>
            <p class="text-sm text-gray-500">{{ course.title }}</p>
          </div>
        </div>
        <Button label="Add Module" icon="pi pi-plus" @click="openNewModule" />
      </div>

      <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4">
        <div v-if="!course.modules || course.modules.length === 0" class="text-center py-8 text-gray-500">
          No modules yet. Click "Add Module" to start building your course.
        </div>
        
        <Accordion :activeIndex="0" v-else>
          <AccordionTab v-for="mod in course.modules" :key="mod.id">
            <template #header>
              <div class="flex justify-between items-center w-full pr-4">
                <span class="font-bold">{{ mod.title }}</span>
                <div class="flex gap-2" @click.stop>
                  <Button icon="pi pi-plus" size="small" label="Add Lesson" text @click="openNewLesson(mod)" />
                  <Button icon="pi pi-pencil" size="small" text rounded @click="openEditModule(mod)" />
                  <Button icon="pi pi-trash" size="small" severity="danger" text rounded @click="deleteModule(mod)" />
                </div>
              </div>
            </template>
            
            <div class="space-y-2 mt-2">
              <div v-if="!mod.lessons || mod.lessons.length === 0" class="text-sm text-gray-400 italic py-2">
                Empty module.
              </div>
              <div v-for="lesson in mod.lessons" :key="lesson.id" class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-100 dark:border-gray-700">
                <div class="flex items-center gap-3">
                  <i :class="lesson.type === 'video' ? 'pi pi-video' : 'pi pi-file'" class="text-gray-400"></i>
                  <span class="font-medium">{{ lesson.title }}</span>
                  <span v-if="lesson.is_free" class="text-xs bg-emerald-100 text-emerald-800 px-2 py-0.5 rounded font-bold">Free Preview</span>
                </div>
                <div class="flex gap-1">
                  <Button icon="pi pi-pencil" text rounded size="small" @click="openEditLesson(lesson)" />
                  <Button icon="pi pi-trash" severity="danger" text rounded size="small" @click="deleteLesson(lesson)" />
                </div>
              </div>
            </div>
          </AccordionTab>
        </Accordion>
      </div>

      <!-- Module Dialog -->
      <Dialog v-model:visible="showModuleDialog" :header="moduleEditMode ? 'Edit Module' : 'Create Module'" :modal="true" class="p-fluid w-full max-w-md">
        <form @submit.prevent="submitModule" class="space-y-4 pt-2">
          <div>
            <label class="block text-sm font-medium mb-1">Module Title <span class="text-red-500">*</span></label>
            <InputText v-model="moduleForm.title" required />
          </div>
          <div class="flex justify-end gap-2 mt-4">
            <Button type="button" label="Cancel" severity="secondary" @click="showModuleDialog = false" />
            <Button type="submit" label="Save Module" :loading="moduleForm.processing" />
          </div>
        </form>
      </Dialog>

      <!-- Lesson Dialog -->
      <Dialog v-model:visible="showLessonDialog" :header="lessonEditMode ? 'Edit Lesson' : 'Create Lesson'" :modal="true" class="p-fluid w-full max-w-2xl">
        <form @submit.prevent="submitLesson" class="space-y-4 pt-2">
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium mb-1">Lesson Title <span class="text-red-500">*</span></label>
              <InputText v-model="lessonForm.title" required />
            </div>
            <div>
              <label class="block text-sm font-medium mb-1">Type</label>
              <Dropdown v-model="lessonForm.type" :options="lessonTypes" optionLabel="label" optionValue="value" />
            </div>
          </div>

          <div v-if="lessonForm.type === 'video'">
            <label class="block text-sm font-medium mb-1">Video URL (YouTube/Vimeo Embed) <span class="text-red-500">*</span></label>
            <InputText v-model="lessonForm.video_url" required />
          </div>

          <div>
            <label class="block text-sm font-medium mb-1">Content / Reading Material</label>
            <Textarea v-model="lessonForm.content" rows="6" />
          </div>

          <div class="flex items-center gap-2">
            <input type="checkbox" id="is_free" v-model="lessonForm.is_free" class="rounded border-gray-300" />
            <label for="is_free" class="text-sm font-medium">Allow free preview</label>
          </div>

          <div class="flex justify-end gap-2 mt-4">
            <Button type="button" label="Cancel" severity="secondary" @click="showLessonDialog = false" />
            <Button type="submit" label="Save Lesson" :loading="lessonForm.processing" />
          </div>
        </form>
      </Dialog>
    </div>
  </TeacherLayout>
</template>
