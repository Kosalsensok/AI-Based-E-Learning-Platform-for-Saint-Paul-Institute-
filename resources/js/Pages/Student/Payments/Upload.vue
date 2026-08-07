<script setup lang="ts">
import { ref } from 'vue'
import { router, useForm } from '@inertiajs/vue3'
import StudentLayout from '@/Layouts/StudentLayout.vue'

const props = defineProps<{
  course: any
}>()

const form = useForm({
  payment_slip: null as File | null,
  aba_transaction_id: ''
})

const fileInput = ref<HTMLInputElement | null>(null)

const submit = () => {
  form.post(route('student.payments.store', props.course.id), {
    preserveScroll: true,
    onSuccess: () => {
      // It will redirect back with success message
      router.get(route('student.courses'))
    }
  })
}

const formatCurrency = (value: number) => {
  return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(value);
}
</script>

<template>
  <StudentLayout title="Upload Payment">
    <div class="max-w-3xl mx-auto p-6">
      <div class="mb-6">
        <button @click="router.get(route('student.courses'))" class="text-primary hover:underline mb-4 inline-block">
          &larr; Back to My Courses
        </button>
        <h1 class="text-2xl font-bold">Complete Your Payment</h1>
        <p class="text-sm text-gray-500">Course: {{ course.title }}</p>
      </div>

      <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 border border-gray-100 dark:border-gray-700">
        
        <div class="bg-blue-50 dark:bg-blue-900/20 text-blue-800 dark:text-blue-300 p-4 rounded-lg mb-6 flex items-start gap-3">
          <i class="pi pi-info-circle mt-1"></i>
          <div>
            <h3 class="font-bold">Payment Instructions</h3>
            <p class="text-sm mt-1">Please transfer <strong class="text-lg">{{ formatCurrency(course.price) }}</strong> to our ABA account below, then upload the receipt screenshot.</p>
          </div>
        </div>

        <!-- Fake ABA Info (Normally from settings) -->
        <div class="flex flex-col md:flex-row gap-6 mb-8 p-4 bg-gray-50 dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700">
          <div class="md:w-1/3 flex justify-center items-center bg-white p-2 rounded-lg border">
            <!-- Fake QR Code placeholder -->
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=ABA%20Payment" alt="ABA QR Code" class="max-w-[150px]"/>
          </div>
          <div class="md:w-2/3 flex flex-col justify-center">
            <h4 class="text-xl font-bold mb-4 flex items-center gap-2 text-[#005E82]">
              ABA Bank
            </h4>
            <div class="mb-2">
              <span class="text-gray-500 text-sm block">Account Name:</span>
              <span class="font-bold text-lg">E-LMS CAMBODIA</span>
            </div>
            <div>
              <span class="text-gray-500 text-sm block">Account Number:</span>
              <span class="font-mono font-bold text-xl tracking-wider text-[#005E82]">000 123 456</span>
            </div>
          </div>
        </div>

        <form @submit.prevent="submit" class="space-y-6">
          <div>
            <label class="block text-sm font-medium mb-1">Transaction ID (Optional)</label>
            <input type="text" v-model="form.aba_transaction_id" class="w-full px-4 py-2 border rounded-lg focus:ring-primary focus:border-primary border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900" placeholder="e.g. 000123456789" />
            <div v-if="form.errors.aba_transaction_id" class="text-red-500 text-sm mt-1">{{ form.errors.aba_transaction_id }}</div>
          </div>

          <div>
            <label class="block text-sm font-medium mb-1">Upload Receipt (Screenshot) <span class="text-red-500">*</span></label>
            <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-6 text-center hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors cursor-pointer"
              @click="fileInput?.click()">
              <input type="file" ref="fileInput" class="hidden" accept="image/*" @change="e => form.payment_slip = (e.target as any).files[0]" />
              <div v-if="form.payment_slip">
                <i class="pi pi-image text-4xl text-primary mb-2"></i>
                <div class="font-bold text-primary">{{ form.payment_slip.name }}</div>
              </div>
              <div v-else>
                <i class="pi pi-cloud-upload text-4xl text-gray-400 mb-2"></i>
                <div class="text-gray-500">Click to browse or drag and drop</div>
                <div class="text-xs text-gray-400 mt-1">PNG, JPG up to 4MB</div>
              </div>
            </div>
            <div v-if="form.errors.payment_slip" class="text-red-500 text-sm mt-1">{{ form.errors.payment_slip }}</div>
          </div>

          <div class="pt-4 border-t dark:border-gray-700">
            <button type="submit" :disabled="form.processing || !form.payment_slip" class="bg-primary text-white w-full py-3 rounded-lg font-bold hover:bg-primary/90 disabled:opacity-50 disabled:cursor-not-allowed">
              {{ form.processing ? 'Submitting...' : 'Submit Payment Receipt' }}
            </button>
          </div>
        </form>

      </div>
    </div>
  </StudentLayout>
</template>
