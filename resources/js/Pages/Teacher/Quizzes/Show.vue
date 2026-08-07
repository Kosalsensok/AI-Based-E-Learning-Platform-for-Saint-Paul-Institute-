<script setup lang="ts">
import { ref } from 'vue'
import { useForm, router, Link } from '@inertiajs/vue3'
import TeacherLayout from '@/Layouts/TeacherLayout.vue'
import Button from 'primevue/button'
import Dialog from 'primevue/dialog'
import InputText from 'primevue/inputtext'
import Dropdown from 'primevue/dropdown'
import InputNumber from 'primevue/inputnumber'
import Toast from 'primevue/toast'
import { useToast } from 'primevue/usetoast'
import Textarea from 'primevue/textarea'

const props = defineProps<{
  quiz: any
}>()

const toast = useToast()
const showDialog = ref(false)
const editMode = ref(false)
const selectedQuestion = ref<any>(null)

const form = useForm({
  type: 'mcq',
  question: '',
  options: [] as string[],
  correct_answer: null as any,
  points: 1
})

const questionTypes = [
  { label: 'Multiple Choice', value: 'mcq' },
  { label: 'True / False', value: 'true_false' },
  { label: 'Short Answer (Fill Blank)', value: 'fill_blank' },
  { label: 'Essay', value: 'essay' }
]

const openNew = () => {
  editMode.value = false
  form.reset()
  if (form.type === 'mcq') form.options = ['', '', '', '']
  showDialog.value = true
}

const openEdit = (question: any) => {
  editMode.value = true
  selectedQuestion.value = question
  form.type = question.type
  form.question = question.question
  form.options = question.options || []
  form.correct_answer = question.correct_answer
  form.points = question.points
  showDialog.value = true
}

const submitForm = () => {
  if (editMode.value && selectedQuestion.value) {
    form.put(route('teacher.questions.update', selectedQuestion.value.id), {
      onSuccess: () => {
        showDialog.value = false
        toast.add({ severity: 'success', summary: 'Success', detail: 'Question updated successfully' })
      }
    })
  } else {
    form.post(route('teacher.quizzes.questions.store', props.quiz.id), {
      onSuccess: () => {
        showDialog.value = false
        toast.add({ severity: 'success', summary: 'Success', detail: 'Question created successfully' })
      }
    })
  }
}

const deleteQuestion = (question: any) => {
  if (confirm('Are you sure you want to delete this question?')) {
    router.delete(route('teacher.questions.destroy', question.id), {
      onSuccess: () => toast.add({ severity: 'success', summary: 'Success', detail: 'Question deleted successfully' })
    })
  }
}

const addOption = () => {
  form.options.push('')
}
const removeOption = (index: number) => {
  form.options.splice(index, 1)
}
</script>

