<template>
  <AppLayout title="Usuarios del sistema">
    <template #actions>
      <button @click="openCreate" class="btn-primary">+ Nuevo usuario</button>
    </template>

    <div class="card">
      <table>
        <thead>
          <tr><th>Nombre</th><th>Usuario</th><th>Rol</th><th>Email</th><th>Acciones</th></tr>
        </thead>
        <tbody>
          <tr v-for="user in users" :key="user.id">
            <td class="bold">{{ user.name }}</td>
            <td><code class="code-tag">{{ user.username }}</code></td>
            <td><RoleBadge :role="user.role" /></td>
            <td class="muted">{{ user.email }}</td>
            <td>
              <div class="actions">
                <button @click="editUser(user)" class="icon-btn">✏️</button>
                <button v-if="user.id !== currentUser.id" @click="deleteUser(user)" class="icon-btn danger">🗑</button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <UserForm v-if="showForm" :user="editing" @close="showForm = false" @saved="router.reload()" />
  </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { router, usePage } from '@inertiajs/vue3'
import { useToast } from 'vue-toastification'
import AppLayout from '../Layouts/AppLayout.vue'
import RoleBadge from '../../Components/RoleBadge.vue'
import UserForm from './Partials/UserForm.vue'

defineProps({ users: Array })
const toast = useToast()
const page  = usePage()
const currentUser = computed(() => page.props.auth.user)

const showForm = ref(false)
const editing  = ref(null)

const openCreate = () => { editing.value = null; showForm.value = true }
const editUser   = (u) => { editing.value = u;   showForm.value = true }

const deleteUser = (user) => {
  if (confirm(`¿Eliminar al usuario ${user.name}?`)) {
    router.delete(route('users.destroy', user.id), {
      onSuccess: () => toast.success('Usuario eliminado'),
    })
  }
}
</script>

<style scoped>
.btn-primary { padding: 8px 18px; background: #f4a024; color: #fff; border: none; border-radius: 6px; font-size: 13px; font-weight: 600; cursor: pointer; }
.card { background: #fff; border-radius: 10px; border: 1px solid #dde4ef; overflow: hidden; }
table { width: 100%; border-collapse: collapse; }
thead th { background: #f5f7fc; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: .8px; color: #6b7a99; padding: 10px 14px; text-align: left; border-bottom: 1px solid #dde4ef; }
tbody td { padding: 12px 14px; font-size: 13px; border-bottom: 1px solid #f0f4fa; vertical-align: middle; }
tbody tr:hover { background: #fafbfe; }
.bold { font-weight: 600; }
.muted { color: #6b7a99; font-size: 12px; }
.code-tag { font-size: 12px; background: #f0f4fa; padding: 2px 7px; border-radius: 4px; }
.actions { display: flex; gap: 6px; }
.icon-btn { width: 28px; height: 28px; border: 1px solid #dde4ef; border-radius: 5px; background: #fff; cursor: pointer; font-size: 13px; }
.icon-btn.danger:hover { background: #fef2f2; }
</style>