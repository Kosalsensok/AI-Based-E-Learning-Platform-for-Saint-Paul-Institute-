<script setup lang="ts">
import { ref } from 'vue'

const props = defineProps<{
  show: boolean
  courses: Array<any>
}>()

const emit = defineEmits<{
  (e: 'close'): void
  (e: 'submit', form: any): void
  (e: 'preview', form: any): void
}>()

const titleKh = ref('Module 3 បានបើកហើយ — សូមចាប់ផ្តើមរៀន')
const titleEn = ref('Module 3 is Open — Start Learning')
const category = ref('Course Update')
const priority = ref('high')

const contentKh = ref('សូមជម្រាបជូនសិស្ស IT & Networking ថា Module 3: Control Flow បានបើកហើយ។ សូមប្រើពេលវេលារៀនមេរៀននេះឱ្យបានល្អ។')
const contentEn = ref('Dear IT & Networking students, Module 3 is now open. Please complete previous modules before starting this one.')

const sendTo = ref('Course Students')
const selectedCourse = ref('C Programming Basics')
const selectedModule = ref('Module 3')
const selectedSemester = ref('Semester 2')
const filterAudience = ref('All Students')

const showDashboard = ref(true)
const showCoursePage = ref(true)
const showLoginBanner = ref(false)
const pinToTop = ref(true)
const expiryDate = ref('2025-06-25')

const buttonTextKh = ref('ចាប់ផ្តើមរៀន')
const buttonLink = ref('/student/courses/c-programming/module-3')

const deliveryMode = ref<'now' | 'schedule'>('now')
const scheduleDate = ref('2025-06-17T08:00')

const handlePublish = () => {
  emit('submit', {
    titleKh: titleKh.value,
    titleEn: titleEn.value,
    category: category.value,
    priority: priority.value,
    contentKh: contentKh.value,
    contentEn: contentEn.value,
    sendTo: sendTo.value,
    course: selectedCourse.value,
    module: selectedModule.value,
    buttonTextKh: buttonTextKh.value,
    buttonLink: buttonLink.value,
    deliveryMode: deliveryMode.value,
  })
}

const handlePreview = () => {
  emit('preview', {
    title_kh: titleKh.value,
    title_en: titleEn.value,
    content_kh: contentKh.value,
    content_en: contentEn.value,
    action_text_kh: buttonTextKh.value,
    action_url: buttonLink.value,
    priority: priority.value,
    created_at: 'Just now',
  })
}
</script>

