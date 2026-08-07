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
</script>

<template>
  <div class="space-y-6">
    <!-- Toolbar Controls -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs space-y-4">
      <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div>
          <h2 class="text-lg font-bold text-slate-900">🏷️ COURSE PRICING GRID</h2>
          <p class="text-xs text-slate-500">កំណត់តម្លៃវគ្គសិក្សា ដោយគ្រូនីមួយៗអាចមានតម្លៃខុសគ្នា និងអាចដាក់បញ្ចុះតម្លៃ</p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
          <!-- View Toggle -->
          <div class="bg-slate-100 p-1 rounded-xl flex items-center gap-1 text-xs">
            <button 
              @click="viewMode = 'table'"
              class="px-2.5 py-1 rounded-lg font-bold transition"
              :class="viewMode === 'table' ? 'bg-white text-indigo-700 shadow-xs' : 'text-slate-600'"
            >
              📋 Table
            </button>
            <button 
              @click="viewMode = 'card'"
              class="px-2.5 py-1 rounded-lg font-bold transition"
              :class="viewMode === 'card' ? 'bg-white text-indigo-700 shadow-xs' : 'text-slate-600'"
            >
              🎴 Card
            </button>
          </div>

          <button @click="$emit('open-modal')" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs rounded-xl shadow-md transition flex items-center gap-1.5">
            <span>＋</span> Set Price
          </button>
          <button @click="$emit('open-discount-modal')" class="px-4 py-2 bg-purple-600 hover:bg-purple-500 text-white font-bold text-xs rounded-xl shadow-md transition flex items-center gap-1.5">
            <span>🏷️</span> Create Discount Code
          </button>
        </div>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3 pt-2 border-t border-slate-100 text-xs">
        <input 
          v-model="search" 
          type="text" 
          placeholder="🔍 Search Course / Teacher..." 
          class="p-2 bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:outline-none" 
        />
        <select v-model="majorFilter" class="p-2 bg-slate-50 border border-slate-300 rounded-xl">
          <option value="All">Major: All</option>
          <option v-for="m in majors" :key="m.id" :value="m.name">{{ m.name }}</option>
        </select>
        <select v-model="teacherFilter" class="p-2 bg-slate-50 border border-slate-300 rounded-xl">
          <option value="All">Teacher: All</option>
          <option v-for="t in teachers" :key="t.id" :value="t.name">{{ t.name }}</option>
        </select>
        <select v-model="typeFilter" class="p-2 bg-slate-50 border border-slate-300 rounded-xl">
          <option value="All">Pricing Type: All</option>
          <option value="Paid">Paid Only</option>
          <option value="Free">Free Only</option>
        </select>
      </div>
    </div>

    <!-- TABLE VIEW -->
    <div v-if="viewMode === 'table'" class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-left text-xs text-slate-700">
          <thead class="bg-slate-50 border-b border-slate-100 text-slate-500 uppercase font-semibold">
            <tr>
              <th class="p-3.5">Course</th>
              <th class="p-3.5">Teacher</th>
              <th class="p-3.5">Base Price</th>
              <th class="p-3.5">Discount</th>
              <th class="p-3.5">Final</th>
              <th class="p-3.5">Revenue Split</th>
              <th class="p-3.5">Currency</th>
              <th class="p-3.5">Status</th>
              <th class="p-3.5 text-right">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            <tr v-for="item in filteredPricing" :key="item.id" class="hover:bg-slate-50/80 transition">
              <td class="p-3.5 font-bold text-slate-900">{{ item.course }}</td>
              <td class="p-3.5 font-medium text-slate-800">{{ item.teacher }}</td>
              <td class="p-3.5 font-semibold text-slate-700">
                <span v-if="item.is_free" class="text-purple-600 font-bold">🆓 Free</span>
                <span v-else>${{ item.base_price.toFixed(2) }}</span>
              </td>
              <td class="p-3.5 text-slate-500 font-medium">{{ item.discount }}</td>
              <td class="p-3.5 font-bold">
                <span v-if="item.is_free" class="text-purple-700">$0</span>
                <span v-else class="text-emerald-600">${{ item.final_price.toFixed(2) }}</span>
              </td>
              <td class="p-3.5 font-bold text-indigo-700">{{ item.revenue_split }}</td>
              <td class="p-3.5 font-mono text-slate-600">{{ item.currency }}</td>
              <td class="p-3.5">
                <span class="px-2 py-0.5 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800 inline-flex items-center gap-1">
                  <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> {{ item.status }}
                </span>
              </td>
              <td class="p-3.5 text-right">
                <div class="flex items-center justify-end gap-1">
                  <button @click="$emit('edit-pricing', item)" class="p-1.5 bg-slate-100 hover:bg-indigo-50 text-indigo-700 rounded-lg" title="Edit">✏️</button>
                  <button @click="$emit('open-discount-modal')" class="p-1.5 bg-slate-100 hover:bg-purple-50 text-purple-700 rounded-lg" title="Discount">🏷️</button>
                  <button class="p-1.5 bg-slate-100 hover:bg-emerald-50 text-emerald-700 rounded-lg" title="Stats">📊</button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- CARD VIEW -->
    <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
      <div v-for="item in filteredPricing" :key="item.id" class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs hover:shadow-md transition flex flex-col justify-between space-y-4">
        <div>
          <div class="flex items-center justify-between">
            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">🟢 {{ item.status }}</span>
            <span class="text-xs font-mono font-bold text-indigo-700">{{ item.revenue_split }}</span>
          </div>
          <h3 class="text-base font-bold text-slate-900 mt-2">{{ item.course }}</h3>
          <p class="text-xs text-slate-500">Teacher: {{ item.teacher }}</p>
        </div>

        <div class="bg-slate-50 p-3 rounded-xl border border-slate-200/80 flex items-center justify-between text-xs font-mono">
          <div>
            <span class="text-slate-400 block text-[10px]">Base / Discount</span>
            <span class="text-slate-700">${{ item.base_price }} / {{ item.discount }}</span>
          </div>
          <div class="text-right">
            <span class="text-slate-400 block text-[10px]">Final Price</span>
            <span class="text-emerald-600 font-bold text-sm">${{ item.final_price }} {{ item.currency }}</span>
          </div>
        </div>

        <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100 text-xs">
          <button @click="$emit('edit-pricing', item)" class="px-3 py-1.5 bg-indigo-50 text-indigo-700 font-bold rounded-xl hover:bg-indigo-100">
            ✏️ Edit Price
          </button>
        </div>
      </div>
    </div>

    <!-- DISCOUNT CODES MANAGEMENT SUB-SECTION -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden p-5 space-y-4">
      <div class="flex items-center justify-between">
        <div>
          <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
            <span>🏷️</span> DISCOUNT CODES MANAGEMENT
          </h3>
          <p class="text-xs text-slate-500">Manage promotional voucher codes, usage limits, and expiration dates</p>
        </div>
        <button @click="$emit('open-discount-modal')" class="px-3.5 py-2 bg-purple-600 hover:bg-purple-500 text-white font-bold text-xs rounded-xl shadow-md transition">
          + Add Discount Code
        </button>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-left text-xs text-slate-700">
          <thead class="bg-slate-50 border-b border-slate-100 text-slate-500 uppercase font-semibold">
            <tr>
              <th class="p-3">Code String</th>
              <th class="p-3">Discount</th>
              <th class="p-3">Usage</th>
              <th class="p-3">Limit</th>
              <th class="p-3">Expires</th>
              <th class="p-3">Status</th>
              <th class="p-3 text-right">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 font-mono">
            <tr v-for="dc in discountCodes" :key="dc.code" class="hover:bg-slate-50/80 transition">
              <td class="p-3 font-bold text-indigo-700">{{ dc.code }}</td>
              <td class="p-3 font-bold text-emerald-600 font-sans">{{ dc.discount }}</td>
              <td class="p-3 text-slate-700">{{ dc.usage }}</td>
              <td class="p-3 text-slate-500">{{ dc.limit }}</td>
              <td class="p-3 text-slate-600 font-sans">{{ dc.expires }}</td>
              <td class="p-3 font-sans">
                <span 
                  class="px-2 py-0.5 rounded-full text-[11px] font-bold"
                  :class="dc.status === 'Active' ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800'"
                >
                  {{ dc.status === 'Active' ? '🟢 Active' : '🔴 Expired' }}
                </span>
              </td>
              <td class="p-3 text-right font-sans">
                <div class="flex items-center justify-end gap-1.5">
                  <button class="p-1 text-slate-600 hover:text-indigo-600">✏️</button>
                  <button class="p-1 text-slate-600 hover:text-rose-600">🗑️</button>
                  <button class="p-1 text-slate-600 hover:text-emerald-600">📊</button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>
