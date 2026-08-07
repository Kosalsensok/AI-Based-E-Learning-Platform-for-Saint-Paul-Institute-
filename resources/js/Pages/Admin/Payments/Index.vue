<script setup lang="ts">
import PaymentABAModule from '../PaymentABAModule/Index.vue'

const props = defineProps<{
  tab?: string
  stats: any
  paymentMethodsDistribution: any[]
  liveActivityFeed: any[]
  coursePricing: any[]
  discountCodes: any[]
  abaTransactions: any[]
  receiptsQueue: any[]
  paymentHistory: any[]
  refundRequests: any[]
  revenueByMajor: any[]
  topEarningCourses: any[]
  teacherEarnings: any[]
  majors: any[]
  teachers: any[]
}>()
</script>

<template>
  <PaymentABAModule v-bind="props" />
</template>
