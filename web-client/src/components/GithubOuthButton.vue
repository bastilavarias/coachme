<script setup lang="ts">
// https://github.com/login/oauth/authorize?client_id=
import { GithubLogoIcon } from "@radix-icons/vue";
import { Button } from "@/components/ui/button";
import BaseLoadingSpinner from "@/components/base/BaseLoadingSpinner.vue";
import { ref } from "vue";

const loading = ref(false);

const onClick = () => {
  loading.value = true;
  const clientID = import.meta.env.VITE_GITHUB_OAUTH_CLIENT_ID;
  window.location.href = `https://github.com/login/oauth/authorize?client_id=${clientID}`;
};
</script>

<template>
  <Button variant="outline" class="w-full" @click="onClick" :disabled="loading">
    <template v-if="loading">
      <BaseLoadingSpinner class="mr-2 w-4 h-4" />
    </template>
    <GithubLogoIcon class="mr-2" />
    GitHub
  </Button>
</template>
