<script setup lang="ts">
import Button from "@/components/ui/button/Button.vue";
import { useRouter } from "vue-router";

const router = useRouter();
</script>

<template>
  <header
    class="sticky z-40 top-0 bg-background/80 backdrop-blur-lg border-b border-border"
  >
    <div
      class="container flex justify-between h-14 max-w-screen-2xl items-center"
    >
      <nav class="flex items-center space-x-6">
        <router-link :to="{ name: 'home-page' }">
          <span class="text-2xl font-bold decoration-0 tracking-tight"
            >COACHME</span
          >
        </router-link>
      </nav>
      <nav class="flex items-center space-x-2">
        <Button variant="outline" @click="router.push({ name: 'login-page' })"
          >Login</Button
        >
        <Button @click="router.push({ name: 'register-page' })"
          >Start now</Button
        >
      </nav>
    </div>
  </header>
</template>
