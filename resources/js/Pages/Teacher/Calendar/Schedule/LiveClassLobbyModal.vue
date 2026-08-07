<script setup lang="ts">
import { ref } from 'vue'

const props = defineProps<{
  show: boolean
  schedule: any
}>()

const emit = defineEmits(['close'])

const activeTab = ref<'attendance' | 'chat' | 'qa'>('attendance')

const joinedStudents = ref([
  { id: 1, name: 'Chan Srey', time: '02:01 PM', status: 'Active (> 10m)' },
  { id: 2, name: 'Keo Sophea', time: '02:02 PM', status: 'Active (> 10m)' },
  { id: 3, name: 'Sok Dara', time: '02:05 PM', status: 'Active (> 10m)' },
  { id: 4, name: 'Vannak Heng', time: '02:08 PM', status: 'Joined' },
  { id: 5, name: 'Ly Nita', time: '02:11 PM', status: 'Joined' },
])

const qaQuestions = ref([
  { id: 1, student: 'Sok Dara', question: 'លោកគ្រូ តើ Parameter បញ្ជូនតាម Value និង Reference ខុសគ្នាយ៉ាងម៉េច?', time: '02:15 PM', answered: true },
  { id: 2, student: 'Chan Srey', question: 'តើ Return type void អាចប្រើ return statement បានទេ?', time: '02:20 PM', answered: false },
  { id: 3, student: 'Ly Nita', question: 'សូមលោកគ្រូពន្យល់ពី Recursion stack overflow ម្តងទៀត', time: '02:25 PM', answered: false },
])

const chatMessages = ref([
  { sender: 'Mr. Sophea (Teacher)', text: 'សូមស្វាគមន៍សិស្សទាំងអស់គ្នាចូលមកកាន់ Live Class ថ្ងៃនេះ!', time: '02:00 PM' },
  { sender: 'Sok Dara', text: 'ជម្រាបសួរលោកគ្រូ!', time: '02:01 PM' },
  { sender: 'Keo Sophea', text: 'សំឡេងច្បាស់ល្អណាស់លោកគ្រូ', time: '02:02 PM' },
])

const newMessage = ref('')
const sendChatMessage = () => {
  if (!newMessage.value.trim()) return
  chatMessages.value.push({
    sender: 'Mr. Sophea (Teacher)',
    text: newMessage.value,
    time: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
  })
  newMessage.value = ''
}
</script>

