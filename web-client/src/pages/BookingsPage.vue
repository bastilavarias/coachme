<script setup lang="ts">
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import BookingCard from '@/components/BookingCard.vue';
import {
    ListAppointmentsPayload,
    useAppointmentStore,
} from '@/stores/appointment.ts';
import { computed, ref, watch } from 'vue';
import { useToast } from '@/components/ui/toast';
import { useAuthStore } from '@/stores/auth.ts';

const tabs = [
    {
        text: 'Pending',
        value: 'pending',
    },
    {
        text: 'Upcoming',
        value: 'upcoming',
    },
    {
        text: 'For review',
        value: 'for_review',
    },
    {
        text: 'Past',
        value: 'past',
    },
    {
        text: 'Cancelled',
        value: 'cancelled',
    },
];

const appointmentStore = useAppointmentStore();
const { toast } = useToast();

const loading = ref(false);
const appointments = ref([]);
const tomorrowAppointments = ref([]);
const tab = ref('pending');

const noAppointmentsMessage = computed(() => {
    const message = {
        pending: 'You have no new appointments.',
        upcoming: 'You have no upcoming appointments.',
        for_review: 'You have no for review appointments.',
        past: 'You have no past appointments.',
        cancelled: 'You have no cancelled appointments.',
    };

    return message[tab.value];
});

watch(
    () => tab.value,
    async (value) => {
        if (value) {
            await listAppointments();
            tomorrowAppointments.value = [];
            if (value === 'upcoming') {
                await listAppointments({ tomorrow: true });
            }
        }
    }
);
const listAppointments = async (extra?: { tomorrow?: boolean }) => {
    loading.value = true;
    appointments.value = [];
    tomorrowAppointments.value = [];
    const hasTomorrow = extra && extra.tomorrow;
    const payload: ListAppointmentsPayload = {
        status: tab.value,
        tomorrow: hasTomorrow,
    };
    const result = await appointmentStore.list(payload);
    if (result.success) {
        if (hasTomorrow) {
            tomorrowAppointments.value = result.data;
        } else {
            appointments.value = result.data;
        }
        loading.value = false;
        return;
    }
    toast({
        variant: 'destructive',
        title: 'Server error.',
        description: result.message,
    });
};
const onSelectTab = ({ value }) => {
    tab.value = value;
};

listAppointments();
</script>

<template>
    <div class="container space-y-4">
        <div>
            <h3 class="text-3xl font-bold">Bookings</h3>
            <p class="text-muted-foreground">See your upcoming appointments</p>
        </div>
        <div>
            <Tabs default-value="pending">
                <TabsList class="space-x-2 mb-5">
                    <template v-for="(tab, index) in tabs" :key="index">
                        <TabsTrigger
                            :value="tab.value"
                            class="py-2 min-w-32"
                            @click="onSelectTab(tab)"
                        >
                            {{ tab.text }}
                        </TabsTrigger>
                    </template>
                </TabsList>

                <template v-if="loading">
                    <div class="flex flex-col items-center">
                        <img
                            class="w-auto h-40"
                            src="/nyan-cat.gif"
                            alt="Auth GIF"
                        />
                    </div>
                </template>
                <template v-else>
                    <div class="p-2">
                        <template
                            v-if="
                                [...appointments, ...tomorrowAppointments]
                                    .length === 0
                            "
                        >
                            <div
                                class="min-h-56 w-full flex items-center justify-center"
                            >
                                <p class="text-xs text-muted-foreground">
                                    {{ noAppointmentsMessage }}
                                </p>
                            </div>
                        </template>
                        <div class="min-h-56 flex items-center justify-center">
                            <TabsContent
                                class="h-full w-full space-y-8"
                                :value="tab"
                            >
                                <template v-if="tab === 'upcoming'">
                                    <div class="space-y-4">
                                        <div
                                            v-if="
                                                appointments.length > 0 &&
                                                tomorrowAppointments.length > 0
                                            "
                                        >
                                            <h6 class="text-md">Today</h6>
                                        </div>
                                        <div
                                            class="h-full w-full grid grid-cols-3 gap-2"
                                        >
                                            <template
                                                v-for="(
                                                    appointment, index
                                                ) in appointments"
                                                :key="index"
                                            >
                                                <div class="col-span-1 w-full">
                                                    <BookingCard
                                                        :appointment="
                                                            appointment
                                                        "
                                                        @onLoadList="
                                                            listAppointments
                                                        "
                                                    />
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                    <template
                                        v-if="tomorrowAppointments.length > 0"
                                    >
                                        <div class="space-y-4">
                                            <div>
                                                <h6 class="text-md">
                                                    Tomorrow
                                                </h6>
                                            </div>
                                            <div
                                                class="h-full w-full grid grid-cols-3 gap-2"
                                            >
                                                <template
                                                    v-for="(
                                                        appointment, index
                                                    ) in tomorrowAppointments"
                                                    :key="index"
                                                >
                                                    <div
                                                        class="col-span-1 w-full"
                                                    >
                                                        <BookingCard
                                                            :appointment="
                                                                appointment
                                                            "
                                                            @onLoadList="
                                                                listAppointments
                                                            "
                                                        />
                                                    </div>
                                                </template>
                                            </div>
                                        </div>
                                    </template>
                                </template>
                                <template v-else>
                                    <div
                                        class="h-full w-full grid grid-cols-3 gap-3"
                                    >
                                        <template
                                            v-for="(
                                                appointment, index
                                            ) in appointments"
                                            :key="index"
                                        >
                                            <div class="col-span-1 w-full">
                                                <BookingCard
                                                    :appointment="appointment"
                                                    @onLoadList="
                                                        listAppointments
                                                    "
                                                />
                                            </div>
                                        </template>
                                    </div>
                                </template>
                            </TabsContent>
                        </div>
                    </div>
                </template>
            </Tabs>
        </div>
    </div>
</template>
