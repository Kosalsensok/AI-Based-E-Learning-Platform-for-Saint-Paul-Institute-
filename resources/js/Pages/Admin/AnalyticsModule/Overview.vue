<script setup lang="ts">
import { ref, computed } from 'vue'
import type { OverviewMetrics, InsightItem } from './types'

const props = defineProps<{
  metrics: OverviewMetrics
}>()

const emit = defineEmits<{
  (e: 'exportReport'): void
  (e: 'refresh'): void
  (e: 'viewFullAiReport'): void
  (e: 'navigateTab', tab: string): void
}>()

// Streamlined date scope filter state
const selectedPeriod = ref('this_month')

// Formatted insights with actionable targets
const formattedInsights = computed(() => {
  if (!props.metrics.insights) return []
  return props.metrics.insights.map((item, idx) => {
    if (typeof item === 'string') {
      let actionText = 'Take Action'
      let targetTab = 'overview'
      if (item.includes('Grammar') || item.includes('course')) {
        actionText = 'View Course'
        targetTab = 'courses'
      } else if (item.includes('IT & Networking') || item.includes('students')) {
        actionText = 'View Major'
        targetTab = 'students'
      } else if (item.includes('Module 3') || item.includes('pass rate')) {
        actionText = 'Review Module'
        targetTab = 'quizzes'
      } else if (item.includes('Peak learning time')) {
        actionText = 'View Schedule'
        targetTab = 'overview'
      } else if (item.includes('teachers')) {
        actionText = 'View Teachers'
        targetTab = 'teachers'
      }
      return { id: String(idx), text: item, actionText, targetTab }
    }
    return {
      id: item.id || String(idx),
      text: item.text,
      actionText: item.actionText || 'Take Action',
      targetTab: item.targetTab || 'overview'
    }
  })
})

function handleInsightAction(item: { text: string; actionText: string; targetTab?: string }) {
  if (item.targetTab) {
    emit('navigateTab', item.targetTab)
  } else {
    emit('viewFullAiReport')
  }
}
</script>

