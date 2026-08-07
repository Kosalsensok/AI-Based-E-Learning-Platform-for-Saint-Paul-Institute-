<script setup lang="ts">
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { useForm } from '@inertiajs/vue3'

const form = useForm({
  advance_threshold: 80,
  review_threshold: 50,
  remedial_threshold: 49,
  idle_days: 3,
  enabled: true,
})

function save() {
  form.post('/admin/ai-rules/update')
}
</script>

<template>
  <AdminLayout title="AI Recommendation Rules">
    <div class="max-w-3xl mx-auto space-y-6">
      <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 border dark:border-gray-700">
        <h2 class="text-xl font-bold mb-1 flex items-center gap-2">
          🤖 AI Learning Path & Remedial Rule Thresholds
        </h2>
        <p class="text-xs text-gray-500 mb-6">Configure automated triggers for student remedial content and next module advancement.</p>

        <form @submit.prevent="save" class="space-y-6">
          <!-- Advance Rule -->
          <div class="p-4 bg-emerald-50 dark:bg-emerald-900/20 rounded-xl border border-emerald-200 dark:border-emerald-800">
            <h3 class="font-bold text-sm text-emerald-800 dark:text-emerald-300 mb-1">🏆 Advance to Next Module Rule</h3>
            <p class="text-xs text-emerald-600 dark:text-emerald-400 mb-3">When student scores at or above this score percentage on post-tests</p>
            <div class="flex items-center gap-2">
              <label class="text-xs font-semibold text-gray-500">Minimum Score (%):</label>
              <input v-model.number="form.advance_threshold" type="number" min="0" max="100" class="border rounded p-2 text-sm w-24 bg-white dark:bg-gray-800" />
            </div>
          </div>

          <!-- Review Rule -->
          <div class="p-4 bg-amber-50 dark:bg-amber-900/20 rounded-xl border border-amber-200 dark:border-amber-800">
            <h3 class="font-bold text-sm text-amber-800 dark:text-amber-300 mb-1">🔄 Review Current Module Rule</h3>
            <p class="text-xs text-amber-600 dark:text-amber-400 mb-3">When student scores in this range percentage on practice quizzes</p>
            <div class="flex items-center gap-2 text-sm">
              <label class="text-xs font-semibold text-gray-500">Min Score (%):</label>
              <input v-model.number="form.review_threshold" type="number" class="border rounded p-2 text-sm w-24 bg-white dark:bg-gray-800" />
              <span class="text-gray-400">to</span>
              <input :value="form.advance_threshold - 1" disabled class="border rounded p-2 text-sm w-24 bg-gray-100 dark:bg-gray-700" />
            </div>
          </div>

          <!-- Remedial Rule -->
          <div class="p-4 bg-red-50 dark:bg-red-900/20 rounded-xl border border-red-200 dark:border-red-800">
            <h3 class="font-bold text-sm text-red-800 dark:text-red-300 mb-1">⚠️ Remedial Content Rule</h3>
            <p class="text-xs text-red-600 dark:text-red-400 mb-3">When student scores below this threshold percentage</p>
            <div class="flex items-center gap-2">
              <label class="text-xs font-semibold text-gray-500">Below Score (%):</label>
              <input v-model.number="form.remedial_threshold" type="number" class="border rounded p-2 text-sm w-24 bg-white dark:bg-gray-800" />
            </div>
          </div>

          <!-- Engagement Rule -->
          <div class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded-xl border border-blue-200 dark:border-blue-800">
            <h3 class="font-bold text-sm text-blue-800 dark:text-blue-300 mb-1">🔔 Re-engagement Notification Alert</h3>
            <p class="text-xs text-blue-600 dark:text-blue-400 mb-3">Send push alert if student has no learning activity for N days</p>
            <div class="flex items-center gap-2">
              <label class="text-xs font-semibold text-gray-500">Idle Days:</label>
              <input v-model.number="form.idle_days" type="number" min="1" max="30" class="border rounded p-2 text-sm w-24 bg-white dark:bg-gray-800" />
            </div>
          </div>

          <div class="flex items-center justify-between pt-4 border-t">
            <label class="flex items-center gap-2 text-sm font-semibold cursor-pointer">
              <input type="checkbox" v-model="form.enabled" class="rounded text-blue-600 w-4 h-4" />
              Enable Automated AI Recommendation Engine
            </label>

            <button
              type="submit"
              :disabled="form.processing"
              class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold text-sm transition"
            >
              Save AI Configuration
            </button>
          </div>
        </form>
      </div>
    </div>
  </AdminLayout>
</template>
