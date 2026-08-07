<script setup lang="ts">
import { ref, computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps<{
  threads: Array<any>
  topStats: any
}>()

// Active selected thread for Panel 3
const selectedThread = ref<any>(props.threads[0] || null)
const selectedFilterTag = ref<string>('all')
const searchQuery = ref<string>('')
const newCommentText = ref<string>('')

// Filtered Thread List
const filteredThreads = computed(() => {
  return props.threads.filter(t => {
    const matchesSearch = t.title.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
                          t.course_name.toLowerCase().includes(searchQuery.value.toLowerCase())
    return matchesSearch
  })
})

const selectThread = (thread: any) => {
  selectedThread.value = thread
}

const openNewTopicModal = () => {
  window.alert('New Topic composer opened!')
}

const togglePin = () => {
  if (selectedThread.value) {
    selectedThread.value.is_pinned = !selectedThread.value.is_pinned
    window.alert(selectedThread.value.is_pinned ? '📌 Thread pinned to top!' : 'Unpinned thread!')
  }
}

const toggleLock = () => {
  if (selectedThread.value) {
    selectedThread.value.is_locked = !selectedThread.value.is_locked
    window.alert(selectedThread.value.is_locked ? '🔒 Thread locked from new comments!' : '🔓 Thread unlocked!')
  }
}

const markSolved = () => {
  if (selectedThread.value) {
    selectedThread.value.is_solved = true
    selectedThread.value.status = 'solved'
    window.alert('✅ Thread marked as Solved!')
  }
}

const convertToFaq = () => {
  window.alert('📚 Discussion converted & added to FAQ Library!')
}

const postReply = () => {
  if (!newCommentText.value.trim()) return
  if (selectedThread.value) {
    if (!selectedThread.value.comments) selectedThread.value.comments = []
    selectedThread.value.comments.push({
      id: Date.now(),
      author_name: 'Admin User',
      author_role: 'admin',
      content: newCommentText.value,
      likes_count: 0,
      is_best_answer: false,
      is_teacher_answer: true,
      created_at: 'Just now'
    })
    selectedThread.value.replies_count++
    newCommentText.value = ''
  }
}
</script>

<template>
  <AdminLayout title="Discussions & Support — General Board">
    <div class="space-y-6">
      
      <!-- Top Header -->
      <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-slate-900/90 border border-slate-800 p-6 rounded-2xl">
        <div>
          <h1 class="text-2xl font-bold text-white flex items-center gap-2.5">
            💬 Community & Support Management Center
          </h1>
          <p class="text-xs text-slate-400 mt-1">
            សំណួរនិស្សិត • ការពិភាក្សា • បណ្តឹងឧប្បត្តិហេតុ • សុវត្ថិភាពមាតិកា
          </p>
        </div>

        <div class="flex items-center gap-2">
          <button @click="openNewTopicModal" class="px-4 py-2 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-bold text-xs rounded-xl shadow-lg shadow-blue-600/20">
            + New Topic
          </button>
        </div>
      </div>

      <!-- Top Stats Bar -->
      <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-xs">
        <div class="bg-slate-900 border border-slate-800 p-4 rounded-xl space-y-1">
          <span class="text-slate-400">💬 Total Discussions</span>
          <p class="text-xl font-bold text-white">1,245 <span class="text-xs text-emerald-400 font-normal">(🟢 Open 860 / 🔒 385)</span></p>
        </div>
        <div class="bg-slate-900 border border-slate-800 p-4 rounded-xl space-y-1">
          <span class="text-slate-400">❓ Student Questions</span>
          <p class="text-xl font-bold text-amber-400">890 <span class="text-xs text-red-400 font-normal">(⏳ Unanswered 47)</span></p>
        </div>
        <div class="bg-slate-900 border border-slate-800 p-4 rounded-xl space-y-1">
          <span class="text-slate-400">🎫 Support Tickets</span>
          <p class="text-xl font-bold text-cyan-400">134 <span class="text-xs text-emerald-400 font-normal">(SLA 96.2%)</span></p>
        </div>
        <div class="bg-slate-900 border border-slate-800 p-4 rounded-xl space-y-1">
          <span class="text-slate-400">🚩 Reported Content</span>
          <p class="text-xl font-bold text-red-400">28 pending review</p>
        </div>
      </div>

      <!-- Navigation Tabs -->
      <div class="flex items-center gap-1 border-b border-slate-800 overflow-x-auto pb-1 custom-scrollbar">
        <Link href="/admin/discussions/board" class="px-4 py-2.5 text-xs rounded-xl border bg-blue-500/15 text-blue-300 border-blue-500/40 font-bold shadow-sm shrink-0">
          💬 Discussions
        </Link>
        <Link href="/admin/discussions/questions" class="px-4 py-2.5 text-xs rounded-xl border text-slate-400 hover:text-slate-200 border-transparent hover:bg-slate-800/60 transition shrink-0">
          ❓ Student Questions
        </Link>
        <Link href="/admin/discussions/tickets" class="px-4 py-2.5 text-xs rounded-xl border text-slate-400 hover:text-slate-200 border-transparent hover:bg-slate-800/60 transition shrink-0">
          🎫 Support Tickets
        </Link>
        <Link href="/admin/discussions/reports" class="px-4 py-2.5 text-xs rounded-xl border text-slate-400 hover:text-slate-200 border-transparent hover:bg-slate-800/60 transition shrink-0">
          🚩 Reported Content
        </Link>
      </div>

      <!-- 3-PANEL SLACK/DISCORD-LIKE LAYOUT -->
      <div class="bg-slate-900 border border-slate-800 rounded-2xl overflow-hidden shadow-2xl grid grid-cols-1 lg:grid-cols-12 h-[720px] text-xs">
        
        <!-- PANEL 1: FILTER (3 cols) -->
        <div class="lg:col-span-3 border-r border-slate-800 p-4 space-y-4 bg-slate-950 overflow-y-auto custom-scrollbar">
          <h3 class="font-bold text-slate-400 uppercase tracking-wider text-[11px]">PANEL 1: FILTERS & TAGS</h3>
          
          <div class="space-y-2">
            <label class="text-slate-400 block mb-0.5">Filter by Course:</label>
            <select class="w-full bg-slate-900 border border-slate-800 rounded p-2 text-white font-semibold">
              <option value="all">📚 All Courses</option>
              <option value="c">C Programming</option>
              <option value="ds">Data Structure</option>
            </select>
          </div>

          <div class="space-y-2">
            <label class="text-slate-400 block mb-0.5">Filter by Module:</label>
            <select class="w-full bg-slate-900 border border-slate-800 rounded p-2 text-white">
              <option value="all">🗂️ All Modules</option>
              <option value="m1">Module 1</option>
              <option value="m2">Module 2</option>
              <option value="m3">Module 3</option>
            </select>
          </div>

          <!-- Tags -->
          <div class="space-y-1.5">
            <label class="text-slate-400 block mb-1">🏷️ Popular Tags:</label>
            <div class="flex flex-wrap gap-1.5">
              <span class="px-2 py-1 bg-slate-900 border border-slate-800 rounded text-blue-400 font-mono text-[11px] hover:border-blue-500 cursor-pointer">#bug</span>
              <span class="px-2 py-1 bg-slate-900 border border-slate-800 rounded text-cyan-400 font-mono text-[11px] hover:border-cyan-500 cursor-pointer">#question</span>
              <span class="px-2 py-1 bg-slate-900 border border-slate-800 rounded text-amber-400 font-mono text-[11px] hover:border-amber-500 cursor-pointer">#idea</span>
              <span class="px-2 py-1 bg-slate-900 border border-slate-800 rounded text-purple-400 font-mono text-[11px] hover:border-purple-500 cursor-pointer">#Loops</span>
              <span class="px-2 py-1 bg-slate-900 border border-slate-800 rounded text-emerald-400 font-mono text-[11px] hover:border-emerald-500 cursor-pointer">#Help</span>
            </div>
          </div>

          <!-- Active Participants Info -->
          <div class="bg-slate-900 border border-slate-800 p-3 rounded-xl space-y-1 pt-3">
            <span class="font-bold text-slate-300 block">👥 Active Community:</span>
            <p class="text-slate-400 text-[11px]">• 520 IT Students online</p>
            <p class="text-slate-400 text-[11px]">• 128 Instructors & TA</p>
          </div>
        </div>

        <!-- PANEL 2: THREAD LIST (4 cols) -->
        <div class="lg:col-span-4 border-r border-slate-800 p-4 space-y-3 bg-slate-900 overflow-y-auto custom-scrollbar flex flex-col">
          <div class="space-y-2">
            <h3 class="font-bold text-slate-400 uppercase tracking-wider text-[11px]">PANEL 2: THREAD LIST</h3>
            <input v-model="searchQuery" type="text" placeholder="🔍 Search discussions..." class="w-full bg-slate-950 border border-slate-800 rounded p-2 text-white" />
            
            <div class="flex items-center gap-1 text-[11px]">
              <button class="px-2 py-1 bg-slate-800 text-white rounded font-semibold">Newest</button>
              <button class="px-2 py-1 text-slate-400 hover:text-white">🔥 Hot</button>
              <button class="px-2 py-1 text-slate-400 hover:text-white">✅ Solved</button>
            </div>
          </div>

          <!-- Thread Cards -->
          <div class="space-y-2 flex-1 overflow-y-auto custom-scrollbar">
            <div
              v-for="t in filteredThreads"
              :key="t.id"
              @click="selectThread(t)"
              :class="selectedThread?.id === t.id ? 'bg-slate-800 border-blue-500/50 shadow-md' : 'bg-slate-950 border-slate-800 hover:border-slate-700'"
              class="border rounded-xl p-3 space-y-2 cursor-pointer transition"
            >
              <div class="flex justify-between items-start">
                <h4 class="font-bold text-white text-xs flex items-center gap-1.5">
                  <span v-if="t.is_pinned" class="text-amber-400" title="Pinned">📌</span>
                  {{ t.title }}
                </h4>
              </div>

              <div class="flex items-center justify-between text-[11px] text-slate-400">
                <span>👤 {{ t.author_name }}</span>
                <span>{{ t.last_activity }}</span>
              </div>

              <div class="flex items-center justify-between text-[10px] pt-1 border-t border-slate-800/80">
                <span class="text-blue-400 font-mono">💬 {{ t.replies_count }}  👍 {{ t.likes_count }}</span>
                
                <span :class="{
                  'bg-emerald-500/20 text-emerald-400 border-emerald-500/30': t.status === 'solved',
                  'bg-amber-500/20 text-amber-400 border-amber-500/30': t.status === 'open',
                  'bg-slate-800 text-slate-400 border-slate-700': t.status === 'locked'
                }" class="px-2 py-0.5 font-bold border rounded-full">
                  {{ t.status === 'solved' ? '✅ Solved' : (t.status === 'open' ? '🟡 Open' : '🔒 Locked') }}
                </span>
              </div>
            </div>
          </div>
        </div>

        <!-- PANEL 3: THREAD DETAIL & MODERATION (5 cols) -->
        <div v-if="selectedThread" class="lg:col-span-5 p-5 space-y-4 bg-slate-950 overflow-y-auto custom-scrollbar flex flex-col justify-between">
          <div class="space-y-4">
            
            <!-- Thread Header & Moderation Bar -->
            <div class="border-b border-slate-800 pb-3 space-y-2">
              <div class="flex items-start justify-between gap-2">
                <h2 class="text-base font-bold text-white flex items-center gap-2">
                  <span v-if="selectedThread.is_pinned" class="text-amber-400">📌</span>
                  {{ selectedThread.title }}
                </h2>
              </div>

              <div class="flex items-center justify-between text-xs text-slate-400">
                <span>By <strong>{{ selectedThread.author_name }}</strong> • {{ selectedThread.last_activity }}</span>
                <span class="font-mono text-cyan-400">👀 {{ selectedThread.views_count }} • 💬 {{ selectedThread.replies_count }}</span>
              </div>

              <!-- Tags list -->
              <div class="flex items-center gap-1">
                <span v-for="tag in selectedThread.tags" :key="tag" class="px-2 py-0.5 bg-slate-900 border border-slate-800 rounded text-blue-400 font-mono text-[10px]">
                  {{ tag }}
                </span>
                <span class="ml-auto text-[10px] text-emerald-400 bg-emerald-500/10 border border-emerald-500/20 px-2 py-0.5 rounded font-mono">
                  🤖 Toxic Score: {{ selectedThread.toxic_score }}% (Safe)
                </span>
              </div>

              <!-- Moderation Actions Toolbar -->
              <div class="pt-2 flex items-center gap-1.5 flex-wrap text-[11px]">
                <button @click="togglePin" class="px-2.5 py-1 bg-slate-900 hover:bg-slate-800 text-amber-300 border border-slate-800 rounded">
                  📌 {{ selectedThread.is_pinned ? 'Unpin' : 'Pin to top' }}
                </button>
                <button @click="markSolved" class="px-2.5 py-1 bg-slate-900 hover:bg-slate-800 text-emerald-300 border border-slate-800 rounded">
                  ✅ Mark Solved
                </button>
                <button @click="toggleLock" class="px-2.5 py-1 bg-slate-900 hover:bg-slate-800 text-slate-300 border border-slate-800 rounded">
                  🔒 {{ selectedThread.is_locked ? 'Unlock' : 'Lock Thread' }}
                </button>
                <button @click="convertToFaq" class="px-2.5 py-1 bg-indigo-600/20 text-indigo-300 border border-indigo-500/30 rounded font-semibold">
                  📚 Convert to FAQ
                </button>
              </div>
            </div>

            <!-- Comments Thread -->
            <div class="space-y-3">
              <h4 class="font-bold text-slate-400 uppercase tracking-wider text-[11px]">💬 Comments & Answers</h4>
              
              <div v-for="c in selectedThread.comments" :key="c.id" class="p-3 bg-slate-900 border border-slate-800 rounded-xl space-y-2">
                <div class="flex justify-between items-center text-xs">
                  <span class="font-bold text-white flex items-center gap-1.5">
                    👤 {{ c.author_name }}
                    <span v-if="c.is_teacher_answer" class="text-[10px] bg-indigo-500/20 text-indigo-300 border border-indigo-500/30 px-1.5 rounded font-mono">
                      👨‍🏫 Teacher
                    </span>
                    <span v-if="c.is_best_answer" class="text-[10px] bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 px-1.5 rounded font-mono">
                      ✅ Best Answer
                    </span>
                  </span>
                  <span class="text-slate-500 text-[10px]">{{ c.created_at }}</span>
                </div>

                <p class="text-slate-300 text-xs">{{ c.content }}</p>

                <div class="flex items-center justify-between text-[11px] pt-1 text-slate-400">
                  <span>👍 {{ c.likes_count }} helpful</span>
                  <button @click="convertToFaq" class="text-indigo-400 hover:underline text-[10px]">Mark as FAQ</button>
                </div>
              </div>

              <div v-if="!selectedThread.comments || selectedThread.comments.length === 0" class="text-slate-500 text-xs py-4 text-center">
                No replies yet. Be the first to comment!
              </div>
            </div>

          </div>

          <!-- Reply Box -->
          <div class="pt-3 border-t border-slate-800 space-y-2">
            <div class="flex items-center gap-2 text-slate-400 text-xs">
              <button class="hover:text-white font-bold">B</button>
              <button class="hover:text-white italic">I</button>
              <button class="hover:text-white font-mono">&lt;/&gt;</button>
              <button class="hover:text-white">📎</button>
              <button class="hover:text-white">😀</button>
            </div>
            <div class="flex gap-2">
              <textarea v-model="newCommentText" rows="2" placeholder="✍️ Write a reply..." class="flex-1 bg-slate-900 border border-slate-800 rounded p-2 text-white text-xs"></textarea>
              <button @click="postReply" class="px-4 py-2 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-xl text-xs">
                Reply
              </button>
            </div>
          </div>
        </div>

      </div>
    </div>
  </AdminLayout>
</template>