<template>
  <div class="space-y-6 text-xs font-sans">
    <!-- Top Filter & Action Bar (Streamlined & Reduced Visual Noise) -->
    <div class="bg-[#0d1222]/90 border border-slate-700/60 p-3.5 rounded-2xl flex flex-col sm:flex-row items-center justify-between gap-3 shadow-xl">
      <div class="flex flex-wrap items-center gap-2.5 w-full sm:w-auto">
        <!-- Single Compact Period Scope Selector -->
        <div class="flex items-center gap-2">
          <label class="text-[11px] text-slate-400 font-semibold flex items-center gap-1.5 shrink-0">
            <svg class="w-3.5 h-3.5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            <span>Period Scope:</span>
          </label>
          <select 
            v-model="selectedPeriod" 
            class="bg-[#121827] text-slate-200 border border-slate-700/80 rounded-xl px-3 py-1.5 text-xs font-medium focus:border-purple-500 focus:outline-none cursor-pointer"
          >
            <option value="this_month">June 2025 vs May 2025 (This Month)</option>
            <option value="last_30_days">Last 30 Days (Custom Scope)</option>
            <option value="this_quarter">Q2 2025 vs Q1 2025 (This Quarter)</option>
            <option value="this_year">2025 YTD vs 2024 (This Year)</option>
          </select>
        </div>

        <!-- Compact Icon Refresh Button (🔄) -->
        <button 
          @click="emit('refresh')" 
          title="Refresh Data"
          class="p-2 bg-slate-800 hover:bg-slate-700/90 border border-slate-700 text-slate-300 hover:text-white rounded-xl transition-all active:scale-95 flex items-center justify-center shrink-0"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
          </svg>
        </button>
      </div>

      <!-- Single Primary Action Button -->
      <button 
        @click="emit('exportReport')" 
        class="w-full sm:w-auto px-4 py-2 bg-purple-600 hover:bg-purple-500 text-white rounded-xl text-xs font-bold shadow-lg shadow-purple-600/30 flex items-center justify-center gap-2 transition-all active:scale-95 ring-1 ring-purple-400/50 shrink-0"
      >
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
        </svg>
        <span>Export Overview Report</span>
      </button>
    </div>

    <!-- 6 Key Metrics Cards (Standardized Design System & Visual Hierarchy) -->
    <div class="space-y-3">
      <div class="flex items-center justify-between">
        <h3 class="font-black text-sm text-white uppercase tracking-wide flex items-center gap-2">
          <span>🎯</span> KEY METRICS THIS MONTH (VS LAST MONTH)
        </h3>
        <span class="text-[11px] text-slate-400 font-medium">Updated 5m ago</span>
      </div>

      <div class="grid grid-cols-2 lg:grid-cols-3 gap-4">
        <!-- 1. Student Engagement -->
        <div class="bg-[#0d1222]/95 border border-slate-700/60 p-4.5 rounded-2xl shadow-xl space-y-2.5 relative overflow-hidden group hover:border-purple-500/50 transition-all">
          <span class="text-slate-400 text-[11px] font-semibold flex items-center gap-1.5">
            <span>👨‍🎓</span> Student Engagement
          </span>
          <div class="flex items-baseline justify-between">
            <span class="text-2xl font-black text-white">{{ metrics.student_engagement.value }}%</span>
            <span class="text-xs font-bold text-emerald-400">↑ +{{ metrics.student_engagement.change }}%</span>
          </div>
          <div class="flex items-center justify-between pt-1 border-t border-slate-800/60">
            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-emerald-500/15 text-emerald-300 text-[10px] font-bold border border-emerald-500/30">
              <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
              {{ metrics.student_engagement.status }}
            </span>
            <span class="text-[10px] text-slate-500">Active Rate</span>
          </div>
        </div>

        <!-- 2. Teacher Performance -->
        <div class="bg-[#0d1222]/95 border border-slate-700/60 p-4.5 rounded-2xl shadow-xl space-y-2.5 relative overflow-hidden group hover:border-purple-500/50 transition-all">
          <span class="text-slate-400 text-[11px] font-semibold flex items-center gap-1.5">
            <span>👨‍🏫</span> Teacher Performance
          </span>
          <div class="flex items-baseline justify-between">
            <span class="text-2xl font-black text-white">{{ metrics.teacher_performance.value }}%</span>
            <span class="text-xs font-bold text-emerald-400">↑ +{{ metrics.teacher_performance.change }}%</span>
          </div>
          <div class="flex items-center justify-between pt-1 border-t border-slate-800/60">
            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-emerald-500/15 text-emerald-300 text-[10px] font-bold border border-emerald-500/30">
              <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
              {{ metrics.teacher_performance.status }}
            </span>
            <span class="text-[10px] text-slate-500">Avg Rating</span>
          </div>
        </div>

        <!-- 3. Course Completion -->
        <div class="bg-[#0d1222]/95 border border-slate-700/60 p-4.5 rounded-2xl shadow-xl space-y-2.5 relative overflow-hidden group hover:border-purple-500/50 transition-all">
          <span class="text-slate-400 text-[11px] font-semibold flex items-center gap-1.5">
            <span>📚</span> Course Completion
          </span>
          <div class="flex items-baseline justify-between">
            <span class="text-2xl font-black text-white">{{ metrics.course_completion.value }}%</span>
            <span class="text-xs font-bold text-emerald-400">↑ +{{ metrics.course_completion.change }}%</span>
          </div>
          <div class="flex items-center justify-between pt-1 border-t border-slate-800/60">
            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-amber-500/15 text-amber-300 text-[10px] font-bold border border-amber-500/30">
              <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>
              {{ metrics.course_completion.status }}
            </span>
            <span class="text-[10px] text-slate-500">Target: 80%</span>
          </div>
        </div>

        <!-- 4. Quiz Pass Rate -->
        <div class="bg-[#0d1222]/95 border border-slate-700/60 p-4.5 rounded-2xl shadow-xl space-y-2.5 relative overflow-hidden group hover:border-purple-500/50 transition-all">
          <span class="text-slate-400 text-[11px] font-semibold flex items-center gap-1.5">
            <span>📝</span> Quiz Pass Rate
          </span>
          <div class="flex items-baseline justify-between">
            <span class="text-2xl font-black text-purple-300">{{ metrics.quiz_pass_rate.value }}%</span>
            <span class="text-xs font-bold text-emerald-400">↑ +{{ metrics.quiz_pass_rate.change }}%</span>
          </div>
          <div class="flex items-center justify-between pt-1 border-t border-slate-800/60">
            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-emerald-500/15 text-emerald-300 text-[10px] font-bold border border-emerald-500/30">
              <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
              {{ metrics.quiz_pass_rate.status }}
            </span>
            <span class="text-[10px] text-slate-500">Passing 70%</span>
          </div>
        </div>

        <!-- 5. Revenue Growth -->
        <div class="bg-[#0d1222]/95 border border-slate-700/60 p-4.5 rounded-2xl shadow-xl space-y-2.5 relative overflow-hidden group hover:border-purple-500/50 transition-all">
          <span class="text-slate-400 text-[11px] font-semibold flex items-center gap-1.5">
            <span>💳</span> Revenue Growth
          </span>
          <div class="flex items-baseline justify-between">
            <span class="text-2xl font-black text-emerald-400">{{ metrics.revenue_growth.value }}</span>
            <span class="text-xs font-bold text-emerald-400">↑ +{{ metrics.revenue_growth.change }}%</span>
          </div>
          <div class="flex items-center justify-between pt-1 border-t border-slate-800/60">
            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-emerald-500/15 text-emerald-300 text-[10px] font-bold border border-emerald-500/30">
              <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
              {{ metrics.revenue_growth.status }}
            </span>
            <span class="text-[10px] text-slate-500">MoM Net</span>
          </div>
        </div>

        <!-- 6. Certs Issued -->
        <div class="bg-[#0d1222]/95 border border-slate-700/60 p-4.5 rounded-2xl shadow-xl space-y-2.5 relative overflow-hidden group hover:border-purple-500/50 transition-all">
          <span class="text-slate-400 text-[11px] font-semibold flex items-center gap-1.5">
            <span>🎓</span> Certs Issued
          </span>
          <div class="flex items-baseline justify-between">
            <span class="text-2xl font-black text-cyan-300">{{ metrics.cert_issued.value }}</span>
            <span class="text-xs font-bold text-emerald-400">↑ +{{ metrics.cert_issued.change }}%</span>
          </div>
          <div class="flex items-center justify-between pt-1 border-t border-slate-800/60">
            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-cyan-500/15 text-cyan-300 text-[10px] font-bold border border-cyan-500/30">
              <span class="w-1.5 h-1.5 rounded-full bg-cyan-400 animate-pulse"></span>
              {{ metrics.cert_issued.status }}
            </span>
            <span class="text-[10px] text-slate-500">+{{ metrics.cert_issued.count || 18 }} this mo</span>
          </div>
        </div>
      </div>
    </div>

    <!-- 6-Month Trend Overview (Dynamic SVG Sparklines Visual Trajectory) -->
    <div class="bg-[#0d1222]/95 border border-slate-700/60 rounded-2xl p-5 shadow-2xl space-y-4">
      <h3 class="font-black text-sm text-white uppercase tracking-wide flex items-center justify-between border-b border-slate-700/60 pb-2.5">
        <span class="flex items-center gap-2">
          <span>📊</span> TREND OVERVIEW (6 MONTHS: JAN - JUN)
        </span>
        <span class="text-slate-400 font-mono text-xs">Sparkline Data Flow</span>
      </h3>

      <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <!-- 1. Enrollment Growth -->
        <div class="bg-[#121827] p-4 rounded-xl border border-slate-700/80 hover:border-emerald-500/40 text-center space-y-2 group transition-all">
          <span class="text-slate-400 text-xs font-bold block">Enrollment Growth</span>
          <!-- Dynamic Sparkline SVG -->
          <div class="h-10 w-full px-2 py-1">
            <svg class="w-full h-full" viewBox="0 0 100 30" preserveAspectRatio="none">
              <defs>
                <linearGradient id="sparkGradEnroll" x1="0" y1="0" x2="0" y2="1">
                  <stop offset="0%" stop-color="#10B981" stop-opacity="0.35"/>
                  <stop offset="100%" stop-color="#10B981" stop-opacity="0"/>
                </linearGradient>
              </defs>
              <path d="M 0,26 Q 20,22 40,24 T 80,12 T 100,4 L 100,30 L 0,30 Z" fill="url(#sparkGradEnroll)" />
              <path d="M 0,26 Q 20,22 40,24 T 80,12 T 100,4" fill="none" stroke="#10B981" stroke-width="2.5" stroke-linecap="round" />
              <circle cx="100" cy="4" r="3" fill="#34D399" />
            </svg>
          </div>
          <span class="text-xl font-black text-emerald-400 block">+{{ metrics.trends.enrollment }}%</span>
          <span class="text-[10px] text-slate-500 block">Jan - Jun Trajectory</span>
        </div>

        <!-- 2. Revenue Growth -->
        <div class="bg-[#121827] p-4 rounded-xl border border-slate-700/80 hover:border-emerald-500/40 text-center space-y-2 group transition-all">
          <span class="text-slate-400 text-xs font-bold block">Revenue Growth</span>
          <!-- Dynamic Sparkline SVG -->
          <div class="h-10 w-full px-2 py-1">
            <svg class="w-full h-full" viewBox="0 0 100 30" preserveAspectRatio="none">
              <defs>
                <linearGradient id="sparkGradRev" x1="0" y1="0" x2="0" y2="1">
                  <stop offset="0%" stop-color="#10B981" stop-opacity="0.35"/>
                  <stop offset="100%" stop-color="#10B981" stop-opacity="0"/>
                </linearGradient>
              </defs>
              <path d="M 0,28 Q 25,24 50,16 T 80,8 T 100,2 L 100,30 L 0,30 Z" fill="url(#sparkGradRev)" />
              <path d="M 0,28 Q 25,24 50,16 T 80,8 T 100,2" fill="none" stroke="#10B981" stroke-width="2.5" stroke-linecap="round" />
              <circle cx="100" cy="2" r="3" fill="#34D399" />
            </svg>
          </div>
          <span class="text-xl font-black text-emerald-400 block">+{{ metrics.trends.revenue }}%</span>
          <span class="text-[10px] text-slate-500 block">Jan - Jun Trajectory</span>
        </div>

        <!-- 3. Completion Rate -->
        <div class="bg-[#121827] p-4 rounded-xl border border-slate-700/80 hover:border-purple-500/40 text-center space-y-2 group transition-all">
          <span class="text-slate-400 text-xs font-bold block">Completion Rate</span>
          <!-- Dynamic Sparkline SVG -->
          <div class="h-10 w-full px-2 py-1">
            <svg class="w-full h-full" viewBox="0 0 100 30" preserveAspectRatio="none">
              <defs>
                <linearGradient id="sparkGradComp" x1="0" y1="0" x2="0" y2="1">
                  <stop offset="0%" stop-color="#A855F7" stop-opacity="0.35"/>
                  <stop offset="100%" stop-color="#A855F7" stop-opacity="0"/>
                </linearGradient>
              </defs>
              <path d="M 0,25 Q 25,23 50,18 T 80,14 T 100,6 L 100,30 L 0,30 Z" fill="url(#sparkGradComp)" />
              <path d="M 0,25 Q 25,23 50,18 T 80,14 T 100,6" fill="none" stroke="#A855F7" stroke-width="2.5" stroke-linecap="round" />
              <circle cx="100" cy="6" r="3" fill="#C084FC" />
            </svg>
          </div>
          <span class="text-xl font-black text-purple-300 block">+{{ metrics.trends.completion }}%</span>
          <span class="text-[10px] text-slate-500 block">Jan - Jun Trajectory</span>
        </div>

        <!-- 4. Pass Rate Improvement -->
        <div class="bg-[#121827] p-4 rounded-xl border border-slate-700/80 hover:border-cyan-500/40 text-center space-y-2 group transition-all">
          <span class="text-slate-400 text-xs font-bold block">Pass Rate Improvement</span>
          <!-- Dynamic Sparkline SVG -->
          <div class="h-10 w-full px-2 py-1">
            <svg class="w-full h-full" viewBox="0 0 100 30" preserveAspectRatio="none">
              <defs>
                <linearGradient id="sparkGradPass" x1="0" y1="0" x2="0" y2="1">
                  <stop offset="0%" stop-color="#06B6D4" stop-opacity="0.35"/>
                  <stop offset="100%" stop-color="#06B6D4" stop-opacity="0"/>
                </linearGradient>
              </defs>
              <path d="M 0,24 Q 25,22 50,18 T 80,13 T 100,7 L 100,30 L 0,30 Z" fill="url(#sparkGradPass)" />
              <path d="M 0,24 Q 25,22 50,18 T 80,13 T 100,7" fill="none" stroke="#06B6D4" stroke-width="2.5" stroke-linecap="round" />
              <circle cx="100" cy="7" r="3" fill="#22D3EE" />
            </svg>
          </div>
          <span class="text-xl font-black text-cyan-300 block">+{{ metrics.trends.pass_rate }}%</span>
          <span class="text-[10px] text-slate-500 block">Jan - Jun Trajectory</span>
        </div>
      </div>
    </div>

    <!-- AI Insights Section (Actionable Interactive UX) -->
    <div class="bg-gradient-to-r from-purple-950/60 via-indigo-950/60 to-slate-900 border border-purple-500/50 p-5 rounded-2xl shadow-2xl space-y-4">
      <div class="flex items-center justify-between border-b border-purple-500/30 pb-3">
        <h3 class="font-black text-sm text-purple-300 uppercase tracking-wide flex items-center gap-2">
          <span>🧠</span> AI INSIGHTS THIS MONTH
        </h3>
        <button @click="emit('viewFullAiReport')" class="text-xs text-purple-300 hover:text-white font-bold flex items-center gap-1 transition-colors">
          <span>View Full AI Report →</span>
        </button>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
        <div 
          v-for="insight in formattedInsights" 
          :key="insight.id" 
          class="p-3.5 bg-[#121827]/90 rounded-xl border border-purple-500/30 hover:border-purple-500/60 flex flex-col sm:flex-row sm:items-center justify-between gap-3 transition-all group shadow-sm"
        >
          <div class="flex items-start gap-2.5">
            <span class="text-base p-1.5 rounded-lg bg-purple-500/10 border border-purple-500/20 text-purple-300 shrink-0">💡</span>
            <p class="text-slate-200 font-medium text-xs leading-relaxed self-center">{{ insight.text }}</p>
          </div>

          <button 
            @click="handleInsightAction(insight)" 
            class="shrink-0 px-3 py-1.5 bg-purple-600/20 hover:bg-purple-600 border border-purple-500/40 text-purple-200 hover:text-white rounded-lg text-[11px] font-bold transition-all flex items-center gap-1.5 shadow-sm active:scale-95"
          >
            <span>{{ insight.actionText }}</span>
            <svg class="w-3.5 h-3.5 transition-transform group-hover:translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
            </svg>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>
