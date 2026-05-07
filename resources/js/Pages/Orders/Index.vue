<template>
  <AppLayout title="Todos los pedidos">
    <template #actions>
      <button v-if="canCreate" @click="openCreate"
        class="inline-flex items-center px-4 py-2 bg-amber-500 text-white text-sm font-semibold rounded-md hover:bg-amber-600 transition">
        + Nuevo pedido
      </button>
    </template>

    <!-- Filtros -->
    <div class="mb-5 flex flex-wrap gap-3 bg-white p-4 rounded-lg shadow-sm border border-gray-100">
      <input v-model="filters.invoice"    placeholder="N° Factura"  @input="search" class="filter-input" />
      <input v-model="filters.client_num" placeholder="N° Cliente"  @input="search" class="filter-input" />
      <input v-model="filters.date"       type="date"               @change="search" class="filter-input" />
      <select v-model="filters.status"    @change="search" class="filter-input">
        <option value="">Todos los estados</option>
        <option value="Ordered">Solicitado</option>
        <option value="In Process">En proceso</option>
        <option value="In Route">En ruta</option>
        <option value="Delivered">Entregado</option>
      </select>
      <button @click="clearFilters" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 text-sm">Limpiar</button>
    </div>

    <!-- Tabla -->
    <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200">
      <div v-if="orders.data.length === 0" class="p-12 text-center">
        <div class="text-4xl mb-2">📋</div>
        <p class="text-gray-500">No hay pedidos que mostrar</p>
      </div>
      <table v-else class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="th">Factura</th>
            <th class="th">Razón social</th>
            <th class="th">N° Cliente</th>
            <th class="th">Fecha</th>
            <th class="th">Estado</th>
            <th class="th text-right">Acciones</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="order in orders.data" :key="order.id" class="hover:bg-gray-50 transition">
            <td class="td font-bold text-amber-600">{{ order.num_factura }}</td>
            <td class="td text-sm text-gray-700">{{ order.razon_social }}</td>
            <td class="td">
              <span class="px-2 py-1 bg-gray-100 text-gray-600 rounded text-xs font-mono">{{ order.num_cliente }}</span>
            </td>
            <td class="td text-sm text-gray-500">{{ formatDate(order.fecha_hora) }}</td>
            <td class="td"><StatusBadge :status="order.status" /></td>
            <td class="td text-right">
              <div class="flex justify-end gap-1">
                <button @click="viewOrder(order)"   class="icon-btn" title="Ver">👁</button>
                <button v-if="canEdit" @click="editOrder(order)"   class="icon-btn" title="Editar">✏️</button>
                <button @click="changeStatus(order)" class="icon-btn" title="Cambiar estado">🔄</button>
                <button v-if="canEdit" @click="confirmDelete(order)" class="icon-btn" title="Eliminar">🗑</button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Paginación -->
    <div class="mt-4 flex justify-center gap-1">
      <template v-for="link in orders.links" :key="link.label">
        <Link v-if="link.url" :href="link.url"
          class="px-3 py-1 border rounded text-sm transition"
          :class="link.active ? 'bg-amber-500 text-white border-amber-500' : 'bg-white text-gray-700 hover:bg-gray-50'"
          v-html="link.label" />
        <span v-else class="px-3 py-1 border rounded text-sm text-gray-400 bg-gray-50 cursor-not-allowed" v-html="link.label" />
      </template>
    </div>

    <!-- ─── Modal: Ver pedido ─── -->
    <div v-if="showView" class="modal-overlay" @click.self="showView = false">
      <div class="modal">
        <div class="modal-header">
          <span class="modal-title">Detalle del pedido</span>
          <button @click="showView = false" class="modal-close">✕</button>
        </div>
        <div class="modal-body" v-if="selected">
          <!-- Timeline -->
          <div class="flex mb-5">
            <div v-for="(s, i) in steps" :key="i" class="flex-1 text-center relative">
              <div class="step-circle" :class="stepClass(s.key)">{{ i+1 }}</div>
              <div class="text-xs text-gray-500 mt-1">{{ s.label }}</div>
              <div v-if="i < steps.length-1" class="step-line"></div>
            </div>
          </div>
          <!-- Datos -->
          <div class="detail-grid">
            <div class="detail-item"><div class="dl">Factura</div><div class="dv">{{ selected.num_factura }}</div></div>
            <div class="detail-item"><div class="dl">N° Cliente</div><div class="dv">{{ selected.num_cliente }}</div></div>
            <div class="detail-item full"><div class="dl">Razón social</div><div class="dv">{{ selected.razon_social }}</div></div>
            <div class="detail-item full"><div class="dl">Dirección</div><div class="dv">{{ selected.direccion_entrega }}</div></div>
            <div class="detail-item full" v-if="selected.datos_fiscales"><div class="dl">Datos fiscales</div><div class="dv">{{ selected.datos_fiscales }}</div></div>
            <div class="detail-item full" v-if="selected.notas"><div class="dl">Notas</div><div class="dv">{{ selected.notas }}</div></div>
          </div>
          <!-- Fotos -->
          <div v-if="selected.delivery_photo" class="mt-4">
            <div class="dl mb-2">Evidencia de entrega</div>
            <img :src="selected.delivery_photo" class="w-full rounded-lg border" />
          </div>
        </div>
      </div>
    </div>

    <!-- ─── Modal: Crear/Editar pedido ─── -->
    <div v-if="showForm" class="modal-overlay" @click.self="showForm = false">
      <div class="modal">
        <div class="modal-header">
          <span class="modal-title">{{ editing ? 'Editar pedido' : 'Nuevo pedido' }}</span>
          <button @click="showForm = false" class="modal-close">✕</button>
        </div>
        <div class="modal-body">
          <form @submit.prevent="saveOrder">
            <div class="form-grid">
              <div class="fg full"><label class="lbl">Razón social</label><input v-model="form.razon_social" required class="inp" /></div>
              <div class="fg"><label class="lbl">N° Cliente</label><input v-model="form.num_cliente" required class="inp" /></div>
              <div class="fg"><label class="lbl">Fecha y hora</label><input v-model="form.fecha_hora" type="datetime-local" required class="inp" /></div>
              <div class="fg full"><label class="lbl">Dirección de entrega</label><input v-model="form.direccion_entrega" required class="inp" /></div>
              <div class="fg full"><label class="lbl">Datos fiscales</label><textarea v-model="form.datos_fiscales" rows="2" class="inp"></textarea></div>
              <div class="fg full"><label class="lbl">Notas</label><textarea v-model="form.notas" rows="2" class="inp"></textarea></div>
            </div>
            <div class="flex justify-end gap-3 mt-5">
              <button type="button" @click="showForm = false" class="btn-secondary">Cancelar</button>
              <button type="submit" :disabled="form.processing" class="btn-primary">{{ form.processing ? 'Guardando...' : 'Guardar' }}</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- ─── Modal: Cambiar estado ─── -->
    <div v-if="showStatus" class="modal-overlay" @click.self="showStatus = false">
      <div class="modal" style="max-width:420px">
        <div class="modal-header">
          <span class="modal-title">Actualizar estado</span>
          <button @click="showStatus = false" class="modal-close">✕</button>
        </div>
        <div class="modal-body" v-if="selected">
          <div class="fg mb-4"><label class="lbl">Estado</label>
            <select v-model="statusForm.status" class="inp">
              <option value="Ordered">Solicitado</option>
              <option value="In Process">En proceso</option>
              <option value="In Route">En ruta</option>
              <option value="Delivered">Entregado</option>
            </select>
          </div>
          <!-- Subir fotos (solo ruta/admin) -->
          <template v-if="canUploadPhoto">
            <div class="fg mb-3">
              <label class="lbl">Foto unidad cargada</label>
              <input type="file" accept="image/*" @change="e => routePhoto = e.target.files[0]" class="inp" />
            </div>
            <div class="fg mb-4">
              <label class="lbl">Evidencia de entrega</label>
              <input type="file" accept="image/*" @change="e => deliveryPhoto = e.target.files[0]" class="inp" />
            </div>
          </template>
          <div class="flex justify-end gap-3">
            <button @click="showStatus = false" class="btn-secondary">Cancelar</button>
            <button @click="saveStatus" :disabled="statusForm.processing" class="btn-primary">Guardar</button>
          </div>
        </div>
      </div>
    </div>

    <!-- ─── Confirm eliminar ─── -->
    <div v-if="showConfirm" class="modal-overlay" @click.self="showConfirm = false">
      <div class="modal" style="max-width:360px;text-align:center">
        <div class="modal-body">
          <div class="text-3xl mb-3">🗑</div>
          <div class="font-semibold text-gray-800 mb-1">¿Eliminar pedido?</div>
          <div class="text-sm text-gray-500 mb-5">Se moverá a la papelera y podrá restaurarse.</div>
          <div class="flex justify-center gap-3">
            <button @click="showConfirm = false" class="btn-secondary">Cancelar</button>
            <button @click="doDelete" class="btn-danger">Eliminar</button>
          </div>
        </div>
      </div>
    </div>

  </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Link, useForm, router, usePage } from '@inertiajs/vue3'
