<script setup lang="ts">
import { ref, watch } from 'vue'

const props = defineProps<{
  show: boolean
  question: any
}>()

const emit = defineEmits<{
  (e: 'close'): void
  (e: 'submitReply', payload: any): void
}>()

const replyText = ref('')
const selectedLang = ref<'km' | 'en' | 'both'>('km')
const priority = ref<'Normal' | 'Urgent' | 'Important'>('Normal')
const notifyEmail = ref(true)
const notifyPush = ref(true)
const addToFaq = ref(false)

watch(
  () => props.question,
  (newQ) => {
    if (newQ) {
      replyText.value = newQ.answer || `💡 Likely you forgot to update the counter variable inside the loop!

Try this:
\`\`\`c
int i = 0;
while(i < 10) {
    printf("%d\\n", i);
    i++; // <-- DON'T FORGET THIS!
}
\`\`\``
    }
  },
  { immediate: true }
)

const handleSend = () => {
  emit('submitReply', {
    questionId: props.question?.id,
    replyText: replyText.value,
    lang: selectedLang.value,
    priority: priority.value,
    notifyEmail: notifyEmail.value,
    notifyPush: notifyPush.value,
    addToFaq: addToFaq.value,
  })
}

const insertCodeSnippet = () => {
  replyText.value += `\n\`\`\`c\n// Enter your C code snippet here\n\`\`\`\n`
}
</script>

