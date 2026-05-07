<template>
  <AppLayout title="Pedidos eliminados">
    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
      <div v-if="orders.data.length === 0" class="p-12 text-center text-gray-500">
        <div class="text-4xl mb-2">🗑</div>
        <p>No hay pedidos eliminados</p>
      </div>
      <table v-else class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="th">Factura</th>
            <th class="th">Razón social</th>
            <th class="th">N° Cliente</th>
            <th class="th">Fecha</th>
            <th class="th">Estado</th>
            <th class="th">Acciones</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
          <tr v-for="order in orders.data" :key="order.id" class="hover:bg-gray-50">
            <td class="td font-bold text-amber-600">{{ order.num_factura }}</td>
            <td class="td">{{ order.razon_social }}</td>
            <td class="td text-xs font-mono bg-gray-100 rounded">{{ order.num_cliente }}</td>
            <td class="td text-sm text-gray-500">{{ formatDate(order.fecha_hora) }}</td>
            <td class="td"><StatusBadge :status="order.status" /></td>
            <td class="td">
              <button @click="restore(order)" class="restore-btn">↩ Restaurar</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Paginación -->
    <div class="mt-4 flex justify-center gap-1">
      <template v-for="link in orders.links" :key="link.label">
        <Link v-if="link.url" :href="link.url"
          class="px-3 py-1 border rounded text-sm"
          :class="link.active ? 'bg-amber-500 text-white' : 'bg-white text-gray-700'"
          v-html="link.label" />
        <span v-else class="px-3 py-1 border rounded text-sm text-gray-400" v-html="link.label" />
      </template>
    </div>
  </AppLayout>
</template>

<script setup>
import { Link, router } from '@inertiajs/vue3'
import { useToast } from 'vue-toastification'
import AppLayout   from '../Layouts/AppLayout.vue'
import StatusBadge from '../../Components/StatusBadge.vue'

defineProps({ orders: Object })

const toast = useToast()

const restore = (order) => {
  router.patch(route('orders.restore', order.id), {}, {
    onSuccess: () => toast.success('Pedido restaurado correctamente'),
  })
}

const formatDate = (d) => d ? new Date(d).toLocaleDateString('es-MX', { day:'2-digit', month:'short', year:'numeric' }) : '—'
</script>

<style scoped>
.th { padding: 10px 16px; text-align: left; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: .8px; color: #6b7280; }
.td { padding: 12px 16px; font-size: 13px; color: #1a2332; vertical-align: middle; }
.restore-btn { padding: 5px 12px; background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0; border-radius: 5px; font-size: 12px; font-weight: 600; cursor: pointer; }
.restore-btn:hover { background: #d1fae5; }
</style>