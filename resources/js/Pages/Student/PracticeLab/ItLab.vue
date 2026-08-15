<script setup lang="ts">
import { ref } from 'vue'
import { Link } from '@inertiajs/vue3'
import StudentLayout from '@/Layouts/StudentLayout.vue'

const codeSource = ref(`#include <stdio.h>
#include <stdlib.h>

// Saint Paul Institute - C Coding Lab
int main() {
    int scores[] = {85, 92, 78, 95, 88};
    int n = sizeof(scores) / sizeof(scores[0]);
    int sum = 0;
    
    printf("=== SPI Student Score Calculator ===\\n");
    for(int i = 0; i < n; i++) {
        sum += scores[i];
        printf("Score #%d: %d\\n", i + 1, scores[i]);
    }
    
    double avg = (double)sum / n;
    printf("------------------------------------\\n");
    printf("Total Sum: %d\\n", sum);
    printf("Average Score: %.2f\\n", avg);
    
    return 0;
}`)

const isRunning = ref(false)
const terminalOutput = ref(`=== SPI Student Score Calculator ===
Score #1: 85
Score #2: 92
Score #3: 78
Score #4: 95
Score #5: 88
------------------------------------
Total Sum: 438
Average Score: 87.60

[Program finished with exit code 0 in 0.042s]`)

const runCode = () => {
  isRunning.value = true
  terminalOutput.value = 'Compiling with gcc (SPI C11 Runner)...'
  setTimeout(() => {
    isRunning.value = false
    terminalOutput.value = `=== SPI Student Score Calculator ===
Score #1: 85
Score #2: 92
Score #3: 78
Score #4: 95
Score #5: 88
------------------------------------
Total Sum: 438
Average Score: 87.60

[Program finished with exit code 0 in 0.038s]`
  }, 500)
}
</script>

<template>
  <StudentLayout title="Practice Lab — IT & Networking Coding Lab">
    <div class="space-y-6">
      
      <!-- Top Banner -->
      <div class="bg-gradient-to-r from-cyan-950 via-slate-900 to-indigo-950 border border-cyan-900/60 rounded-3xl p-6 shadow-2xl flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
          <span class="px-3 py-1 rounded-full bg-cyan-500/20 text-cyan-300 border border-cyan-500/30 text-xs font-bold uppercase tracking-wider">
            💻 Department of Information Technology & Networking
          </span>
          <h1 class="text-xl md:text-2xl font-black text-white mt-1.5 flex items-center gap-2">
            <span>INTERACTIVE CODING LAB & TERMINAL</span>
          </h1>
          <p class="text-xs text-slate-300 mt-1">
            Online GCC C/C++ compiler and Linux terminal sandbox environment for hands-on programming exercises
          </p>
        </div>

        <div class="flex items-center gap-2">
          <Link
            href="/student/practice-lab"
            class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 text-xs font-medium transition-colors"
          >
            All Major Labs
          </Link>
          <button
            @click="runCode"
            :disabled="isRunning"
            class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-lg transition-all flex items-center gap-1.5"
          >
            <span>▶ Run Code (F5)</span>
          </button>
        </div>
      </div>

      <!-- IDE Editor + Terminal Simulator Grid -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Code Editor -->
        <div class="bg-slate-900 border border-slate-800 rounded-3xl overflow-hidden shadow-2xl flex flex-col h-[520px]">
          <div class="px-4 py-3 bg-slate-950 border-b border-slate-800 flex items-center justify-between">
            <div class="flex items-center gap-2">
              <span class="w-3 h-3 rounded-full bg-red-500/80"></span>
              <span class="w-3 h-3 rounded-full bg-amber-500/80"></span>
              <span class="w-3 h-3 rounded-full bg-emerald-500/80"></span>
              <span class="ml-2 text-xs font-mono text-slate-400">main.c (C11 Compiler)</span>
            </div>
            <span class="text-[10px] text-cyan-400 font-mono">GCC 13.2</span>
          </div>

          <textarea
            v-model="codeSource"
            class="flex-1 w-full bg-slate-900 text-emerald-300 font-mono text-xs p-4 leading-relaxed resize-none focus:outline-none custom-scrollbar"
            spellcheck="false"
          ></textarea>
        </div>

        <!-- Terminal Output -->
        <div class="bg-slate-950 border border-slate-800 rounded-3xl overflow-hidden shadow-2xl flex flex-col h-[520px]">
          <div class="px-4 py-3 bg-slate-900/80 border-b border-slate-800 flex items-center justify-between">
            <div class="flex items-center gap-2">
              <span class="text-xs font-mono text-slate-300">📟 Terminal Output (stdout)</span>
            </div>
            <button
              @click="terminalOutput = ''"
              class="text-[11px] text-slate-400 hover:text-slate-200"
            >
              Clear Console
            </button>
          </div>

          <div class="flex-1 p-4 font-mono text-xs text-slate-200 overflow-y-auto custom-scrollbar whitespace-pre-wrap leading-relaxed">
            {{ terminalOutput }}
          </div>
        </div>
      </div>

    </div>
  </StudentLayout>
</template>
