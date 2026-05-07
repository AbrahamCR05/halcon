<template>
  <div class="modal-overlay" @click.self="$emit('close')">
    <div class="modal">
      <div class="modal-header">
        <h3>Detalle del pedido</h3>
        <button @click="$emit('close')" class="close-btn">✕</button>
      </div>
      <div class="modal-body">
        <div class="detail-grid">
          <div class="detail-item"><div class="d-label">Factura</div><div class="d-value bold">{{ order.invoice }}</div></div>
          <div class="detail-item"><div class="d-label">N° Cliente</div><div class="d-value"><code class="code-tag">{{ order.client_num }}</code></div></div>
          <div class="detail-item full"><div class="d-label">Cliente</div><div class="d-value">{{ order.client_name }}</div></div>
          <div class="detail-item"><div class="d-label">RFC</div><div class="d-value">{{ order.rfc || '—' }}</div></div>
          <div class="detail-item"><div class="d-label">Teléfono</div><div class="d-value">{{ order.phone || '—' }}</div></div>
          <div class="detail-item full"><div class="d-label">Dirección</div><div class="d-value">{{ order.address }}</div></div>
          <div class="detail-item"><div class="d-label">Fecha</div><div class="d-value muted">{{ formatDate(order.order_date) }}</div></div>
          <div class="detail-item"><div class="d-label">Estado</div><div class="d-value"><StatusBadge :status="order.status" /></div></div>
          <div v-if="order.notes" class="detail-item full"><div class="d-label">Notas</div><div class="d-value">{{ order.notes }}</div></div>
        </div>

        <div v-if="order.route_photo" class="photo-box purple">
          <div class="photo-label">📷 Foto de unidad cargada</div>
          <img :src="'/storage/' + order.route_photo" alt="Unidad" />
        </div>

        <div v-if="order.delivery_photo" class="photo-box green">
          <div class="photo-label">✅ Evidencia de entrega</div>
          <img :src="'/storage/' + order.delivery_photo" alt="Entrega" />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import StatusBadge from '../../../Components/StatusBadge.vue'
defineProps({ order: Object })
defineEmits(['close'])
const formatDate = (d) => d ? d.replace('T',' ').slice(0,16) : '—'
</script>

<style scoped>
.modal-overlay { position: fixed; inset: 0; background: rgba(20,30,50,.55); display: flex; align-items: center; justify-content: center; z-index: 100; }
.modal { background: #fff; border-radius: 12px; width: min(580px,96vw); max-height: 90vh; overflow-y: auto; }
.modal-header { padding: 20px 24px 0; display: flex; justify-content: space-between; align-items: center; }
.modal-header h3 { font-size: 16px; font-weight: 600; color: #1a2332; }
.close-btn { width: 28px; height: 28px; border: none; background: #f0f4fa; border-radius: 6px; cursor: pointer; }
.modal-body { padding: 20px 24px 24px; }
.detail-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
.detail-item { background: #f8fafd; border-radius: 7px; padding: 12px; }
.detail-item.full { grid-column: 1/-1; }
.d-label { font-size: 10px; font-weight: 600; color: #6b7a99; text-transform: uppercase; letter-spacing: .5px; margin-bottom: 4px; }
.d-value { font-size: 14px; color: #1a2332; font-weight: 500; }
.d-value.muted { color: #6b7a99; font-size: 13px; }
.bold { font-weight: 700; }
.code-tag { font-size: 12px; background: #e8eff8; padding: 2px 7px; border-radius: 4px; }
.photo-box { margin-top: 14px; padding: 14px; border-radius: 8px; }
.photo-box.purple { background: #f5f3ff; border: 1px solid #ddd6fe; }
.photo-box.green  { background: #ecfdf5; border: 1px solid #a7f3d0; }
.photo-label { font-size: 12px; font-weight: 600; margin-bottom: 8px; color: #1a2332; }
.photo-box img { width: 100%; border-radius: 6px; }
</style>