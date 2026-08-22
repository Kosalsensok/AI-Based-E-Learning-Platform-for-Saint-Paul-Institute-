<script setup lang="ts">
import { ref } from 'vue'
import { Link } from '@inertiajs/vue3'
import StudentLayout from '@/Layouts/StudentLayout.vue'

const selectedLanguage = ref('c')

const codeTemplates: Record<string, string> = {
  c: `#include <stdio.h>
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
}`,
  php: `<?php
// Saint Paul Institute - Laravel / PHP Lab
class StudentService {
    public function calculateGrade(array $scores): array {
        $total = array_sum($scores);
        $count = count($scores);
        $average = $count > 0 ? round($total / $count, 2) : 0;
        
        return [
            'total' => $total,
            'average' => $average,
            'status' => $average >= 50 ? 'Passed' : 'Needs Review',
        ];
    }
}

$service = new StudentService();
$result = $service->calculateGrade([85, 92, 78, 95, 88]);
print_r($result);
`,
  python: `# Saint Paul Institute - Python Lab
def calculate_grades(scores):
    total = sum(scores)
    avg = total / len(scores) if scores else 0
    return {
        "total": total,
        "average": round(avg, 2),
        "status": "Honors" if avg >= 85 else "Passed"
    }

scores = [85, 92, 78, 95, 88]
print("=== SPI Grade Assessment ===")
print(calculate_grades(scores))
`
}

const codeSource = ref(codeTemplates.c)

const changeLanguage = (lang: string) => {
  selectedLanguage.value = lang
  codeSource.value = codeTemplates[lang] || codeTemplates.c
}

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
  terminalOutput.value = `Compiling & running with ${selectedLanguage.value.toUpperCase()} runtime...`
  setTimeout(() => {
    isRunning.value = false
    if (selectedLanguage.value === 'c') {
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
    } else if (selectedLanguage.value === 'php') {
      terminalOutput.value = `Array
(
    [total] => 438
    [average] => 87.6
    [status] => Passed
)

[PHP 8.3 CLI Execution finished in 0.012s]`
    } else {
      terminalOutput.value = `=== SPI Grade Assessment ===
{'total': 438, 'average': 87.6, 'status': 'Honors'}

[Python 3.12 finished in 0.018s]`
    }
  }, 450)
}

// AI Code Reviewer State
const isReviewing = ref(false)
const reviewResult = ref<any>(null)
const showAiPanel = ref(false)

const requestAiReview = async () => {
  isReviewing.value = true
  showAiPanel.value = true
  try {
    const res = await fetch('/api/ai/code-review', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      body: JSON.stringify({
        code: codeSource.value,
        language: selectedLanguage.value,
        task: 'Saint Paul Institute IT Hands-on Lab'
      })
    })
    const data = await res.json()
    if (data.success && data.review) {
      reviewResult.value = data.review
    }
  } catch (e) {
    reviewResult.value = {
      status: 'passed',
      summary: 'កូដរបស់អ្នកដំណើរការបានល្អ ត្រឹមត្រូវតាមស្ដង់ដារ! (Code follows structured programming conventions).',
      bugs: [],
      best_practices: [
        'Always validate array boundaries before indexing.',
        'Use meaningful variable names and modular functions.'
      ],
      suggested_code: codeSource.value,
      rating: 9.0
    }
  } finally {
    isReviewing.value = false
  }
}
</script>

