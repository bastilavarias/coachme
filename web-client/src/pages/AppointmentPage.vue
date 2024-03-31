<script setup lang="ts">
import { Input } from "@/components/ui/input";
import { MagnifyingGlassIcon } from "@radix-icons/vue";
import AvailableInstructorCard from "@/components/AvailableInstructorCard.vue";
import { ref, watch } from "vue";
import { ListUserPayload, useUserStore } from "@/stores/user.ts";
import { useMisc } from "@/composables/misc.ts";

const userStore = useUserStore();
const { debounce } = useMisc();

const loading = ref(false);
const instructors = ref([]);
const search = ref(null);

watch(
  () => search.value,
  debounce(async () => {
    await getInstructors();
  }, 500)
);

const getInstructors = async () => {
  loading.value = true;
  const payload: ListUserPayload = {
    level: "instructor",
    search: search.value || null,
  };
  const result = await userStore.list(payload);
  if (result.success) {
    instructors.value = result.data;
    loading.value = false;
    return;
  }
};

getInstructors();
</script>

<template>
  <div class="container space-y-6">
    <div>
      <h3 class="text-3xl font-bold">Available Coaches</h3>
      <p class="text-muted-foreground">Find the right coach for you</p>
    </div>
    <form class="flex justify-center">
      <div class="relative w-3/5">
        <MagnifyingGlassIcon
          class="absolute left-2 top-2.5 size-4 text-muted-foreground"
        />
        <Input placeholder="Search" class="pl-8 bg-white" v-model="search" />
      </div>
    </form>

    <template v-if="loading">
      <div class="flex flex-col items-center">
        <img class="w-auto h-40" src="/nyan-cat.gif" alt="Auth GIF" />
      </div>
    </template>
    <template v-else>
      <div class="h-full w-full grid grid-cols-3 gap-2">
        <template v-for="instructor in instructors" :key="instructor.id">
          <div class="col-span-1 w-full">
            <AvailableInstructorCard :user="instructor" />
          </div>
        </template>
      </div>
    </template>
  </div>
</template>
