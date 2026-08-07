<script setup lang="ts">
import { ref } from 'vue'
import StudentLayout from '@/Layouts/StudentLayout.vue'

const activeThreadModal = ref<any | null>(null)
const replyText = ref('')
const helpfulMarked = ref(false)

const topicFeed = ref([
  {
    id: 1,
    title: 'Pointer vs Array — confusion',
    author: 'Bun Rithy',
    replies: 24,
    lastActive: '10m ago',
    messages: [
      { sender: '👤 Bun Rithy', time: '09:30 AM', text: 'I thought array and pointer are same in C language?' },
      { sender: '👨‍🏫 Teacher Mr. Sophea', time: '09:45 AM', text: 'Not the same. An array is a continuous block of memory, while a pointer stores a memory address. Array decays to pointer in function arguments.' }
    ]
  },
  {
    id: 2,
    title: 'How to solve ABA payment pending?',
    author: 'Sok Chanra',
    replies: 15,
    lastActive: '1h ago',
    messages: [
      { sender: '👤 Sok Chanra', time: '10:15 AM', text: 'My ABA transaction is pending for 10 mins. What should I do?' },
      { sender: '🎓 Student Chan Dara', time: '10:20 AM', text: 'Just click "I have paid" or wait up to 15 minutes for automated webhook check!' }
    ]
  }
])

const openThread = (topic: any) => {
  activeThreadModal.value = topic
  replyText.value = ''
  helpfulMarked.value = false
}

const sendReply = () => {
  if (!replyText.value.trim() || !activeThreadModal.value) return
  activeThreadModal.value.messages.push({
    sender: '👤 Chan Dara (You)',
    time: 'Just now',
    text: replyText.value
  })
  activeThreadModal.value.replies++
  replyText.value = ''
}
</script>

<template>
  <StudentLayout title="Course Community Discussions">
    <div class="space-y-6">
      
      <!-- Top Header Summary Bar -->
      <div class="bg-gradient-to-r from-purple-950 via-slate-900 to-indigo-950 border border-purple-900/60 rounded-3xl p-5 md:p-6 shadow-2xl flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
          <span class="px-3 py-1 rounded-full bg-purple-500/20 text-purple-300 border border-purple-500/30 text-xs font-bold uppercase tracking-wider">
            🗣️ PUBLIC COMMUNITY FORUM
          </span>
          <h1 class="text-xl md:text-2xl font-black text-white mt-1.5 flex items-center gap-2">
            <span>🗣️ COURSE DISCUSSIONS — C Programming Basics</span>
          </h1>
          <p class="text-xs text-slate-300 mt-1">
            ទំព័រពិភាក្សាជាសាធារណៈរវាងនិស្សិត និងគ្រូបង្រៀន ចែករំលែកបទពិសោធន៍រៀនសូត្រ
          </p>
        </div>

        <button class="px-4 py-2.5 rounded-2xl bg-purple-600 hover:bg-purple-500 text-white font-bold text-xs shadow-md self-start">
          ➕ Start New Topic
        </button>
      </div>

      <!-- TOPIC FEED FILTERS -->
      <div class="bg-slate-800/90 border border-slate-700/80 rounded-3xl p-5 shadow-xl flex flex-wrap items-center justify-between gap-3 text-xs">
        <div class="flex items-center gap-2">
          <select class="bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white">
            <option>All Topics</option>
            <option>Most Active</option>
            <option>Solved</option>
          </select>
        </div>
        <input type="text" placeholder="Search discussion topic..." class="bg-slate-900 border border-slate-700 rounded-xl px-3.5 py-2 text-xs text-white placeholder-slate-500" />
      </div>

      <!-- PUBLIC TOPIC FEED (Matching Prompt Mock Layout) -->
      <div class="space-y-4">
        <div
          v-for="topic in topicFeed"
          :key="topic.id"
          class="bg-slate-800/90 border border-slate-700/80 rounded-3xl p-6 shadow-xl space-y-4 hover:border-purple-500/50 transition-all"
        >
          <div class="flex items-center justify-between border-b border-slate-700/60 pb-3">
            <h3 class="text-base font-black text-white">🧵 Topic: “{{ topic.title }}”</h3>
            <span class="text-xs text-slate-400 font-mono">Last active: {{ topic.lastActive }}</span>
          </div>

          <p class="text-xs text-slate-300">
            Started by: <strong class="text-indigo-300">{{ topic.author }}</strong> | <span class="text-purple-400 font-bold font-mono">{{ topic.replies }} replies</span>
          </p>

          <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-700/60 text-xs">
            <button @click="openThread(topic)" class="px-4 py-2 rounded-xl bg-purple-600 hover:bg-purple-500 text-white font-bold shadow-md">
              👁 Open Thread
            </button>
            <button class="px-3 py-2 rounded-xl bg-slate-900 border border-slate-700 text-slate-300 font-bold">
              🔔 Follow
            </button>
            <button class="px-3 py-2 rounded-xl bg-slate-900 border border-slate-700 text-rose-400 font-bold">
              🚩 Report
            </button>
          </div>
        </div>
      </div>

      <!-- INSIDE THREAD UI MODAL (Matching Prompt Spec) -->
      <div v-if="activeThreadModal" class="fixed inset-0 z-50 bg-slate-950/85 backdrop-blur-md flex items-center justify-center p-4">
        <div class="bg-slate-900 border border-purple-500/40 rounded-3xl max-w-lg w-full p-6 space-y-4 shadow-2xl">
          <div class="border-b border-slate-800 pb-3 flex items-center justify-between">
            <h3 class="text-sm font-black text-white">🧵 Thread: {{ activeThreadModal.title }}</h3>
            <button @click="activeThreadModal = null" class="text-slate-400 hover:text-white font-bold text-sm">✕</button>
          </div>

          <!-- Messages Stream -->
          <div class="space-y-3 max-h-72 overflow-y-auto custom-scrollbar pr-1">
            <div
              v-for="(msg, idx) in activeThreadModal.messages"
              :key="idx"
              :class="[msg.sender.includes('Teacher') ? 'bg-emerald-950/60 border-emerald-500/40' : 'bg-slate-950 border-slate-800', 'p-4 rounded-2xl border text-xs space-y-1']"
            >
              <div class="flex items-center justify-between font-bold">
                <span :class="[msg.sender.includes('Teacher') ? 'text-emerald-400' : 'text-indigo-300']">{{ msg.sender }}</span>
                <span class="text-[10px] text-slate-500 font-mono">{{ msg.time }}</span>
              </div>
              <p class="text-slate-200">{{ msg.text }}</p>
              <div v-if="msg.sender.includes('Teacher')" class="pt-1">
                <button @click="helpfulMarked = true" class="px-2.5 py-1 rounded bg-emerald-600/30 text-emerald-300 font-bold text-[10px]">
                  ✅ Mark as Helpful
                </button>
              </div>
            </div>
          </div>

          <!-- Reply Input Box -->
          <div class="flex items-center gap-2 pt-2 border-t border-slate-800">
            <input
              v-model="replyText"
              type="text"
              placeholder="Type your reply..."
              class="flex-1 bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white"
            />
            <button @click="sendReply" class="px-4 py-2 rounded-xl bg-purple-600 hover:bg-purple-500 text-white font-bold text-xs shadow-md">
              Reply
            </button>
          </div>
        </div>
      </div>

    </div>
  </StudentLayout>
</template>
