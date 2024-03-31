<script setup lang="ts">
import {
    Card,
    CardContent,
    CardDescription,
    CardFooter,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Button } from '@/components/ui/button';
import { ExclamationTriangleIcon } from '@radix-icons/vue';
import { ref } from 'vue';
import { useAuthStore } from '@/stores/auth.ts';
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';
import { useToast } from '@/components/ui/toast/use-toast';
import { useRouter } from 'vue-router';
import BaseLoadingSpinner from '@/components/base/BaseLoadingSpinner.vue';
import GithubOuthButton from '@/components/GithubOuthButton.vue';

const authStore = useAuthStore();
const { toast } = useToast();
const router = useRouter();

const defaultForm = {
    email: null,
    password: null,
};
const form = ref(Object.assign({}, defaultForm));
const error = ref(null);
const loading = ref(false);

const onFormSubmit = async () => {
    loading.value = true;
    const result = await authStore.login(form.value);
    if (result.success) {
        toast({
            title: 'Account login completed.',
            description:
                'Congratulations! Your account is now authenticated. You will be redirected to the dashboard shortly.',
        });
        await router.push({ name: 'bookings-page' });

        return;
    }
    loading.value = false;
    error.value = result.message;
};
</script>

<template>
    <div class="max-w-full h-full flex flex-row items-center justify-center">
        <div class="basis-1/4">
            <Card>
                <CardHeader>
                    <CardTitle class="text-2xl font-bold">COACHME</CardTitle>
                    <CardDescription>Login to your account</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="grid items-center w-full gap-4">
                        <template v-if="!!error">
                            <Alert variant="destructive">
                                <ExclamationTriangleIcon class="w-4 h-4" />
                                <AlertTitle>Request Error</AlertTitle>
                                <AlertDescription>
                                    {{ error }}
                                </AlertDescription>
                            </Alert>
                        </template>
                        <div class="flex flex-col space-y-1.5">
                            <Label for="email">Email</Label>
                            <Input
                                id="email"
                                placeholder="m@example.com"
                                v-model="form.email"
                            />
                        </div>
                        <div class="flex flex-col space-y-1.5">
                            <Label for="password">Password</Label>
                            <Input
                                type="password"
                                id="password"
                                v-model="form.password"
                            />
                        </div>
                    </div>
                </CardContent>
                <CardFooter class="flex flex-col space-y-4">
                    <Button
                        class="w-full"
                        :disabled="loading"
                        @click="onFormSubmit"
                    >
                        <template v-if="loading">
                            <BaseLoadingSpinner class="mr-2 w-4 h-4" />
                        </template>
                        Login
                    </Button>
                    <div class="w-full">
                        <div class="relative">
                            <div class="absolute inset-0 flex items-center">
                                <span class="w-full border-t" />
                            </div>
                            <div
                                class="relative flex justify-center text-xs uppercase"
                            >
                                <span
                                    class="bg-background px-2 text-muted-foreground"
                                >
                                    Or continue with
                                </span>
                            </div>
                        </div>
                    </div>
                    <GithubOuthButton />
                </CardFooter>
                <CardFooter class="flex flex-col">
                    <div class="text-sm text-muted-foreground">
                        Don't have an account yet?
                        <router-link :to="{ name: 'register-page' }"
                            ><span class="text-black underline"
                                >Create an account</span
                            ></router-link
                        >
                    </div>
                </CardFooter>
            </Card>
        </div>
    </div>
</template>
