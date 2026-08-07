<script setup lang="ts">
import { ref, watch } from 'vue'
import { useForm } from '@inertiajs/vue3'

const props = defineProps<{
  show: boolean
  courses: Array<any>
  deadlineToEdit?: any
}>()

const emit = defineEmits(['close', 'saved'])

const form = useForm({
  id: null as number | null,
  linked_type: 'quiz',
  item_title: 'Post-Test Module 1 - C Programming',
  title: 'Post-Test Module 1: C Fundamentals',
  course_id: null as number | null,
  deadline_type: 'hard',
  due_date: new Date(Date.now() + 86400000 * 2).toISOString().split('T')[0],
  due_time: '23:59',
  grace_days: 2,
  penalty_percent: 10,
  applicable_to: 'all',
  show_countdown: true,
  auto_reminder_24h: true,
  auto_reminder_1h: true,
  auto_lock: true,
  message_kh: 'សូមប្រញាប់ធ្វើ Post-Test មុនផុតកំណត់ ដើម្បីទទួលបានពិន្ទុពេញ!',
  message_en: 'Please complete Post-Test before deadline to avoid penalties.',
})

watch(() => props.deadlineToEdit, (newVal) => {
  if (newVal) {
    form.id = newVal.id
    form.linked_type = newVal.linked_type || 'quiz'
    form.title = newVal.title || ''
    form.course_id = newVal.course_id || null
    form.deadline_type = newVal.deadline_type || 'hard'
    if (newVal.due_at) {
      const d = new Date(newVal.due_at)
      form.due_date = d.toISOString().split('T')[0]
      form.due_time = d.toTimeString().substring(0, 5)
    }
    form.grace_days = newVal.grace_days ?? 0
    form.penalty_percent = newVal.penalty_percent ?? 0
    form.applicable_to = newVal.applicable_to || 'all'
    form.show_countdown = newVal.show_countdown ?? true
    form.auto_reminder_24h = newVal.auto_reminder_24h ?? true
    form.auto_reminder_1h = newVal.auto_reminder_1h ?? true
    form.auto_lock = newVal.auto_lock ?? true
    form.message_kh = newVal.message_kh || ''
    form.message_en = newVal.message_en || ''
  } else {
    form.reset()
    if (props.courses.length > 0) {
      form.course_id = props.courses[0].id
    }
  }
}, { immediate: true })

