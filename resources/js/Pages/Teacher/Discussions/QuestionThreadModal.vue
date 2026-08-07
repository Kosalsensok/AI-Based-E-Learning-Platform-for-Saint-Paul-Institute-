<script setup lang="ts">
import { ref } from 'vue'

const props = defineProps<{
  show: boolean
  question: any
}>()

const emit = defineEmits<{
  (e: 'close'): void
  (e: 'openReply', question: any): void
}>()

const quickReplyText = ref('')
const helpfulCount = ref(12)
const unhelpfulCount = ref(0)

const toggleHelpful = () => {
  helpfulCount.value++
}
</script>

<template>
  <div v-if="show" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4 z-50 overflow-y-auto">
    <div class="bg-white dark:bg-gray-800 rounded-3xl max-w-2xl w-full p-6 space-y-5 shadow-2xl border border-slate-200 dark:border-gray-700 animate-in fade-in zoom-in duration-200">
      <!-- Modal Header -->
      <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-gray-700">
        <div class="flex items-center gap-2">
          <span class="text-xl">💬</span>
          <div>
            <h3 class="text-base font-black text-slate-900 dark:text-white">
              DISCUSSION THREAD — {{ question?.chapter_name }}
            </h3>
            <p class="text-xs text-slate-500 font-semibold">
              {{ question?.course_name }} · {{ question?.module_name }}
            </p>
          </div>
        </div>

        <button @click="emit('close')" class="text-slate-400 hover:text-slate-600 dark:hover:text-white font-black text-xl">
          ✕
        </button>
      </div>

      <!-- Conversation Thread Timeline -->
      <div class="space-y-4 max-h-[400px] overflow-y-auto pr-1">
        <div
          v-for="(rep, idx) in question?.replies || []"
          :key="idx"
          :class="[
            'p-4 rounded-2xl border space-y-2 text-xs transition',
            rep.role === 'teacher'
              ? 'bg-blue-50/70 border-blue-200 dark:bg-blue-950/40 dark:border-blue-900 ml-4'
              : 'bg-slate-50 border-slate-200/80 dark:bg-gray-700/60 dark:border-gray-600 mr-4'
          ]"
        >
          <div class="flex items-center justify-between font-bold">
            <div class="flex items-center gap-2">
              <span class="text-base">{{ rep.avatar }}</span>
              <span class="text-slate-900 dark:text-white">{{ rep.author }}</span>
              <span v-if="rep.role === 'teacher'" class="px-2 py-0.5 rounded-full text-[10px] font-black bg-blue-600 text-white">
                Teacher
              </span>
            </div>
            <span class="text-[11px] text-slate-400 font-normal">{{ rep.time }}</span>
          </div>

          <p class="text-slate-800 dark:text-slate-200 leading-relaxed font-medium">
            💬 "{{ rep.text }}"
          </p>

          <div v-if="rep.attachment" class="pt-1">
            <span class="px-2.5 py-1 bg-slate-200 dark:bg-gray-600 text-slate-700 dark:text-slate-200 rounded-lg text-[10px] font-bold inline-flex items-center gap-1">
              <span>📎</span> {{ rep.attachment }}
            </span>
          </div>
        </div>
      </div>

      <!-- Student View Action Bar -->
      <div class="flex flex-wrap items-center justify-between gap-3 pt-3 border-t border-slate-100 dark:border-gray-700 text-xs">
        <div class="flex items-center gap-2">
          <button
            @click="toggleHelpful"
            class="px-3 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300 font-bold rounded-xl transition flex items-center gap-1"
          >
            <span>👍 Helpful</span> <span>({{ helpfulCount }})</span>
          </button>

          <button
            class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-600 dark:bg-gray-700 dark:text-slate-300 font-semibold rounded-xl transition"
          >
            <span>👎 Not Helpful</span>
          </button>
        </div>

        <div class="flex items-center gap-2">
          <button
            @click="emit('openReply', question); emit('close')"
            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl text-xs shadow-sm transition flex items-center gap-1"
          >
            <span>✍️ Reply to Thread</span>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>
