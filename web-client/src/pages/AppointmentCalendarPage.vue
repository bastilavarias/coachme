<script setup lang="ts">
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import {
    StarIcon,
    DoubleArrowLeftIcon,
    ExclamationTriangleIcon,
} from '@radix-icons/vue';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { useRoute, useRouter } from 'vue-router';
import { Calendar } from '@/components/ui/calendar';
import { Badge } from '@/components/ui/badge/';
import { computed, ref, watch } from 'vue';
import { GetOneUserPayload, useUserStore } from '@/stores/user.ts';
import { useDatetime } from '@/composables/datetime.ts';
import BaseLoadingSpinner from '@/components/base/BaseLoadingSpinner.vue';
import { useToast } from '@/components/ui/toast';
import { useAppointmentStore } from '@/stores/appointment.ts';
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';
import { Input } from '@/components/ui/input';
import {
    ListAvailabilityRequest,
    useAvailabilityStore,
} from '@/stores/availability.ts';
import { useMisc } from '@/composables/misc.ts';
import {
    Dialog,
    DialogContent,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';

const router = useRouter();
const route = useRoute();
const userStore = useUserStore();
const { formatDate, formatTime } = useDatetime();
const { toast } = useToast();
const appointmentStore = useAppointmentStore();
const availabilityStore = useAvailabilityStore();
const { toImageURL } = useMisc();

const dataLoading = ref(true);
const user = ref(null);
const defaultForm = {
    service_id: null,
    availability_id: null,
    date: null,
    instructor_id: null,
    meeting_url: null,
};
const form = ref(Object.assign({}, defaultForm));
const formError = ref(null);
const formLoading = ref(false);
const minimumDate = ref(new Date());
const availabilities = ref([]);
const isAvailabilitiesLoaded = ref(false);
const hasAvailabilities = ref(false);
const availabilitiesLoading = ref(false);
const isReviewsDialogOpen = ref(false);

const isFormValid = computed(() => {
    const { service_id, availability_id, date } = form.value;
    return service_id && availability_id && date;
});

watch(
    () => form.value.date,
    async () => {
        await getAvailabilities();
    }
);

const getUser = async () => {
    dataLoading.value = true;
    const payload: GetOneUserPayload = {
        user_id: route.params.userID,
        with_services: 1,
    };
    const result = await userStore.getOne(payload);
    if (result.success) {
        user.value = Object.assign({}, result.data);
        dataLoading.value = false;
        return;
    }
};
const getAvailabilities = async () => {
    availabilitiesLoading.value = true;
    const payload: ListAvailabilityRequest = {
        user_id: route.params.userID,
        date: formatDate(form.value.date),
        is_active: 1,
    };
    const result = await availabilityStore.list(payload);
    if (result.success) {
        availabilitiesLoading.value = false;
        isAvailabilitiesLoaded.value = true;
        availabilities.value = result.data;
        if (availabilities.value.length > 0) {
            hasAvailabilities.value = true;
            return;
        }
        hasAvailabilities.value = false;
    }
};

const onSelectServiceID = (id) => {
    form.value.service_id = id;
};
const onSelectAvailabilityID = (id) => {
    form.value.availability_id = id;
};
const onConfirmBooking = async () => {
    formLoading.value = true;
    const result = await appointmentStore.create(
        Object.assign(
            {},
            {
                ...form.value,
                instructor_id: user.value.id,
                date: formatDate(form.value.date),
            }
        )
    );
    if (result.success) {
        toast({
            title: 'Booking an appointment completed.',
            description:
                'Congratulations! Your desired appointment is booked. You will be redirected to your upcoming appointments.',
        });
        await router.push({ name: 'bookings-page' });

        return;
    }
    formLoading.value = false;
    formError.value = result.message;
};
const onCloseReviewsDialog = () => {
    isReviewsDialogOpen.value = false;
};
const onOpenReviewsDialog = () => {
    isReviewsDialogOpen.value = true;
};

getUser();
</script>

<template>
    <div class="container space-y-10 pb-5">
        <Button
            variant="ghost"
            @click="router.push({ name: 'appointment-page' })"
        >
            <DoubleArrowLeftIcon class="mr-1" />
            Go back
        </Button>
        <template v-if="dataLoading">
            <div class="flex flex-col items-center">
                <img class="w-auto h-40" src="/nyan-cat.gif" alt="Auth GIF" />
            </div>
        </template>
        <template v-else>
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <Avatar class="w-20 h-20 bg-white">
                        <AvatarImage
                            :src="toImageURL(user.image)"
                            :alt="user.name"
                        />
                        <AvatarFallback class="text-xl">{{
                            user.name.split('')[0]
                        }}</AvatarFallback>
                    </Avatar>
                    <div>
                        <div class="space-x-1 flex items-center">
                            <h3 class="text-3xl font-bold capitalize">
                                {{ user.name }}
                            </h3>
                            <Badge
                                variant="outline"
                                class="bg-white capitalize"
                                v-if="user.profile && user.profile.occupation"
                                >{{ user.profile.occupation }}</Badge
                            >
                        </div>
                        <p class="text-sm flex items-center">
                            <StarIcon class="mr-1" />
                            <span class="font-medium mr-1"
                                >{{ user.overall_rating }}/5</span
                            >
                            <Button
                                variant="link"
                                class="font-medium text-blue-600 underline flex items-center p-0"
                                @click="onOpenReviewsDialog"
                                >Read reviews</Button
                            >
                        </p>
                    </div>
                </div>
            </div>

            <template v-if="!!formError">
                <Alert variant="destructive">
                    <ExclamationTriangleIcon class="w-4 h-4" />
                    <AlertTitle>Request Error</AlertTitle>
                    <AlertDescription>
                        {{ formError }}
                    </AlertDescription>
                </Alert>
            </template>

            <Card class="h-auto pt-6 overflow-x-auto">
                <CardContent class="grid grid-cols-3 gap-2 h-full">
                    <Calendar :min-date="minimumDate" v-model="form.date" />
                    <template v-if="isAvailabilitiesLoaded && form.date">
                        <template v-if="hasAvailabilities">
                            <template v-if="availabilitiesLoading">
                                <div
                                    class="h-full w-full col-span-2 flex justify-center items-center"
                                >
                                    <BaseLoadingSpinner class="w-5 h-5" />
                                </div>
                            </template>
                            <template v-else>
                                <div
                                    class="h-full w-full col-span-2 flex flex-col justify-between space-y-4"
                                >
                                    <div class="space-y-4">
                                        <p class="font-bold">Select service</p>
                                        <div
                                            class="flex flex-wrap justify-start items-center gap-2"
                                        >
                                            <template
                                                v-for="(
                                                    service, index
                                                ) in user.services"
                                                :key="index"
                                            >
                                                <Button
                                                    :variant="
                                                        form.service_id ===
                                                        service.id
                                                            ? 'default'
                                                            : 'outline'
                                                    "
                                                    @click="
                                                        onSelectServiceID(
                                                            service.id
                                                        )
                                                    "
                                                    >{{ service.title }}</Button
                                                >
                                            </template>
                                        </div>
                                    </div>
                                    <div class="space-y-4">
                                        <p class="font-bold">
                                            Choose your time
                                        </p>
                                        <div
                                            class="flex flex-wrap justify-start items-center gap-2"
                                        >
                                            <template
                                                v-for="(
                                                    availability, index
                                                ) in availabilities"
                                                :key="index"
                                            >
                                                <Button
                                                    :variant="
                                                        form.availability_id ===
                                                        availability.id
                                                            ? 'default'
                                                            : 'outline'
                                                    "
                                                    @click="
                                                        onSelectAvailabilityID(
                                                            availability.id
                                                        )
                                                    "
                                                    >{{
                                                        formatTime(
                                                            availability.time_from
                                                        )
                                                    }}
                                                    -
                                                    {{
                                                        formatTime(
                                                            availability.time_to
                                                        )
                                                    }}</Button
                                                >
                                            </template>
                                        </div>
                                    </div>
                                    <div class="space-y-4">
                                        <div class="flex flex-col space-y-1.5">
                                            <p class="font-bold">
                                                Meeting Details
                                            </p>
                                            <Input
                                                id="meeting_url"
                                                placeholder="https://meeting@url.com"
                                                v-model="form.meeting_url"
                                            />
                                        </div>
                                    </div>
                                    <div class="flex justify-between">
                                        <div></div>
                                        <Button
                                            :disabled="
                                                !isFormValid || formLoading
                                            "
                                            @click="onConfirmBooking"
                                        >
                                            <template v-if="formLoading">
                                                <BaseLoadingSpinner
                                                    class="mr-2 w-4 h-4"
                                                />
                                            </template>
                                            Confirm Booking</Button
                                        >
                                    </div>
                                </div>
                            </template>
                        </template>
                        <template v-else>
                            <div
                                class="h-full w-full col-span-2 flex justify-center items-center"
                            >
                                <p class="text-muted-foreground text-sm">
                                    No availabilities in selected date.
                                </p>
                            </div>
                        </template>
                    </template>
                    <template v-else>
                        <div
                            class="h-full w-full col-span-2 flex justify-center items-center"
                        >
                            <p class="text-muted-foreground text-sm">
                                Select your desired of appointment with
                                <span class="capitalize">{{ user.name }}</span
                                >.
                            </p>
                        </div>
                    </template>
                </CardContent>
            </Card>
        </template>

        <Dialog :open="isReviewsDialogOpen" @update:open="onCloseReviewsDialog">
            <DialogContent
                class="sm:max-w-[650px] max-h-[800px] overflow-y-auto"
            >
                <DialogHeader>
                    <DialogTitle class="d-flex items-center"
                        >Reviews
                        <Badge variant="outline" class="ml-1"
                            ><StarIcon class="mr-1" />
                            {{ user.overall_rating }}/5</Badge
                        ></DialogTitle
                    >
                </DialogHeader>

                <template v-for="(review, index) in user.reviews" :key="index">
                    <Card>
                        <CardHeader>
                            <CardTitle
                                class="flex items-center justify-between space-x-4"
                            >
                                <div class="flex items-center space-x-4">
                                    <div class="flex items-center space-x-4">
                                        <Avatar>
                                            <AvatarImage
                                                :src="
                                                    toImageURL(
                                                        review.reviewer.image
                                                    )
                                                "
                                                :alt="review.reviewer.name"
                                            />
                                            <AvatarFallback>{{
                                                review.reviewer.name.split(
                                                    ''
                                                )[0]
                                            }}</AvatarFallback>
                                        </Avatar>
                                        <div class="space-y-1">
                                            <p
                                                class="text-sm font-medium leading-none capitalize"
                                            >
                                                {{ review.reviewer.name }}
                                            </p>
                                            <p
                                                class="text-sm font-medium leading-none capitalize flex"
                                            >
                                                <StarIcon class="mr-1" />
                                                {{ review.rating }}/5
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <span
                                    class="text-xs font-normal text-muted-foreground"
                                    >{{
                                        formatDate(
                                            review.created_at,
                                            'distance'
                                        )
                                    }}</span
                                >
                            </CardTitle>
                        </CardHeader>
                        <CardContent>
                            {{ review.comment }}
                        </CardContent>
                    </Card>
                </template>
            </DialogContent>
        </Dialog>
    </div>
</template>
