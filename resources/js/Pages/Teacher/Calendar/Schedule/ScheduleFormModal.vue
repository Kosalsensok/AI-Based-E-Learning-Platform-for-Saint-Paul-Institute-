<script setup lang="ts">
import { ref, watch } from 'vue'
import { useForm } from '@inertiajs/vue3'

const props = defineProps<{
  show: boolean
  courses: Array<any>
  modules: Array<any>
  majors: Array<any>
  scheduleToEdit?: any
}>()

const emit = defineEmits(['close', 'saved'])

const form = useForm({
  id: null as number | null,
  type: 'live_class',
  title: 'Live Class: C Functions - Deep Dive',
  course_id: null as number | null,
  module_id: null as number | null,
  major_id: null as number | null,
  learning_mode: 'instructor_led',
  start_date: new Date().toISOString().split('T')[0],
  start_time: '14:00',
  end_time: '16:00',
  timezone: 'Asia/Phnom_Penh',
  repeat_rule: 'none',
  location_type: 'online',
  room_number: 'Room 302',
  meeting_link: 'https://zoom.us/j/123-456-789',
  capacity: 60,
  description: 'ថ្ងៃនេះយើងនឹងរៀនអំពី Function, Parameter និង Return Type ព្រមទាំងការប្រើប្រាស់ Recursion ក្នុងភាសា C។',
  notify_email: true,
  notify_push: true,
  notify_announcement: true,
  reminder_15m: true,
  reminder_1h: true,
  auto_record: true,
})

watch(() => props.scheduleToEdit, (newVal) => {
  if (newVal) {
    form.id = newVal.id
    form.type = newVal.type || 'live_class'
    form.title = newVal.title || ''
    form.course_id = newVal.course_id || null
    form.module_id = newVal.module_id || null
    form.major_id = newVal.major_id || null
    form.learning_mode = newVal.learning_mode || 'instructor_led'
    if (newVal.start_at) {
      const d = new Date(newVal.start_at)
      form.start_date = d.toISOString().split('T')[0]
      form.start_time = d.toTimeString().substring(0, 5)
    }
    if (newVal.end_at) {
      const d = new Date(newVal.end_at)
      form.end_time = d.toTimeString().substring(0, 5)
    }
    form.meeting_link = newVal.meeting_link || ''
    form.location_type = newVal.location_type || 'online'
    form.room_number = newVal.room_number || ''
    form.capacity = newVal.capacity || 60
    form.description = newVal.description || ''
  } else {
    form.reset()
    if (props.courses.length > 0) {
      form.course_id = props.courses[0].id
    }
  }
}, { immediate: true })

const submitForm = (isDraft = false) => {
  if (form.id) {
    form.put(route('teacher.calendar.schedule.update', form.id), {
      onSuccess: () => {
        emit('saved')
        emit('close')
      }
    })
  } else {
    form.post(route('teacher.calendar.schedule.store'), {
      onSuccess: () => {
        emit('saved')
        emit('close')
      }
    })
  }
}
</script>