<template>
  <div v-if="show" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4 z-50 overflow-y-auto">
    <div class="bg-white dark:bg-gray-800 rounded-3xl max-w-2xl w-full p-6 space-y-5 shadow-2xl border border-slate-200 dark:border-gray-700 animate-in fade-in zoom-in duration-200">
      <!-- Modal Header -->
      <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-gray-700">
        <div class="flex items-center gap-2">
          <span class="text-xl">✍️</span>
          <div>
            <h3 class="text-base font-black text-slate-900 dark:text-white">
              REPLY TO QUESTION — {{ question?.student_name }}
            </h3>
            <p class="text-xs text-slate-500 font-semibold">
              {{ question?.course_name }} · {{ question?.module_name }} · {{ question?.chapter_name }}
            </p>
          </div>
        </div>

        <button @click="emit('close')" class="text-slate-400 hover:text-slate-600 dark:hover:text-white font-black text-xl">
          ✕
        </button>
      </div>

      <!-- Original Question Box -->
      <div class="p-4 bg-slate-50 dark:bg-gray-700/60 rounded-2xl border border-slate-200/80 dark:border-gray-600 space-y-2">
        <div class="flex items-center justify-between text-xs font-bold text-slate-500">
          <span>Original Question:</span>
          <span class="text-blue-600 font-extrabold cursor-pointer hover:underline">📎 View Screenshot</span>
        </div>
        <p class="text-xs font-medium text-slate-800 dark:text-slate-100 italic">
          "{{ question?.question }}"
        </p>
      </div>

      <!-- Rich Editor Area -->
      <div class="space-y-2">
        <div class="flex items-center justify-between">
          <label class="text-xs font-extrabold text-slate-700 dark:text-slate-300">
            YOUR REPLY (Rich Text / Code Formatting)
          </label>

          <div class="flex items-center gap-2 text-xs">
            <button
              @click="insertCodeSnippet"
              class="px-2.5 py-1 bg-slate-100 dark:bg-gray-700 hover:bg-slate-200 text-slate-700 dark:text-slate-200 rounded-lg font-bold flex items-center gap-1"
            >
              <span>💻</span> Insert Code Block
            </button>
          </div>
        </div>

        <textarea
          v-model="replyText"
          rows="6"
          class="w-full p-4 font-mono text-xs bg-white dark:bg-gray-900 border border-slate-300 dark:border-gray-600 rounded-2xl focus:ring-2 focus:ring-blue-500 dark:text-slate-100 leading-relaxed"
          placeholder="Write your explanation or code solution here..."
        ></textarea>

        <!-- Editor Toolbar Attachments -->
        <div class="flex flex-wrap items-center gap-2 pt-1">
          <button class="px-3 py-1.5 bg-slate-100 dark:bg-gray-700 hover:bg-slate-200 text-xs font-semibold text-slate-700 dark:text-slate-200 rounded-xl">
            📎 Attach File
          </button>
          <button class="px-3 py-1.5 bg-slate-100 dark:bg-gray-700 hover:bg-slate-200 text-xs font-semibold text-slate-700 dark:text-slate-200 rounded-xl">
            📸 Attach Screenshot
          </button>
          <button class="px-3 py-1.5 bg-slate-100 dark:bg-gray-700 hover:bg-slate-200 text-xs font-semibold text-slate-700 dark:text-slate-200 rounded-xl">
            🎥 Attach Video Clip
          </button>
        </div>
      </div>

      <!-- Settings Grid -->
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 bg-slate-50 dark:bg-gray-700/40 p-4 rounded-2xl border border-slate-200/80 dark:border-gray-600 text-xs">
        <!-- Language -->
        <div>
          <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Language Options</label>
          <div class="flex items-center gap-1">
            <button
              @click="selectedLang = 'km'"
              :class="['px-3 py-1.5 rounded-xl font-bold transition', selectedLang === 'km' ? 'bg-blue-600 text-white shadow-sm' : 'bg-white dark:bg-gray-700 text-slate-600 dark:text-slate-300']"
            >
              🇰🇭 Khmer
            </button>
            <button
              @click="selectedLang = 'en'"
              :class="['px-3 py-1.5 rounded-xl font-bold transition', selectedLang === 'en' ? 'bg-blue-600 text-white shadow-sm' : 'bg-white dark:bg-gray-700 text-slate-600 dark:text-slate-300']"
            >
              🇬🇧 English
            </button>
            <button
              @click="selectedLang = 'both'"
              :class="['px-3 py-1.5 rounded-xl font-bold transition', selectedLang === 'both' ? 'bg-blue-600 text-white shadow-sm' : 'bg-white dark:bg-gray-700 text-slate-600 dark:text-slate-300']"
            >
              Both
            </button>
          </div>
        </div>

        <!-- Priority -->
        <div>
          <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">Priority Tag</label>
          <div class="flex items-center gap-1">
            <button
              @click="priority = 'Normal'"
              :class="['px-3 py-1.5 rounded-xl font-bold transition', priority === 'Normal' ? 'bg-slate-800 text-white shadow-sm' : 'bg-white dark:bg-gray-700 text-slate-600 dark:text-slate-300']"
            >
              Normal
            </button>
            <button
              @click="priority = 'Urgent'"
              :class="['px-3 py-1.5 rounded-xl font-bold transition', priority === 'Urgent' ? 'bg-rose-600 text-white shadow-sm' : 'bg-white dark:bg-gray-700 text-slate-600 dark:text-slate-300']"
            >
              Urgent
            </button>
            <button
              @click="priority = 'Important'"
              :class="['px-3 py-1.5 rounded-xl font-bold transition', priority === 'Important' ? 'bg-amber-600 text-white shadow-sm' : 'bg-white dark:bg-gray-700 text-slate-600 dark:text-slate-300']"
            >
              Important
            </button>
          </div>
        </div>

        <!-- Notifications -->
        <div class="space-y-1.5">
          <label class="block font-bold text-slate-700 dark:text-slate-300">Notify Student</label>
          <div class="flex items-center gap-4">
            <label class="flex items-center gap-1.5 cursor-pointer">
              <input v-model="notifyEmail" type="checkbox" class="rounded text-blue-600 focus:ring-blue-500" />
              <span class="font-semibold text-slate-700 dark:text-slate-200">✓ Email</span>
            </label>

            <label class="flex items-center gap-1.5 cursor-pointer">
              <input v-model="notifyPush" type="checkbox" class="rounded text-blue-600 focus:ring-blue-500" />
              <span class="font-semibold text-slate-700 dark:text-slate-200">✓ Push Notification</span>
            </label>
          </div>
        </div>

        <!-- FAQ Builder toggle -->
        <div class="space-y-1.5">
          <label class="block font-bold text-slate-700 dark:text-slate-300">Add To FAQ</label>
          <label class="flex items-center gap-1.5 cursor-pointer">
            <input v-model="addToFaq" type="checkbox" class="rounded text-indigo-600 focus:ring-indigo-500" />
            <span class="font-bold text-indigo-600 dark:text-indigo-300">☐ Yes — Save to Course FAQ</span>
          </label>
        </div>
      </div>

      <!-- Action Footer -->
      <div class="flex flex-wrap items-center justify-between gap-3 pt-2">
        <button
          @click="emit('close')"
          class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 dark:bg-gray-700 dark:text-slate-200 font-bold rounded-xl text-xs transition"
        >
          💾 Save Draft
        </button>

        <div class="flex items-center gap-2">
          <button
            @click="emit('close')"
            class="px-4 py-2.5 bg-rose-50 hover:bg-rose-100 text-rose-700 dark:bg-rose-900/40 dark:text-rose-300 font-bold rounded-xl text-xs transition"
          >
            🚫 Mark As Spam
          </button>

          <button
            @click="handleSend"
            class="px-5 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-black text-xs rounded-xl shadow-md transition flex items-center gap-1.5"
          >
            <span>📨</span> Send Reply
          </button>
        </div>
      </div>
    </div>
  </div>
</template>
