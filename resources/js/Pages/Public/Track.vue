<script setup>
defineProps({
  order: Object
})
</script>

<template>
  <div class="max-w-md mx-auto mt-10">

    <div v-if="order" class="bg-green-100 p-4 rounded">
      <h2 class="text-xl font-bold">Estado del pedido:</h2>
      <p class="text-2xl text-green-600">{{ order.status }}</p>
    </div>

    <div v-else class="text-red-500">
      Pedido no encontrado
    </div>

  </div>
</template>