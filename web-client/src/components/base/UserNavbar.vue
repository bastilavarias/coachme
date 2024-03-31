<script setup lang="ts">
import { useRouter } from 'vue-router';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { useAuthStore } from '@/stores/auth.ts';
import { useMisc } from '@/composables/misc.ts';

const router = useRouter();
const authStore = useAuthStore();
const { toImageURL } = useMisc();

const onLogout = () => {
    authStore.disableAuth();
    authStore.removeAuth();
    router.push({ name: 'login-page' });
    authStore.logout();
};
</script>

<template>
    <header
        class="absolute w-full z-40 top-0 bg-background/80 backdrop-blur-lg border-b border-border"
    >
        <div
            class="container flex justify-between h-14 max-w-screen-2xl items-center"
        >
            <div class="flex-1"></div>
            <nav class="flex items-center space-x-2">
                <DropdownMenu>
                    <DropdownMenuTrigger as-child>
                        <Button
                            variant="ghost"
                            class="relative h-8 w-8 rounded-full"
                        >
                            <Avatar class="h-9 w-9">
                                <AvatarImage
                                    :src="toImageURL(authStore.user.image)"
                                    :alt="authStore.user.name"
                                />
                                <AvatarFallback class="text-xl">{{
                                    authStore.user.name.split('')[0]
                                }}</AvatarFallback>
                            </Avatar>
                        </Button>
                    </DropdownMenuTrigger>
                    <DropdownMenuContent class="w-56" align="end">
                        <DropdownMenuLabel class="font-normal flex">
                            <div class="flex flex-col space-y-1">
                                <p class="text-sm font-medium leading-none">
                                    {{ authStore.user.name }}
                                </p>
                                <p
                                    class="text-xs leading-none text-muted-foreground"
                                    v-if="authStore.user.email"
                                >
                                    {{ authStore.user.email }}
                                </p>
                            </div>
                        </DropdownMenuLabel>
                        <DropdownMenuSeparator />
                        <DropdownMenuItem @click="onLogout">
                            Log out
                        </DropdownMenuItem>
                    </DropdownMenuContent>
                </DropdownMenu>
            </nav>
        </div>
    </header>
</template>