<template>
  <TeacherLayout :title="`Quiz: ${quiz.title}`">
    <div class="p-6">
      <Toast />
      
      <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-3">
          <Link :href="route('teacher.quizzes.index')">
            <Button icon="pi pi-arrow-left" text rounded />
          </Link>
          <div>
            <h1 class="text-2xl font-bold">{{ quiz.title }}</h1>
            <p class="text-sm text-gray-500">Manage questions and configurations</p>
          </div>
        </div>
        <Button label="Add Question" icon="pi pi-plus" @click="openNew" />
      </div>

      <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 space-y-4">
        <div v-if="!quiz.questions || quiz.questions.length === 0" class="text-center py-10 text-gray-500">
          No questions have been added to this quiz yet.
        </div>

        <div v-for="(question, index) in quiz.questions" :key="question.id" class="p-4 border dark:border-gray-700 rounded-lg bg-gray-50 dark:bg-gray-700/30">
          <div class="flex justify-between items-start">
            <div class="flex gap-4">
              <div class="font-bold text-xl text-primary">{{ Number(index) + 1 }}.</div>
              <div>
                <p class="font-semibold text-lg">{{ question.question }}</p>
                <div class="text-xs font-bold text-gray-500 uppercase tracking-wider mt-1">{{ question.type.replace('_', ' ') }} • {{ question.points }} pts</div>
                
                <!-- Display based on type -->
                <div v-if="question.type === 'mcq'" class="mt-3 space-y-1">
                  <div v-for="(opt, oIdx) in question.options" :key="oIdx" class="flex items-center gap-2">
                    <i class="pi pi-circle-fill text-xs" :class="question.correct_answer == oIdx ? 'text-green-500' : 'text-gray-300'"></i>
                    <span :class="{'font-bold text-green-700 dark:text-green-400': question.correct_answer == oIdx}">{{ opt }}</span>
                  </div>
                </div>

                <div v-else-if="question.type === 'true_false'" class="mt-3 font-bold text-green-600">
                  Answer: {{ question.correct_answer }}
                </div>

                <div v-else-if="question.type === 'fill_blank'" class="mt-3 font-bold text-green-600">
                  Correct Answer: {{ question.correct_answer }}
                </div>
              </div>
            </div>
            <div class="flex gap-2">
              <Button icon="pi pi-pencil" text rounded size="small" @click="openEdit(question)" />
              <Button icon="pi pi-trash" severity="danger" text rounded size="small" @click="deleteQuestion(question)" />
            </div>
          </div>
        </div>
      </div>

      <!-- Question Dialog -->
      <Dialog v-model:visible="showDialog" :header="editMode ? 'Edit Question' : 'Add Question'" :modal="true" class="p-fluid w-full max-w-3xl">
        <form @submit.prevent="submitForm" class="space-y-4 pt-2">
          
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium mb-1">Question Type <span class="text-red-500">*</span></label>
              <Dropdown v-model="form.type" :options="questionTypes" optionLabel="label" optionValue="value" required @change="() => {
                if (form.type === 'mcq') form.options = ['', '', '', ''];
                else form.options = [];
                form.correct_answer = null;
              }" />
            </div>
            <div>
              <label class="block text-sm font-medium mb-1">Points <span class="text-red-500">*</span></label>
              <InputNumber v-model="form.points" :min="1" required />
            </div>
          </div>

          <div>
            <label class="block text-sm font-medium mb-1">Question Text <span class="text-red-500">*</span></label>
            <Textarea v-model="form.question" required rows="3" />
          </div>

          <!-- MCQ Options -->
          <div v-if="form.type === 'mcq'" class="bg-gray-50 dark:bg-gray-800 p-4 rounded-lg border dark:border-gray-700">
            <div class="flex justify-between items-center mb-3">
              <label class="font-bold">Answers (Check the correct one)</label>
              <Button label="Add Option" icon="pi pi-plus" text size="small" @click="addOption" />
            </div>
            <div class="space-y-2">
              <div v-for="(opt, idx) in form.options" :key="idx" class="flex items-center gap-3">
                <input type="radio" :value="idx" v-model="form.correct_answer" class="w-5 h-5 text-primary focus:ring-primary border-gray-300" required />
                <InputText v-model="form.options[idx]" placeholder="Option text..." class="flex-1" required />
                <Button icon="pi pi-times" severity="danger" text rounded size="small" @click="removeOption(idx)" />
              </div>
            </div>
          </div>

          <!-- True/False Options -->
          <div v-if="form.type === 'true_false'" class="flex items-center gap-6 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg border dark:border-gray-700">
            <label class="font-bold">Correct Answer:</label>
            <div class="flex items-center gap-2">
              <input type="radio" value="True" v-model="form.correct_answer" required id="tf_t" />
              <label for="tf_t">True</label>
            </div>
            <div class="flex items-center gap-2">
              <input type="radio" value="False" v-model="form.correct_answer" required id="tf_f" />
              <label for="tf_f">False</label>
            </div>
          </div>

          <!-- Fill in the blank -->
          <div v-if="form.type === 'fill_blank'">
            <label class="block text-sm font-medium mb-1">Correct Answer (Exact match) <span class="text-red-500">*</span></label>
            <InputText v-model="form.correct_answer" required />
          </div>

          <!-- Essay doesn't need a correct answer defined -->
          <div v-if="form.type === 'essay'" class="text-sm text-gray-500 italic p-3 bg-blue-50 dark:bg-blue-900/20 text-blue-800 dark:text-blue-200 rounded">
            Essay questions require manual grading by the teacher.
          </div>

          <div class="flex justify-end gap-2 mt-6">
            <Button type="button" label="Cancel" severity="secondary" @click="showDialog = false" />
            <Button type="submit" label="Save Question" :loading="form.processing" />
          </div>
        </form>
      </Dialog>
    </div>
  </TeacherLayout>
</template>
