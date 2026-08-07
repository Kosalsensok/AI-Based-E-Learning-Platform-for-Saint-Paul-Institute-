<script setup lang="ts">
import { ref } from 'vue'

const props = defineProps<{
  show: boolean
}>()

const emit = defineEmits(['close', 'saved'])

const step = ref(1)

// Step 1 Form State
const reminderName = ref('Learning reminder')
const selectedCourse = ref('none')
const searchQuery = ref('')
const sampleCourses = [
  { id: 'c1', title: 'Course: C Programming Basics ( Beginner to Advance )' },
  { id: 'c2', title: 'Course: Ethical Hacking for beginners: Beginner to Advance' },
  { id: 'c3', title: 'Course: Ethical Hacking - Basics (Kali 2021)' },
  { id: 'c4', title: 'Course: Introduction to Dark Web, Anonymity and Cryptocurrency' }
]

// Step 2 Form State
const frequency = ref<'daily' | 'weekly'>('weekly')
const selectedDays = ref<Array<string>>(['Mon', 'Thu'])
const reminderTime = ref('20:00')

// Step 3 Form State
const notificationType = ref<'push' | 'calendar'>('push')

const toggleDay = (day: string) => {
  if (selectedDays.value.includes(day)) {
    selectedDays.value = selectedDays.value.filter(d => d !== day)
  } else {
    selectedDays.value.push(day)
  }
}

const handleNext = () => {
  if (step.value < 3) {
    step.value++
  } else {
    handleSave()
  }
}

const handleBack = () => {
  if (step.value > 1) {
    step.value--
  }
}

const handleSave = () => {
  const summary = `${reminderName.value} (${frequency.value === 'daily' ? 'Every Day' : selectedDays.value.join(', ')} at ${reminderTime.value})`
  emit('saved', summary)
  resetModal()
  emit('close')
}

const resetModal = () => {
  step.value = 1
  reminderName.value = 'Learning reminder'
  selectedCourse.value = 'none'
  searchQuery.value = ''
}
</script>

