<script setup lang="ts">
import { ref, computed } from 'vue'

const props = defineProps<{
  pricingList: any[]
  discountCodes: any[]
  stats: any
  majors: any[]
  teachers: any[]
}>()

const emit = defineEmits(['open-modal', 'edit-pricing', 'open-discount-modal'])

const search = ref('')
const majorFilter = ref('All')
const teacherFilter = ref('All')
const typeFilter = ref('All')
const viewMode = ref<'table' | 'card'>('table')

const filteredPricing = computed(() => {
  return props.pricingList.filter(p => {
    const matchesSearch = !search.value || 
      p.course.toLowerCase().includes(search.value.toLowerCase()) ||
      p.teacher.toLowerCase().includes(search.value.toLowerCase())
    const matchesTeacher = teacherFilter.value === 'All' || p.teacher === teacherFilter.value
    const matchesType = typeFilter.value === 'All' || 
      (typeFilter.value === 'Free' && p.is_free) || 
      (typeFilter.value === 'Paid' && !p.is_free)
    return matchesSearch && matchesTeacher && matchesType
  })
})

// Clean usage count helper (Option B: returns pure usage count to avoid duplicating the limit value)
const formatUsageCount = (dc: any) => {
  if (!dc.usage) return '0'
  if (typeof dc.usage === 'string' && dc.usage.includes('/')) {
    return dc.usage.split('/')[0]
  }
  return dc.usage
}

// Quick Copy Code Handler
const copiedCode = ref<string | null>(null)
const copyCode = (code: string) => {
  if (navigator.clipboard) {
    navigator.clipboard.writeText(code)
  }
  copiedCode.value = code
  setTimeout(() => {
    if (copiedCode.value === code) copiedCode.value = null
  }, 2000)
}
</script>

