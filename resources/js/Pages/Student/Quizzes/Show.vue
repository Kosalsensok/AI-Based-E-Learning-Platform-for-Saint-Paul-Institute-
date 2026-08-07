<script setup lang="ts">
import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import StudentLayout from '@/Layouts/StudentLayout.vue'

const props = defineProps<{
  quiz: any,
  attempts_used: number,
  attempt_number: number
}>()

const currentQuestionIndex = ref(0)
const answers = ref<Record<number, any>>({})
const isSubmitting = ref(false)
const result = ref<any>(null)

const currentQuestion = computed(() => {
  return props.quiz.questions[currentQuestionIndex.value]
})

const isLastQuestion = computed(() => {
  return currentQuestionIndex.value === props.quiz.questions.length - 1
})

const allAnswered = computed(() => {
  return props.quiz.questions.every((q: any) => {
    return answers.value[q.id] !== undefined && answers.value[q.id] !== '' && answers.value[q.id] !== null
  })
})

const nextQuestion = () => {
  if (!isLastQuestion.value) {
    currentQuestionIndex.value++
  }
}

const prevQuestion = () => {
  if (currentQuestionIndex.value > 0) {
    currentQuestionIndex.value--
  }
}

const submitQuiz = () => {
  isSubmitting.value = true
  fetch(route('student.quiz.submit', props.quiz.id), {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': (document.head.querySelector('meta[name="csrf-token"]') as HTMLMetaElement)?.content || ''
    },
    body: JSON.stringify({
      answers: answers.value,
      client_uuid: crypto.randomUUID()
    })
  }).then(res => res.json()).then(data => {
    isSubmitting.value = false
    result.value = data
  }).catch(() => {
    isSubmitting.value = false
    alert('Failed to submit quiz. Please try again.')
  })
}
</script>

<template>
  <StudentLayout :title="quiz.title">
    <div class="max-w-4xl mx-auto p-6">
      
      <div v-if="result" class="bg-white dark:bg-gray-800 rounded-xl shadow p-8 text-center border border-gray-100 dark:border-gray-700">
        <i v-if="result.passed" class="pi pi-check-circle text-6xl text-green-500 mb-4"></i>
        <i v-else class="pi pi-times-circle text-6xl text-red-500 mb-4"></i>
        
        <h1 class="text-3xl font-bold mb-2">{{ result.passed ? 'Congratulations!' : 'Keep Trying!' }}</h1>
        <p class="text-gray-500 mb-6">You scored {{ result.score }}% on this quiz.</p>
        
        <div class="flex justify-center gap-4">
          <button @click="router.get(route('student.courses'))" class="bg-primary text-white px-6 py-2 rounded-lg font-bold hover:bg-primary/90">
            Back to Courses
          </button>
          <button v-if="!result.passed && Number(attempts_used) + 1 < quiz.max_attempts" @click="router.reload()" class="bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-white px-6 py-2 rounded-lg font-bold">
            Retake Quiz
          </button>
        </div>
      </div>
      
      <div v-else>
        <div class="mb-6 flex items-end justify-between">
          <div>
            <button @click="router.get(route('student.courses'))" class="text-primary hover:underline mb-2 inline-block text-sm">
              &larr; Back to Course
            </button>
            <h1 class="text-2xl font-bold">{{ quiz.title }}</h1>
            <p class="text-sm text-gray-500">Attempt {{ attempt_number }} of {{ quiz.max_attempts }}</p>
          </div>
          <div class="text-sm font-bold bg-primary/10 text-primary px-3 py-1 rounded">
            Question {{ currentQuestionIndex + 1 }} / {{ quiz.questions.length }}
          </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 md:p-8 border border-gray-100 dark:border-gray-700">
          
          <div v-if="currentQuestion">
            <h3 class="text-xl font-bold mb-6">{{ currentQuestion.question }}</h3>
            
            <div v-if="currentQuestion.type === 'multiple_choice'" class="space-y-3">
              <label v-for="(opt, idx) in currentQuestion.options" :key="idx" 
                class="flex items-center p-4 border dark:border-gray-700 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                :class="{'bg-primary/5 border-primary': answers[currentQuestion.id] === opt}">
                <input type="radio" :name="`q_${currentQuestion.id}`" :value="opt" v-model="answers[currentQuestion.id]" class="w-5 h-5 text-primary border-gray-300 focus:ring-primary" />
                <span class="ml-3">{{ opt }}</span>
              </label>
            </div>
            
            <div v-else-if="currentQuestion.type === 'true_false'" class="space-y-3">
              <label class="flex items-center p-4 border dark:border-gray-700 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                :class="{'bg-primary/5 border-primary': answers[currentQuestion.id] === 'true'}">
                <input type="radio" :name="`q_${currentQuestion.id}`" value="true" v-model="answers[currentQuestion.id]" class="w-5 h-5 text-primary border-gray-300 focus:ring-primary" />
                <span class="ml-3">True</span>
              </label>
              <label class="flex items-center p-4 border dark:border-gray-700 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                :class="{'bg-primary/5 border-primary': answers[currentQuestion.id] === 'false'}">
                <input type="radio" :name="`q_${currentQuestion.id}`" value="false" v-model="answers[currentQuestion.id]" class="w-5 h-5 text-primary border-gray-300 focus:ring-primary" />
                <span class="ml-3">False</span>
              </label>
            </div>
            
            <div v-else-if="currentQuestion.type === 'fill_blank'">
              <input type="text" v-model="answers[currentQuestion.id]" class="w-full px-4 py-3 border rounded-lg focus:ring-primary focus:border-primary border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900" placeholder="Type your answer here..." />
            </div>

            <div v-else-if="currentQuestion.type === 'essay'">
              <textarea v-model="answers[currentQuestion.id]" rows="5" class="w-full px-4 py-3 border rounded-lg focus:ring-primary focus:border-primary border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900" placeholder="Write your essay answer here..."></textarea>
            </div>
          </div>
          <div v-else class="text-center text-gray-500 py-8">
            No questions available in this quiz.
          </div>

          <!-- Controls -->
          <div class="mt-8 pt-6 border-t dark:border-gray-700 flex justify-between items-center">
            <button @click="prevQuestion" :disabled="currentQuestionIndex === 0" class="px-6 py-2 rounded-lg font-bold border dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed">
              Previous
            </button>
            
            <button v-if="!isLastQuestion" @click="nextQuestion" class="bg-primary text-white px-6 py-2 rounded-lg font-bold hover:bg-primary/90">
              Next
            </button>
            
            <button v-if="isLastQuestion" @click="submitQuiz" :disabled="!allAnswered || isSubmitting" class="bg-green-600 text-white px-8 py-2 rounded-lg font-bold hover:bg-green-700 disabled:opacity-50 disabled:cursor-not-allowed">
              {{ isSubmitting ? 'Submitting...' : 'Submit Quiz' }}
            </button>
          </div>
        </div>
      </div>
      
    </div>
  </StudentLayout>
</template>