<template>
  <div v-if="show" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4 z-50 overflow-y-auto">
    <div class="bg-white dark:bg-gray-800 rounded-3xl max-w-3xl w-full p-6 space-y-5 shadow-2xl border border-slate-200 dark:border-gray-700 animate-in fade-in zoom-in duration-200">
      <!-- Header -->
      <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-gray-700">
        <div class="flex items-center gap-2">
          <span class="text-xl">➕</span>
          <div>
            <h3 class="text-base font-black text-slate-900 dark:text-white">
              CREATE ANNOUNCEMENT — តែងសារប្រកាសទៅកាន់សិស្ស
            </h3>
            <p class="text-xs text-slate-500 font-semibold">
              Broadcast targeted course updates, payment reminders, or quiz notices.
            </p>
          </div>
        </div>

        <button @click="emit('close')" class="text-slate-400 hover:text-slate-600 dark:hover:text-white font-black text-xl">
          ✕
        </button>
      </div>

      <!-- Form Body Grid -->
      <div class="space-y-4 max-h-[500px] overflow-y-auto pr-1 text-xs">
        <!-- 1. BASIC INFO -->
        <div class="p-4 bg-slate-50 dark:bg-gray-700/50 rounded-2xl border border-slate-200/80 dark:border-gray-600 space-y-3">
          <div class="font-extrabold text-slate-900 dark:text-white flex items-center gap-1.5">
            <span>📌 BASIC INFO</span>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div>
              <label class="block font-bold mb-1">Announcement Title (KH)</label>
              <input
                v-model="titleKh"
                type="text"
                class="w-full p-2.5 bg-white dark:bg-gray-800 border border-slate-200 dark:border-gray-700 rounded-xl font-medium"
              />
            </div>
            <div>
              <label class="block font-bold mb-1">Announcement Title (EN)</label>
              <input
                v-model="titleEn"
                type="text"
                class="w-full p-2.5 bg-white dark:bg-gray-800 border border-slate-200 dark:border-gray-700 rounded-xl font-medium"
              />
            </div>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div>
              <label class="block font-bold mb-1">Category</label>
              <select v-model="category" class="w-full p-2.5 bg-white dark:bg-gray-800 border border-slate-200 dark:border-gray-700 rounded-xl font-medium">
                <option value="Course Update">📚 Course Update</option>
                <option value="Payment">💳 Payment</option>
                <option value="Quiz">🧪 Quiz</option>
                <option value="General">📢 General</option>
              </select>
            </div>

            <div>
              <label class="block font-bold mb-1">Priority</label>
              <select v-model="priority" class="w-full p-2.5 bg-white dark:bg-gray-800 border border-slate-200 dark:border-gray-700 rounded-xl font-medium">
                <option value="high">🔴 High</option>
                <option value="normal">🟡 Normal</option>
                <option value="low">🟢 Low</option>
              </select>
            </div>
          </div>
        </div>

        <!-- 2. MESSAGE CONTENT (Bilingual) -->
        <div class="p-4 bg-slate-50 dark:bg-gray-700/50 rounded-2xl border border-slate-200/80 dark:border-gray-600 space-y-3">
          <div class="font-extrabold text-slate-900 dark:text-white">💬 MESSAGE CONTENT (Bilingual)</div>
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div>
              <label class="block font-bold mb-1">Khmer Message:</label>
              <textarea v-model="contentKh" rows="3" class="w-full p-2.5 bg-white dark:bg-gray-800 border border-slate-200 dark:border-gray-700 rounded-xl font-medium"></textarea>
            </div>
            <div>
              <label class="block font-bold mb-1">English Message:</label>
              <textarea v-model="contentEn" rows="3" class="w-full p-2.5 bg-white dark:bg-gray-800 border border-slate-200 dark:border-gray-700 rounded-xl font-medium"></textarea>
            </div>
          </div>
        </div>

        <!-- 3. TARGET AUDIENCE -->
        <div class="p-4 bg-slate-50 dark:bg-gray-700/50 rounded-2xl border border-slate-200/80 dark:border-gray-600 space-y-3">
          <div class="font-extrabold text-slate-900 dark:text-white">🎯 TARGET AUDIENCE</div>
          <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div>
              <label class="block font-bold mb-1">Send To</label>
              <select v-model="sendTo" class="w-full p-2.5 bg-white dark:bg-gray-800 border border-slate-200 dark:border-gray-700 rounded-xl font-medium">
                <option value="Course Students">Course Students</option>
                <option value="Major">Major</option>
                <option value="All Users">All Users</option>
              </select>
            </div>

            <div>
              <label class="block font-bold mb-1">Course</label>
              <select v-model="selectedCourse" class="w-full p-2.5 bg-white dark:bg-gray-800 border border-slate-200 dark:border-gray-700 rounded-xl font-medium">
                <option v-for="c in courses" :key="c.id" :value="c.title">{{ c.title }}</option>
              </select>
            </div>

            <div>
              <label class="block font-bold mb-1">Module / Chapter</label>
              <select v-model="selectedModule" class="w-full p-2.5 bg-white dark:bg-gray-800 border border-slate-200 dark:border-gray-700 rounded-xl font-medium">
                <option value="Module 1">Module 1</option>
                <option value="Module 2">Module 2</option>
                <option value="Module 3">Module 3</option>
              </select>
            </div>
          </div>
        </div>

        <!-- 4. ACTION BUTTON & DISPLAY -->
        <div class="p-4 bg-slate-50 dark:bg-gray-700/50 rounded-2xl border border-slate-200/80 dark:border-gray-600 space-y-3">
          <div class="font-extrabold text-slate-900 dark:text-white">🚀 ACTION BUTTON & DISPLAY</div>
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div>
              <label class="block font-bold mb-1">Button Text (KH)</label>
              <input v-model="buttonTextKh" type="text" class="w-full p-2.5 bg-white dark:bg-gray-800 border border-slate-200 dark:border-gray-700 rounded-xl font-medium" />
            </div>
            <div>
              <label class="block font-bold mb-1">Button Link</label>
              <input v-model="buttonLink" type="text" class="w-full p-2.5 bg-white dark:bg-gray-800 border border-slate-200 dark:border-gray-700 rounded-xl font-medium" />
            </div>
          </div>

          <div class="flex flex-wrap items-center gap-4 pt-1 font-semibold text-slate-700 dark:text-slate-300">
            <label class="flex items-center gap-1.5">
              <input v-model="showDashboard" type="checkbox" /> ✓ Student Dashboard
            </label>
            <label class="flex items-center gap-1.5">
              <input v-model="showCoursePage" type="checkbox" /> ✓ Course Page
            </label>
            <label class="flex items-center gap-1.5">
              <input v-model="pinToTop" type="checkbox" /> 📌 Pin to Top
            </label>
          </div>
        </div>

        <!-- 5. DELIVERY -->
        <div class="p-4 bg-slate-50 dark:bg-gray-700/50 rounded-2xl border border-slate-200/80 dark:border-gray-600 space-y-2">
          <div class="font-extrabold text-slate-900 dark:text-white">📅 DELIVERY SCHEDULE</div>
          <div class="flex items-center gap-6">
            <label class="flex items-center gap-2 cursor-pointer font-bold">
              <input type="radio" value="now" v-model="deliveryMode" /> (●) Send Now
            </label>
            <label class="flex items-center gap-2 cursor-pointer font-bold">
              <input type="radio" value="schedule" v-model="deliveryMode" /> ( ) Schedule for Later
            </label>
          </div>
        </div>
      </div>

      <!-- Action Footer -->
      <div class="flex flex-wrap items-center justify-between gap-3 pt-2">
        <button
          @click="handlePreview"
          class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 dark:bg-gray-700 dark:text-slate-200 font-bold rounded-xl text-xs transition"
        >
          👁 Preview
        </button>

        <div class="flex items-center gap-2">
          <button
            @click="emit('close')"
            class="px-4 py-2.5 bg-slate-200 hover:bg-slate-300 text-slate-700 rounded-xl text-xs font-bold"
          >
            💾 Save Draft
          </button>
          <button
            @click="handlePublish"
            class="px-5 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-black text-xs rounded-xl shadow-md transition flex items-center gap-1.5"
          >
            <span>🚀</span> Publish Announcement
          </button>
        </div>
      </div>
    </div>
  </div>
</template>