<template>
  <div class="space-y-6 text-slate-100 font-sans">
    <!-- Toolbar Controls -->
    <div class="bg-slate-800/90 p-5 rounded-2xl border border-slate-700/80 shadow-xl space-y-4">
      <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div>
          <h2 class="text-base font-extrabold text-white flex items-center gap-2">
            <span class="p-1.5 bg-indigo-500/10 border border-indigo-500/20 rounded-lg text-indigo-400 inline-flex items-center justify-center">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
              </svg>
            </span>
            <span>COURSE PRICING GRID</span>
          </h2>
          <p class="text-xs text-slate-200 font-medium mt-0.5">Manage course pricing tiers, custom teacher pricing, and revenue share splits</p>
        </div>

        <div class="flex flex-wrap items-center gap-2.5">
          <!-- View Mode Toggle -->
          <div class="bg-slate-950 p-1 rounded-xl flex items-center gap-1 text-xs border border-slate-700">
            <button 
              @click="viewMode = 'table'"
              class="px-3 py-1.5 rounded-lg font-bold transition cursor-pointer flex items-center gap-1.5"
              :class="viewMode === 'table' ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-400 hover:text-white'"
            >
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
              </svg>
              <span>Table</span>
            </button>
            <button 
              @click="viewMode = 'card'"
              class="px-3 py-1.5 rounded-lg font-bold transition cursor-pointer flex items-center gap-1.5"
              :class="viewMode === 'card' ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-400 hover:text-white'"
            >
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
              </svg>
              <span>Card</span>
            </button>
          </div>

          <!-- Single Set Price Primary Action -->
          <button @click="$emit('open-modal')" class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs rounded-xl shadow-lg shadow-indigo-600/30 transition cursor-pointer flex items-center gap-1.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            <span>Set Price</span>
          </button>
        </div>
      </div>

      <!-- Filter Controls Row -->
      <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3 pt-2 border-t border-slate-700/60 text-xs">
        <div class="relative">
          <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
          </span>
          <input 
            v-model="search" 
            type="text" 
            placeholder="Search Course / Teacher..." 
            class="w-full pl-9 pr-3 py-2 bg-slate-950 border border-slate-700 rounded-xl focus:ring-2 focus:ring-indigo-500 text-white placeholder:text-slate-400 focus:outline-none transition-all" 
          />
        </div>
        <select v-model="majorFilter" class="p-2 bg-slate-950 border border-slate-700 rounded-xl text-slate-200 focus:outline-none focus:border-indigo-500">
          <option value="All">Major: All</option>
          <option v-for="m in majors" :key="m.id" :value="m.name">{{ m.name }}</option>
        </select>
        <select v-model="teacherFilter" class="p-2 bg-slate-950 border border-slate-700 rounded-xl text-slate-200 focus:outline-none focus:border-indigo-500">
          <option value="All">Teacher: All</option>
          <option v-for="t in teachers" :key="t.id" :value="t.name">{{ t.name }}</option>
        </select>
        <select v-model="typeFilter" class="p-2 bg-slate-950 border border-slate-700 rounded-xl text-slate-200 focus:outline-none focus:border-indigo-500">
          <option value="All">Pricing Type: All</option>
          <option value="Paid">Paid Only</option>
          <option value="Free">Free Only</option>
        </select>
      </div>
    </div>

    <!-- TABLE VIEW -->
    <div v-if="viewMode === 'table'" class="bg-slate-800/90 rounded-2xl border border-slate-700/80 shadow-xl overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-left text-xs text-slate-200">
          <thead class="bg-slate-900/90 border-b border-slate-700 text-slate-200 uppercase font-bold text-xs">
            <tr>
              <th class="p-3.5">Course</th>
              <th class="p-3.5">Teacher</th>
              <th class="p-3.5 text-right">Base Price</th>
              <th class="p-3.5 text-right">Discount</th>
              <th class="p-3.5 text-right">Final</th>
              <th class="p-3.5 text-center">Revenue Split</th>
              <th class="p-3.5 text-center">Currency</th>
              <th class="p-3.5 text-center">Status</th>
              <th class="p-3.5 text-right">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-700/60 bg-slate-900/40">
            <tr v-for="item in filteredPricing" :key="item.id" class="hover:bg-slate-700/50 transition">
              <td class="p-3.5 font-bold text-white text-xs">{{ item.course }}</td>
              <td class="p-3.5 font-medium text-slate-200 text-xs">{{ item.teacher }}</td>
              <td class="p-3.5 text-right font-semibold text-slate-200 font-mono text-xs">
                <span v-if="item.is_free" class="text-purple-400 font-bold font-sans">Free</span>
                <span v-else>${{ item.base_price.toFixed(2) }}</span>
              </td>
              <td class="p-3.5 text-right text-slate-300 font-medium text-xs">{{ item.discount }}</td>
              <td class="p-3.5 text-right font-black font-mono text-xs">
                <span v-if="item.is_free" class="text-purple-300 font-sans">$0</span>
                <span v-else class="text-emerald-400">${{ item.final_price.toFixed(2) }}</span>
              </td>
              <td class="p-3.5 text-center font-extrabold text-indigo-300 font-mono text-xs">{{ item.revenue_split }}</td>
              <td class="p-3.5 text-center font-mono text-slate-300 text-xs">{{ item.currency }}</td>
              <td class="p-3.5 text-center">
                <span class="px-2.5 py-1 rounded-full text-[11px] font-extrabold bg-emerald-950 text-emerald-300 border border-emerald-700/80 inline-flex items-center gap-1.5">
                  <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span> {{ item.status }}
                </span>
              </td>
              <td class="p-3.5 text-right">
                <div class="flex items-center justify-end gap-2">
                  <button @click="$emit('edit-pricing', item)" class="p-2 bg-slate-900 hover:bg-indigo-950 text-indigo-300 hover:text-white rounded-xl border border-slate-700/80 transition cursor-pointer" title="Edit Price">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                  </button>
                  <button @click="$emit('open-discount-modal')" class="p-2 bg-slate-900 hover:bg-purple-950 text-purple-300 hover:text-white rounded-xl border border-slate-700/80 transition cursor-pointer" title="Discount Code">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                    </svg>
                  </button>
                  <button class="p-2 bg-slate-900 hover:bg-emerald-950 text-emerald-300 hover:text-white rounded-xl border border-slate-700/80 transition cursor-pointer" title="Analytics Stats">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- CARD VIEW -->
    <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
      <div v-for="item in filteredPricing" :key="item.id" class="bg-slate-800/90 p-5 rounded-2xl border border-slate-700/80 shadow-xl hover:border-slate-600 transition flex flex-col justify-between space-y-4">
        <div>
          <div class="flex items-center justify-between gap-2">
            <div class="flex items-center gap-2 flex-wrap">
              <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-950 text-emerald-300 border border-emerald-700/80 inline-flex items-center gap-1">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span> {{ item.status }}
              </span>
              <span class="text-[11px] font-semibold text-slate-300 bg-slate-950 px-2 py-0.5 rounded-md border border-slate-800/80" title="Revenue Share Split (Teacher/Platform)">
                Split: <strong class="text-indigo-300 font-mono">{{ item.revenue_split }}</strong>
              </span>
            </div>

            <!-- Sleek Edit Pencil Vector Icon Button -->
            <button 
              @click="$emit('edit-pricing', item)" 
              class="w-8 h-8 rounded-xl bg-slate-900/90 hover:bg-indigo-600 text-slate-300 hover:text-white border border-slate-700/80 transition flex items-center justify-center shrink-0 cursor-pointer shadow-sm"
              title="Edit Price"
            >
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
              </svg>
            </button>
          </div>
          <h3 class="text-base font-bold text-white mt-3">{{ item.course }}</h3>
          <p class="text-xs text-slate-300 mt-0.5">Teacher: {{ item.teacher }}</p>
        </div>

        <div class="bg-slate-950 p-3 rounded-xl border border-slate-700/60 flex items-center justify-between text-xs font-mono">
          <div>
            <span class="text-slate-400 block text-[10px] font-sans">Base / Discount</span>
            <span class="text-slate-200">${{ item.base_price }} / {{ item.discount }}</span>
          </div>
          <div class="text-right">
            <span class="text-slate-400 block text-[10px] font-sans">Final Price</span>
            <span class="text-emerald-400 font-bold text-sm">${{ item.final_price }} {{ item.currency }}</span>
          </div>
        </div>
      </div>
    </div>

    <!-- DISCOUNT CODES MANAGEMENT SUB-SECTION -->
    <div class="bg-slate-800/90 rounded-2xl border border-slate-700/80 shadow-xl overflow-hidden p-5 space-y-4">
      <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 border-b border-slate-700/80 pb-4">
        <div>
          <h3 class="text-base font-extrabold text-white flex items-center gap-2">
            <span class="p-1.5 bg-purple-500/10 border border-purple-500/20 rounded-lg text-purple-400 inline-flex items-center justify-center">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
              </svg>
            </span>
            <span>DISCOUNT CODES MANAGEMENT</span>
          </h3>
          <p class="text-sm text-slate-200 mt-0.5 font-medium">Manage promotional voucher codes, usage limits, and expiration dates</p>
        </div>
        <button @click="$emit('open-discount-modal')" class="px-4 py-2.5 bg-purple-600 hover:bg-purple-500 text-white font-bold text-xs rounded-xl shadow-lg shadow-purple-600/30 transition cursor-pointer flex items-center gap-1.5">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
          </svg>
          <span>Add Discount Code</span>
        </button>
      </div>

      <div class="overflow-x-auto rounded-xl border border-slate-700/60">
        <table class="w-full text-left text-xs text-slate-200">
          <thead class="bg-slate-900/90 border-b border-slate-700 text-slate-200 uppercase font-bold text-xs">
            <tr>
              <th class="p-3.5">Code String</th>
              <th class="p-3.5 text-right">Discount</th>
              <th class="p-3.5 text-right">Usage</th>
              <th class="p-3.5 text-right">Limit</th>
              <th class="p-3.5 text-right">Expires</th>
              <th class="p-3.5 text-center">Status</th>
              <th class="p-3.5 text-right">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-700/60 bg-slate-900/40 font-mono">
            <tr v-for="dc in discountCodes" :key="dc.code" class="hover:bg-slate-700/50 transition">
              <!-- Code String with Click-to-Copy Feature -->
              <td class="p-3.5 font-bold text-xs font-sans">
                <button 
                  @click="copyCode(dc.code)" 
                  class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-indigo-950/90 hover:bg-indigo-900 text-indigo-300 hover:text-white border border-indigo-700/60 transition cursor-pointer group/copy shadow-sm"
                  :title="copiedCode === dc.code ? 'Copied to Clipboard!' : 'Click to copy code string'"
                >
                  <span class="font-mono font-extrabold tracking-wide">{{ dc.code }}</span>
                  <span class="text-[11px]">
                    <svg v-if="copiedCode === dc.code" class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <svg v-else class="w-3.5 h-3.5 opacity-70 group-hover/copy:opacity-100 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                  </span>
                </button>
              </td>

              <!-- Numerical & Date Data -->
              <td class="p-3.5 text-right font-black text-emerald-400 font-sans text-xs">{{ dc.discount }}</td>
              <td class="p-3.5 text-right text-slate-200 text-xs font-bold font-mono">{{ formatUsageCount(dc) }}</td>
              <td class="p-3.5 text-right text-slate-300 text-xs font-mono">{{ dc.limit }}</td>
              <td class="p-3.5 text-right text-slate-200 font-sans text-xs">{{ dc.expires }}</td>

              <!-- Status Badge (Vector SVG Icon) -->
              <td class="p-3.5 text-center font-sans">
                <span 
                  class="px-3 py-1 rounded-full text-[11px] font-extrabold shadow-sm inline-flex items-center gap-1.5"
                  :class="dc.status === 'Active' ? 'bg-emerald-950 text-emerald-300 border border-emerald-700/80' : 'bg-rose-950 text-rose-300 border border-rose-700/80'"
                >
                  <svg v-if="dc.status === 'Active'" class="w-3 h-3 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                  <svg v-else class="w-3 h-3 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                  <span>{{ dc.status }}</span>
                </span>
              </td>

              <!-- Vector SVG Action Buttons -->
              <td class="p-3.5 text-right font-sans">
                <div class="flex items-center justify-end gap-2.5">
                  <button 
                    class="w-8 h-8 rounded-xl bg-slate-900 hover:bg-indigo-950 text-indigo-300 hover:text-white border border-slate-700/80 transition flex items-center justify-center cursor-pointer shadow-sm relative group/btn" 
                    title="Edit Code"
                  >
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    <span class="absolute bottom-full mb-1.5 hidden group-hover/btn:block bg-slate-950 text-white text-[10px] py-1 px-2 rounded-md whitespace-nowrap border border-slate-700 shadow-xl z-20 pointer-events-none">Edit Code</span>
                  </button>

                  <button 
                    class="w-8 h-8 rounded-xl bg-slate-900 hover:bg-rose-950 text-rose-300 hover:text-white border border-slate-700/80 transition flex items-center justify-center cursor-pointer shadow-sm relative group/btn" 
                    title="Delete Code"
                  >
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    <span class="absolute bottom-full mb-1.5 hidden group-hover/btn:block bg-slate-950 text-white text-[10px] py-1 px-2 rounded-md whitespace-nowrap border border-slate-700 shadow-xl z-20 pointer-events-none">Delete Code</span>
                  </button>

                  <button 
                    class="w-8 h-8 rounded-xl bg-slate-900 hover:bg-emerald-950 text-emerald-300 hover:text-white border border-slate-700/80 transition flex items-center justify-center cursor-pointer shadow-sm relative group/btn" 
                    title="View Usage Analytics & History"
                  >
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    <span class="absolute bottom-full mb-1.5 hidden group-hover/btn:block bg-slate-950 text-white text-[10px] py-1 px-2 rounded-md whitespace-nowrap border border-slate-700 shadow-xl z-20 pointer-events-none">View Analytics</span>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>
