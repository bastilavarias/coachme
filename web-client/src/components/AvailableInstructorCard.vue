<script setup lang="ts">
import { Card, CardContent, CardFooter } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Avatar, AvatarImage, AvatarFallback } from '@/components/ui/avatar';
import { Badge } from '@/components/ui/badge';
import { useRouter } from 'vue-router';
import { useMisc } from '@/composables/misc.ts';

const props = defineProps({
    user: {
        type: Object,
        required: true,
    },
});

const router = useRouter();
const { toImageURL } = useMisc();

const onBook = () => {
    router.push({
        name: 'appointment-calendar-page',
        params: { userID: props.user.id },
    });
};
</script>

<template>
    <Card class="pt-6">
        <CardContent>
            <div class="flex items-center justify-between space-x-4">
                <div class="flex items-center space-x-4">
                    <div class="flex items-center space-x-4">
                        <Avatar>
                            <AvatarImage
                                :src="toImageURL(user.image)"
                                :alt="user.name"
                            />
                            <AvatarFallback>{{
                                user.name.split('')[0]
                            }}</AvatarFallback>
                        </Avatar>
                        <div>
                            <p
                                class="text-sm font-medium leading-none capitalize"
                            >
                                {{ user.name }}
                            </p>
                            <p
                                class="text-sm text-muted-foreground capitalize"
                                v-if="user.profile && user.profile.occupation"
                            >
                                {{ user.profile.occupation }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </CardContent>
        <CardContent
            class="space-y-4"
            v-if="user.profile || user.services.length > 0"
        >
            <p
                class="text-muted-foreground text-sm"
                v-if="user.profile && user.profile.bio"
            >
                {{ user.profile.bio }}
            </p>
            <div class="flex gap-2" v-if="user.services.length > 0">
                <template
                    v-for="(service, index) in user.services"
                    :key="index"
                >
                    <Badge variant="secondary" class="capitalize">{{
                        service.title
                    }}</Badge>
                </template>
            </div>
        </CardContent>
        <CardFooter class="flex justify-between px-6 space-x-2 pb-6 pt-5">
            <span></span>
            <div class="flex justify-end space-x-2">
                <Button @click="onBook">Book</Button>
            </div>
        </CardFooter>
    </Card>
</template>