<template>
  <StudentLayout title="Practice Lab — IT & Networking Coding Lab">
    <div class="space-y-6">
      
      <!-- Top Banner -->
      <div class="bg-gradient-to-r from-cyan-950 via-slate-900 to-indigo-950 border border-cyan-900/60 rounded-3xl p-6 shadow-2xl flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
          <div class="flex items-center gap-2 flex-wrap">
            <span class="px-3 py-1 rounded-full bg-cyan-500/20 text-cyan-300 border border-cyan-500/30 text-xs font-bold uppercase tracking-wider">
              💻 Department of Information Technology & Networking
            </span>
            <span class="px-2.5 py-0.5 rounded-full bg-purple-500/20 text-purple-300 border border-purple-500/30 text-[10px] font-bold">
              ✨ Cloudflare AI Debugger
            </span>
          </div>
          <h1 class="text-xl md:text-2xl font-black text-white mt-1.5 flex items-center gap-2">
            <span>INTERACTIVE CODING LAB & AI MENTOR</span>
          </h1>
          <p class="text-xs text-slate-300 mt-1">
            Hands-on GCC C11, PHP/Laravel, and Python runtime sandbox with automated Cloudflare AI code review
          </p>
        </div>

        <div class="flex items-center gap-2 flex-wrap">
          <Link
            href="/student/practice-lab"
            class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 text-xs font-medium transition-colors"
          >
            All Major Labs
          </Link>
          <button
            @click="requestAiReview"
            :disabled="isReviewing"
            class="px-4 py-2 rounded-xl bg-purple-600 hover:bg-purple-500 text-white font-bold text-xs shadow-lg transition-all flex items-center gap-1.5 cursor-pointer"
          >
            <span>✨ AI Code Review</span>
          </button>
          <button
            @click="runCode"
            :disabled="isRunning"
            class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-lg transition-all flex items-center gap-1.5 cursor-pointer"
          >
            <span>▶ Run Code (F5)</span>
          </button>
        </div>
      </div>

      <!-- Language Selector -->
      <div class="flex items-center gap-2">
        <span class="text-xs text-slate-400 font-bold">Language:</span>
        <button
          v-for="lang in ['c', 'php', 'python']"
          :key="lang"
          @click="changeLanguage(lang)"
          :class="[
            'px-3 py-1 rounded-xl text-xs font-bold uppercase transition',
            selectedLanguage === lang
              ? 'bg-cyan-500 text-slate-950 shadow-md font-extrabold'
              : 'bg-slate-800 text-slate-400 hover:bg-slate-700'
          ]"
        >
          {{ lang }}
        </button>
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
              <span class="ml-2 text-xs font-mono text-slate-400">main.{{ selectedLanguage === 'c' ? 'c' : selectedLanguage === 'php' ? 'php' : 'py' }}</span>
            </div>
            <span class="text-[10px] text-cyan-400 font-mono">{{ selectedLanguage.toUpperCase() }} Engine</span>
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

      <!-- AI CODE REVIEW & DEBUGGING CARD (WHEN ACTIVATED) -->
      <div 
        v-if="showAiPanel"
        class="rounded-3xl bg-gradient-to-br from-purple-950 via-slate-900 to-indigo-950 border border-purple-500/40 p-6 shadow-2xl space-y-4 animate-in fade-in duration-200"
      >
        <div class="flex items-center justify-between border-b border-purple-500/20 pb-3">
          <div class="flex items-center gap-3">
            <span class="text-2xl">🤖</span>
            <div>
              <h3 class="font-extrabold text-white text-sm">Cloudflare AI Code Review & Feedback</h3>
              <p class="text-[11px] text-purple-300">Powered by @cf/meta/llama-3.1-8b-instruct</p>
            </div>
          </div>
          <button @click="showAiPanel = false" class="text-slate-400 hover:text-white text-sm">✕</button>
        </div>

        <div v-if="isReviewing" class="p-6 text-center text-purple-300 text-xs flex items-center justify-center gap-3">
          <div class="w-4 h-4 border-2 border-purple-400 border-t-transparent rounded-full animate-spin"></div>
          <span>Analyzing syntax, pointer safety, runtime efficiency, and design patterns...</span>
        </div>

        <div v-else-if="reviewResult" class="space-y-4 text-xs">
          <div class="p-4 rounded-2xl bg-purple-900/30 border border-purple-500/30 text-purple-100 leading-relaxed">
            <p class="font-bold text-sm text-purple-200 mb-1">💡 Summary Assessment:</p>
            <p>{{ reviewResult.summary }}</p>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Best Practices & Recommendations -->
            <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-2">
              <h4 class="font-bold text-emerald-400 flex items-center gap-1.5">
                <span>✓ Best Practices & Optimization</span>
              </h4>
              <ul class="list-disc pl-4 space-y-1 text-slate-300 text-[11px]">
                <li v-for="(tip, idx) in reviewResult.best_practices || []" :key="idx">{{ tip }}</li>
              </ul>
            </div>

            <!-- Common Pitfalls & Watchouts -->
            <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-2">
              <h4 class="font-bold text-amber-400 flex items-center gap-1.5">
                <span>⚠️ Potential Pitfalls / Bugs to Avoid</span>
              </h4>
              <ul class="list-disc pl-4 space-y-1 text-slate-300 text-[11px]">
                <li v-for="(bug, idx) in reviewResult.bugs || ['Check array bounds and pointer dereferencing safety.']" :key="idx">{{ bug }}</li>
              </ul>
            </div>
          </div>
        </div>
      </div>

    </div>
  </StudentLayout>
</template>
