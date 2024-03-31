<script setup lang="ts">
import BaseSidebar from '@/components/base/Sidebar.vue';
import BaseUserNavbar from '@/components/base/UserNavbar.vue';
import { useAuthStore } from '@/stores/auth.ts';

const authStore = useAuthStore();
</script>

<template>
    <div class="grid lg:grid-cols-5 h-screen">
        <BaseSidebar class="hidden lg:block h-full" />
        <main class="bg-main col-span-3 lg:col-span-4 lg:border-l relative">
            <template v-if="authStore.isAuthenticated && authStore.user">
                <BaseUserNavbar />
            </template>
            <div class="h-full pt-36">
                <RouterView />
            </div>
        </main>
    </div>
</template>

<style scoped>
.bg-main {
    background-color: #f2f5f9;
}
</style>
