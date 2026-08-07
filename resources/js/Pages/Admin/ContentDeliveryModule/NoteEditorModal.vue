<script setup lang="ts">
import { ref, computed, watch, nextTick } from 'vue'
import { marked } from 'marked'
import hljs from 'highlight.js'
import 'highlight.js/styles/github-dark.css'

const props = defineProps<{
  show: boolean
  note?: any
}>()

const emit = defineEmits(['close', 'saved'])

const title = ref(props.note?.title || 'C Syntax Cheat Sheet')
const selectedCourse = ref(props.note?.course || 'C Programming Basics')
const selectedChapter = ref(props.note?.chapter || 'Chapter 1.2: Setup Environment')
const content = ref(props.note?.content || `# C Programming Syntax Cheat Sheet

## 1. Variables & Basic Data Types
\`\`\`c
#include <stdio.h>

int main() {
    int age = 20;
    float price = 9.99;
    char grade = 'A';
    printf("Age: %d, Price: %.2f\\n", age, price);
    return 0;
}
\`\`\`

## 2. Control Flow & Loops
- **if / else** conditional branches
- **for loop** iteration over arrays
- **while loop** conditional execution

> [!NOTE]
> Always check for array boundaries when working with C pointers and indices!

[Learn more about C documentation](https://en.cppreference.com)
`)

const status = ref(props.note?.status || 'Published')
const allowPdfExport = ref(props.note?.allowPdfExport !== undefined ? props.note.allowPdfExport : true)

const editorRef = ref<HTMLTextAreaElement | null>(null)
const previewRef = ref<HTMLElement | null>(null)
const txtFileInputRef = ref<HTMLInputElement | null>(null)
const isPreviewMode = ref(false)
const loadedFileName = ref<string | null>(null)
const isDraggingOver = ref(false)

// Keep state updated when modal opens or note prop changes
watch(() => [props.show, props.note], () => {
  if (props.show) {
    title.value = props.note?.title || 'C Syntax Cheat Sheet'
    selectedCourse.value = props.note?.course || 'C Programming Basics'
    selectedChapter.value = props.note?.chapter || 'Chapter 1.2: Setup Environment'
    if (props.note?.content) {
      content.value = props.note.content
    }
    status.value = props.note?.status || 'Published'
    isPreviewMode.value = false
    loadedFileName.value = props.note?.file_name || null
  }
}, { immediate: true })

// 1. TXT / MD File Import Handler (Reads .txt or .md files into textarea)
const triggerTxtFileSelect = () => {
  if (txtFileInputRef.value) {
    txtFileInputRef.value.value = ''
  }
  txtFileInputRef.value?.click()
}

const handleTxtFileImport = (event: Event) => {
  const target = event.target as HTMLInputElement
  if (target.files && target.files[0]) {
    readTxtFile(target.files[0])
  }
}

const handleTextareaDrop = (event: DragEvent) => {
  isDraggingOver.value = false
  if (event.dataTransfer?.files && event.dataTransfer.files[0]) {
    const file = event.dataTransfer.files[0]
    if (file.name.endsWith('.txt') || file.name.endsWith('.md') || file.name.endsWith('.markdown') || file.type.includes('text')) {
      readTxtFile(file)
    }
  }
}

const importNotification = ref<string | null>(null)

const readTxtFile = (file: File) => {
  const reader = new FileReader()
  reader.onload = (e) => {
    const textContent = e.target?.result as string
    if (textContent !== undefined) {
      content.value = textContent
      loadedFileName.value = file.name
      if (!title.value || title.value === 'C Syntax Cheat Sheet' || title.value === 'Untitled Note') {
        title.value = file.name.replace(/\.[^/.]+$/, "").replace(/_/g, " ")
      }
      
      // Auto notification banner
      const sizeKb = (file.size / 1024).toFixed(1)
      importNotification.value = `Loaded ${file.name} (${sizeKb} KB) automatically!`
      setTimeout(() => {
        importNotification.value = null
      }, 4000)

      nextTick(() => {
        if (editorRef.value) editorRef.value.focus()
        if (isPreviewMode.value) highlightCodeBlocks()
      })
    }
  }
  reader.readAsText(file)
}