<template>
  <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm overflow-y-auto">
    <div class="bg-white dark:bg-gray-800 rounded-3xl max-w-2xl w-full shadow-2xl border border-slate-200 dark:border-gray-700 my-8 overflow-hidden transform transition-all">
      <!-- Modal Header -->
      <div class="px-6 py-4 bg-gradient-to-r from-blue-600 to-indigo-700 text-white flex items-center justify-between">
        <div class="flex items-center gap-3">
          <span class="text-2xl">📌</span>
          <div>
            <h3 class="font-bold text-lg leading-tight">
              {{ form.id ? 'កែប្រែកាលវិភាគ / EDIT SCHEDULE' : '➕ CREATE LIVE CLASS / SCHEDULE' }}
            </h3>
            <p class="text-xs text-blue-100">រៀបចំកាលវិភាគបង្រៀនផ្ទាល់, Q&A, Office Hour ឬ Exam Review</p>
          </div>
        </div>
        <button @click="$emit('close')" class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center text-white font-bold transition">✕</button>
      </div>

      <!-- Form Body -->
      <div class="p-6 space-y-4 text-xs text-slate-700 dark:text-slate-200 max-h-[75vh] overflow-y-auto">
        <!-- Type Selection -->
        <div>
          <label class="block font-bold mb-1.5 text-slate-800 dark:text-gray-100">Schedule Type (ប្រភេទកាលវិភាគ)</label>
          <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
            <button
              type="button"
              @click="form.type = 'live_class'"
              :class="form.type === 'live_class' ? 'bg-blue-600 text-white shadow-md ring-2 ring-blue-400' : 'bg-slate-100 dark:bg-gray-700 text-slate-700 dark:text-slate-300 hover:bg-blue-50'"
              class="px-3 py-2.5 rounded-xl font-bold flex items-center justify-center gap-1.5 transition text-[11px]"
            >
              <span>🟦 Live Class</span>
            </button>

            <button
              type="button"
              @click="form.type = 'qa_session'"
              :class="form.type === 'qa_session' ? 'bg-purple-600 text-white shadow-md ring-2 ring-purple-400' : 'bg-slate-100 dark:bg-gray-700 text-slate-700 dark:text-slate-300 hover:bg-purple-50'"
              class="px-3 py-2.5 rounded-xl font-bold flex items-center justify-center gap-1.5 transition text-[11px]"
            >
              <span>🟪 Q&A Session</span>
            </button>

            <button
              type="button"
              @click="form.type = 'office_hour'"
              :class="form.type === 'office_hour' ? 'bg-indigo-600 text-white shadow-md ring-2 ring-indigo-400' : 'bg-slate-100 dark:bg-gray-700 text-slate-700 dark:text-slate-300 hover:bg-indigo-50'"
              class="px-3 py-2.5 rounded-xl font-bold flex items-center justify-center gap-1.5 transition text-[11px]"
            >
              <span>🏢 Office Hour</span>
            </button>

            <button
              type="button"
              @click="form.type = 'exam'"
              :class="form.type === 'exam' ? 'bg-red-600 text-white shadow-md ring-2 ring-red-400' : 'bg-slate-100 dark:bg-gray-700 text-slate-700 dark:text-slate-300 hover:bg-red-50'"
              class="px-3 py-2.5 rounded-xl font-bold flex items-center justify-center gap-1.5 transition text-[11px]"
            >
              <span>🟥 Exam Review</span>
            </button>
          </div>
        </div>

        <!-- Schedule Title -->
        <div>
          <label class="block font-bold mb-1">Schedule Title (ចំណងជើង)</label>
          <input
            v-model="form.title"
            type="text"
            placeholder="e.g. Live Class: C Functions - Deep Dive"
            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-gray-700 bg-slate-50 dark:bg-gray-700 font-semibold text-slate-800 dark:text-white focus:bg-white focus:ring-2 focus:ring-blue-500"
          />
        </div>

        <!-- Course & Module Selection -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
          <div>
            <label class="block font-bold mb-1">Course (មុខវិជ្ជា)</label>
            <select
              v-model="form.course_id"
              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-gray-700 bg-slate-50 dark:bg-gray-700 font-medium text-slate-800 dark:text-white"
            >
              <option :value="null">-- Select Course --</option>
              <option v-for="c in courses" :key="c.id" :value="c.id">{{ c.title }}</option>
            </select>
          </div>

          <div>
            <label class="block font-bold mb-1">Module (មេរៀន)</label>
            <select
              v-model="form.module_id"
              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-gray-700 bg-slate-50 dark:bg-gray-700 font-medium text-slate-800 dark:text-white"
            >
              <option :value="null">-- Select Module --</option>
              <option v-for="m in modules" :key="m.id" :value="m.id">{{ m.title }}</option>
            </select>
          </div>
        </div>

        <!-- Faculty / Major & Learning Mode -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
          <div>
            <label class="block font-bold mb-1">Faculty / Major</label>
            <select
              v-model="form.major_id"
              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-gray-700 bg-slate-50 dark:bg-gray-700 font-medium text-slate-800 dark:text-white"
            >
              <option :value="null">-- All Majors (IT & Networking / Computing) --</option>
              <option v-for="mj in majors" :key="mj.id" :value="mj.id">{{ mj.name }}</option>
            </select>
          </div>

          <div>
            <label class="block font-bold mb-1">Learning Mode</label>
            <select
              v-model="form.learning_mode"
              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-gray-700 bg-slate-50 dark:bg-gray-700 font-medium text-slate-800 dark:text-white"
            >
              <option value="instructor_led">📖 Instructor-Led (តម្រូវឱ្យមាន Schedule)</option>
              <option value="self_paced">⚡ Self-Paced (មិនត្រូវការ Schedule)</option>
            </select>
          </div>
        </div>

        <!-- Date & Time & Timezone -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3 p-3 bg-slate-50 dark:bg-gray-700/50 rounded-2xl border border-slate-200/80 dark:border-gray-700">
          <div>
            <label class="block font-bold mb-1">📅 Date</label>
            <input
              v-model="form.start_date"
              type="date"
              class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-gray-600 bg-white dark:bg-gray-700 font-medium"
            />
          </div>

          <div>
            <label class="block font-bold mb-1">⏰ Start Time</label>
            <input
              v-model="form.start_time"
              type="time"
              class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-gray-600 bg-white dark:bg-gray-700 font-medium"
            />
          </div>

          <div>
            <label class="block font-bold mb-1">⌛ End Time</label>
            <input
              v-model="form.end_time"
              type="time"
              class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-gray-600 bg-white dark:bg-gray-700 font-medium"
            />
          </div>
        </div>

        <!-- Timezone & Repeat -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
          <div>
            <label class="block font-bold mb-1">Timezone</label>
            <select
              v-model="form.timezone"
              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-gray-700 bg-slate-50 dark:bg-gray-700 font-medium"
            >
              <option value="Asia/Phnom_Penh">Asia/Phnom_Penh (GMT+7)</option>
              <option value="UTC">UTC (Coordinated Universal Time)</option>
            </select>
          </div>

          <div>
            <label class="block font-bold mb-1">Repeat Schedule</label>
            <select
              v-model="form.repeat_rule"
              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-gray-700 bg-slate-50 dark:bg-gray-700 font-medium"
            >
              <option value="none">No Repeat (ម្តងម្កាល)</option>
              <option value="weekly">Weekly (រៀងរាល់សប្តាហ៍)</option>
              <option value="custom">Custom (កំណត់ផ្ទាល់ខ្លួន)</option>
            </select>
          </div>
        </div>

        <!-- Location & Meeting Link & Capacity -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
          <div>
            <label class="block font-bold mb-1">Location</label>
            <select
              v-model="form.location_type"
              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-gray-700 bg-slate-50 dark:bg-gray-700 font-medium"
            >
              <option value="online">💻 Online (Zoom/Meet)</option>
              <option value="room">🏫 Physical Room (អគារ)</option>
            </select>
          </div>

          <div v-if="form.location_type === 'online'">
            <label class="block font-bold mb-1">Meeting Link (Zoom/Meet)</label>
            <input
              v-model="form.meeting_link"
              type="text"
              placeholder="https://zoom.us/j/123-456-789"
              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-gray-700 bg-slate-50 dark:bg-gray-700 font-mono text-[11px]"
            />
          </div>

          <div v-else>
            <label class="block font-bold mb-1">Room Number</label>
            <input
              v-model="form.room_number"
              type="text"
              placeholder="Room 302"
              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-gray-700 bg-slate-50 dark:bg-gray-700"
            />
          </div>

          <div>
            <label class="block font-bold mb-1">Capacity (ចំនួនសិស្ស)</label>
            <input
              v-model="form.capacity"
              type="number"
              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-gray-700 bg-slate-50 dark:bg-gray-700 font-medium"
            />
          </div>
        </div>

        <!-- Description -->
        <div>
          <label class="block font-bold mb-1">Description / Agenda (ការពិពណ៌នា)</label>
          <textarea
            v-model="form.description"
            rows="3"
            placeholder="ថ្ងៃនេះយើងនឹងរៀនអំពី Function, Parameter និង Return Type..."
            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-gray-700 bg-slate-50 dark:bg-gray-700 text-slate-800 dark:text-white"
          ></textarea>
        </div>

        <!-- Notifications & Options -->
        <div class="p-4 bg-blue-50/70 dark:bg-gray-700/60 rounded-2xl border border-blue-200/60 dark:border-gray-600 space-y-3">
          <div class="font-bold text-blue-900 dark:text-blue-200 flex items-center gap-2">
            <span>🔔 Notifications & Automation Rules</span>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 text-[11px]">
            <label class="flex items-center gap-2 cursor-pointer">
              <input type="checkbox" v-model="form.notify_email" class="rounded text-blue-600" />
              <span>✓ Email Notification</span>
            </label>

            <label class="flex items-center gap-2 cursor-pointer">
              <input type="checkbox" v-model="form.notify_push" class="rounded text-blue-600" />
              <span>✓ Push Notification</span>
            </label>

            <label class="flex items-center gap-2 cursor-pointer">
              <input type="checkbox" v-model="form.notify_announcement" class="rounded text-blue-600" />
              <span>✓ Post Course Announcement</span>
            </label>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 text-[11px] pt-1 border-t border-blue-200/50 dark:border-gray-600">
            <label class="flex items-center gap-2 cursor-pointer">
              <input type="checkbox" v-model="form.reminder_15m" class="rounded text-blue-600" />
              <span>✓ 15 min before reminder</span>
            </label>

            <label class="flex items-center gap-2 cursor-pointer">
              <input type="checkbox" v-model="form.reminder_1h" class="rounded text-blue-600" />
              <span>✓ 1 hour before reminder</span>
            </label>

            <label class="flex items-center gap-2 cursor-pointer">
              <input type="checkbox" v-model="form.auto_record" class="rounded text-blue-600" />
              <span>✓ Auto-upload recording</span>
            </label>
          </div>
        </div>
      </div>

      <!-- Modal Footer -->
      <div class="px-6 py-4 bg-slate-100 dark:bg-gray-700/80 border-t border-slate-200 dark:border-gray-700 flex flex-wrap items-center justify-between gap-3">
        <button
          type="button"
          @click="$emit('close')"
          class="px-4 py-2.5 rounded-xl bg-slate-200 dark:bg-gray-600 text-slate-700 dark:text-slate-200 font-bold hover:bg-slate-300 transition"
        >
          ↩ Cancel
        </button>

        <div class="flex items-center gap-2">
          <button
            type="button"
            @click="submitForm(true)"
            class="px-4 py-2.5 rounded-xl bg-slate-200 dark:bg-gray-600 text-slate-800 dark:text-white font-bold hover:bg-slate-300 transition"
          >
            💾 Save Draft
          </button>

          <button
            type="button"
            @click="submitForm(false)"
            :disabled="form.processing"
            class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold shadow-lg shadow-blue-500/30 transition flex items-center gap-2"
          >
            <span>🚀 Publish & Notify Students</span>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>