<template>
  <div v-if="show && schedule" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/70 backdrop-blur-sm overflow-y-auto">
    <div class="bg-white dark:bg-gray-800 rounded-3xl max-w-3xl w-full shadow-2xl border border-slate-200 dark:border-gray-700 my-8 overflow-hidden transform transition-all">
      <!-- Header -->
      <div class="px-6 py-4 bg-gradient-to-r from-blue-700 via-indigo-700 to-purple-800 text-white flex items-center justify-between">
        <div class="space-y-1">
          <div class="flex items-center gap-2">
            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-red-500 text-white animate-pulse flex items-center gap-1">
              <span class="w-1.5 h-1.5 rounded-full bg-white"></span>
              🔴 LIVE NOW
            </span>
            <h3 class="font-bold text-lg leading-tight truncate max-w-md">
              🟦 {{ schedule.title }}
            </h3>
          </div>
          <p class="text-xs text-blue-100 flex items-center gap-3">
            <span>📅 {{ schedule.start_at ? new Date(schedule.start_at).toLocaleDateString() : 'May 15, 2025' }}</span>
            <span>⏰ 02:00 PM - 04:00 PM</span>
            <span class="font-bold text-emerald-300">👥 48/60 Students Joined</span>
          </p>
        </div>
        <button @click="$emit('close')" class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center text-white font-bold transition">✕</button>
      </div>

      <!-- Live Indicators -->
      <div class="px-6 py-3 bg-slate-100 dark:bg-gray-700/60 border-b border-slate-200 dark:border-gray-700 flex flex-wrap items-center justify-between gap-3 text-xs">
        <div class="flex items-center gap-4">
          <span class="text-emerald-700 dark:text-emerald-300 font-bold flex items-center gap-1">
            <span class="text-base">✅</span> Auto-tracked when student joins > 10 min
          </span>
          <span class="text-amber-700 dark:text-amber-300 font-bold flex items-center gap-1">
            <span class="text-base">⏺️</span> Recording... will save to Content Delivery > Videos
          </span>
        </div>

        <a
          :href="schedule.meeting_link || '#'"
          target="_blank"
          class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold shadow-md transition flex items-center gap-2"
        >
          <span>🎥 Start Zoom Meeting</span>
          <span>→</span>
        </a>
      </div>

      <!-- Body Tabs -->
      <div class="p-6 space-y-4 text-xs">
        <!-- Lobby Tabs Navigation -->
        <div class="flex border-b border-slate-200 dark:border-gray-700 gap-2">
          <button
            @click="activeTab = 'attendance'"
            :class="activeTab === 'attendance' ? 'border-b-2 border-blue-600 text-blue-600 font-bold' : 'text-slate-500 font-medium'"
            class="pb-3 px-3 transition flex items-center gap-1.5"
          >
            <span>📋 Attendance</span>
            <span class="px-2 py-0.5 rounded-full bg-blue-100 dark:bg-blue-900/50 text-blue-700 dark:text-blue-300 font-bold text-[10px]">48</span>
          </button>

          <button
            @click="activeTab = 'chat'"
            :class="activeTab === 'chat' ? 'border-b-2 border-blue-600 text-blue-600 font-bold' : 'text-slate-500 font-medium'"
            class="pb-3 px-3 transition flex items-center gap-1.5"
          >
            <span>💬 Live Chat</span>
          </button>

          <button
            @click="activeTab = 'qa'"
            :class="activeTab === 'qa' ? 'border-b-2 border-blue-600 text-blue-600 font-bold' : 'text-slate-500 font-medium'"
            class="pb-3 px-3 transition flex items-center gap-1.5"
          >
            <span>❓ Student Q&A</span>
            <span class="px-2 py-0.5 rounded-full bg-purple-100 dark:bg-purple-900/50 text-purple-700 dark:text-purple-300 font-bold text-[10px]">5 new</span>
          </button>
        </div>

        <!-- Attendance Content -->
        <div v-if="activeTab === 'attendance'" class="space-y-3">
          <div class="flex items-center justify-between">
            <span class="font-bold text-slate-700 dark:text-slate-200">Real-time Joined Students (48)</span>
            <span class="text-[11px] text-slate-400">Auto Attendance Status</span>
          </div>

          <div class="divide-y divide-slate-100 dark:divide-gray-700 max-h-60 overflow-y-auto rounded-xl border border-slate-200 dark:border-gray-700">
            <div v-for="st in joinedStudents" :key="st.id" class="p-3 flex items-center justify-between hover:bg-slate-50 dark:hover:bg-gray-700/50">
              <div class="flex items-center gap-2.5">
                <div class="w-7 h-7 rounded-full bg-blue-100 text-blue-700 font-bold flex items-center justify-center text-xs">
                  {{ st.name[0] }}
                </div>
                <div>
                  <p class="font-bold text-slate-800 dark:text-white">{{ st.name }}</p>
                  <p class="text-[10px] text-slate-400">Joined at {{ st.time }}</p>
                </div>
              </div>
              <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300">
                ✅ {{ st.status }}
              </span>
            </div>
          </div>
        </div>

        <!-- Chat Content -->
        <div v-if="activeTab === 'chat'" class="space-y-3">
          <div class="h-56 overflow-y-auto space-y-2 p-3 bg-slate-50 dark:bg-gray-700/50 rounded-2xl border border-slate-200 dark:border-gray-700">
            <div v-for="(msg, i) in chatMessages" :key="i" class="p-2 rounded-xl bg-white dark:bg-gray-800 border border-slate-100 dark:border-gray-700 shadow-sm space-y-0.5">
              <div class="flex items-center justify-between font-bold text-slate-800 dark:text-white text-[11px]">
                <span>{{ msg.sender }}</span>
                <span class="text-[10px] text-slate-400">{{ msg.time }}</span>
              </div>
              <p class="text-slate-600 dark:text-slate-300 text-[11px]">{{ msg.text }}</p>
            </div>
          </div>

          <div class="flex gap-2">
            <input
              v-model="newMessage"
              @keyup.enter="sendChatMessage"
              type="text"
              placeholder="ផ្ញើសារក្នុង Class Chat..."
              class="flex-1 px-3.5 py-2 rounded-xl border border-slate-200 dark:border-gray-700 bg-white dark:bg-gray-700 text-xs"
            />
            <button @click="sendChatMessage" class="px-4 py-2 bg-blue-600 text-white rounded-xl font-bold">Send</button>
          </div>
        </div>

        <!-- Q&A Content -->
        <div v-if="activeTab === 'qa'" class="space-y-3">
          <div class="space-y-2 max-h-60 overflow-y-auto">
            <div v-for="q in qaQuestions" :key="q.id" class="p-3.5 rounded-2xl border border-slate-200 dark:border-gray-700 bg-white dark:bg-gray-800 space-y-2">
              <div class="flex items-center justify-between">
                <span class="font-bold text-purple-700 dark:text-purple-300">❓ {{ q.student }}</span>
                <span class="text-[10px] text-slate-400">{{ q.time }}</span>
              </div>
              <p class="text-slate-800 dark:text-slate-100 font-semibold">{{ q.question }}</p>
              <div class="flex justify-end">
                <button
                  @click="q.answered = !q.answered"
                  :class="q.answered ? 'bg-emerald-100 text-emerald-800' : 'bg-purple-600 text-white'"
                  class="px-3 py-1 rounded-lg text-[10px] font-bold"
                >
                  {{ q.answered ? '✅ Answered' : 'Mark as Answered' }}
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Footer -->
      <div class="px-6 py-4 bg-slate-100 dark:bg-gray-700/80 border-t border-slate-200 dark:border-gray-700 flex justify-end">
        <button @click="$emit('close')" class="px-5 py-2.5 rounded-xl bg-slate-800 text-white font-bold hover:bg-slate-900 transition">
          Close Lobby
        </button>
      </div>
    </div>
  </div>
</template>
