<template>
  <div class="p-6">
    <h2 class="text-xl font-bold mb-4">Papelera de Reciclaje (Eliminación Lógica)</h2>
    <table class="w-full bg-white rounded-lg overflow-hidden shadow">
      <thead class="bg-gray-100 text-left text-xs uppercase font-bold text-gray-600">
        <tr>
          <th class="p-3">Factura</th>
          <th class="p-3">Cliente</th>
          <th class="p-3">Fecha Eliminado</th>
          <th class="p-3">Acción</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="o in orders" :key="o.id" class="border-t border-gray-100">
          <td class="p-3">{{ o.invoice }}</td>
          <td class="p-3">{{ o.client_name }}</td>
          <td class="p-3">{{ new Date(o.deleted_at).toLocaleDateString() }}</td>
          <td class="p-3 text-right">
            <button @click="restore(o.id)" class="text-blue-600 hover:underline font-bold">
              Restaurar
            </button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script setup>
import { router } from '@inertiajs/vue3'

defineProps({ orders: Array })

const restore = (id) => {
  if(confirm('¿Deseas restaurar este pedido?')) {
    router.post(route('orders.restore', id))
  }
}
</script>