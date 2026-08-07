<script setup lang="ts">
import { ref, computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps<{
  pushes: Array<any>
  deviceStats: any
  topStats: any
}>()

const showPushComposer = ref(false)

const form = ref({
  icon: '🎬',
  title: 'វីដេអូថ្មី! Module 3: Advanced Pointers',
  body: 'Mr. Sophea បានបង្ហោះវីដេអូបង្រៀនថ្មី។ ចូលមើលដើម្បីកុំឲ្យបាត់បង់មេរៀន!',
  rich_media_url: 'https://images.unsplash.com/photo-1517694712202-14dd9538aa97?w=300',
  btn1_text: '▶️ Watch Now',
  btn1_deeplink: '/course/12/mod3',
  btn2_text: '⏰ Remind Later',
  target_audience: 'IT & Networking Students',
  timing_type: 'smart_ai',
  skip_muted: true,
  quiet_hours: true,
  max_daily_limit: true
})

const submitPush = () => {
  alert('🚀 Push Notification scheduled and sent to 520 devices!')
  showPushComposer.value = false
}

const sendTest = () => {
  alert('📱 Test push notification sent to your device!')
}
</script>

<template>
  <AdminLayout title="Notification Module — Push Notifications">
    <div class="space-y-6">
      
      <!-- Top Header -->
      <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-slate-900/90 border border-slate-800 p-6 rounded-2xl">
        <div>
          <h1 class="text-2xl font-bold text-white flex items-center gap-2.5">
            📱 PUSH NOTIFICATIONS
            <span class="text-xs bg-indigo-500/20 text-indigo-300 border border-indigo-500/30 px-2.5 py-0.5 rounded-full font-mono">
              Firebase FCM Mobile Push
            </span>
          </h1>
          <p class="text-xs text-slate-400 mt-1">
            ផ្ញើសារលេចឡើងភ្លាមៗទៅកាន់ទូរស័ព្ទដៃនិស្សិត (Android / iOS) ដើម្បីជំរុញការចូលរៀន។
          </p>
        </div>

        <div class="flex items-center gap-2">
          <button @click="showPushComposer = true" class="px-4 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-500 hover:to-purple-500 text-white font-bold text-xs rounded-xl shadow-lg shadow-indigo-600/20 flex items-center gap-1">
            ➕ New Push Notification
          </button>
        </div>
      </div>

      <!-- Navigation Tabs -->
      <div class="flex items-center gap-1 border-b border-slate-800 overflow-x-auto pb-1 custom-scrollbar">
        <Link href="/admin/notifications/announcements" class="px-4 py-2.5 text-xs rounded-xl border text-slate-400 hover:text-slate-200 border-transparent hover:bg-slate-800/60 transition shrink-0">
          📢 Announcements
        </Link>
        <Link href="/admin/notifications/emails" class="px-4 py-2.5 text-xs rounded-xl border text-slate-400 hover:text-slate-200 border-transparent hover:bg-slate-800/60 transition shrink-0">
          📧 Email Notifications
        </Link>
        <Link href="/admin/notifications/push" class="px-4 py-2.5 text-xs rounded-xl border bg-indigo-500/15 text-indigo-300 border-indigo-500/40 font-bold shadow-sm shrink-0">
          📱 Push Notifications
        </Link>
        <Link href="/admin/notifications/scheduled" class="px-4 py-2.5 text-xs rounded-xl border text-slate-400 hover:text-slate-200 border-transparent hover:bg-slate-800/60 transition shrink-0">
          ⏰ Scheduled Notifications
        </Link>
        <Link href="/admin/notifications/history" class="px-4 py-2.5 text-xs rounded-xl border text-slate-400 hover:text-slate-200 border-transparent hover:bg-slate-800/60 transition shrink-0">
          📜 Notification History
        </Link>
      </div>

      <!-- Registered Devices Stats -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 text-xs">
        <div class="bg-slate-900 border border-slate-800 p-4 rounded-xl space-y-1">
          <span class="text-slate-400">🤖 Android Devices</span>
          <p class="text-2xl font-bold text-emerald-400">1,845 active</p>
        </div>
        <div class="bg-slate-900 border border-slate-800 p-4 rounded-xl space-y-1">
          <span class="text-slate-400">🍏 iOS Devices</span>
          <p class="text-2xl font-bold text-cyan-400">620 active</p>
        </div>
        <div class="bg-slate-900 border border-slate-800 p-4 rounded-xl space-y-1">
          <span class="text-slate-400">🌐 Web Browsers</span>
          <p class="text-2xl font-bold text-indigo-400">890 registered</p>
        </div>
        <div class="bg-slate-900 border border-slate-800 p-4 rounded-xl space-y-1">
          <span class="text-slate-400">🎯 Avg CTR (Click Rate)</span>
          <p class="text-2xl font-bold text-amber-400">45.8%</p>
        </div>
      </div>

      <!-- Push History Table -->
      <div class="bg-slate-900 border border-slate-800 rounded-2xl overflow-hidden shadow-xl">
        <div class="p-4 border-b border-slate-800 flex justify-between items-center">
          <h2 class="text-sm font-bold text-white uppercase tracking-wider">Push History & Performance</h2>
        </div>
        <div class="overflow-x-auto">
          <table class="w-full text-left border-collapse text-xs">
            <thead>
              <tr class="bg-slate-950 border-b border-slate-800 text-slate-400 font-semibold uppercase tracking-wider">
                <th class="p-4">Title</th>
                <th class="p-4">Type</th>
                <th class="p-4">Audience</th>
                <th class="p-4">Sent</th>
                <th class="p-4">Open %</th>
                <th class="p-4">Status</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-800 text-slate-300">
              <tr v-for="p in pushes" :key="p.id" class="hover:bg-slate-800/40 transition">
                <td class="p-4 font-bold text-white">{{ p.title }}</td>
                <td class="p-4">
                  <span class="px-2 py-0.5 bg-indigo-500/20 text-indigo-300 border border-indigo-500/30 rounded">
                    {{ p.type }}
                  </span>
                </td>
                <td class="p-4 text-slate-400">{{ p.audience }}</td>
                <td class="p-4 font-mono text-slate-200">{{ p.sent_count }} devices</td>
                <td class="p-4 font-bold text-emerald-400">{{ p.open_rate }}</td>
                <td class="p-4"><span class="text-emerald-400 font-semibold">✅ Sent</span></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Push Types Library Table Reference -->
      <div class="bg-slate-900 border border-slate-800 p-5 rounded-2xl space-y-3 text-xs">
        <h3 class="font-bold text-slate-300 uppercase tracking-wider">📚 Push Notification Types Library</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3">
          <div class="bg-slate-950 p-3 rounded-xl border border-slate-800 space-y-1">
            <span class="font-bold text-indigo-400 flex items-center gap-1">🎬 Content Update</span>
            <p class="text-slate-400 text-[11px]">"វីដេអូថ្មីនៅ Module 3!"</p>
          </div>
          <div class="bg-slate-950 p-3 rounded-xl border border-slate-800 space-y-1">
            <span class="font-bold text-amber-400 flex items-center gap-1">⏰ Reminder</span>
            <p class="text-slate-400 text-[11px]">"Quiz ចាប់ផ្តើមក្នុង ១ ម៉ោង"</p>
          </div>
          <div class="bg-slate-950 p-3 rounded-xl border border-slate-800 space-y-1">
            <span class="font-bold text-red-400 flex items-center gap-1">🔥 Engagement</span>
            <p class="text-slate-400 text-[11px]">"កុំបាត់បង់ streak របស់អ្នក!"</p>
          </div>
          <div class="bg-slate-950 p-3 rounded-xl border border-slate-800 space-y-1">
            <span class="font-bold text-emerald-400 flex items-center gap-1">🏆 Achievement</span>
            <p class="text-slate-400 text-[11px]">"អបអរសាទរ! អ្នកបានវិញ្ញាបនបត្រ"</p>
          </div>
        </div>
      </div>
    </div>

    <!-- ========================================================================= -->
    <!-- PUSH COMPOSER MODAL WITH LIVE PHONE PREVIEW -->
    <!-- ========================================================================= -->
    <div v-if="showPushComposer" class="fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-md flex items-center justify-center p-4 overflow-y-auto">
      <div class="bg-slate-900 border border-slate-800 rounded-2xl w-full max-w-5xl max-h-[90vh] flex flex-col shadow-2xl overflow-hidden my-auto">
        <div class="px-6 py-4 border-b border-slate-800 flex justify-between items-center bg-slate-950">
          <h3 class="text-base font-bold text-white flex items-center gap-2">📱 Create Mobile Push Notification</h3>
          <button @click="showPushComposer = false" class="text-slate-400 hover:text-white text-lg">✕</button>
        </div>

        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6 overflow-y-auto custom-scrollbar text-xs">
          
          <!-- Column 1: Composer -->
          <div class="space-y-4">
            <div>
              <label class="text-slate-400 block mb-1">Icon Category:</label>
              <select v-model="form.icon" class="w-full bg-slate-950 border border-slate-800 rounded p-2 text-white">
                <option value="🎬">🎬 Content Update</option>
                <option value="⏰">⏰ Deadline Reminder</option>
                <option value="🔥">🔥 Streak / Engagement</option>
                <option value="🏆">🏆 Achievement / Badge</option>
              </select>
            </div>

            <div>
              <div class="flex justify-between text-slate-400 mb-1">
                <span>Title:</span>
                <span class="font-mono text-[11px]">{{ form.title.length }}/50 chars</span>
              </div>
              <input v-model="form.title" maxlength="50" type="text" class="w-full bg-slate-950 border border-slate-800 rounded p-2 text-white" />
            </div>

            <div>
              <div class="flex justify-between text-slate-400 mb-1">
                <span>Body:</span>
                <span class="font-mono text-[11px]">{{ form.body.length }}/120 chars</span>
              </div>
              <textarea v-model="form.body" maxlength="120" rows="3" class="w-full bg-slate-950 border border-slate-800 rounded p-2 text-white"></textarea>
            </div>

            <div>
              <label class="text-slate-400 block mb-1">🖼️ Rich Media Image Thumbnail:</label>
              <input v-model="form.rich_media_url" type="text" class="w-full bg-slate-950 border border-slate-800 rounded p-2 text-white font-mono text-[11px]" />
            </div>

            <!-- Action Buttons -->
            <div class="bg-slate-950 p-3 rounded-xl border border-slate-800 space-y-2">
              <span class="font-bold text-slate-300 block">🔘 Action Buttons:</span>
              <div class="grid grid-cols-2 gap-2">
                <input v-model="form.btn1_text" type="text" placeholder="Btn 1 Text" class="bg-slate-900 border border-slate-800 rounded p-1.5 text-white" />
                <input v-model="form.btn1_deeplink" type="text" placeholder="Deep Link" class="bg-slate-900 border border-slate-800 rounded p-1.5 text-white font-mono text-[11px]" />
              </div>
            </div>

            <div>
              <label class="text-slate-400 block mb-1">🎯 Target Audience:</label>
              <select v-model="form.target_audience" class="w-full bg-slate-950 border border-slate-800 rounded p-2 text-white font-bold">
                <option value="IT & Networking Students">IT & Networking Students (520 devices)</option>
                <option value="All Students">All Students (2,458 devices)</option>
              </select>
            </div>

            <!-- Timing & Respect Settings -->
            <div class="bg-slate-950 p-3 rounded-xl border border-slate-800 space-y-2">
              <span class="font-bold text-amber-400 block">⏰ Smart Timing & Restrictions:</span>
              <label class="flex items-center gap-2 cursor-pointer text-slate-300">
                <input type="radio" v-model="form.timing_type" value="smart_ai" /> 🤖 Smart Timing (AI sends when user is active: 7-9 PM)
              </label>
              <div class="pt-2 border-t border-slate-800 space-y-1 text-slate-400">
                <label class="flex items-center gap-2 cursor-pointer"><input type="checkbox" v-model="form.skip_muted" /> Skip users who muted notifications</label>
                <label class="flex items-center gap-2 cursor-pointer"><input type="checkbox" v-model="form.quiet_hours" /> Quiet hours protection (10 PM – 7 AM)</label>
              </div>
            </div>
          </div>

          <!-- Column 2: Live Phone Preview -->
          <div class="bg-slate-950 p-5 rounded-2xl border border-slate-800 space-y-4 flex flex-col items-center">
            <h4 class="font-bold text-slate-300 uppercase tracking-wider text-[11px]">📱 Live Device Preview</h4>

            <!-- iPhone 15 Frame -->
            <div class="w-64 bg-slate-900 border-4 border-slate-700 rounded-[32px] p-3 shadow-2xl space-y-2 relative">
              <div class="w-20 h-4 bg-slate-800 rounded-full mx-auto mb-2"></div>
              
              <!-- Push Card -->
              <div class="bg-slate-800/90 backdrop-blur-md p-3 rounded-2xl border border-slate-700/60 text-white space-y-1.5 shadow-lg">
                <div class="flex items-center gap-1.5">
                  <span class="w-4 h-4 bg-indigo-500 rounded text-[9px] flex items-center justify-center font-bold">🎓</span>
                  <span class="font-bold text-[11px]">E.LMS</span>
                  <span class="text-[9px] text-slate-400 ml-auto">now</span>
                </div>
                <h5 class="font-bold text-xs">{{ form.title }}</h5>
                <p class="text-[10px] text-slate-300 leading-tight">{{ form.body }}</p>
                
                <div v-if="form.rich_media_url" class="h-20 rounded-lg bg-cover bg-center mt-1 border border-slate-700" :style="{ backgroundImage: `url(${form.rich_media_url})` }"></div>

                <div class="pt-2 border-t border-slate-700/60 flex items-center justify-between text-[10px] text-indigo-300 font-bold">
                  <span>{{ form.btn1_text }}</span>
                  <span>{{ form.btn2_text }}</span>
                </div>
              </div>
            </div>
          </div>

        </div>

        <div class="px-6 py-3 border-t border-slate-800 bg-slate-950 flex justify-between items-center">
          <button @click="sendTest" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 rounded-xl text-xs font-semibold">
            📤 Send Test to Me
          </button>
          <button @click="submitPush" class="px-6 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-500 hover:to-purple-500 text-white font-bold rounded-xl text-xs shadow-lg shadow-indigo-600/20">
            🚀 Send to 520 devices
          </button>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