// 2. Export / Download Note content as .txt or .md file
const exportAsFile = (extension: 'txt' | 'md' = 'txt') => {
  const blob = new Blob([content.value], { type: 'text/plain;charset=utf-8' })
  const url = URL.createObjectURL(blob)
  const a = document.createElement('a')
  a.href = url
  a.download = `${(title.value || 'note').replace(/[^a-zA-Z0-9_-]/g, '_')}.${extension}`
  document.body.appendChild(a)
  a.click()
  document.body.removeChild(a)
  URL.revokeObjectURL(url)
}

// 3. Text Formatting Insertion Function (Toolbar B, I, U, H1, H2, List, Link, Code...)
const insertFormatting = (type: string) => {
  const editor = editorRef.value
  if (!editor) return

  const start = editor.selectionStart || 0
  const end = editor.selectionEnd || 0
  const selectedText = content.value.substring(start, end)
  let replacement = ""

  switch (type) {
    case 'bold':
      replacement = `**${selectedText || 'bold text'}**`
      break
    case 'italic':
      replacement = `*${selectedText || 'italic text'}*`
      break
    case 'underline':
      replacement = `<u>${selectedText || 'underlined text'}</u>`
      break
    case 'h1':
      replacement = `# ${selectedText || 'Heading 1'}\n`
      break
    case 'h2':
      replacement = `## ${selectedText || 'Heading 2'}\n`
      break
    case 'ul':
      replacement = `- ${selectedText || 'List item'}\n`
      break
    case 'ol':
      replacement = `1. ${selectedText || 'List item'}\n`
      break
    case 'link':
      replacement = `[${selectedText || 'Link Title'}](https://example.com)`
      break
    case 'code':
      replacement = `\n\`\`\`c\n${selectedText || '// Write your C code here\n#include <stdio.h>\n\nint main() {\n    printf("Hello World!\\n");\n    return 0;\n}'}\n\`\`\`\n`
      break
  }

  const before = content.value.substring(0, start)
  const after = content.value.substring(end)
  content.value = before + replacement + after

  nextTick(() => {
    editor.focus()
    const newCursorPos = start + replacement.length
    editor.setSelectionRange(start, newCursorPos)
  })
}

// 4. Computed rendered HTML from Markdown using Marked.js
const renderedHtml = computed(() => {
  if (!content.value) return ''
  try {
    return marked.parse(content.value) as string
  } catch (err) {
    return `<p class="text-rose-400">Error parsing Markdown</p>`
  }
})

// 5. Toggle Note Preview & Highlight Syntax in Code Blocks
const toggleNotePreview = () => {
  isPreviewMode.value = !isPreviewMode.value

  if (isPreviewMode.value) {
    nextTick(() => {
      highlightCodeBlocks()
    })
  }
}

const highlightCodeBlocks = () => {
  if (previewRef.value) {
    previewRef.value.querySelectorAll('pre code').forEach((block) => {
      hljs.highlightElement(block as HTMLElement)
    })
  }
}

const handleSave = () => {
  emit('saved', {
    ...(props.note || {}),
    title: title.value,
    course: selectedCourse.value,
    chapter: selectedChapter.value,
    content: content.value,
    status: status.value,
    allowPdfExport: allowPdfExport.value,
    file_name: loadedFileName.value || `${title.value}.txt`
  })
  emit('close')
}
</script>

