<template>
  <div class="login-bg">
    <div class="login-card">
      <div class="brand">HALCÓN</div>
      <div class="subtitle">Panel Administrativo</div>

      <h2 class="form-title">Acceso a empleados</h2>

      <div v-if="errors.email" class="alert-error">{{ errors.email }}</div>

      <form @submit.prevent="submit">
        <div class="form-group">
          <label>Correo electrónico</label>
          <input v-model="form.email" type="email" placeholder="correo@halcon.mx" autofocus />
        </div>

        <div class="form-group">
          <label>Contraseña</label>
          <input v-model="form.password" type="password" placeholder="••••••••" />
          <span v-if="errors.password" class="error-msg">{{ errors.password }}</span>
        </div>

        <button type="submit" :disabled="form.processing" class="btn-primary">
          {{ form.processing ? 'Entrando...' : 'Iniciar sesión' }}
        </button>
      </form>

      <div class="back-link">
        <a :href="route('lookup')">← Consulta de pedidos</a>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'

defineProps({ errors: { type: Object, default: () => ({}) } })

const form = useForm({
  email:    '',
  password: '',
})

const submit = () => {
  form.post(route('login'))
}
</script>

<style scoped>
* { box-sizing: border-box; margin: 0; padding: 0; }
.login-bg {
  min-height: 100vh;
  background: linear-gradient(135deg, #1a2332 0%, #2d3f57 100%);
  display: flex; align-items: center; justify-content: center;
  font-family: 'Segoe UI', sans-serif;
}
.login-card {
  background: #fff; border-radius: 14px; padding: 40px;
  width: 380px; box-shadow: 0 20px 60px rgba(0,0,0,.3);
}
.brand { font-size: 32px; font-weight: 800; color: #f4a024; letter-spacing: 2px; text-align: center; }
.subtitle { font-size: 11px; color: #6b7a99; letter-spacing: 2px; text-transform: uppercase; text-align: center; margin-top: 2px; }
.form-title { font-size: 15px; font-weight: 600; color: #1a2332; margin: 24px 0 18px; text-align: center; }
.alert-error { background: #fef2f2; color: #b91c1c; border: 1px solid #fca5a5; border-radius: 6px; padding: 10px 14px; font-size: 13px; margin-bottom: 14px; }
.form-group { display: flex; flex-direction: column; gap: 5px; margin-bottom: 14px; }
label { font-size: 11px; font-weight: 600; color: #6b7a99; text-transform: uppercase; letter-spacing: .5px; }
input { padding: 10px 12px; border: 1px solid #dde4ef; border-radius: 6px; font-size: 13px; color: #1a2332; outline: none; width: 100%; }
input:focus { border-color: #93aedb; box-shadow: 0 0 0 3px rgba(59,130,246,.1); }
.error-msg { color: #b91c1c; font-size: 12px; }
.btn-primary { width: 100%; padding: 11px; background: #f4a024; color: #fff; border: none; border-radius: 6px; font-size: 14px; font-weight: 600; cursor: pointer; margin-top: 4px; }
.btn-primary:hover { background: #e09020; }
.btn-primary:disabled { opacity: .6; cursor: not-allowed; }
.back-link { text-align: center; margin-top: 16px; font-size: 12px; }
.back-link a { color: #6b7a99; text-decoration: none; }
.back-link a:hover { color: #1a2332; }
</style>