<template>
  <div class="app-layout">

    <!-- Sidebar -->
    <aside class="sidebar">
      <div class="logo">
        <div class="brand">HALCÓN</div>
        <div class="logo-sub">Admin Panel</div>
      </div>

      <nav class="nav">
        <div class="nav-section">Principal</div>
        <Link :href="route('dashboard')" class="nav-item" :class="{ active: isRoute('dashboard') }">
          <span class="nav-icon">▦</span> Dashboard
        </Link>

        <div class="nav-section">Pedidos</div>
        <Link :href="route('orders.index')" class="nav-item" :class="{ active: isRoute('orders.index') }">
          <span class="nav-icon">📋</span> Todos los pedidos
          <span class="nav-badge">{{ $page.props.ordersCount }}</span>
        </Link>
        <Link :href="route('orders.deleted')" class="nav-item" :class="{ active: isRoute('orders.deleted') }">
          <span class="nav-icon">🗑</span> Eliminados
        </Link>

        <template v-if="isAdmin">
          <div class="nav-section">Administración</div>
          <Link :href="route('users.index')" class="nav-item" :class="{ active: isRoute('users.index') }">
            <span class="nav-icon">👥</span> Usuarios
          </Link>
        </template>
      </nav>

      <div class="user-area">
        <div class="user-name">{{ user.name }}</div>
        <div class="user-role">{{ roleLabel }}</div>
        <button @click="logout" class="logout-btn">Cerrar sesión</button>
      </div>
    </aside>

    <!-- Main -->
    <div class="main">
      <div class="topbar">
        <div class="page-title">{{ title }}</div>
        <div class="topbar-right">
          <slot name="actions" />
        </div>
      </div>
      <div class="content">
        <slot />
      </div>
    </div>

  </div>
</template>

<script setup>
import { computed } from 'vue'
import { Link, router, usePage } from '@inertiajs/vue3'

defineProps({ title: { type: String, default: '' } })

const page = usePage()
const user = computed(() => page.props.auth.user)
const isAdmin = computed(() => user.value.role?.toLowerCase() === 'admin')

const roles = {
  admin: 'Administrador', sales: 'Ventas',
  warehouse: 'Almacén', route: 'Ruta', purchasing: 'Compras'
}
const roleLabel = computed(() => roles[user.value.role] || user.value.role)

const isRoute = (name) => route().current(name)

const logout = () => router.post(route('logout'))
</script>

<style scoped>
.app-layout { display: flex; height: 100vh; overflow: hidden; font-family: 'Segoe UI', sans-serif; }
.sidebar { width: 220px; background: #1a2332; display: flex; flex-direction: column; flex-shrink: 0; }
.logo { padding: 20px 16px; border-bottom: 1px solid rgba(255,255,255,.1); }
.brand { font-size: 22px; font-weight: 800; color: #f4a024; letter-spacing: 2px; }
.logo-sub { font-size: 10px; color: rgba(255,255,255,.4); letter-spacing: 2px; text-transform: uppercase; margin-top: 2px; }
.nav { padding: 12px 0; flex: 1; overflow-y: auto; }
.nav-section { padding: 10px 16px 4px; font-size: 10px; letter-spacing: 2px; text-transform: uppercase; color: rgba(255,255,255,.3); font-weight: 600; }
.nav-item { display: flex; align-items: center; gap: 8px; padding: 9px 16px; cursor: pointer; color: rgba(255,255,255,.6); font-size: 13px; font-weight: 500; border-left: 3px solid transparent; text-decoration: none; transition: all .15s; }
.nav-item:hover { background: rgba(255,255,255,.06); color: #fff; }
.nav-item.active { background: rgba(244,160,36,.12); color: #f4a024; border-left-color: #f4a024; }
.nav-badge { background: #f4a024; color: #fff; border-radius: 10px; padding: 1px 7px; font-size: 10px; font-weight: 700; margin-left: auto; }
.user-area { padding: 14px 16px; border-top: 1px solid rgba(255,255,255,.1); }
.user-name { font-size: 13px; color: #fff; font-weight: 500; }
.user-role { font-size: 11px; color: rgba(255,255,255,.4); margin-top: 1px; }
.logout-btn { margin-top: 10px; width: 100%; padding: 7px; background: rgba(255,255,255,.06); border: 1px solid rgba(255,255,255,.12); border-radius: 6px; color: rgba(255,255,255,.5); font-size: 12px; cursor: pointer; }
.logout-btn:hover { background: rgba(255,255,255,.1); color: #fff; }
.main { flex: 1; display: flex; flex-direction: column; overflow: hidden; background: #e8eff8; }
.topbar { background: #fff; border-bottom: 1px solid #dde4ef; padding: 0 28px; height: 56px; display: flex; align-items: center; justify-content: space-between; flex-shrink: 0; }
.page-title { font-size: 18px; font-weight: 700; color: #1a2332; letter-spacing: .3px; }
.content { flex: 1; overflow-y: auto; padding: 24px 28px; }
</style>