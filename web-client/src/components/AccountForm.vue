<script setup lang="ts">
import { Label } from '@/components/ui/label';
import { Input } from '@/components/ui/input';
import {
    Card,
    CardHeader,
    CardDescription,
    CardContent,
    CardFooter,
} from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { useAuthStore } from '@/stores/auth.ts';
import { ref } from 'vue';
import Textarea from '@/components/ui/textarea/Textarea.vue';
import { useDatetime } from '@/composables/datetime.ts';
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';
import { ExclamationTriangleIcon } from '@radix-icons/vue';
import { useToast } from '@/components/ui/toast';
import { useUserStore } from '@/stores/user.ts';
import BaseLoadingSpinner from '@/components/base/BaseLoadingSpinner.vue';

const { user } = useAuthStore();
const { formatDate } = useDatetime();
const userStore = useUserStore();
const { toast } = useToast();

const form = ref(
    Object.assign(
        {},
        {
            user_id: user.id,
            name: user.name,
            email: user.email,
            mobile_number: user.profile?.mobile_number ?? null,
            occupation: user.profile?.occupation ?? null,
            bio: user.profile?.bio,
            image: null,
        }
    )
);
const error = ref(null);
const loading = ref(false);

const onFileInputChange = (event: Element) => {
    const file = event.target.files[0];
    if (file) {
        form.value.image = file;
    }
};
const onAccountFormSubmit = async () => {
    loading.value = true;
    const result = await userStore.update(form.value);
    loading.value = false;
    if (result.success) {
        toast({
            title: 'Account updated.',
            description: 'Congratulations! You updated your account.',
        });
        error.value = null;
        return;
    }
    error.value = result.message;
};
</script>

<template>
    <div class="grid grid-cols-1 gap-">
        <div>
            <form @submit.prevent="onAccountFormSubmit">
                <Card>
                    <CardHeader>
                        <CardDescription>General</CardDescription>
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
                                <Label for="name">Name</Label>
                                <Input id="name" v-model="form.name" />
                            </div>
                            <div class="flex flex-col space-y-1.5">
                                <Label for="email">Email</Label>
                                <Input
                                    id="email"
                                    placeholder="m@example.com"
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
                                <Label for="mobile_number">Mobile No.</Label>
                                <Input
                                    id="mobile_number"
                                    placeholder="09922249952"
                                    v-model="form.mobile_number"
                                />
                            </div>
                            <template v-if="user.level === 'instructor'">
                                <div class="flex flex-col space-y-1.5">
                                    <Label for="occupation">Occupation</Label>
                                    <Input
                                        id="occupation"
                                        placeholder="Eg. Web Developer"
                                        v-model="form.occupation"
                                    />
                                </div>
                                <div class="flex flex-col space-y-1.5">
                                    <Label for="bio">Bio</Label>
                                    <Textarea id="bio" v-model="form.bio" />
                                </div>
                            </template>
                        </div>
                    </CardContent>
                    <CardFooter
                        class="flex justify-between px-6 space-x-2 pb-6 pt-5"
                    >
                        <span class="text-xs text-muted-foreground"
                            >Updated
                            {{ formatDate(user.updated_at, 'distance') }}</span
                        >
                        <Button type="submit" :disabled="loading">
                            <template v-if="loading">
                                <BaseLoadingSpinner class="mr-2 w-4 h-4" />
                            </template>
                            Save
                        </Button>
                    </CardFooter>
                </Card>
            </form>
        </div>
        <!--
        <div>
            <Card>
                <CardHeader>
                    <CardDescription>Reset password</CardDescription>
                </CardHeader>
                <CardContent>
                    <form>
                        <div class="grid items-center w-full gap-4">
                            <div class="flex flex-col space-y-1.5">
                                <Label for="password">Password</Label>
                                <Input id="password" />
                            </div>
                            <div class="flex flex-col space-y-1.5">
                                <Label for="password_confirmation"
                                    >Password Confirmation</Label
                                >
                                <Input id="password_confirmation" />
                            </div>
                        </div>
                    </form>
                </CardContent>
                <CardFooter
                    class="flex justify-between px-6 space-x-2 pb-6 pt-5"
                >
                    <span class="text-xs text-muted-foreground"
                        >Updated 3hrs ago</span
                    >
                    <Button>Change</Button>
                </CardFooter>
            </Card>
        </div>

-->
    </div>
</template>
