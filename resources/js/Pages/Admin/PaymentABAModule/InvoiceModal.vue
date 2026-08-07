<script setup lang="ts">
const props = defineProps<{
  show: boolean
  invoice: any
}>()

const emit = defineEmits(['close'])

const handlePrint = () => {
  const originalTitle = document.title
  document.title = `Receipt_${props.invoice?.txn_id || props.invoice?.receipt_no || 'TXN-05781'}`
  window.print()
  setTimeout(() => {
    document.title = originalTitle
  }, 1000)
}

const handleDownloadPDF = () => {
  const originalTitle = document.title
  document.title = `Receipt_${props.invoice?.txn_id || props.invoice?.receipt_no || 'TXN-05781'}`
  window.print()
  setTimeout(() => {
    document.title = originalTitle
  }, 1000)
}

const formatDate = (dateStr?: string) => {
  if (dateStr) return dateStr
  const d = new Date()
  return `${d.toLocaleDateString()} ${d.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}`
}
</script>

<template>
  <div v-if="show && invoice" class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4 print-container">
    
    <!-- 1. SCREEN VIEW (DARK MODERN WEB MODAL) -->
    <div class="bg-slate-900 rounded-3xl shadow-2xl border border-slate-800 w-full max-w-lg overflow-hidden my-6 transform transition-all text-slate-100 font-sans screen-only-modal print:hidden">
      
      <!-- Modal Header with Top-Right [X] Button -->
      <div class="bg-slate-950 px-6 py-5 text-white flex items-center justify-between border-b border-slate-800 relative">
        <div>
          <div class="flex items-center gap-2">
            <span class="text-lg font-black text-indigo-400 tracking-wide font-mono">E.LMS EDU KH</span>
            <span class="text-[10px] bg-indigo-500/10 border border-indigo-500/30 px-2 py-0.5 rounded-full font-mono font-bold text-indigo-300 uppercase tracking-wider">RECEIPT</span>
          </div>
          <p class="text-xs text-slate-400 mt-1 font-medium">Payment Confirmation & Course Access Voucher</p>
        </div>

        <div class="flex items-center gap-4">
          <!-- Receipt # Badge -->
          <div class="text-right">
            <div class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Receipt #</div>
            <div class="text-base font-mono font-black text-amber-400 tracking-wider">{{ invoice.txn_id || invoice.receipt_no || 'TXN-05781' }}</div>
          </div>

          <!-- Top Right [X] Close Button -->
          <button 
            @click="$emit('close')" 
            class="w-8 h-8 rounded-xl bg-slate-900 hover:bg-slate-800 text-slate-400 hover:text-white flex items-center justify-center transition border border-slate-800 text-lg cursor-pointer shrink-0"
            title="Close Modal (Esc)"
          >
            ×
          </button>
        </div>
      </div>

      <!-- Receipt Body Details -->
      <div class="p-6 space-y-4 text-xs text-slate-200">
        
        <!-- Status Pill Badge -->
        <div class="flex items-center justify-between p-3.5 bg-slate-950/60 rounded-2xl border border-slate-800">
          <span class="font-bold text-slate-300 text-xs">Payment Status:</span>
          <span class="px-3 py-1 bg-emerald-500/15 text-emerald-400 border border-emerald-500/30 rounded-full text-xs font-bold inline-flex items-center gap-1.5 shadow-xs">
            <svg class="w-3.5 h-3.5 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>{{ invoice.status || 'Success Verified' }}</span>
          </span>
        </div>

        <!-- Information Card with Divider Lines -->
        <div class="bg-slate-950/80 rounded-2xl border border-slate-800 divide-y divide-slate-800/80">
          <div class="p-3.5 flex items-center justify-between">
            <span class="text-slate-300 font-medium">Issue Date & Time:</span>
            <span class="text-slate-200 font-mono text-xs font-semibold">{{ formatDate(invoice.date_time) }}</span>
          </div>

          <div class="p-3.5 flex items-center justify-between">
            <span class="text-slate-300 font-medium">Student Name:</span>
            <strong class="text-white text-xs font-bold">{{ invoice.student?.name || 'Chan Dara' }}</strong>
          </div>

          <div class="p-3.5 flex items-center justify-between">
            <span class="text-slate-300 font-medium">Course Enrolled:</span>
            <strong class="text-indigo-300 text-xs font-bold">{{ invoice.course || 'C Programming Basics' }}</strong>
          </div>

          <div class="p-3.5 flex items-center justify-between">
            <span class="text-slate-300 font-medium">Instructor / Teacher:</span>
            <span class="text-slate-200 font-semibold">{{ invoice.teacher || 'Mr. Sok Heng' }}</span>
          </div>

          <div class="p-3.5 flex items-center justify-between">
            <span class="text-slate-300 font-medium">Payment Method:</span>
            <span class="font-bold text-indigo-400 inline-flex items-center gap-1 font-mono">
              <svg class="w-3.5 h-3.5 text-indigo-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
              </svg>
              <span>{{ invoice.method || 'ABA PayWay' }}</span>
            </span>
          </div>

          <div class="p-3.5 flex items-center justify-between bg-slate-900/40 font-bold">
            <span class="text-white text-xs">Total Amount Paid:</span>
            <span class="text-emerald-400 font-mono text-sm font-black">${{ (invoice.amount || 45).toFixed(2) }} USD</span>
          </div>
        </div>

      </div>

      <!-- Action Footer -->
      <div class="bg-slate-950 px-6 py-4 border-t border-slate-800 flex items-center justify-between">
        <button 
          @click="$emit('close')" 
          class="px-4 py-2 text-slate-400 hover:text-white hover:bg-slate-800/80 rounded-xl transition text-xs font-semibold cursor-pointer"
        >
          Close
        </button>

        <div class="flex items-center gap-2">
          <button 
            @click="handlePrint" 
            class="px-3.5 py-2 bg-slate-900 hover:bg-slate-800 text-slate-200 font-semibold text-xs rounded-xl border border-slate-700 transition flex items-center gap-1.5 cursor-pointer shadow-xs"
            title="Print Official Invoice"
          >
            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
            </svg>
            <span>Print</span>
          </button>

          <button 
            @click="handleDownloadPDF" 
            class="px-5 py-2 bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs rounded-xl shadow-lg shadow-indigo-600/30 transition flex items-center gap-2 cursor-pointer"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
            </svg>
            <span>Download PDF</span>
          </button>
        </div>
      </div>

    </div>

    <!-- 2. PRINT-ONLY ENTERPRISE OFFICIAL INVOICE (PAPER FORMAT) -->
    <div class="hidden official-paper-invoice font-sans">
      
      <!-- Top Institutional Header -->
      <div class="invoice-header flex items-start justify-between pb-4 border-b-2 border-slate-900">
        <div>
          <div class="flex items-center gap-2">
            <span class="text-2xl font-black tracking-tight text-slate-900">E.LMS EDU KH</span>
            <span class="text-[10px] border border-slate-900 px-2 py-0.5 font-mono font-bold text-slate-900 uppercase tracking-widest">VERIFIED INSTITUTION</span>
          </div>
          <p class="text-xs text-slate-600 mt-1 font-medium">Higher Education E-Learning Portal & Academic Management</p>
          <p class="text-[11px] text-slate-500 mt-0.5">Phnom Penh, Kingdom of Cambodia · support@elms.edu.kh · www.elms.edu.kh</p>
        </div>

        <div class="text-right">
          <h1 class="text-2xl font-black uppercase tracking-widest text-slate-900">INVOICE</h1>
          <div class="mt-2 space-y-0.5 text-xs font-mono">
            <p><span class="text-slate-500 font-bold">INVOICE #:</span> <strong class="text-slate-900">{{ invoice.txn_id || invoice.receipt_no || 'INV-05781' }}</strong></p>
            <p><span class="text-slate-500 font-bold">DATE:</span> <strong class="text-slate-900">{{ formatDate(invoice.date_time) }}</strong></p>
            <p><span class="text-slate-500 font-bold">STATUS:</span> <strong class="text-emerald-700 font-bold uppercase">[ PAID / VERIFIED ]</strong></p>
          </div>
        </div>
      </div>

      <!-- Bill To & Issued By Grid -->
      <div class="grid grid-cols-2 gap-6 my-5 text-xs">
        <div class="p-3 bg-slate-50 border border-slate-200 rounded-lg">
          <h3 class="font-bold text-slate-400 uppercase text-[10px] tracking-wider">BILLED TO (STUDENT):</h3>
          <p class="text-sm font-bold text-slate-900 mt-1">{{ invoice.student?.name || 'Chan Dara' }}</p>
          <p class="text-slate-600 mt-0.5 font-mono">Student ID: {{ invoice.student?.id ? 'STD-' + invoice.student.id : 'STD-2025-0892' }}</p>
          <p class="text-slate-600">Email: {{ invoice.student?.email || 'student@elms.edu.kh' }}</p>
        </div>

        <div class="p-3 bg-slate-50 border border-slate-200 rounded-lg">
          <h3 class="font-bold text-slate-400 uppercase text-[10px] tracking-wider">PAYMENT SUMMARY:</h3>
          <p class="text-slate-700 mt-1"><span class="font-bold">Payment Method:</span> {{ invoice.method || 'ABA PayWay Online' }}</p>
          <p class="text-slate-700"><span class="font-bold">Instructor:</span> {{ invoice.teacher || 'Mr. Sok Heng' }}</p>
          <p class="text-slate-700 font-mono"><span class="font-bold font-sans">Transaction Hash:</span> {{ invoice.txn_id }}-SUCCESS</p>
        </div>
      </div>

      <!-- Itemized Billing Table -->
      <div class="my-5">
        <table class="w-full text-left text-xs border-collapse">
          <thead>
            <tr class="bg-slate-900 text-white uppercase text-[10px] tracking-wider font-mono">
              <th class="py-2.5 px-3">#</th>
              <th class="py-2.5 px-3">Item Description</th>
              <th class="py-2.5 px-3">Category</th>
              <th class="py-2.5 px-3 text-center">Qty</th>
              <th class="py-2.5 px-3 text-right">Unit Price</th>
              <th class="py-2.5 px-3 text-right">Total</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-200 border-b border-slate-900">
            <tr>
              <td class="py-3 px-3 font-mono">01</td>
              <td class="py-3 px-3">
                <p class="font-bold text-slate-900">{{ invoice.course || 'C Programming Basics' }}</p>
                <p class="text-[10px] text-slate-500">Full Academic Course Access Voucher & Certification</p>
              </td>
              <td class="py-3 px-3 text-slate-600">Tuition Fee</td>
              <td class="py-3 px-3 text-center font-mono">1</td>
              <td class="py-3 px-3 text-right font-mono">${{ (invoice.amount || 45).toFixed(2) }}</td>
              <td class="py-3 px-3 text-right font-mono font-bold text-slate-900">${{ (invoice.amount || 45).toFixed(2) }}</td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Total Amount & Summary Row -->
      <div class="flex justify-end my-4">
        <div class="w-64 space-y-1.5 text-xs font-mono">
          <div class="flex justify-between text-slate-600">
            <span>Subtotal:</span>
            <span>${{ (invoice.amount || 45).toFixed(2) }} USD</span>
          </div>
          <div class="flex justify-between text-slate-600">
            <span>Discount / Tax:</span>
            <span>$0.00 USD</span>
          </div>
          <div class="flex justify-between text-sm font-bold border-t-2 border-slate-900 pt-2 text-slate-900">
            <span>TOTAL PAID:</span>
            <span class="text-emerald-700">${{ (invoice.amount || 45).toFixed(2) }} USD</span>
          </div>
        </div>
      </div>

      <!-- Signature & Stamp Section with Official Verification Seal & QR Code -->
      <div class="mt-6 pt-4 border-t-2 border-slate-900 flex items-center justify-between text-xs">
        
        <!-- Verification QR Code Block -->
        <div class="flex items-center gap-3">
          <div class="w-16 h-16 bg-white border-2 border-slate-900 p-1 flex items-center justify-center shrink-0 rounded">
            <!-- SVG QR Code Representation -->
            <svg class="w-14 h-14 text-slate-900" viewBox="0 0 24 24" fill="currentColor">
              <path d="M2 2h8v8H2V2zm2 2v4h4V4H4zm1 1h2v2H5V5zm9-3h8v8h-8V2zm2 2v4h4V4h-4zm1 1h2v2h-2V5zM2 14h8v8H2v-8zm2 2v4h4v-4H4zm1 1h2v2H5v-2zm13-3h4v2h-4v-2zm-4 2h2v2h-2v-2zm2 2h2v2h-2v-2zm2-2h2v4h-2v-4zm-4 4h2v2h-2v-2zm4 0h4v2h-4v-2z" />
            </svg>
          </div>
          <div class="space-y-0.5 text-[10px] text-slate-600 font-mono">
            <p class="font-bold text-slate-900 font-sans text-xs">SCAN TO VERIFY</p>
            <p>Hash: {{ invoice.txn_id || 'TXN-05781' }}-VERIFIED</p>
            <p class="text-[9px] text-slate-400">elms.edu.kh/verify/{{ invoice.txn_id || 'TXN-05781' }}</p>
          </div>
        </div>

        <!-- Official Stamp Seal Badge -->
        <div class="relative flex items-center justify-center p-3 border-2 border-emerald-700 bg-emerald-50/50 rounded-full text-center text-emerald-900 font-bold uppercase text-[9px] tracking-wider shrink-0 w-28 h-28 transform -rotate-6 shadow-xs">
          <div class="space-y-0.5">
            <svg class="w-5 h-5 text-emerald-700 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
            </svg>
            <p class="font-black leading-tight text-[10px] text-emerald-800">VERIFIED PAYMENT</p>
            <p class="text-[8px] font-mono text-emerald-700">E.LMS ACADEMIC</p>
          </div>
        </div>

        <!-- Official Signature Box -->
        <div class="text-center space-y-6 shrink-0">
          <div class="w-40 border-b-2 border-slate-900 pb-1">
            <span class="font-serif italic text-sm text-indigo-900 font-bold tracking-widest">Sok Heng</span>
          </div>
          <div>
            <p class="font-bold text-slate-900 text-xs">Academic Finance Officer</p>
            <p class="text-[10px] text-slate-500">Authorized Signature & Seal</p>
          </div>
        </div>

      </div>

      <!-- Bottom Disclaimer -->
      <div class="mt-4 pt-2 border-t border-slate-200 text-center text-[9px] text-slate-400 font-mono">
        This is an official computer-generated receipt from E.LMS EDU KH System. Verification Code: SHA256-AUTH-RECEIPT-2025-ENROLLED
      </div>

    </div>

  </div>
