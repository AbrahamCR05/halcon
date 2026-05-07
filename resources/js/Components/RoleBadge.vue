<template>
  <span class="badge" :class="cls">{{ label }}</span>
</template>

<script setup>
import { computed } from 'vue'
const props = defineProps({ role: String })
const map = {
  admin:     { label: 'Administrador', cls: 'admin'     },
  sales:     { label: 'Ventas',        cls: 'sales'     },
  warehouse: { label: 'Almacén',       cls: 'warehouse' },
  route:     { label: 'Ruta',          cls: 'route'     },
  purchasing:{ label: 'Compras',       cls: 'purchasing'},
}
const label = computed(() => map[props.role]?.label || props.role)
const cls   = computed(() => map[props.role]?.cls   || '')
</script>

<style scoped>
.badge { display: inline-flex; padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; }
.admin      { background: #eff6ff; color: #1d4ed8; }
.sales      { background: #ecfdf5; color: #047857; }
.warehouse  { background: #fffbeb; color: #b45309; }
.route      { background: #f5f3ff; color: #6d28d9; }
.purchasing { background: #fdf4ff; color: #7e22ce; }
</style>