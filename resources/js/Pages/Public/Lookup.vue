<template>
  <div class="lookup-bg">
    <div class="lookup-card">
      <div class="brand">HALCÓN</div>
      <div class="subtitle">Distribución de Materiales</div>

      <h2 class="form-title">Consulta tu pedido</h2>

      <div v-if="$page.props.flash?.error || lookup_error" class="alert-error">
        {{ $page.props.flash?.error || lookup_error }}
      </div>

      <!-- Resultado -->
      <div v-if="result" class="result-box">
        <div class="result-icon">{{ statusIcon(result.status) }}</div>
        <div class="result-status">{{ statusLabel(result.status) }}</div>
        <div class="result-client">{{ result.client_name }}</div>
        <div class="result-invoice">Factura: {{ result.invoice }}</div>
        <div v-if="result.status === 'Delivered' && result.delivery_photo" class="photo-box">
          <p class="photo-label">Evidencia de entrega</p>
          <img :src="result.delivery_photo" alt="Evidencia" />
        </div>
        <button class="btn-secondary" @click="resetForm">Nueva consulta</button>
      </div>

      <!-- Formulario -->
      <form v-else @submit.prevent="submit">
        <div class="form-group">
          <label>Número de factura</label>
          <input v-model="form.invoice" placeholder="Ej. FAC-00001" />
        </div>
        <div class="form-group">
          <label>Número de cliente</label>
          <input v-model="form.client_num" placeholder="Ej. CLI-001" />
        </div>
        <button type="submit" :disabled="form.processing" class="btn-primary">
          {{ form.processing ? 'Consultando...' : 'Consultar estado' }}
        </button>
      </form>

      <div class="login-link">
        ¿Eres empleado? <a :href="route('login')">Iniciar sesión</a>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useForm, usePage } from '@inertiajs/vue3'

const page = usePage()

const props = defineProps({
  lookup_result: Object,
  lookup_error:  String,
})

const result = computed(() => props.lookup_result ?? null)

const form = useForm({ invoice: '', client_num: '' })

const submit = () => {
  form.post(route('lookup.check'))
}

const resetForm = () => {
  form.reset()
}

const statusLabel = (s) => ({
  'Ordered':    'Solicitado',
  'In Process': 'En proceso',
  'In Route':   'En ruta',
  'Delivered':  'Entregado',
}[s] ?? s)

const statusIcon = (s) => ({
  'Ordered':    '📋',
  'In Process': '⚙️',
  'In Route':   '🚛',
  'Delivered':  '✅',
}[s] ?? '📦')
</script>

<style scoped>
* { box-sizing: border-box; margin: 0; padding: 0; }
.lookup-bg {
  min-height: 100vh;
  background: linear-gradient(135deg, #1a2332 0%, #2d3f57 100%);
  display: flex; align-items: center; justify-content: center;
  font-family: 'Segoe UI', sans-serif;
}
.lookup-card {
  background: #fff; border-radius: 14px; padding: 40px;
  width: 420px; box-shadow: 0 20px 60px rgba(0,0,0,.3);
}
.brand { font-size: 32px; font-weight: 800; color: #f4a024; letter-spacing: 2px; text-align: center; }
.subtitle { font-size: 11px; color: #6b7a99; letter-spacing: 2px; text-transform: uppercase; text-align: center; margin-top: 2px; }
.form-title { font-size: 15px; font-weight: 600; color: #1a2332; margin: 20px 0 16px; text-align: center; }
.alert-error { background: #fef2f2; color: #b91c1c; border: 1px solid #fca5a5; border-radius: 6px; padding: 10px 14px; font-size: 13px; margin-bottom: 14px; }
.form-group { display: flex; flex-direction: column; gap: 5px; margin-bottom: 14px; }
label { font-size: 11px; font-weight: 600; color: #6b7a99; text-transform: uppercase; letter-spacing: .5px; }
input { padding: 10px 12px; border: 1px solid #dde4ef; border-radius: 6px; font-size: 13px; color: #1a2332; outline: none; width: 100%; }
input:focus { border-color: #93aedb; box-shadow: 0 0 0 3px rgba(59,130,246,.1); }
.btn-primary { width: 100%; padding: 11px; background: #f4a024; color: #fff; border: none; border-radius: 6px; font-size: 14px; font-weight: 600; cursor: pointer; }
.btn-primary:hover { background: #e09020; }
.btn-primary:disabled { opacity: .6; cursor: not-allowed; }
.btn-secondary { width: 100%; padding: 9px; background: #f0f4fa; color: #1a2332; border: 1px solid #dde4ef; border-radius: 6px; font-size: 13px; cursor: pointer; margin-top: 12px; }
.login-link { text-align: center; margin-top: 16px; font-size: 12px; color: #6b7a99; }
.login-link a { color: #1a6db5; text-decoration: none; font-weight: 500; }
.result-box { text-align: center; padding: 10px 0; }
.result-icon { font-size: 48px; margin-bottom: 8px; }
.result-status { display: inline-block; padding: 4px 14px; border-radius: 20px; background: #ecfdf5; color: #047857; font-size: 13px; font-weight: 600; margin-bottom: 10px; }
.result-client { font-size: 16px; font-weight: 600; color: #1a2332; }
.result-invoice { font-size: 12px; color: #6b7a99; margin-top: 4px; }
.photo-box { margin-top: 16px; }
.photo-label { font-size: 11px; font-weight: 600; color: #6b7a99; text-transform: uppercase; letter-spacing: .5px; margin-bottom: 8px; }
.photo-box img { width: 100%; border-radius: 8px; border: 1px solid #dde4ef; }
</style>