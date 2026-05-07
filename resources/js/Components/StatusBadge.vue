<template>
  <span class="badge" :class="badgeClass">{{ label }}</span>
</template>

<script setup>
import { computed } from 'vue'
const props = defineProps({ status: String })

const map = {
  'Ordered':    { label: 'Solicitado',  cls: 'ordered'   },
  'In Process': { label: 'En proceso',  cls: 'process'   },
  'In Route':   { label: 'En ruta',     cls: 'route'     },
  'Delivered':  { label: 'Entregado',   cls: 'delivered' },
}
const label      = computed(() => map[props.status]?.label || props.status)
const badgeClass = computed(() => map[props.status]?.cls   || '')
</script>

<style scoped>
.badge { display: inline-flex; align-items: center; padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; letter-spacing: .3px; }
.ordered   { background: #eff6ff; color: #1d4ed8; }
.process   { background: #fffbeb; color: #b45309; }
.route     { background: #f5f3ff; color: #6d28d9; }
.delivered { background: #ecfdf5; color: #047857; }
</style>