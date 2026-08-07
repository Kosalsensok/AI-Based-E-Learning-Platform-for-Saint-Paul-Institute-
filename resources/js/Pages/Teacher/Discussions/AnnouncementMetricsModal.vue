<script setup lang="ts">
const props = defineProps<{
  show: boolean
  announcement: any
}>()

const emit = defineEmits<{
  (e: 'close'): void
}>()
</script>

<template>
  <div v-if="show" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4 z-50 overflow-y-auto">
    <div class="bg-white dark:bg-gray-800 rounded-3xl max-w-2xl w-full p-6 space-y-5 shadow-2xl border border-slate-200 dark:border-gray-700 animate-in fade-in zoom-in duration-200">
      <!-- Modal Header -->
      <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-gray-700">
        <div class="flex items-center gap-2">
          <span class="text-xl">📊</span>
          <div>
            <h3 class="text-base font-black text-slate-900 dark:text-white">
              ANNOUNCEMENT ANALYTICS — {{ announcement?.title_kh || announcement?.title_en }}
            </h3>
            <p class="text-xs text-slate-500 font-semibold">
              Track engagement, channel reach, and conversion impact.
            </p>
          </div>
        </div>

        <button @click="emit('close')" class="text-slate-400 hover:text-slate-600 dark:hover:text-white font-black text-xl">
          ✕
        </button>
      </div>

      <!-- Key Metrics Row -->
      <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <div class="p-3.5 bg-blue-50 dark:bg-blue-900/30 rounded-2xl border border-blue-200/80 dark:border-blue-900/50">
          <div class="text-[11px] font-bold text-slate-500">Total Views</div>
          <div class="text-xl font-black text-blue-700 dark:text-blue-300">
            {{ announcement?.analytics?.views || 320 }} / {{ announcement?.analytics?.total_students || 520 }}
          </div>
          <div class="text-[10px] font-extrabold text-blue-600">
            {{ announcement?.analytics?.views_pct || 62 }}% Engagement
          </div>
        </div>

        <div class="p-3.5 bg-purple-50 dark:bg-purple-900/30 rounded-2xl border border-purple-200/80 dark:border-purple-900/50">
          <div class="text-[11px] font-bold text-slate-500">Action Clicks</div>
          <div class="text-xl font-black text-purple-700 dark:text-purple-300">
            👈 {{ announcement?.analytics?.clicks || 120 }}
          </div>
          <div class="text-[10px] font-extrabold text-purple-600">
            Direct conversion
          </div>
        </div>

        <div class="p-3.5 bg-emerald-50 dark:bg-emerald-900/30 rounded-2xl border border-emerald-200/80 dark:border-emerald-900/50">
          <div class="text-[11px] font-bold text-slate-500">Email Open</div>
          <div class="text-xl font-black text-emerald-700 dark:text-emerald-300">
            📧 {{ announcement?.analytics?.email_open || 58 }}%
          </div>
          <div class="text-[10px] font-extrabold text-emerald-600">
            SLA Standard
          </div>
        </div>

        <div class="p-3.5 bg-amber-50 dark:bg-amber-900/30 rounded-2xl border border-amber-200/80 dark:border-amber-900/50">
          <div class="text-[11px] font-bold text-slate-500">Push Open</div>
          <div class="text-xl font-black text-amber-700 dark:text-amber-300">
            🔔 {{ announcement?.analytics?.push_open || 64 }}%
          </div>
          <div class="text-[10px] font-extrabold text-amber-600">
            Mobile App
          </div>
        </div>
      </div>

      <!-- Breakdown By Student Type -->
      <div class="p-4 bg-slate-50 dark:bg-gray-700/50 rounded-2xl border border-slate-200/80 dark:border-gray-600 space-y-3 text-xs">
        <div class="font-extrabold text-slate-900 dark:text-white">By Student Type Engagement</div>

        <div class="space-y-2">
          <div class="flex items-center justify-between p-3 bg-white dark:bg-gray-800 rounded-xl border border-slate-200/60 dark:border-gray-700">
            <span class="font-bold text-slate-800 dark:text-slate-200">📖 Teacher-Led (Instructor)</span>
            <div class="flex items-center gap-4 font-extrabold">
              <span class="text-emerald-600">✅ {{ announcement?.analytics?.teacher_led_views || 98 }}% viewed</span>
              <span class="text-blue-600">🔘 {{ announcement?.analytics?.teacher_led_clicks || 72 }}% clicked</span>
            </div>
          </div>

          <div class="flex items-center justify-between p-3 bg-white dark:bg-gray-800 rounded-xl border border-slate-200/60 dark:border-gray-700">
            <span class="font-bold text-slate-800 dark:text-slate-200">💻 Self-Paced</span>
            <div class="flex items-center gap-4 font-extrabold">
              <span class="text-emerald-600">✅ {{ announcement?.analytics?.self_paced_views || 85 }}% viewed</span>
              <span class="text-blue-600">🔘 {{ announcement?.analytics?.self_paced_clicks || 45 }}% clicked</span>
            </div>
          </div>
        </div>
      </div>

      <!-- 🧠 AI INSIGHT BOX -->
      <div class="p-4 bg-gradient-to-r from-indigo-900 via-purple-900 to-slate-900 text-white rounded-2xl shadow-md space-y-1.5 text-xs">
        <div class="font-black text-amber-300 flex items-center gap-1.5">
          <span>🧠 AI INSIGHT</span>
        </div>
        <p class="text-slate-200 leading-relaxed font-medium">
          "{{ announcement?.analytics?.ai_insight || 'Students who clicked Start Learning within 2 hours completed Module 3 30% faster than those who delayed.' }}"
        </p>
      </div>

      <!-- Footer -->
      <div class="flex justify-end pt-1">
        <button
          @click="emit('close')"
          class="px-5 py-2.5 bg-slate-800 hover:bg-slate-900 text-white font-bold rounded-xl text-xs shadow transition"
        >
          Close Analytics
        </button>
      </div>
    </div>
  </div>
</template>