import { useToast } from 'vue-toastification'
import AppLayout  from '../Layouts/AppLayout.vue'
import StatusBadge from '../../Components/StatusBadge.vue'

const props = defineProps({ orders: Object, filters: Object })
const page  = usePage()
const toast = useToast()

const user = computed(() => page.props.auth.user)
const role = computed(() => user.value.role?.toLowerCase())
const canCreate = computed(() => ['admin','sales'].includes(role.value))
const canEdit   = computed(() => ['admin','sales'].includes(role.value))
const canUploadPhoto = computed(() => ['admin','route'].includes(role.value))

// Filtros
const filters = ref({ invoice: props.filters?.invoice || '', client_num: props.filters?.client_num || '', date: props.filters?.date || '', status: props.filters?.status || '' })
const search = () => router.get(route('orders.index'), filters.value, { preserveState: true, replace: true })
const clearFilters = () => { filters.value = { invoice:'', client_num:'', date:'', status:'' }; search() }

// Estado modales
const showView    = ref(false)
const showForm    = ref(false)
const showStatus  = ref(false)
const showConfirm = ref(false)
const selected    = ref(null)
const editing     = ref(false)
let   toDeleteId  = null

// Formulario pedido
const form = useForm({ razon_social:'', num_cliente:'', fecha_hora:'', direccion_entrega:'', datos_fiscales:'', notas:'' })