const submitForm = (sendReminderNow = false) => {
  if (form.id) {
    form.put(route('teacher.calendar.deadline.update', form.id), {
      onSuccess: () => {
        emit('saved')
        emit('close')
      }
    })
  } else {
    form.post(route('teacher.calendar.deadline.store'), {
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
      <div class="px-6 py-4 bg-gradient-to-r from-amber-600 via-orange-600 to-red-600 text-white flex items-center justify-between">
        <div class="flex items-center gap-3">
          <span class="text-2xl">⏰</span>
          <div>
            <h3 class="font-bold text-lg leading-tight">
              {{ form.id ? 'កែប្រែកាលកំណត់ / EDIT DEADLINE' : '➕ SET / EDIT DEADLINE' }}
            </h3>
            <p class="text-xs text-amber-100">កំណត់ Due Date សម្រាប់ Quiz, Assignment, Payment, Module</p>
          </div>
        </div>
        <button @click="$emit('close')" class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center text-white font-bold transition">✕</button>
      </div>

      <!-- Form Body -->
      <div class="p-6 space-y-4 text-xs text-slate-700 dark:text-slate-200 max-h-[75vh] overflow-y-auto">
        <!-- Link To Selection -->
        <div>
          <label class="block font-bold mb-1.5 text-slate-800 dark:text-gray-100">Link Deadline To (ភ្ជាប់ទៅកាន់)</label>
          <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
            <button
              type="button"
              @click="form.linked_type = 'quiz'"
              :class="form.linked_type === 'quiz' ? 'bg-red-600 text-white ring-2 ring-red-400' : 'bg-slate-100 dark:bg-gray-700 text-slate-700 dark:text-slate-300'"
              class="px-3 py-2.5 rounded-xl font-bold transition text-[11px] flex items-center justify-center gap-1"
            >
              <span>📝 Quiz</span>
            </button>

            <button
              type="button"
              @click="form.linked_type = 'assignment'"
              :class="form.linked_type === 'assignment' ? 'bg-orange-600 text-white ring-2 ring-orange-400' : 'bg-slate-100 dark:bg-gray-700 text-slate-700 dark:text-slate-300'"
              class="px-3 py-2.5 rounded-xl font-bold transition text-[11px] flex items-center justify-center gap-1"
            >
              <span>🟧 Assignment</span>
            </button>

            <button
              type="button"
              @click="form.linked_type = 'module'"
              :class="form.linked_type === 'module' ? 'bg-blue-600 text-white ring-2 ring-blue-400' : 'bg-slate-100 dark:bg-gray-700 text-slate-700 dark:text-slate-300'"
              class="px-3 py-2.5 rounded-xl font-bold transition text-[11px] flex items-center justify-center gap-1"
            >
              <span>📦 Module</span>
            </button>

            <button
              type="button"
              @click="form.linked_type = 'payment'"
              :class="form.linked_type === 'payment' ? 'bg-yellow-600 text-white ring-2 ring-yellow-400' : 'bg-slate-100 dark:bg-gray-700 text-slate-700 dark:text-slate-300'"
              class="px-3 py-2.5 rounded-xl font-bold transition text-[11px] flex items-center justify-center gap-1"
            >
              <span>🟨 Payment</span>
            </button>
          </div>
        </div>

        <!-- Select Item & Course -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
          <div>
            <label class="block font-bold mb-1">Deadline Title / Item</label>
            <input
              v-model="form.title"
              type="text"
              placeholder="e.g. Post-Test Module 1 - C Programming"
              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-gray-700 bg-slate-50 dark:bg-gray-700 font-semibold"
            />
          </div>

          <div>
            <label class="block font-bold mb-1">Course (មុខវិជ្ជា)</label>
            <select
              v-model="form.course_id"
              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-gray-700 bg-slate-50 dark:bg-gray-700 font-medium"
            >
              <option :value="null">-- Select Course --</option>
              <option v-for="c in courses" :key="c.id" :value="c.id">{{ c.title }}</option>
            </select>
          </div>
        </div>

        <!-- Deadline Type & Grace Period & Penalty -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3 p-3 bg-amber-50/60 dark:bg-gray-700/50 rounded-2xl border border-amber-200/80 dark:border-gray-700">
          <div>
            <label class="block font-bold mb-1">Deadline Type</label>
            <select
              v-model="form.deadline_type"
              class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-gray-600 bg-white dark:bg-gray-700 font-bold"
            >
              <option value="hard">🔴 Hard Deadline (Auto-lock)</option>
              <option value="soft">🟡 Soft Deadline (Late Allowed)</option>
            </select>
          </div>

          <div>
            <label class="block font-bold mb-1">Grace Period (យល់យោគ)</label>
            <select
              v-model="form.grace_days"
              class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-gray-600 bg-white dark:bg-gray-700 font-medium"
            >
              <option :value="0">No grace period</option>
              <option :value="1">Allow 1 day late</option>
              <option :value="2">Allow 2 days late</option>
              <option :value="3">Allow 3 days late</option>
            </select>
          </div>

          <div>
            <label class="block font-bold mb-1">Late Penalty (ពិន័យ)</label>
            <select
              v-model="form.penalty_percent"
              class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-gray-600 bg-white dark:bg-gray-700 font-medium"
            >
              <option :value="0">0% (No penalty)</option>
              <option :value="5">5% per day</option>
              <option :value="10">10% per day</option>
              <option :value="20">20% per day</option>
            </select>
          </div>
        </div>

        <!-- Due Date & Time -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
          <div>
            <label class="block font-bold mb-1">📅 Due Date</label>
            <input
              v-model="form.due_date"
              type="date"
              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-gray-700 bg-slate-50 dark:bg-gray-700 font-medium"
            />
          </div>

          <div>
            <label class="block font-bold mb-1">⏰ Due Time</label>
            <input
              v-model="form.due_time"
              type="time"
              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-gray-700 bg-slate-50 dark:bg-gray-700 font-medium"
            />
          </div>
        </div>

        <!-- Applicable To -->
        <div>
          <label class="block font-bold mb-1">Applicable To (អនុវត្តលើសិស្ស)</label>
          <select
            v-model="form.applicable_to"
            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-gray-700 bg-slate-50 dark:bg-gray-700 font-medium"
          >
            <option value="all">👥 All Students (សិស្សទាំងអស់)</option>
            <option value="instructor_led_only">📖 Only Instructor-Led Students</option>
            <option value="unsubmitted_only">⏳ Only Unsubmitted Students</option>
          </select>
        </div>

        <!-- Checkboxes & Rules -->
        <div class="p-4 bg-slate-50 dark:bg-gray-700/60 rounded-2xl border border-slate-200 dark:border-gray-600 space-y-2">
          <label class="flex items-center gap-2 cursor-pointer font-semibold">
            <input type="checkbox" v-model="form.show_countdown" class="rounded text-amber-600" />
            <span>[✓] Show urgency countdown timer to students (⏳ Countdown Timer)</span>
          </label>

          <div class="flex flex-wrap gap-4 pt-1">
            <label class="flex items-center gap-2 cursor-pointer">
              <input type="checkbox" v-model="form.auto_reminder_24h" class="rounded text-amber-600" />
              <span>✓ Auto-reminder 24h before</span>
            </label>

            <label class="flex items-center gap-2 cursor-pointer">
              <input type="checkbox" v-model="form.auto_reminder_1h" class="rounded text-amber-600" />
              <span>✓ Auto-reminder 1h before</span>
            </label>

            <label class="flex items-center gap-2 cursor-pointer">
              <input type="checkbox" v-model="form.auto_lock" class="rounded text-amber-600" />
              <span>✓ Auto-lock submission after deadline</span>
            </label>
          </div>
        </div>

        <!-- Student Messages -->
        <div class="space-y-3">
          <div>
            <label class="block font-bold mb-1">Message to Students (KH)</label>
            <textarea
              v-model="form.message_kh"
              rows="2"
              placeholder="សូមប្រញាប់ធ្វើ Post-Test មុនផុតកំណត់..."
              class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-gray-700 bg-slate-50 dark:bg-gray-700"
            ></textarea>
          </div>

          <div>
            <label class="block font-bold mb-1">Message to Students (EN)</label>
            <textarea
              v-model="form.message_en"
              rows="2"
              placeholder="Please complete Post-Test before deadline..."
              class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-gray-700 bg-slate-50 dark:bg-gray-700"
            ></textarea>
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
            class="px-4 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-bold transition flex items-center gap-1.5"
          >
            <span>📢 Save & Send Reminder Now</span>
          </button>

          <button
            type="button"
            @click="submitForm(false)"
            :disabled="form.processing"
            class="px-5 py-2.5 rounded-xl bg-orange-600 hover:bg-orange-700 text-white font-bold shadow-lg shadow-orange-500/30 transition"
          >
            <span>💾 Save Deadline</span>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>
