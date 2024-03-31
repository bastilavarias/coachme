<script setup lang="ts">
import { useRoute, useRouter } from "vue-router";
import { useAuthStore } from "@/stores/auth.ts";
import { computed, ref } from "vue";
import { useToast } from "@/components/ui/toast";
import {
  Dialog,
  DialogContent,
  DialogFooter,
  DialogHeader,
  DialogTitle,
} from "@/components/ui/dialog";
import { ExclamationTriangleIcon, GithubLogoIcon } from "@radix-icons/vue";
import { RadioGroup, RadioGroupItem } from "@/components/ui/radio-group";
import { Label } from "@/components/ui/label";
import { Button } from "@/components/ui/button";
import BaseLoadingSpinner from "@/components/base/BaseLoadingSpinner.vue";
import { Alert, AlertDescription, AlertTitle } from "@/components/ui/alert";

const router = useRouter();
const route = useRoute();
const authStore = useAuthStore();
const { toast } = useToast();

const loading = ref(false);
const isDialogOpen = ref(true);
const level = ref("student");
const error = ref(null);

const isFormValid = computed(() => {
  return !!route.query.code && level.value;
});

const onOAuthentication = async () => {
  loading.value = true;
  const payload = {
    code: route.query.code,
    level: level.value,
    provider: "github",
  };
  const result = await authStore.oAuthentication(payload);
  if (result.success) {
    toast({
      title: "Account login completed.",
      description:
        "Congratulations! Your account credentials are valid. You will be redirected to the dashboard shortly.",
    });
    await router.push({ name: "bookings-page" });

    return;
  }
  loading.value = false;
  error.value = result.message;
};
</script>

<template>
  <div class="h-screen flex flex-col justify-center items-center">
    <div class="flex flex-col items-center">
      <img class="w-auto h-40" src="/nyan-cat.gif" alt="Auth GIF" />
    </div>

    <Dialog :open="isDialogOpen">
      <DialogContent class="sm:max-w-[425px]">
        <DialogHeader>
          <DialogTitle class="flex items-center">
            <GithubLogoIcon class="mr-2 w-5 h-5" /> GitHub OAuth
          </DialogTitle>
        </DialogHeader>
        <template v-if="!!error">
          <Alert variant="destructive">
            <ExclamationTriangleIcon class="w-4 h-4" />
            <AlertTitle>Request Error</AlertTitle>
            <AlertDescription>
              {{ error }}
            </AlertDescription>
          </Alert>
        </template>

        <RadioGroup :default-value="level" v-model="level">
          <Label class="font-medium mb-1">Select account type:</Label>
          <div class="flex items-center space-x-2">
            <RadioGroupItem id="r1" value="student" />
            <Label for="r1">Student</Label>
          </div>
          <div class="flex items-center space-x-2">
            <RadioGroupItem id="r2" value="instructor" />
            <Label for="r2">Instructor</Label>
          </div>
        </RadioGroup>
        <DialogFooter>
          <Button
            :disabled="!isFormValid || loading"
            @click="onOAuthentication"
          >
            <template v-if="loading">
              <BaseLoadingSpinner class="mr-2 w-4 h-4" />
            </template>
            Continue
          </Button>
        </DialogFooter>
      </DialogContent>
    </Dialog>
  </div>
</template>