function openCreate() { editing.value = false; form.reset(); showForm.value = true }
function editOrder(o)  { editing.value = true; selected.value = o; form.razon_social = o.razon_social; form.num_cliente = o.num_cliente; form.fecha_hora = o.fecha_hora?.slice(0,16); form.direccion_entrega = o.direccion_entrega; form.datos_fiscales = o.datos_fiscales; form.notas = o.notas; showForm.value = true }
function viewOrder(o)  { selected.value = o; showView.value = true }

function saveOrder() {
  if (editing.value) {
    form.put(route('orders.update', selected.value.id), {
      onSuccess: () => { showForm.value = false; toast.success('Pedido actualizado.') }
    })
  } else {
    form.post(route('orders.store'), {
      onSuccess: () => { showForm.value = false; toast.success('Pedido creado.') }
    })
  }
}

// Estado
const statusForm   = useForm({ status: '' })
const routePhoto   = ref(null)
const deliveryPhoto= ref(null)

function changeStatus(o) { selected.value = o; statusForm.status = o.status; routePhoto.value = null; deliveryPhoto.value = null; showStatus.value = true }

function saveStatus() {
  statusForm.patch(route('orders.status', selected.value.id), {
    onSuccess: async () => {
      if (routePhoto.value)   await uploadPhoto('route',    routePhoto.value)
      if (deliveryPhoto.value) await uploadPhoto('delivery', deliveryPhoto.value)
      showStatus.value = false
      toast.success('Estado actualizado.')
    }
  })
}

function uploadPhoto(type, file) {
  const fd = new FormData()
  fd.append('type', type)
  fd.append('photo', file)
  return fetch(route('orders.photo', selected.value.id), {
    method: 'POST',
    headers: { 'X-CSRF-TOKEN': page.props.csrf_token ?? document.querySelector('meta[name=csrf-token]')?.content },
    body: fd
  })
}

