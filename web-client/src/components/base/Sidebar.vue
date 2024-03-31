<script setup lang="ts">
import { cn } from '@/lib/utils';
import { Button } from '@/components/ui/button';
import {
    CalendarIcon,
    BookmarkIcon,
    GearIcon,
    BackpackIcon,
} from '@radix-icons/vue';
import { useRouter } from 'vue-router';
import { onMounted, ref } from 'vue';
import ServicesTable from '@/components/ServicesTable.vue';
import AvailabilityScheduler from '@/components/AvailabilityScheduler.vue';
import { useAuthStore } from '@/stores/auth.ts';

const router = useRouter();
const { user } = useAuthStore();

const navs = ref([
    {
        name: 'Bookings',
        icon: CalendarIcon,
        route: {
            name: 'bookings-page',
        },
    },

    {
        name: 'Settings',
        icon: GearIcon,
        route: {
            name: 'settings-page',
        },
    },
]);

onMounted(() => {
    if (user.level === 'student') {
        navs.value = [
            {
                name: 'Bookings',
                icon: CalendarIcon,
                route: {
                    name: 'bookings-page',
                },
            },
            {
                name: 'Book an appointment',
                icon: BookmarkIcon,
                route: {
                    name: 'appointment-page',
                },
            },

            {
                name: 'Settings',
                icon: GearIcon,
                route: {
                    name: 'settings-page',
                },
            },
        ];
    } else {
        navs.value = [
            {
                name: 'Bookings',
                icon: CalendarIcon,
                route: {
                    name: 'bookings-page',
                },
            },
            {
                name: 'Settings',
                icon: GearIcon,
                route: {
                    name: 'settings-page',
                },
            },
        ];
    }
});
</script>

<template>
    <div :class="cn('pb-12', $attrs.class ?? '')">
        <div class="space-y-4 py-4">
            <div class="px-3 py-2">
                <h2
                    class="mb-5 px-4 text-2xl font-semibold tracking-tight font-bold"
                >
                    COACHME
                </h2>
                <div class="space-y-1">
                    <template v-for="(nav, index) in navs" :key="index">
                        <Button
                            variant="ghost"
                            class="w-full justify-start"
                            @click="router.push(nav.route)"
                        >
                            <Component :is="nav.icon" class="mr-1" />
                            {{ nav.name }}
                        </Button>
                    </template>
                </div>
            </div>
        </div>
    </div>
</template>
