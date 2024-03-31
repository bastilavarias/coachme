<script setup lang="ts">
import BaseAppbar from "@/components/base/Appbar.vue";
</script>

<template>
  <div class="relative">
    <BaseAppbar />
    <main class="bg-main h-screen relative bg-background">
      <RouterView />
    </main>
  </div>
</template>

<style scoped>
.bg-main {
  background-color: #f2f5f9;
}
</style>
