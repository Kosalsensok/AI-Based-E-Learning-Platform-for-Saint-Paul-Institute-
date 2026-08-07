<script setup lang="ts">
import { ref, computed } from 'vue'
import { router, Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps<{
  announcements: Array<any>
  topStats: any
}>()

// Filter & Search State
const searchQuery = ref('')
const selectedStatus = ref('all')
const selectedAudience = ref('all')
const selectedPriority = ref('all')

// Modals State
const showComposerModal = ref(false)
const showPreviewDrawer = ref(false)
const activeLangTab = ref<'kh' | 'en' | 'auto'>('kh')
const selectedAnnouncement = ref<any>(null)

// Form State for New Announcement
const form = ref({
  title_kh: '',
  title_en: '',
  body_kh: '',
  body_en: '',
  audience_type: 'custom',
  faculty: 'Computing',
  major: 'IT & Networking',
  course: 'All Courses',
  year: 'Year 2',
  payment_status: 'All',
  priority: 'high', // low, medium, high, urgent
  channels: {
    in_app: true,
    email: true,
    push: true,
    sms: false
  },
  schedule_type: 'now', // now, scheduled, recurring
  schedule_date: '2025-05-28',
  schedule_time: '08:00',
  pin_days: 7,
  is_pinned: true,
  require_ack: true,
  allow_comments: false,
  attachment_file: 'exam_schedule.pdf'
})

// Estimated reach logic based on audience selection
const estimatedReach = computed(() => {
  if (form.value.audience_type === 'everyone') return 2751
  if (form.value.audience_type === 'all_students') return 2458
  if (form.value.audience_type === 'all_teachers') return 145
  return 245
})

const smsCost = computed(() => {
  return (0.02 * estimatedReach.value).toFixed(2)
})

// Auto Translate Simulator
const autoTranslate = () => {
  if (form.value.title_kh && !form.value.title_en) {
    form.value.title_en = form.value.title_kh + ' (English Translated)'
  }
  if (form.value.body_kh && !form.value.body_en) {
    form.value.body_en = 'Notice to all students: ' + form.value.body_kh
  }
  alert('🤖 Auto-translation complete!')
}

// Submit Form
const submitAnnouncement = () => {
  router.post('/admin/notifications/announcement', form.value, {
    onSuccess: () => {
      showComposerModal.value = false
      alert('📢 Announcement published successfully!')
    }
  })
}

// Open Live Multi-Device Preview
const openPreview = (announcement?: any) => {
  selectedAnnouncement.value = announcement || form.value
  showPreviewDrawer.value = true
}

// Filtered Announcements
const filteredAnnouncements = computed(() => {
  return props.announcements.filter(item => {
    const matchesSearch = item.title_kh.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
                          (item.title_en && item.title_en.toLowerCase().includes(searchQuery.value.toLowerCase()))
    const matchesPriority = selectedPriority.value === 'all' || item.priority === selectedPriority.value
    return matchesSearch && matchesPriority
  })
})
</script>

<template>
  <AdminLayout title="Notification Module — Communication & Engagement Center">
    <div class="space-y-6">
      
      <!-- Top Header & Theme Banner (Amber / Yellow & Blue) -->
      <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-slate-900/90 border border-slate-800 p-6 rounded-2xl relative overflow-hidden">
        <div class="absolute -right-10 -bottom-10 w-60 h-60 bg-amber-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div>
          <h1 class="text-2xl font-bold text-white flex items-center gap-2.5">
            📢 Communication & Engagement Center
            <span class="text-xs bg-amber-500/20 text-amber-300 border border-amber-500/30 px-2.5 py-0.5 rounded-full font-mono">
              Alert & Attention
            </span>
          </h1>
          <p class="text-xs text-slate-400 mt-1">
            គ្រប់គ្រងសេចក្តីប្រកាស, អ៊ីមែល, Push Notifications និងកាលវិភាគផ្ញើសារអប់រំ។
          </p>
        </div>

        <div class="flex items-center gap-2 z-10">
          <button
            @click="showComposerModal = true"
            class="px-4 py-2 bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-400 hover:to-orange-500 text-slate-950 font-bold text-xs rounded-xl shadow-lg shadow-amber-500/20 transition flex items-center gap-1.5"
          >
            ➕ New Announcement
          </button>
        </div>
      </div>

      <!-- Service Status Bar -->
      <div class="bg-slate-900 border border-slate-800 rounded-xl px-4 py-2.5 flex flex-wrap items-center justify-between gap-3 text-xs">
        <div class="flex items-center gap-4 flex-wrap">
          <span class="text-slate-400 font-semibold">🔗 Service Status:</span>
          <span class="flex items-center gap-1.5 text-slate-300"><span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span> SMTP (Email): <strong class="text-emerald-400">Connected</strong></span>
          <span class="flex items-center gap-1.5 text-slate-300"><span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span> Firebase (Push): <strong class="text-emerald-400">Active</strong></span>
          <span class="flex items-center gap-1.5 text-slate-300"><span class="w-2 h-2 rounded-full bg-amber-400"></span> SMS Gateway: <strong class="text-amber-400">Limited Credit ($12)</strong></span>
        </div>
        <button class="text-indigo-400 hover:text-indigo-300 font-medium">⚙️ Configure Services</button>
      </div>

      <!-- Top Summary Metrics Bar -->
      <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
        <div class="bg-slate-900 border border-slate-800 p-3.5 rounded-xl space-y-1">
          <span class="text-[11px] text-slate-400">📢 Total Sent (Month)</span>
          <p class="text-xl font-bold text-white">28,450</p>
        </div>
        <div class="bg-slate-900 border border-slate-800 p-3.5 rounded-xl space-y-1">
          <span class="text-[11px] text-slate-400">📧 Email Delivered</span>
          <p class="text-xl font-bold text-emerald-400">96.8%</p>
        </div>
        <div class="bg-slate-900 border border-slate-800 p-3.5 rounded-xl space-y-1">
          <span class="text-[11px] text-slate-400">📱 Push Opened</span>
          <p class="text-xl font-bold text-cyan-400">72.4%</p>
        </div>
        <div class="bg-slate-900 border border-slate-800 p-3.5 rounded-xl space-y-1">
          <span class="text-[11px] text-slate-400">⏰ Scheduled Queue</span>
          <p class="text-xl font-bold text-amber-400">17 pending</p>
        </div>
        <div class="bg-slate-900 border border-slate-800 p-3.5 rounded-xl space-y-1">
          <span class="text-[11px] text-slate-400">🔴 Failed Delivery</span>
          <p class="text-xl font-bold text-red-400">142 logs</p>
        </div>
        <div class="bg-slate-900 border border-slate-800 p-3.5 rounded-xl space-y-1">
          <span class="text-[11px] text-slate-400">👁️ Avg Read Rate</span>
          <p class="text-xl font-bold text-indigo-400">84.2%</p>
        </div>
      </div>

      <!-- Navigation Tabs -->
      <div class="flex items-center gap-1 border-b border-slate-800 overflow-x-auto pb-1 custom-scrollbar">
        <Link href="/admin/notifications/announcements" class="px-4 py-2.5 text-xs rounded-xl border bg-amber-500/15 text-amber-300 border-amber-500/40 font-bold shadow-sm shrink-0">
          📢 Announcements
        </Link>
        <Link href="/admin/notifications/emails" class="px-4 py-2.5 text-xs rounded-xl border text-slate-400 hover:text-slate-200 border-transparent hover:bg-slate-800/60 transition shrink-0">
          📧 Email Notifications
        </Link>
        <Link href="/admin/notifications/push" class="px-4 py-2.5 text-xs rounded-xl border text-slate-400 hover:text-slate-200 border-transparent hover:bg-slate-800/60 transition shrink-0">
          📱 Push Notifications
        </Link>
        <Link href="/admin/notifications/scheduled" class="px-4 py-2.5 text-xs rounded-xl border text-slate-400 hover:text-slate-200 border-transparent hover:bg-slate-800/60 transition shrink-0">
          ⏰ Scheduled Notifications
        </Link>
        <Link href="/admin/notifications/history" class="px-4 py-2.5 text-xs rounded-xl border text-slate-400 hover:text-slate-200 border-transparent hover:bg-slate-800/60 transition shrink-0">
          📜 Notification History
        </Link>
      </div>

      <!-- Search & Filters Bar -->
      <div class="bg-slate-900 border border-slate-800 p-4 rounded-xl flex flex-col md:flex-row gap-3 items-center justify-between text-xs">
        <div class="flex flex-wrap items-center gap-2 w-full md:w-auto">
          <select v-model="selectedPriority" class="bg-slate-950 border border-slate-800 rounded-lg px-3 py-2 text-slate-200">
            <option value="all">Priority: All</option>
            <option value="high">🔴 High / Urgent</option>
            <option value="medium">🟡 Medium</option>
            <option value="low">🟢 Low</option>
          </select>

          <select class="bg-slate-950 border border-slate-800 rounded-lg px-3 py-2 text-slate-200">
            <option value="all">Audience: All</option>
            <option value="students">All Students</option>
            <option value="teachers">All Teachers</option>
            <option value="it">IT & Networking</option>
          </select>
        </div>

        <div class="w-full md:w-64">
          <input
            v-model="searchQuery"
            type="text"
            placeholder="🔍 Search announcements..."
            class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3 py-2 text-slate-200 focus:outline-none focus:border-amber-500/50"
          />
        </div>
      </div>

      <!-- Announcements Data Table -->
      <div class="bg-slate-900 border border-slate-800 rounded-2xl overflow-hidden shadow-xl">
        <div class="overflow-x-auto">
          <table class="w-full text-left border-collapse text-xs">
            <thead>
              <tr class="bg-slate-950 border-b border-slate-800 text-slate-400 font-semibold uppercase tracking-wider">
                <th class="p-4">Title</th>
                <th class="p-4">Audience</th>
                <th class="p-4">Sent Count</th>
                <th class="p-4">Read %</th>
                <th class="p-4">Priority</th>
                <th class="p-4 text-right">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-800 text-slate-300">
              <tr v-for="item in filteredAnnouncements" :key="item.id" class="hover:bg-slate-800/40 transition">
                <td class="p-4">
                  <div class="font-bold text-white flex items-center gap-2">
                    <span v-if="item.is_pinned" class="text-amber-400" title="Pinned to Top">📌</span>
                    {{ item.title_kh }}
                  </div>
                  <div class="text-[11px] text-slate-400 font-sans mt-0.5">{{ item.title_en || item.body_kh.substring(0, 45) + '...' }}</div>
                </td>
                <td class="p-4">
                  <span class="px-2.5 py-1 bg-slate-800 text-slate-300 border border-slate-700 rounded-lg font-mono text-[11px]">
                    👥 {{ item.audience_name || item.audience_type }}
                  </span>
                </td>
                <td class="p-4 font-mono text-slate-200">{{ item.sent_count.toLocaleString() }} recipients</td>
                <td class="p-4">
                  <span class="px-2 py-0.5 bg-emerald-500/10 text-emerald-400 font-bold border border-emerald-500/20 rounded">
                    {{ item.read_rate }}
                  </span>
                </td>
                <td class="p-4">
                  <span
                    :class="{
                      'bg-red-500/20 text-red-400 border-red-500/30': item.priority === 'high' || item.priority === 'urgent',
                      'bg-amber-500/20 text-amber-400 border-amber-500/30': item.priority === 'medium',
                      'bg-emerald-500/20 text-emerald-400 border-emerald-500/30': item.priority === 'low'
                    }"
                    class="px-2 py-0.5 text-[10px] font-semibold border rounded-full"
                  >
                    {{ item.priority_label || item.priority }}
                  </span>
                </td>
                <td class="p-4 text-right space-x-2">
                  <button @click="openPreview(item)" class="p-1.5 hover:bg-slate-800 text-slate-300 rounded" title="Multi-Device Preview">👁️</button>
                  <button class="p-1.5 hover:bg-slate-800 text-slate-300 rounded" title="Edit">✏️</button>
                  <button class="p-1.5 hover:bg-slate-800 text-red-400 rounded" title="Delete">🗑️</button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- ========================================================================= -->
    <!-- COMPOSER MODAL (📢 Create New Announcement) -->
    <!-- ========================================================================= -->
    <div v-if="showComposerModal" class="fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-md flex items-center justify-center p-4 overflow-y-auto">
      <div class="bg-slate-900 border border-slate-800 rounded-2xl w-full max-w-4xl max-h-[90vh] flex flex-col shadow-2xl overflow-hidden my-auto">
        
        <!-- Header -->
        <div class="px-6 py-4 border-b border-slate-800 flex justify-between items-center bg-slate-950">
          <div>
            <h3 class="text-base font-bold text-white flex items-center gap-2">
              📢 Create New Announcement
            </h3>
            <p class="text-xs text-slate-400">Rich Text Composer with Target Audience & Delivery Channels</p>
          </div>
          <button @click="showComposerModal = false" class="text-slate-400 hover:text-white text-lg">✕</button>
        </div>

        <!-- Form Body -->
        <div class="p-6 space-y-6 overflow-y-auto custom-scrollbar text-xs">
          
          <!-- Language Tabs -->
          <div class="flex items-center justify-between border-b border-slate-800 pb-3">
            <div class="flex items-center gap-2">
              <button @click="activeLangTab = 'kh'" :class="activeLangTab === 'kh' ? 'bg-amber-500/20 text-amber-300 border-amber-500/40' : 'bg-slate-800 text-slate-400 border-slate-700'" class="px-3 py-1.5 font-bold rounded-lg border">
                🇰🇭 ខ្មែរ
              </button>
              <button @click="activeLangTab = 'en'" :class="activeLangTab === 'en' ? 'bg-amber-500/20 text-amber-300 border-amber-500/40' : 'bg-slate-800 text-slate-400 border-slate-700'" class="px-3 py-1.5 font-bold rounded-lg border">
                🇬🇧 English
              </button>
            </div>

            <button @click="autoTranslate" class="px-3 py-1.5 bg-indigo-600/20 text-indigo-300 border border-indigo-500/30 rounded-lg font-bold hover:bg-indigo-600/30">
              🤖 Auto-Translate
            </button>
          </div>

          <!-- Titles -->
          <div class="space-y-3">
            <div v-show="activeLangTab === 'kh'">
              <label class="text-slate-300 font-semibold block mb-1">Title (KH):</label>
              <input v-model="form.title_kh" type="text" placeholder="កាលវិភាគប្រឡងឆមាសទី២..." class="w-full bg-slate-950 border border-slate-800 rounded-lg p-2.5 text-white" />
            </div>
            <div v-show="activeLangTab === 'en'">
              <label class="text-slate-300 font-semibold block mb-1">Title (EN):</label>
              <input v-model="form.title_en" type="text" placeholder="Semester 2 Exam Schedule..." class="w-full bg-slate-950 border border-slate-800 rounded-lg p-2.5 text-white" />
            </div>
          </div>

          <!-- Rich Text Mockup Toolbar + Message Body -->
          <div>
            <label class="text-slate-300 font-semibold block mb-1">Message Body:</label>
            <div class="bg-slate-950 border border-slate-800 rounded-lg overflow-hidden">
              <div class="bg-slate-900 border-b border-slate-800 p-2 flex items-center gap-1.5 flex-wrap text-slate-300">
                <button class="p-1 px-2 hover:bg-slate-800 rounded font-bold">B</button>
                <button class="p-1 px-2 hover:bg-slate-800 rounded italic">I</button>
                <button class="p-1 px-2 hover:bg-slate-800 rounded underline">U</button>
                <span class="text-slate-700">|</span>
                <button class="p-1 px-2 hover:bg-slate-800 rounded">🔗 Link</button>
                <button class="p-1 px-2 hover:bg-slate-800 rounded">🖼️ Image</button>
                <button class="p-1 px-2 hover:bg-slate-800 rounded">📎 Attach</button>
                <button class="p-1 px-2 hover:bg-slate-800 rounded">📋 List</button>
                <button class="p-1 px-2 hover:bg-slate-800 rounded">😀 Emoji</button>
              </div>
              <textarea
                v-if="activeLangTab === 'kh'"
                v-model="form.body_kh"
                rows="4"
                placeholder="សូមជម្រាបជូនដល់និស្សិតទាំងអស់... ការប្រឡងឆមាសទី២ នឹងចាប់ផ្តើមនៅថ្ងៃទី ៣០ ឧសភា។"
                class="w-full bg-transparent p-3 text-slate-200 focus:outline-none"
              ></textarea>
              <textarea
                v-else
                v-model="form.body_en"
                rows="4"
                placeholder="Please be informed that Semester 2 exams will begin on May 30th."
                class="w-full bg-transparent p-3 text-slate-200 focus:outline-none"
              ></textarea>
            </div>
            <div v-if="form.attachment_file" class="mt-2 text-[11px] text-indigo-400 font-mono flex items-center gap-1">
              📎 Attached: {{ form.attachment_file }}
            </div>
          </div>

          <!-- Target Audience Section -->
          <div class="bg-slate-950 border border-slate-800 p-4 rounded-xl space-y-3">
            <h4 class="font-bold text-amber-400 uppercase tracking-wider">🎯 Target Audience</h4>
            
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
              <label class="flex items-center gap-2 p-2 bg-slate-900 border border-slate-800 rounded-lg cursor-pointer">
                <input type="radio" v-model="form.audience_type" value="everyone" /> Everyone (2,751)
              </label>
              <label class="flex items-center gap-2 p-2 bg-slate-900 border border-slate-800 rounded-lg cursor-pointer">
                <input type="radio" v-model="form.audience_type" value="all_students" /> All Students (2,458)
              </label>
              <label class="flex items-center gap-2 p-2 bg-slate-900 border border-slate-800 rounded-lg cursor-pointer">
                <input type="radio" v-model="form.audience_type" value="all_teachers" /> All Teachers (145)
              </label>
              <label class="flex items-center gap-2 p-2 bg-slate-900 border border-slate-800 rounded-lg cursor-pointer">
                <input type="radio" v-model="form.audience_type" value="custom" /> Custom Filter
              </label>
            </div>

            <!-- Custom Audience Filter Controls -->
            <div v-if="form.audience_type === 'custom'" class="grid grid-cols-2 sm:grid-cols-5 gap-2 pt-2">
              <div>
                <label class="text-slate-400 block mb-0.5">Faculty:</label>
                <select v-model="form.faculty" class="w-full bg-slate-900 border border-slate-800 rounded p-1.5 text-white">
                  <option value="Computing">Computing</option>
                  <option value="Business">Business</option>
                </select>
              </div>
              <div>
                <label class="text-slate-400 block mb-0.5">Major:</label>
                <select v-model="form.major" class="w-full bg-slate-900 border border-slate-800 rounded p-1.5 text-white">
                  <option value="IT & Networking">IT & Networking</option>
                  <option value="Tourism">Tourism Mgt</option>
                </select>
              </div>
              <div>
                <label class="text-slate-400 block mb-0.5">Course:</label>
                <select v-model="form.course" class="w-full bg-slate-900 border border-slate-800 rounded p-1.5 text-white">
                  <option value="All Courses">All Courses</option>
                  <option value="C Programming">C Programming</option>
                </select>
              </div>
              <div>
                <label class="text-slate-400 block mb-0.5">Year:</label>
                <select v-model="form.year" class="w-full bg-slate-900 border border-slate-800 rounded p-1.5 text-white">
                  <option value="Year 2">Year 2</option>
                  <option value="Year 1">Year 1</option>
                </select>
              </div>
              <div>
                <label class="text-slate-400 block mb-0.5">Payment:</label>
                <select v-model="form.payment_status" class="w-full bg-slate-900 border border-slate-800 rounded p-1.5 text-white">
                  <option value="All">All</option>
                  <option value="Unpaid">Unpaid Only</option>
                </select>
              </div>
            </div>

            <div class="text-right font-bold text-emerald-400 text-xs">
              ➔ Estimated Reach: {{ estimatedReach }} students 👥
            </div>
          </div>

          <!-- Priority Level & Channels -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Priority Level -->
            <div class="bg-slate-950 border border-slate-800 p-4 rounded-xl space-y-2">
              <h4 class="font-bold text-amber-400 uppercase tracking-wider">⚡ Priority Level</h4>
              <div class="space-y-1.5">
                <label class="flex items-center gap-2 cursor-pointer">
                  <input type="radio" v-model="form.priority" value="low" /> 🟢 Low (Dashboard only)
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                  <input type="radio" v-model="form.priority" value="medium" /> 🟡 Medium (Dashboard + Email)
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                  <input type="radio" v-model="form.priority" value="high" /> 🔴 High (Dashboard + Email + Push)
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                  <input type="radio" v-model="form.priority" value="urgent" /> 🚨 Urgent (All channels + SMS + Popup)
                </label>
              </div>
            </div>

            <!-- Delivery Channels -->
            <div class="bg-slate-950 border border-slate-800 p-4 rounded-xl space-y-2">
              <h4 class="font-bold text-amber-400 uppercase tracking-wider">📡 Delivery Channels</h4>
              <div class="space-y-1.5">
                <label class="flex items-center gap-2 cursor-pointer">
                  <input type="checkbox" v-model="form.channels.in_app" /> 🔔 In-App Notification
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                  <input type="checkbox" v-model="form.channels.email" /> 📧 Email
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                  <input type="checkbox" v-model="form.channels.push" /> 📱 Push Notification
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                  <input type="checkbox" v-model="form.channels.sms" /> 💬 SMS (Cost: $0.02 × {{ estimatedReach }} = ${{ smsCost }})
                </label>
              </div>
            </div>
          </div>

          <!-- Schedule & Options -->
          <div class="bg-slate-950 border border-slate-800 p-4 rounded-xl space-y-3">
            <h4 class="font-bold text-amber-400 uppercase tracking-wider">⏰ Schedule & Options</h4>
            
            <div class="flex items-center gap-4 flex-wrap">
              <label class="flex items-center gap-2 cursor-pointer">
                <input type="radio" v-model="form.schedule_type" value="now" /> Send Now
              </label>
              <label class="flex items-center gap-2 cursor-pointer">
                <input type="radio" v-model="form.schedule_type" value="scheduled" /> Schedule
              </label>
              <div v-if="form.schedule_type === 'scheduled'" class="flex items-center gap-2">
                <input v-model="form.schedule_date" type="date" class="bg-slate-900 border border-slate-800 rounded p-1 text-white" />
                <input v-model="form.schedule_time" type="time" class="bg-slate-900 border border-slate-800 rounded p-1 text-white" />
              </div>
            </div>

            <div class="pt-2 border-t border-slate-800 space-y-1.5">
              <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" v-model="form.is_pinned" /> 📌 Pin to top of dashboard for <strong>{{ form.pin_days }}</strong> days
              </label>
              <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" v-model="form.require_ack" /> ☑️ Require acknowledgment (Student must click "I understand")
              </label>
            </div>
          </div>

        </div>

        <!-- Footer Bar -->
        <div class="px-6 py-3 border-t border-slate-800 bg-slate-950 flex justify-between items-center">
          <button @click="openPreview(form)" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 font-semibold rounded-xl">
            👁 Preview Multi-Device
          </button>
          <div class="flex items-center gap-2">
            <button @click="showComposerModal = false" class="px-4 py-2 bg-slate-800 text-slate-300 rounded-xl">💾 Save Draft</button>
            <button @click="submitAnnouncement" class="px-6 py-2 bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-400 hover:to-orange-500 text-slate-950 font-bold rounded-xl shadow-lg shadow-amber-500/20">
              🚀 Send Now
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- ========================================================================= -->
    <!-- PREVIEW PANEL (Multi-Device Preview Drawer) -->
    <!-- ========================================================================= -->
    <div v-if="showPreviewDrawer" class="fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-md flex items-center justify-center p-4">
      <div class="bg-slate-900 border border-slate-800 rounded-2xl w-full max-w-5xl p-6 space-y-6 shadow-2xl">
        <div class="flex justify-between items-center border-b border-slate-800 pb-3">
          <h3 class="font-bold text-white text-base">👁️ Multi-Device Live Preview</h3>
          <button @click="showPreviewDrawer = false" class="text-slate-400 hover:text-white">✕</button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs">
          <!-- 🖥️ Desktop Preview -->
          <div class="bg-slate-950 border border-slate-800 rounded-xl p-4 space-y-3">
            <span class="font-bold text-slate-400 uppercase tracking-wider block border-b border-slate-800 pb-1">🖥️ Desktop View</span>
            <div class="bg-slate-900 border border-slate-800 p-3 rounded-lg space-y-2">
              <div class="flex justify-between items-center">
                <span class="bg-amber-500/20 text-amber-300 text-[10px] px-2 py-0.5 rounded font-bold">📌 Pinned</span>
                <span class="text-[10px] text-slate-500">2 min ago</span>
              </div>
              <h4 class="font-bold text-white text-sm">{{ selectedAnnouncement?.title_kh || 'កាលវិភាគប្រឡងឆមាសទី២' }}</h4>
              <p class="text-slate-300">{{ selectedAnnouncement?.body_kh || 'សូមជម្រាបជូនដល់និស្សិតទាំងអស់...' }}</p>
              <div class="pt-2 border-t border-slate-800 flex justify-end">
                <button class="px-3 py-1 bg-amber-500 text-slate-950 font-bold rounded text-[11px]">I Understand</button>
              </div>
            </div>
          </div>

          <!-- 📱 Mobile App Preview -->
          <div class="bg-slate-950 border border-slate-800 rounded-xl p-4 space-y-3">
            <span class="font-bold text-slate-400 uppercase tracking-wider block border-b border-slate-800 pb-1">📱 Mobile Push</span>
            <div class="bg-slate-900 border border-slate-800 p-3 rounded-2xl space-y-2 shadow-lg">
              <div class="flex items-center gap-2">
                <span class="w-5 h-5 bg-amber-500 rounded flex items-center justify-center text-[10px] font-bold text-slate-950">🔔</span>
                <span class="font-bold text-white text-[11px]">E.LMS Notification</span>
                <span class="text-[9px] text-slate-500 ml-auto">now</span>
              </div>
              <h5 class="font-bold text-white text-xs">{{ selectedAnnouncement?.title_kh }}</h5>
              <p class="text-[11px] text-slate-400 line-clamp-2">{{ selectedAnnouncement?.body_kh }}</p>
            </div>
          </div>

          <!-- 📧 Email Preview -->
          <div class="bg-slate-950 border border-slate-800 rounded-xl p-4 space-y-3">
            <span class="font-bold text-slate-400 uppercase tracking-wider block border-b border-slate-800 pb-1">📧 Email Inbox</span>
            <div class="bg-white text-slate-900 p-4 rounded-xl space-y-2 font-serif text-[11px]">
              <div class="border-b pb-2">
                <strong>Subject:</strong> {{ selectedAnnouncement?.title_kh }}
              </div>
              <p>Dear Student,</p>
              <p>{{ selectedAnnouncement?.body_kh }}</p>
              <div class="pt-3 border-t text-[10px] text-slate-500">
                E.LMS Education System · Phnom Penh
              </div>
            </div>
          </div>
        </div>

        <div class="text-right">
          <button @click="showPreviewDrawer = false" class="px-5 py-2 bg-slate-800 text-slate-200 font-semibold rounded-xl text-xs">
            Close Preview
          </button>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
