<template>
  <div class="modal-overlay" @click.self="$emit('close')">
    <div class="modal">
      <div class="modal-header">
        <h3>{{ order ? 'Editar pedido' : 'Nuevo pedido' }}</h3>
        <button @click="$emit('close')" class="close-btn">✕</button>
      </div>
      <div class="modal-body">
        <div class="form-grid">
          <div class="form-group">
            <label>N° Factura</label>
            <input v-model="form.invoice" :readonly="!!order" :class="{ readonly: !!order }" />
            <span class="err">{{ form.errors.invoice }}</span>
          </div>
          <div class="form-group">
            <label>N° Cliente</label>
            <input v-model="form.client_num" />
            <span class="err">{{ form.errors.client_num }}</span>
          </div>
          <div class="form-group full">
            <label>Nombre / Razón Social</label>
            <input v-model="form.client_name" />
            <span class="err">{{ form.errors.client_name }}</span>
          </div>
          <div class="form-group">
            <label>RFC</label>
            <input v-model="form.rfc" />
          </div>
          <div class="form-group">
            <label>Teléfono</label>
            <input v-model="form.phone" />
          </div>
          <div class="form-group">
            <label>Fecha y hora</label>
            <input v-model="form.order_date" type="datetime-local" />
            <span class="err">{{ form.errors.order_date }}</span>
          </div>
          <div class="form-group full">
            <label>Dirección de entrega</label>
            <input v-model="form.address" />
            <span class="err">{{ form.errors.address }}</span>
          </div>
          <div class="form-group full">
            <label>Notas</label>
            <textarea v-model="form.notes" rows="3"></textarea>
          </div>
        </div>
        <div class="form-actions">
          <button @click="$emit('close')" class="btn-secondary">Cancelar</button>
          <button @click="save" :disabled="form.processing" class="btn-primary">
            {{ form.processing ? 'Guardando...' : 'Guardar pedido' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'
import { useToast } from 'vue-toastification'

const props = defineProps({ order: { type: Object, default: null } })
const emit  = defineEmits(['close', 'saved'])
const toast = useToast()

const now = new Date().toISOString().slice(0,16)

const form = useForm({
  invoice:    props.order?.invoice    ?? '',
  client_num: props.order?.client_num ?? '',
  client_name:props.order?.client_name?? '',
  rfc:        props.order?.rfc        ?? '',
  phone:      props.order?.phone      ?? '',
  order_date: props.order?.order_date ?? now,
  address:    props.order?.address    ?? '',
  notes:      props.order?.notes      ?? '',
})

const save = () => {
  if (props.order) {
    form.put(route('orders.update', props.order.id), {
      onSuccess: () => { toast.success('Pedido actualizado'); emit('saved'); emit('close') },
    })
  } else {
    form.post(route('orders.store'), {
      onSuccess: () => { toast.success('Pedido creado exitosamente'); emit('saved'); emit('close') },
    })
  }
}
</script>

<style scoped>
.modal-overlay { position: fixed; inset: 0; background: rgba(20,30,50,.55); display: flex; align-items: center; justify-content: center; z-index: 100; }
.modal { background: #fff; border-radius: 12px; width: min(600px,96vw); max-height: 90vh; overflow-y: auto; }
.modal-header { padding: 20px 24px 0; display: flex; justify-content: space-between; align-items: center; }
.modal-header h3 { font-size: 16px; font-weight: 600; color: #1a2332; }
.close-btn { width: 28px; height: 28px; border: none; background: #f0f4fa; border-radius: 6px; cursor: pointer; font-size: 14px; color: #6b7a99; }
.modal-body { padding: 20px 24px 24px; }
.form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
.form-group { display: flex; flex-direction: column; gap: 5px; }
.form-group.full { grid-column: 1 / -1; }
label { font-size: 11px; font-weight: 600; color: #6b7a99; text-transform: uppercase; letter-spacing: .5px; }
input, select, textarea { padding: 9px 12px; border: 1px solid #dde4ef; border-radius: 6px; font-size: 13px; color: #1a2332; outline: none; width: 100%; font-family: inherit; }
input:focus, textarea:focus { border-color: #93aedb; }
input.readonly { background: #f5f7fc; }
textarea { resize: vertical; }
.err { font-size: 11px; color: #ef4444; }
.form-actions { display: flex; justify-content: flex-end; gap: 10px; margin-top: 20px; }
.btn-primary { padding: 9px 20px; background: #f4a024; color: #fff; border: none; border-radius: 6px; font-size: 13px; font-weight: 600; cursor: pointer; }
.btn-primary:disabled { opacity: .6; }
.btn-secondary { padding: 9px 16px; background: #f0f4fa; color: #1a2332; border: 1px solid #dde4ef; border-radius: 6px; font-size: 13px; cursor: pointer; }
</style>