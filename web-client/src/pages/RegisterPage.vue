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
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
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
    name: null,
    email: null,
    password: null,
    password_confirmation: null,
    level: null,
    image: null,
};
const form = ref(Object.assign({}, defaultForm));
const error = ref(null);
const loading = ref(false);

const onFileInputChange = (event: Element) => {
    const file = event.target.files[0];
    if (file) {
        form.value.image = file;
    }
};
const onFormSubmit = async () => {
    loading.value = true;
    const result = await authStore.register(form.value);
    if (result.success) {
        toast({
            title: 'Account registration completed.',
            description:
                'Congratulations! Your account is now registered. You will be redirected to the dashboard shortly.',
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
            <form @submit.prevent="onFormSubmit">
                <Card>
                    <CardHeader>
                        <CardTitle class="text-2xl font-bold"
                            >COACHME</CardTitle
                        >
                        <CardDescription>Register your account</CardDescription>
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
                                <Label for="account-type">Account type</Label>
                                <Select v-model="form.level">
                                    <SelectTrigger id="account-type">
                                        <SelectValue placeholder="Select" />
                                    </SelectTrigger>
                                    <SelectContent position="popper">
                                        <SelectItem value="instructor"
                                            >Instructor</SelectItem
                                        >
                                        <SelectItem value="student"
                                            >Student</SelectItem
                                        >
                                    </SelectContent>
                                </Select>
                            </div>
                            <div class="flex flex-col space-y-1.5">
                                <Label for="name">Name</Label>
                                <Input id="name" v-model="form.name" />
                            </div>
                            <div class="flex flex-col space-y-1.5">
                                <Label for="email">Email</Label>
                                <Input
                                    id="email"
                                    placeholder="m@example.com"
                                    type="email"
                                    v-model="form.email"
                                />
                            </div>
                            <div class="flex flex-col space-y-1.5">
                                <Label for="picture">Picture</Label>
                                <Input
                                    id="picture"
                                    type="file"
                                    accept="image/png, image/jpg, image/jpeg"
                                    @change="onFileInputChange"
                                />
                            </div>
                            <div class="flex flex-col space-y-1.5">
                                <Label for="password">Password</Label>
                                <Input
                                    id="password"
                                    type="password"
                                    v-model="form.password"
                                />
                            </div>
                            <div class="flex flex-col space-y-1.5">
                                <Label for="password_confirmation"
                                    >Password Confirmation</Label
                                >
                                <Input
                                    id="password_confirmation"
                                    type="password"
                                    v-model="form.password_confirmation"
                                />
                            </div>
                        </div>
                    </CardContent>
                    <CardFooter class="flex flex-col space-y-4">
                        <Button
                            class="w-full"
                            type="submit"
                            :disabled="loading"
                        >
                            <template v-if="loading">
                                <BaseLoadingSpinner class="mr-2 w-4 h-4" />
                            </template>
                            Register
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
                                        Or register via
                                    </span>
                                </div>
                            </div>
                        </div>
                        <GithubOuthButton />
                    </CardFooter>
                    <CardFooter class="flex flex-col">
                        <div class="text-sm text-muted-foreground">
                            Already have an account?
                            <router-link :to="{ name: 'login-page' }"
                                ><span class="text-black underline"
                                    >Login</span
                                ></router-link
                            >
                        </div>
                    </CardFooter>
                </Card>
            </form>
        </div>
    </div>
</template>