<template>
  <div v-if="show" class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/80 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-slate-900 rounded-3xl shadow-2xl border border-slate-700/80 w-full max-w-lg overflow-hidden transform transition-all text-slate-100 font-khmer">
      
      <!-- Modal Header -->
      <div class="p-6 border-b border-slate-800 flex items-center justify-between">
        <div>
          <h3 class="text-lg font-bold text-white flex items-center gap-2">
            <span>⏰</span>
            <span>Learning reminders</span>
          </h3>
          <span class="text-xs text-slate-400 font-mono">Step {{ step }} of 3</span>
        </div>
        <button @click="resetModal(); $emit('close')" class="text-slate-400 hover:text-white text-xl font-bold w-8 h-8 flex items-center justify-center rounded-xl bg-slate-800 hover:bg-slate-700 transition cursor-pointer">✕</button>
      </div>

      <!-- Modal Body -->
      <div class="p-6 space-y-5 text-xs text-slate-200">
        
        <!-- STEP 1: Name & Attach Content (Matching User Screenshot 100%) -->
        <div v-if="step === 1" class="space-y-4 animate-in fade-in duration-200">
          <div>
            <div class="flex items-center justify-between mb-1.5">
              <label class="font-bold text-slate-300">Name</label>
              <span class="text-slate-500 text-[11px]">optional</span>
            </div>
            <input 
              v-model="reminderName"
              type="text" 
              class="w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white font-medium focus:outline-none focus:border-indigo-500"
            />
          </div>

          <div class="space-y-2.5">
            <label class="font-bold text-slate-300 block">Attach content (optional)</label>
            <p class="text-slate-400 text-[11px]">Most recent courses or labs:</p>

            <div class="space-y-2 max-h-48 overflow-y-auto pr-1">
              <label 
                v-for="course in sampleCourses" 
                :key="course.id"
                class="flex items-center gap-3 p-3 bg-slate-950 hover:bg-slate-850 rounded-xl border border-slate-800/80 cursor-pointer transition"
              >
                <input 
                  type="radio" 
                  name="attached_content" 
                  :value="course.id" 
                  v-model="selectedCourse" 
                  class="text-indigo-600 bg-slate-900 border-slate-700 focus:ring-0"
                />
                <span class="text-slate-200 font-medium text-xs">{{ course.title }}</span>
              </label>

              <label class="flex items-center gap-3 p-3 bg-slate-950 hover:bg-slate-850 rounded-xl border border-slate-800/80 cursor-pointer transition">
                <input 
                  type="radio" 
                  name="attached_content" 
                  value="none" 
                  v-model="selectedCourse" 
                  class="text-indigo-600 bg-slate-900 border-slate-700 focus:ring-0"
                />
                <span class="text-slate-300 font-medium text-xs">None</span>
              </label>
            </div>

            <div class="relative mt-2">
              <span class="absolute left-3.5 top-2.5 text-slate-500 text-xs">🔍</span>
              <input 
                v-model="searchQuery"
                type="text" 
                placeholder="Search" 
                class="w-full pl-9 pr-4 py-2 bg-slate-950 border border-slate-800 rounded-xl text-white placeholder:text-slate-600 text-xs focus:outline-none focus:border-indigo-500"
              />
            </div>
          </div>
        </div>

        <!-- STEP 2: Schedule & Time -->
        <div v-else-if="step === 2" class="space-y-4 animate-in fade-in duration-200">
          <div>
            <label class="font-bold text-slate-300 block mb-2">Frequency</label>
            <div class="grid grid-cols-2 gap-3">
              <button 
                @click="frequency = 'weekly'"
                :class="['py-2.5 rounded-xl border font-bold text-xs transition', frequency === 'weekly' ? 'bg-indigo-600 border-indigo-500 text-white' : 'bg-slate-950 border-slate-800 text-slate-400']"
              >
                📅 Weekly
              </button>
              <button 
                @click="frequency = 'daily'"
                :class="['py-2.5 rounded-xl border font-bold text-xs transition', frequency === 'daily' ? 'bg-indigo-600 border-indigo-500 text-white' : 'bg-slate-950 border-slate-800 text-slate-400']"
              >
                ☀️ Every Day
              </button>
            </div>
          </div>

          <div v-if="frequency === 'weekly'" class="space-y-2">
            <label class="font-bold text-slate-300 block">Select Days</label>
            <div class="flex items-center gap-1.5 flex-wrap">
              <button 
                v-for="day in ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']" 
                :key="day"
                @click="toggleDay(day)"
                :class="['px-3 py-2 rounded-xl text-xs font-bold border transition', selectedDays.includes(day) ? 'bg-indigo-600 border-indigo-500 text-white' : 'bg-slate-950 border-slate-800 text-slate-400']"
              >
                {{ day }}
              </button>
            </div>
          </div>

          <div>
            <label class="font-bold text-slate-300 block mb-1">Time</label>
            <input 
              v-model="reminderTime"
              type="time" 
              class="w-full px-4 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-white font-mono font-bold text-sm focus:outline-none focus:border-indigo-500"
            />
          </div>
        </div>

        <!-- STEP 3: Notification Preference -->
        <div v-else-if="step === 3" class="space-y-4 animate-in fade-in duration-200">
          <label class="font-bold text-slate-300 block mb-2">Notification Channel</label>

          <div class="space-y-2.5">
            <label 
              @click="notificationType = 'push'"
              :class="['p-4 rounded-2xl border flex items-center justify-between cursor-pointer transition', notificationType === 'push' ? 'bg-indigo-950/60 border-indigo-500 text-white' : 'bg-slate-950 border-slate-800 text-slate-400']"
            >
              <div class="flex items-center gap-3">
                <span class="text-xl">🔔</span>
                <div>
                  <p class="font-bold text-slate-200">Push Notifications</p>
                  <p class="text-[11px] text-slate-400">Receive browser & mobile app reminders</p>
                </div>
              </div>
              <input type="radio" name="notif" value="push" :checked="notificationType === 'push'" />
            </label>

            <label 
              @click="notificationType = 'calendar'"
              :class="['p-4 rounded-2xl border flex items-center justify-between cursor-pointer transition', notificationType === 'calendar' ? 'bg-indigo-950/60 border-indigo-500 text-white' : 'bg-slate-950 border-slate-800 text-slate-400']"
            >
              <div class="flex items-center gap-3">
                <span class="text-xl">📅</span>
                <div>
                  <p class="font-bold text-slate-200">Sync with Google Calendar</p>
                  <p class="text-[11px] text-slate-400">Export event (.ics) to your personal calendar</p>
                </div>
              </div>
              <input type="radio" name="notif" value="calendar" :checked="notificationType === 'calendar'" />
            </label>
          </div>
        </div>

      </div>

      <!-- Modal Footer -->
      <div class="p-6 border-t border-slate-800 flex items-center justify-between bg-slate-950/50">
        <button 
          v-if="step > 1"
          @click="handleBack"
          class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 rounded-xl text-xs font-bold transition"
        >
          Back
        </button>
        <div v-else></div>

        <button 
          @click="handleNext"
          class="px-6 py-2.5 bg-purple-600 hover:bg-purple-500 text-white font-bold rounded-xl text-xs shadow-lg shadow-purple-600/30 transition cursor-pointer"
        >
          {{ step === 3 ? 'Save Reminder' : 'Next' }}
        </button>
      </div>

    </div>
  </div>
</template>
