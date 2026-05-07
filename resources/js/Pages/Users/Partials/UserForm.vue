<template>
  <form @submit.prevent="save">
    <div class="form-grid">
      <div class="fg full">
        <label class="lbl">Nombre completo</label>
        <input v-model="form.name" required class="inp" />
      </div>
      <div class="fg">
        <label class="lbl">Correo electrónico</label>
        <input v-model="form.email" type="email" required class="inp" />
      </div>
      <div class="fg">
        <label class="lbl">Rol / Departamento</label>
        <select v-model="form.role" required class="inp">
          <option value="Admin">Administrador</option>
          <option value="Sales">Ventas</option>
          <option value="Warehouse">Almacén</option>
          <option value="Route">Ruta</option>
          <option value="Purchasing">Compras</option>
        </select>
      </div>
      <div class="fg full">
        <label class="lbl">Contraseña {{ user ? '(dejar vacío = no cambiar)' : '' }}</label>
        <input v-model="form.password" type="password" :required="!user" placeholder="••••••••" class="inp" />
      </div>
    </div>
    <div class="flex justify-end gap-3 mt-5">
      <button type="button" @click="$emit('cancel')" class="btn-secondary">Cancelar</button>
      <button type="submit" :disabled="form.processing" class="btn-primary">
        {{ form.processing ? 'Guardando...' : 'Guardar usuario' }}
      </button>
    </div>
  </form>
</template>

<script setup>
import { watch } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { useToast } from 'vue-toastification'

const props = defineProps({ user: Object })
const emit  = defineEmits(['saved', 'cancel'])
const toast = useToast()

const form = useForm({ name: '', email: '', role: 'Sales', password: '' })

watch(() => props.user, u => {
  if (u) { form.name = u.name; form.email = u.email; form.role = u.role; form.password = '' }
  else   { form.name = ''; form.email = ''; form.role = 'Sales'; form.password = '' }
}, { immediate: true })

function save() {
  if (props.user?.id) {
    form.put(route('users.update', props.user.id), {
      onSuccess: () => emit('saved', 'Usuario actualizado.')
    })
  } else {
    form.post(route('users.store'), {
      onSuccess: () => emit('saved', 'Usuario creado.')
    })
  }
}
</script>

<style scoped>
.form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
.fg { display: flex; flex-direction: column; gap: 4px; }
.fg.full { grid-column: 1/-1; }
.lbl { font-size: 11px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: .5px; }
.inp { padding: 9px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 13px; outline: none; width: 100%; }
.inp:focus { border-color: #f59e0b; box-shadow: 0 0 0 3px rgba(245,158,11,.1); }
.btn-primary { padding: 9px 20px; background: #f4a024; color: #fff; border: none; border-radius: 6px; font-size: 13px; font-weight: 600; cursor: pointer; }
.btn-primary:disabled { opacity: .6; cursor: not-allowed; }
.btn-secondary { padding: 9px 20px; background: #f3f4f6; color: #374151; border: 1px solid #d1d5db; border-radius: 6px; font-size: 13px; cursor: pointer; }
</style>