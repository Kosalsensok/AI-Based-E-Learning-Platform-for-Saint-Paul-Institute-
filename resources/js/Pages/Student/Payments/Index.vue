<script setup lang="ts">
import { ref, watch, onMounted } from 'vue'
import MyPayments from './MyPayments.vue'
import PendingPayments from './PendingPayments.vue'
import PaymentHistory from './PaymentHistory.vue'
import ReceiptsInvoices from './ReceiptsInvoices.vue'

const props = defineProps<{
  activeTab?: string
}>()

const currentTab = ref(props.activeTab || 'my-payments')

onMounted(() => {
  const urlParams = new URLSearchParams(window.location.search)
  const tabParam = urlParams.get('tab')
  if (tabParam && ['my-payments', 'pending', 'history', 'receipts'].includes(tabParam)) {
    currentTab.value = tabParam
  }
})

watch(() => props.activeTab, (newTab) => {
  if (newTab) currentTab.value = newTab
})
</script>

<template>
  <div>
    <MyPayments v-if="currentTab === 'my-payments'" />
    <PendingPayments v-else-if="currentTab === 'pending'" />
    <PaymentHistory v-else-if="currentTab === 'history'" />
    <ReceiptsInvoices v-else-if="currentTab === 'receipts'" />
    <MyPayments v-else />
  </div>
</template>