<template>
  <div v-if="show" class="fixed inset-0 z-50 overflow-hidden bg-slate-950/75 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
    <!-- Hidden File Input for TXT/MD Import -->
    <input 
      ref="txtFileInputRef" 
      type="file" 
      accept=".txt,.md,.markdown,text/plain" 
      class="hidden" 
      @change="handleTxtFileImport" 
    />

    <div class="bg-slate-900 rounded-2xl shadow-xl border border-slate-800 w-full max-w-4xl overflow-hidden my-auto transform transition-all text-slate-100 flex flex-col h-[88vh] max-h-[760px]">
      
      <!-- 1. Modal Header (Subtle & Clean) -->
      <div class="bg-slate-900/90 px-5 py-3 text-white flex items-center justify-between border-b border-slate-800 shrink-0">
        <div class="flex items-center gap-3">
          <!-- Icon -->
          <div class="w-8 h-8 rounded-lg bg-amber-500/10 border border-amber-500/20 flex items-center justify-center shrink-0">
            <svg class="w-4.5 h-4.5" viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M6 8C6 5.79086 7.79086 4 10 4H26C28.2091 4 30 5.79086 30 8V28C30 30.2091 28.2091 32 26 32H10C7.79086 32 6 30.2091 6 28V8Z" fill="#F59E0B"/>
              <rect x="10" y="10" width="16" height="2.5" rx="1" fill="#78350F"/>
              <rect x="10" y="15" width="16" height="2.5" rx="1" fill="#78350F"/>
              <rect x="10" y="20" width="10" height="2.5" rx="1" fill="#78350F"/>
            </svg>
          </div>

          <div>
            <div class="flex items-center gap-2 flex-wrap">
              <h3 class="text-xs font-semibold text-white flex items-center gap-1.5">
                <span class="text-slate-400 uppercase tracking-wide font-bold text-[11px]">NOTE EDITOR</span>
                <span class="text-slate-600">—</span>
                <span class="text-amber-300 font-semibold truncate max-w-[240px] sm:max-w-[360px]">{{ title || 'Untitled Note' }}</span>
              </h3>
              <span class="px-2 py-0.5 rounded bg-slate-800 text-slate-300 border border-slate-700/70 text-[10px] font-mono font-medium">
                TXT & MD
              </span>
              <span v-if="loadedFileName" class="px-2 py-0.5 rounded bg-emerald-950/60 text-emerald-300 border border-emerald-800/40 text-[10px] font-mono truncate max-w-[160px]">
                📄 {{ loadedFileName }}
              </span>
            </div>
            <p class="text-[11px] text-slate-400 mt-0.5 font-normal flex items-center gap-2">
              <span>Rich Text & TXT File Editor</span>
              <span class="text-slate-600">•</span>
              <span class="text-slate-300 font-mono">{{ selectedCourse }}</span>
            </p>
          </div>
        </div>

        <!-- Close Button -->
        <button 
          @click="$emit('close')" 
          class="w-7 h-7 flex items-center justify-center rounded-lg bg-slate-800/80 hover:bg-slate-700 text-slate-400 hover:text-white border border-slate-700/60 text-sm font-semibold transition cursor-pointer shrink-0"
          title="Close"
        >
          ×
        </button>
      </div>

      <!-- 2. Modal Body (Fixed Single Scrollbar Container) -->
      <div class="p-4 space-y-3 text-xs text-slate-200 flex-1 flex flex-col overflow-hidden">
        
        <!-- Auto Import Notification Banner -->
        <div v-if="importNotification" class="bg-emerald-950/80 border border-emerald-500/30 text-emerald-300 px-3.5 py-1.5 rounded-lg text-xs font-medium flex items-center justify-between shrink-0">
          <div class="flex items-center gap-2">
            <span>📄</span>
            <span>{{ importNotification }}</span>
          </div>
          <button @click="importNotification = null" class="text-emerald-400 hover:text-white font-bold px-2 py-0.5 rounded hover:bg-emerald-900/40">✕</button>
        </div>
        
        <!-- Compact Inputs -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 shrink-0">
          <div>
            <label class="block font-medium text-slate-300 mb-0.5 text-[11px]">Note Title:</label>
            <input id="noteTitleInput" v-model="title" type="text" placeholder="e.g. C Syntax Cheat Sheet" class="w-full px-3 py-1.5 bg-slate-950/80 border border-slate-800 rounded-lg text-white font-medium text-xs focus:border-amber-500/60 focus:outline-none" />
          </div>
          <div>
            <label class="block font-medium text-slate-300 mb-0.5 text-[11px]">Course Assignment:</label>
            <input v-model="selectedCourse" type="text" placeholder="e.g. C Programming Basics" class="w-full px-3 py-1.5 bg-slate-950/80 border border-slate-800 rounded-lg text-slate-200 text-xs focus:border-amber-500/60 focus:outline-none" />
          </div>
        </div>

        <!-- Toolbar (Moderate Soft Styling) -->
        <div class="note-toolbar bg-slate-950/80 p-1.5 rounded-lg border border-slate-800 flex flex-wrap items-center justify-between gap-1.5 text-xs font-mono font-medium shrink-0">
          <div class="flex flex-wrap items-center gap-1">
            <button 
              type="button"
              @click="insertFormatting('bold')" 
              class="px-2 py-0.5 bg-slate-900 hover:bg-slate-800 hover:text-amber-300 text-slate-300 rounded border border-slate-800 transition cursor-pointer font-bold"
              title="Bold (**text**)"
            >
              B
            </button>
            <button 
              type="button"
              @click="insertFormatting('italic')" 
              class="px-2 py-0.5 bg-slate-900 hover:bg-slate-800 hover:text-amber-300 text-slate-300 rounded border border-slate-800 italic transition cursor-pointer font-semibold"
              title="Italic (*text*)"
            >
              I
            </button>
            <button 
              type="button"
              @click="insertFormatting('underline')" 
              class="px-2 py-0.5 bg-slate-900 hover:bg-slate-800 hover:text-amber-300 text-slate-300 rounded border border-slate-800 underline transition cursor-pointer"
              title="Underline (<u>text</u>)"
            >
              U
            </button>

            <span class="text-slate-800 px-0.5">|</span>

            <button 
              type="button"
              @click="insertFormatting('h1')" 
              class="px-2 py-0.5 bg-slate-900 hover:bg-slate-800 hover:text-amber-300 text-slate-300 rounded border border-slate-800 transition cursor-pointer"
              title="Heading 1 (# Heading)"
            >
              H1
            </button>
            <button 
              type="button"
              @click="insertFormatting('h2')" 
              class="px-2 py-0.5 bg-slate-900 hover:bg-slate-800 hover:text-amber-300 text-slate-300 rounded border border-slate-800 transition cursor-pointer"
              title="Heading 2 (## Heading)"
            >
              H2
            </button>

            <span class="text-slate-800 px-0.5">|</span>

            <button 
              type="button"
              @click="insertFormatting('ul')" 
              class="px-2 py-0.5 bg-slate-900 hover:bg-slate-800 hover:text-amber-300 text-slate-300 rounded border border-slate-800 transition cursor-pointer"
              title="Unordered List (- Item)"
            >
              • List
            </button>
            <button 
              type="button"
              @click="insertFormatting('ol')" 
              class="px-2 py-0.5 bg-slate-900 hover:bg-slate-800 hover:text-amber-300 text-slate-300 rounded border border-slate-800 transition cursor-pointer"
              title="Ordered List (1. Item)"
            >
              1. List
            </button>

            <span class="text-slate-800 px-0.5">|</span>

            <button 
              type="button"
              @click="insertFormatting('link')" 
              class="px-2 py-0.5 bg-slate-900 hover:bg-slate-800 hover:text-amber-300 text-slate-300 rounded border border-slate-800 transition cursor-pointer"
              title="Hyperlink ([title](url))"
            >
              🔗 Link
            </button>
            <button 
              type="button"
              @click="insertFormatting('code')" 
              class="px-2 py-0.5 bg-slate-900 hover:bg-slate-800 hover:text-amber-300 text-amber-400 rounded border border-slate-800 transition cursor-pointer"
              title="Code Block (```c ... ```)"
            >
              ``` Code
            </button>
          </div>

          <div class="flex items-center gap-1.5">
            <!-- Import TXT button -->
            <button 
              type="button"
              @click="triggerTxtFileSelect"
              class="px-2.5 py-1 bg-slate-900 hover:bg-slate-800 text-amber-300 rounded border border-slate-800 text-[11px] font-sans font-medium transition flex items-center gap-1 cursor-pointer"
              title="Import .txt or .md file"
            >
              <span>📂 Import .TXT</span>
            </button>

            <!-- Toggle Preview Button -->
            <button 
              type="button" 
              @click="toggleNotePreview" 
              :class="['px-2.5 py-1 rounded font-sans font-medium transition flex items-center gap-1 cursor-pointer text-[11px] border', isPreviewMode ? 'bg-amber-600/90 border-amber-500/60 text-white' : 'bg-slate-800 hover:bg-slate-700 border-slate-700 text-slate-200']"
            >
              <span>{{ isPreviewMode ? '✏️ Edit Markdown' : '👁️ Preview' }}</span>
            </button>
          </div>
        </div>

        <!-- Textarea Editor / Preview (Single Scrollbar) -->
        <div class="relative w-full flex-1 flex flex-col min-h-0 overflow-hidden">
          <div 
            v-show="!isPreviewMode" 
            class="flex-1 flex flex-col min-h-0 relative space-y-1"
            @dragover.prevent="isDraggingOver = true"
            @dragleave.prevent="isDraggingOver = false"
            @drop.prevent="handleTextareaDrop"
          >
            <div v-if="isDraggingOver" class="absolute inset-0 bg-amber-500/10 backdrop-blur-xs border border-dashed border-amber-400 rounded-xl flex items-center justify-center z-10 text-amber-300 font-medium text-xs">
              📂 Drop .TXT or .MD File to Import Content
            </div>

            <textarea 
              id="markdownBody"
              ref="editorRef" 
              v-model="content" 
              class="w-full flex-1 p-3 bg-slate-950/90 border border-slate-800 rounded-xl text-slate-200 font-mono text-xs focus:border-amber-500/50 shadow-inner focus:outline-none resize-none overflow-y-auto"
              placeholder="សរសេរ Note Markdown ឬ Drop .txt file ទីនេះ..."
            ></textarea>
            
            <p class="text-[11px] text-slate-400 font-mono flex justify-end px-1 shrink-0">
              <span id="charCounter">{{ content.length }} characters</span>
            </p>
          </div>

          <div 
            v-show="isPreviewMode" 
            id="markdownPreview"
            ref="previewRef"
            class="markdown-preview w-full flex-1 bg-slate-950/90 text-slate-100 border border-slate-800 rounded-xl p-4 overflow-y-auto shadow-inner space-y-3"
            v-html="renderedHtml"
          ></div>
        </div>

      </div>

      <!-- 3. Action Footer (Subtle, Clean & Balanced) -->
      <div class="bg-slate-900/90 px-5 py-2.5 border-t border-slate-800 flex flex-col sm:flex-row items-center justify-between gap-3 shrink-0">
        <!-- Left: Cancel Button -->
        <button @click="$emit('close')" class="px-3.5 py-1.5 bg-slate-800 hover:bg-slate-700 text-slate-300 font-medium rounded-lg border border-slate-700/60 transition cursor-pointer text-xs">
          Cancel
        </button>

        <!-- Center: Status & Checkbox -->
        <div class="flex items-center gap-3 flex-wrap justify-center text-xs">
          <select v-model="status" class="px-2.5 py-1 bg-slate-950 border border-slate-800 rounded-lg text-slate-200 font-medium text-xs focus:border-amber-500/50 focus:outline-none">
            <option value="Published">🟢 Published</option>
            <option value="Draft">🟡 Draft</option>
          </select>
          <label class="flex items-center gap-1.5 cursor-pointer font-normal text-slate-300 select-none text-[11px]">
            <input type="checkbox" v-model="allowPdfExport" class="w-3.5 h-3.5 rounded bg-slate-950 border-slate-700 text-amber-500 focus:ring-0 cursor-pointer" />
            <span>Allow student download</span>
          </label>
        </div>

        <!-- Right: Export & Save Note Button -->
        <div class="flex items-center gap-2">
          <button @click="exportAsFile('txt')" class="px-3 py-1.5 bg-slate-800 hover:bg-slate-700 text-slate-200 font-medium rounded-lg border border-slate-700/70 transition flex items-center gap-1 cursor-pointer text-xs">
            <span>📥 Export .TXT</span>
          </button>
          <button @click="handleSave" class="px-4 py-1.5 bg-amber-600 hover:bg-amber-500 text-white font-medium rounded-lg shadow-sm transition cursor-pointer text-xs">
            Save Note
          </button>
        </div>
      </div>

    </div>
  </div>
</template>


<style>
/* Markdown Preview Styling */
.markdown-preview {
  font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
  line-height: 1.6;
}

.markdown-preview h1 {
  font-size: 1.5rem;
  font-weight: 800;
  color: #fbbf24;
  margin-top: 1rem;
  margin-bottom: 0.5rem;
  padding-bottom: 0.25rem;
  border-bottom: 1px solid #334155;
}

.markdown-preview h2 {
  font-size: 1.25rem;
  font-weight: 700;
  color: #f59e0b;
  margin-top: 0.85rem;
  margin-bottom: 0.4rem;
}

.markdown-preview h3 {
  font-size: 1.1rem;
  font-weight: 700;
  color: #cbd5e1;
  margin-top: 0.75rem;
  margin-bottom: 0.3rem;
}

.markdown-preview p {
  margin-bottom: 0.75rem;
  color: #e2e8f0;
}

.markdown-preview ul {
  list-style-type: disc;
  padding-left: 1.5rem;
  margin-bottom: 0.75rem;
}

.markdown-preview ol {
  list-style-type: decimal;
  padding-left: 1.5rem;
  margin-bottom: 0.75rem;
}

.markdown-preview li {
  margin-bottom: 0.25rem;
}

.markdown-preview blockquote {
  border-left: 4px solid #6366f1;
  background-color: #1e1b4b33;
  padding: 0.5rem 1rem;
  margin: 0.75rem 0;
  border-radius: 0 0.5rem 0.5rem 0;
  color: #cbd5e1;
  font-style: italic;
}

.markdown-preview a {
  color: #818cf8;
  text-decoration: underline;
  text-underline-offset: 2px;
}
.markdown-preview a:hover {
  color: #a5b4fc;
}

.markdown-preview code:not(pre code) {
  background-color: #1e293b;
  color: #f472b6;
  padding: 0.15rem 0.4rem;
  border-radius: 0.25rem;
  font-family: monospace;
  font-size: 0.85em;
  border: 1px solid #334155;
}

.markdown-preview pre {
  background-color: #0d1117 !important;
  border: 1px solid #30363d;
  border-radius: 0.75rem;
  padding: 1rem;
  overflow-x: auto;
  margin: 0.85rem 0;
  box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.4);
}

.markdown-preview pre code {
  font-family: 'Fira Code', Consolas, Monaco, 'Andale Mono', monospace;
  font-size: 0.85rem;
  background-color: transparent !important;
  padding: 0;
}

.markdown-preview table {
  width: 100%;
  border-collapse: collapse;
  margin: 1rem 0;
}

.markdown-preview th,
.markdown-preview td {
  border: 1px solid #334155;
  padding: 0.5rem 0.75rem;
  text-align: left;
}

.markdown-preview th {
  background-color: #1e293b;
  color: #fbbf24;
  font-weight: 700;
}

.markdown-preview tr:nth-child(even) {
  background-color: #0f172a66;
}
</style>

