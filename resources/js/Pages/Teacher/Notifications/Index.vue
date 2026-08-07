<script setup lang="ts">
import { ref } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import TeacherLayout from '@/Layouts/TeacherLayout.vue'

const notifications = ref([
  {
    id: 1,
    title: 'សិស្សបានផ្ញើកិច្ចការ Assignment 1',
    desc: 'Chan Dara បានដាក់កិច្ចការ First C Program សម្រាប់ពិនិត្យ',
    time: '5 នាទីមុន',
    type: 'assignment',
    read: false,
    link: '/teacher/assessment?tab=assignments'
  },
  {
    id: 2,
    title: 'សំណួរថ្មីក្នុងសភាពិភាក្សា Q&A',
    desc: 'Sok Dara បានសួរសំណួរអំពី Pointers & Memory Management',
    time: '30 នាទីមុន',
    type: 'question',
    read: false,
    link: '/teacher/discussion?tab=questions'
  },
  {
    id: 3,
    title: 'ការផ្ទៀងផ្ទាត់ការបង់ប្រាក់ ABA',
    desc: 'សិស្សបានទូទាត់ប្រាក់សម្រាប់វគ្គ C Programming Basics',
    time: '2 ម៉ោងមុន',
    type: 'payment',
    read: false,
    link: '/teacher/earnings'
  },
  {
    id: 4,
    title: 'កាលវិភាគបង្រៀន Live Class ជិតដល់ម៉ោង',
    desc: 'វគ្គសិក្សា C Programming Basics នឹងចាប់ផ្តើមនៅម៉ោង 2:00 PM',
    time: '3 ម៉ោងមុន',
    type: 'schedule',
    read: false,
    link: '/teacher/calendar'
  },
  {
    id: 5,
    title: 'សិស្សប្រឈមមុខនឹងការបោះបង់ការសិក្សា (At-Risk)',
    desc: 'AI បានវិភាគឃើញសិស្ស 3 នាក់មិនបានចូលរៀនលើសពី 7 ថ្ងៃ',
    time: '1 ថ្ងៃមុន',
    type: 'system',
    read: false,
    link: '/teacher/progress?tab=at-risk'
  }
])

const markAsRead = (id: number) => {
  const item = notifications.value.find(n => n.id === id)
  if (item) item.read = true
}

const markAllAsRead = () => {
  notifications.value.forEach(n => n.read = true)
}
</script>

<template>
  <TeacherLayout title="Notifications - ការជូនដំណឹង">
    <Head title="Notifications - ការជូនដំណឹង" />

    <div class="space-y-6 max-w-5xl mx-auto">
      <!-- Header Banner -->
      <div class="bg-gradient-to-r from-indigo-900/60 via-slate-800 to-slate-900 border border-slate-800 rounded-2xl p-6 shadow-xl flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
          <div class="flex items-center gap-2">
            <span class="px-2.5 py-1 rounded-full bg-rose-500/20 text-rose-300 border border-rose-500/30 text-xs font-bold">
              🔴 5 ការជូនដំណឹងថ្មី
            </span>
          </div>
          <h1 class="text-xl font-bold text-white mt-2">ការជូនដំណឹង (Notifications)</h1>
          <p class="text-xs text-slate-400 mt-1">តាមដានរាល់បច្ចុប្បន្នភាព កិច្ចការសិស្ស សំណួរ ស្ថានភាពបង់ប្រាក់ ABA និងកាលវិភាគ</p>
        </div>

        <button
          @click="markAllAsRead"
          type="button"
          class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white font-semibold text-xs rounded-xl shadow-md hover:shadow-indigo-500/20 transition-all cursor-pointer self-start md:self-auto"
        >
          ✓ អានទាំងអស់ (Mark All Read)
        </button>
      </div>

      <!-- Notification List -->
      <div class="bg-slate-900/90 border border-slate-800 rounded-2xl overflow-hidden shadow-xl">
        <div class="divide-y divide-slate-800/80">
          <div
            v-for="notif in notifications"
            :key="notif.id"
            @click="markAsRead(notif.id)"
            :class="[
              notif.read ? 'bg-slate-900/40 opacity-75' : 'bg-slate-800/40 hover:bg-slate-800/80',
              'p-4 sm:p-5 transition-colors cursor-pointer flex items-start justify-between gap-4 group'
            ]"
          >
            <div class="flex items-start gap-4 min-w-0">
              <div :class="[
                notif.type === 'payment' ? 'bg-emerald-500/20 text-emerald-400 border-emerald-500/30' :
                notif.type === 'assignment' ? 'bg-amber-500/20 text-amber-400 border-amber-500/30' :
                notif.type === 'schedule' ? 'bg-sky-500/20 text-sky-400 border-sky-500/30' :
                notif.type === 'system' ? 'bg-rose-500/20 text-rose-400 border-rose-500/30' :
                'bg-indigo-500/20 text-indigo-400 border-indigo-500/30',
                'p-3 rounded-xl border shrink-0'
              ]">
                <svg v-if="notif.type === 'payment'" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V6m0 12v-2m0 0c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <svg v-else-if="notif.type === 'assignment'" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                <svg v-else-if="notif.type === 'schedule'" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                <svg v-else class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
              </div>

              <div class="space-y-1">
                <div class="flex items-center gap-2 flex-wrap">
                  <h3 class="text-sm font-bold text-white group-hover:text-indigo-300 transition-colors">{{ notif.title }}</h3>
                  <span v-if="!notif.read" class="w-2 h-2 rounded-full bg-rose-500"></span>
                </div>
                <p class="text-xs text-slate-300 leading-relaxed">{{ notif.desc }}</p>
                <p class="text-[11px] text-slate-500 font-medium pt-1">{{ notif.time }}</p>
              </div>
            </div>

            <Link
              :href="notif.link"
              class="shrink-0 px-3 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-indigo-400 text-xs font-semibold transition-colors flex items-center gap-1"
            >
              មើលលម្អិត →
            </Link>
          </div>
        </div>
      </div>
    </div>
  </TeacherLayout>
</template>