</template>

<style>
@media print {
  /* 1. Reset A4 Page & Remove Browser Headers/Footers (Date, URL, Page Numbers) */
  @page {
    size: A4 portrait;
    margin: 0;
  }

  html, body {
    height: 100% !important;
    max-height: 100vh !important;
    margin: 0 !important;
    padding: 0 !important;
    overflow: hidden !important;
    background: #ffffff !important;
    color: #0f172a !important;
    -webkit-print-color-adjust: exact !important;
    print-color-adjust: exact !important;
    font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif !important;
  }

  /* 2. Hide Web Elements */
  body * {
    visibility: hidden !important;
  }

  /* 3. Hide Screen Modal */
  .screen-only-modal {
    display: none !important;
  }

  /* 4. Display Paper Invoice */
  .official-paper-invoice,
  .official-paper-invoice * {
    visibility: visible !important;
    display: block !important;
  }

  .official-paper-invoice .flex {
    display: flex !important;
  }

  .official-paper-invoice .grid {
    display: grid !important;
  }

  .official-paper-invoice table {
    display: table !important;
  }

  .official-paper-invoice tr {
    display: table-row !important;
  }

  .official-paper-invoice td,
  .official-paper-invoice th {
    display: table-cell !important;
  }

  /* 5. Paper Invoice Container Position */
  .official-paper-invoice {
    position: absolute !important;
    left: 0 !important;
    right: 0 !important;
    top: 20px !important;
    margin: 0 auto !important;
    width: 100% !important;
    max-width: 680px !important;
    padding: 30px !important;
    background: #ffffff !important;
    color: #0f172a !important;
    border: 1px solid #e2e8f0 !important;
    box-shadow: none !important;
    page-break-inside: avoid !important;
    break-inside: avoid !important;
  }
}
</style>