// Eliminar
function confirmDelete(o) { toDeleteId = o.id; showConfirm.value = true }
function doDelete() {
  router.delete(route('orders.destroy', toDeleteId), {
    onSuccess: () => { showConfirm.value = false; toast.info('Pedido eliminado.') }
  })
}

// Timeline
const steps = [
  { key: 'Ordered',    label: 'Solicitado' },
  { key: 'In Process', label: 'En proceso' },
  { key: 'In Route',   label: 'En ruta'    },
  { key: 'Delivered',  label: 'Entregado'  },
]
const statusOrder = ['Ordered','In Process','In Route','Delivered']
function stepClass(key) {
  const ci = statusOrder.indexOf(selected.value?.status)
  const ki = statusOrder.indexOf(key)
  if (ki < ci)  return 'step-done'
  if (ki === ci) return 'step-active'
  return 'step-pending'
}

const formatDate = (d) => d ? new Date(d).toLocaleDateString('es-MX', { day:'2-digit', month:'short', year:'numeric' }) : '—'
</script>

<style scoped>
.filter-input { padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 13px; outline: none; }
.filter-input:focus { border-color: #f59e0b; }
.th { padding: 10px 16px; text-align: left; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: .8px; color: #6b7280; }
.td { padding: 12px 16px; font-size: 13px; color: #1a2332; vertical-align: middle; }
.icon-btn { width: 28px; height: 28px; border: 1px solid #e5e7eb; border-radius: 5px; background: #fff; cursor: pointer; font-size: 13px; }
.icon-btn:hover { background: #f3f4f6; }
/* Modal */
.modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,.5); display: flex; align-items: center; justify-content: center; z-index: 100; }
.modal { background: #fff; border-radius: 12px; width: min(620px, 96vw); max-height: 90vh; overflow-y: auto; box-shadow: 0 20px 60px rgba(0,0,0,.2); }
.modal-header { padding: 18px 24px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #f0f0f0; }
.modal-title { font-size: 16px; font-weight: 600; color: #1a2332; }
.modal-close { width: 28px; height: 28px; border: none; background: #f3f4f6; border-radius: 6px; cursor: pointer; font-size: 15px; color: #6b7280; }
.modal-body { padding: 20px 24px 24px; }
/* Form */
.form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
.fg { display: flex; flex-direction: column; gap: 4px; }
.fg.full { grid-column: 1/-1; }
.lbl { font-size: 11px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: .5px; }
.inp { padding: 9px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 13px; outline: none; width: 100%; }
.inp:focus { border-color: #f59e0b; box-shadow: 0 0 0 3px rgba(245,158,11,.1); }
/* Buttons */
.btn-primary { padding: 9px 20px; background: #f4a024; color: #fff; border: none; border-radius: 6px; font-size: 13px; font-weight: 600; cursor: pointer; }
.btn-primary:disabled { opacity: .6; cursor: not-allowed; }
.btn-secondary { padding: 9px 20px; background: #f3f4f6; color: #374151; border: 1px solid #d1d5db; border-radius: 6px; font-size: 13px; cursor: pointer; }
.btn-danger { padding: 9px 20px; background: #fee2e2; color: #b91c1c; border: 1px solid #fca5a5; border-radius: 6px; font-size: 13px; cursor: pointer; }
/* Detail */
.detail-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
.detail-item { background: #f8fafd; border-radius: 6px; padding: 10px 12px; }
.detail-item.full { grid-column: 1/-1; }
.dl { font-size: 10px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: .5px; margin-bottom: 3px; }
.dv { font-size: 13px; color: #1a2332; font-weight: 500; }
/* Timeline */
.step-circle { width: 26px; height: 26px; border-radius: 50%; margin: 0 auto 4px; display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 700; position: relative; z-index: 1; }
.step-active  { background: #f4a024; color: #fff; }
.step-done    { background: #10b981; color: #fff; }
.step-pending { background: #e5e7eb; color: #9ca3af; }
.step-line { position: absolute; top: 13px; left: 50%; right: -50%; height: 2px; background: #e5e7eb; z-index: 0; }
</style>