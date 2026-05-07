<template>
  <AppLayout title="Dashboard">
    <template #actions>
      <Link v-if="canCreate" :href="route('orders.index')" class="btn-primary">
        Ver pedidos
      </Link>
    </template>

    <!-- Stats -->
    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-icon blue">📋</div>
        <div>
          <div class="stat-label">Total pedidos</div>
          <div class="stat-value">{{ stats.total }}</div>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon amber">⚙️</div>
        <div>
          <div class="stat-label">En proceso</div>
          <div class="stat-value">{{ stats.in_process }}</div>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon purple">🚚</div>
        <div>
          <div class="stat-label">En ruta</div>
          <div class="stat-value">{{ stats.in_route }}</div>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon green">✅</div>
        <div>
          <div class="stat-label">Entregados</div>
          <div class="stat-value">{{ stats.delivered }}</div>
        </div>
      </div>
    </div>

    <!-- Pedidos recientes -->
    <div class="card">
      <div class="card-header">
        <h2 class="section-title">Pedidos recientes</h2>
        <Link :href="route('orders.index')" class="link-more">Ver todos →</Link>
      </div>
      <table>
        <thead>
          <tr>
            <th>Factura</th>
            <th>Cliente</th>
            <th>Fecha</th>
            <th>Estado</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="order in recentOrders" :key="order.id">
            <td class="bold">{{ order.invoice }}</td>
            <td>{{ order.client_name }}</td>
            <td class="muted">{{ formatDate(order.order_date) }}</td>
            <td><StatusBadge :status="order.status" /></td>
          </tr>
        </tbody>
      </table>
    </div>
  </AppLayout>
</template>

<script setup>
import { computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import AppLayout from './Layouts/AppLayout.vue'
import StatusBadge from '../Components/StatusBadge.vue'

const props = defineProps({
  stats: Object,
  recentOrders: Array,
})

const page = usePage()
const canCreate = computed(() => ['admin','sales'].includes(page.props.auth.user.role))

const formatDate = (d) => d ? d.replace('T', ' ').slice(0, 16) : '—'
</script>

<style scoped>
.stats-grid { display: grid; grid-template-columns: repeat(4,1fr); gap: 14px; margin-bottom: 24px; }
.stat-card { background: #fff; border-radius: 10px; border: 1px solid #dde4ef; padding: 16px; display: flex; align-items: center; gap: 14px; }
.stat-icon { width: 44px; height: 44px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 20px; }
.stat-icon.blue   { background: #eff6ff; }
.stat-icon.amber  { background: #fffbeb; }
.stat-icon.purple { background: #f5f3ff; }
.stat-icon.green  { background: #ecfdf5; }
.stat-label { font-size: 12px; color: #6b7a99; font-weight: 500; }
.stat-value { font-size: 26px; font-weight: 700; color: #1a2332; }
.card { background: #fff; border-radius: 10px; border: 1px solid #dde4ef; padding: 20px; }
.card-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px; }
.section-title { font-size: 15px; font-weight: 600; color: #1a2332; }
.link-more { font-size: 13px; color: #1a6db5; text-decoration: none; }
table { width: 100%; border-collapse: collapse; }
thead th { background: #f5f7fc; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: .8px; color: #6b7a99; padding: 10px 14px; text-align: left; border-bottom: 1px solid #dde4ef; }
tbody td { padding: 12px 14px; font-size: 13px; border-bottom: 1px solid #f0f4fa; color: #1a2332; }
tbody tr:hover { background: #fafbfe; }
.bold { font-weight: 600; }
.muted { color: #6b7a99; font-size: 12px; }
</style>