<template>
  <div class="modal-overlay" @click.self="$emit('close')">
    <div class="modal">
      <div class="modal-header">
        <h3>Actualizar estado — {{ order.invoice }}</h3>
        <button @click="$emit('close')" class="close-btn">✕</button>
      </div>
      <div class="modal-body">

        <!-- Timeline -->
        <div class="timeline">
          <div v-for="(s, i) in steps" :key="s.key" class="t-step">
            <div class="t-circle" :class="stepClass(s.key)">
              {{ stepDone(s.key) ? '✓' : i + 1 }}
            </div>
            <div class="t-label">{{ s.label }}</div>
          </div>
        </div>

        <div class="form-group" style="margin-bottom: 16px">
          <label>Cambiar estado</label>
          <select v-model="form.status">
            <option v-for="s in steps" :key="s.key" :value="s.key">{{ s.label }}</option>
          </select>
        </div>

        <!-- Fotos — solo para Ruta y Admin -->
        <template v-if="canUploadPhotos">
          <div class="form-group" style="margin-bottom: 12px">
            <label>Foto de unidad cargada</label>
            <input type="file" accept="image/*" @change="e => routePhoto = e.target.files[0]" />
          </div>
          <div class="form-group" style="margin-bottom: 16px">
            <label>Evidencia de entrega</label>
            <input type="file" accept="image/*" @change="e => deliveryPhoto = e.target.files[0]" />
          </div>
        </template>

        <div class="form-actions">
          <button @click="$emit('close')" class="btn-secondary">Cancelar</button>
          <button @click="save" class="btn-primary">Actualizar</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { router, usePage } from '@inertiajs/vue3'
import { useToast } from 'vue-toastification'

const props = defineProps({ order: Object })
const emit  = defineEmits(['close', 'saved'])
const toast = useToast()
const page  = usePage()

const steps = [
  { key: 'Ordered',    label: 'Solicitado' },
  { key: 'In Process', label: 'En proceso' },
  { key: 'In Route',   label: 'En ruta'    },
  { key: 'Delivered',  label: 'Entregado'  },
]

const form = ref({ status: props.order.status })
const routePhoto    = ref(null)
const deliveryPhoto = ref(null)

const canUploadPhotos = computed(() =>
  ['admin','route'].includes(page.props.auth.user.role)
)

const stepIndex  = (key) => steps.findIndex(s => s.key === key)
const currentIdx = computed(() => stepIndex(props.order.status))
const stepDone   = (key) => stepIndex(key) < currentIdx.value
const stepClass  = (key) => {
  const i = stepIndex(key)
  if (i < currentIdx.value) return 't-done'
  if (key === props.order.status) return 't-active'
  return 't-pending'
}

const save = () => {
  const data = new FormData()
  data.append('status', form.value.status)
  data.append('_method', 'PATCH')
  if (routePhoto.value)    data.append('route_photo',    routePhoto.value)
  if (deliveryPhoto.value) data.append('delivery_photo', deliveryPhoto.value)

  router.post(route('orders.status', props.order.id), data, {
    onSuccess: () => {
      toast.success('Estado actualizado correctamente')
      emit('saved')
      emit('close')
    },
  })
}
</script>

<style scoped>
.modal-overlay { position: fixed; inset: 0; background: rgba(20,30,50,.55); display: flex; align-items: center; justify-content: center; z-index: 100; }
.modal { background: #fff; border-radius: 12px; width: min(500px,96vw); max-height: 90vh; overflow-y: auto; }
.modal-header { padding: 20px 24px 0; display: flex; justify-content: space-between; align-items: center; }
.modal-header h3 { font-size: 15px; font-weight: 600; color: #1a2332; }
.close-btn { width: 28px; height: 28px; border: none; background: #f0f4fa; border-radius: 6px; cursor: pointer; }
.modal-body { padding: 20px 24px 24px; }
.timeline { display: flex; margin-bottom: 20px; }
.t-step { flex: 1; text-align: center; position: relative; }
.t-step::before { content: ''; position: absolute; top: 13px; left: 50%; right: -50%; height: 2px; background: #dde4ef; z-index: 0; }
.t-step:last-child::before { display: none; }
.t-circle { width: 26px; height: 26px; border-radius: 50%; margin: 0 auto 6px; display: flex; align-items: center; justify-content: center; position: relative; z-index: 1; font-size: 11px; font-weight: 700; }
.t-done    { background: #10b981; color: #fff; }
.t-active  { background: #f4a024; color: #fff; }
.t-pending { background: #e8eff8; color: #9ab; }
.t-label { font-size: 9px; font-weight: 600; color: #6b7a99; text-transform: uppercase; letter-spacing: .4px; }
.form-group { display: flex; flex-direction: column; gap: 5px; }
label { font-size: 11px; font-weight: 600; color: #6b7a99; text-transform: uppercase; letter-spacing: .5px; }
select, input[type=file] { padding: 9px 12px; border: 1px solid #dde4ef; border-radius: 6px; font-size: 13px; outline: none; width: 100%; }
.form-actions { display: flex; justify-content: flex-end; gap: 10px; margin-top: 20px; }
.btn-primary { padding: 9px 20px; background: #f4a024; color: #fff; border: none; border-radius: 6px; font-size: 13px; font-weight: 600; cursor: pointer; }
.btn-secondary { padding: 9px 16px; background: #f0f4fa; color: #1a2332; border: 1px solid #dde4ef; border-radius: 6px; font-size: 13px; cursor: pointer; }
</style>