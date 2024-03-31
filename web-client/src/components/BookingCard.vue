<script setup lang="ts">
import {
    Card,
    CardContent,
    CardDescription,
    CardFooter,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import {
    CameraIcon,
    EnvelopeClosedIcon,
    StarIcon,
    MobileIcon,
    ExclamationTriangleIcon,
} from '@radix-icons/vue';
import { Badge } from '@/components/ui/badge';
import { useDatetime } from '@/composables/datetime.ts';
import { computed, onMounted, ref, watch } from 'vue';
import {
    UpdateAppointmentPayload,
    useAppointmentStore,
} from '@/stores/appointment.ts';
import { useToast } from '@/components/ui/toast';
import BaseLoadingSpinner from '@/components/base/BaseLoadingSpinner.vue';
import { useAuthStore } from '@/stores/auth.ts';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { useMisc } from '@/composables/misc.ts';
import {
    Dialog,
    DialogContent,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';
import DialogDescription from '@/components/ui/dialog/DialogDescription.vue';
import { Label } from '@/components/ui/label';
import Textarea from '@/components/ui/textarea/Textarea.vue';
import {
    Select,
    SelectContent,
    SelectGroup,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { useFeedbackStore } from '@/stores/feedback';

const props = defineProps({
    appointment: { type: Object, required: true },
});
const emit = defineEmits(['onLoadList']);
const { formatDate, formatTime, today } = useDatetime();
const appointmentStore = useAppointmentStore();
const { toast } = useToast();
const { user } = useAuthStore();
const { toImageURL } = useMisc();
const feedbackStore = useFeedbackStore();

const loading = ref(false);
const isFormDialogOpen = ref(false);
const defaultForm = {
    rating: null,
    comment: null,
};
const form = ref(Object.assign({}, defaultForm));
const formLoading = ref(false);
const formError = ref(false);
const feedbacksLocal = ref([...props.appointment.feedbacks]); // The feedback for the appointments
const reviewsLocal = ref([]); // The reviews of the user
const isReviewsDialogOpen = ref(false);
const overallRating = ref(0);

const isCurrUserInstructor = computed(() => user.level === 'instructor');
const showAcceptButton = computed(
    () =>
        ['pending'].includes(props.appointment.status) &&
        isCurrUserInstructor.value
);
const showRejectButton = computed(() =>
    ['pending', 'upcoming'].includes(props.appointment.status)
);
const showDoneButton = computed(
    () =>
        ['upcoming'].includes(props.appointment.status) &&
        isCurrUserInstructor.value
);
const showReviewButton = computed(() =>
    ['for_review'].includes(props.appointment.status)
);
const information = computed(() => {
    return user.level === 'student'
        ? props.appointment.instructor
        : props.appointment.student;
});
const isFormValid = computed(() => {
    const { rating, comment } = form.value;

    return comment && rating;
});
const doesReviewed = computed(() => {
    return feedbacksLocal.value
        .map((feedback) => feedback.reviewer.id)
        .includes(user.id);
});

const onChangeStatus = async (status) => {
    console.log(status);
    loading.value = true;
    const payload: UpdateAppointmentPayload = {
        appointment_id: props.appointment.id,
        status,
    };
    const result = await appointmentStore.update(payload);
    if (result.success) {
        emit('onLoadList');
        return;
    }
    loading.value = false;
    toast({
        variant: 'destructive',
        title: 'Server error.',
        description: result.message,
    });
};
const onFormSubmit = async () => {
    formLoading.value = true;
    const payload = {
        ...form.value,
        reviewee_id: information.value.id,
        appointment_id: props.appointment.id,
    };
    const result = await feedbackStore.create(payload);
    if (result.success) {
        toast({
            title: 'Feedback created',
            description: `Nice!, You've successfully created a review for ${information.value.name}`,
        });
        emit('onLoadList');
        isFormDialogOpen.value = false;
        form.value = Object.assign({}, defaultForm);
        formLoading.value = false;
        formError.value = null;

        return;
    }
    formLoading.value = false;
    formError.value = result.message;
};
const onCloseFormDialog = () => {
    isFormDialogOpen.value = false;
};
const onOpenFormDialog = () => {
    isFormDialogOpen.value = true;
};
const onOpenReviewsDialog = () => {
    isReviewsDialogOpen.value = true;
};
const onCloseReviewsDialog = () => {
    isReviewsDialogOpen.value = false;
};

onMounted(() => {
    reviewsLocal.value = [...information.value.reviews];
    overallRating.value = information.value.overall_rating;
});
</script>

<template>
    <Card>
        <CardHeader>
            <CardTitle class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="flex items-center space-x-4">
                        <Avatar>
                            <AvatarImage
                                :src="toImageURL(information.image)"
                                :alt="information.name"
                            />
                            <AvatarFallback>{{
                                information.name.split('')[0]
                            }}</AvatarFallback>
                        </Avatar>
                        <div>
                            <p
                                class="text-sm font-medium leading-none capitalize"
                            >
                                {{ information.name }}
                            </p>
                        </div>
                    </div>
                </div>
                <Badge variant="outline">{{ appointment.service.title }}</Badge>
            </CardTitle>
            <CardDescription v-if="user.level === 'student'">{{
                appointment.instructor.profile.bio || 'N/A'
            }}</CardDescription>
        </CardHeader>
        <CardContent class="mb-0 pb-2">
            <p class="text-xs text-muted-foreground">Date</p>
            <p class="text-md">
                <template v-if="today(appointment.date)">
                    <span class="font-bold"> Today </span>
                </template>
                <template v-else>
                    <span class="font-bold">
                        {{ formatDate(appointment.date, 'formatted') }}
                    </span>
                    <template
                        v-if="
                            appointment.status === 'pending' ||
                            appointment.status === 'upcoming'
                        "
                    >
                        ({{ formatDate(appointment.date, 'distance') }})
                    </template>
                </template>
            </p>
        </CardContent>
        <CardContent>
            <p class="text-xs text-muted-foreground">Duration</p>
            <p class="text-md font-bold">
                {{ formatTime(appointment.availability.time_from) }} -
                {{ formatTime(appointment.availability.time_to) }}
            </p>
        </CardContent>
        <CardContent class="space-y-2">
            <p class="text-sm flex items-center">
                <EnvelopeClosedIcon class="mr-1" />
                <span class="underline cursor-pointer">{{
                    information.email ? information.email : 'N/A'
                }}</span>
            </p>
            <p class="text-sm flex items-center">
                <MobileIcon class="mr-1" />
                <span class="underline cursor">
                    {{
                        information.profile && information.profile.mobile_number
                            ? `(+63) ${information.profile.mobile_number}`
                            : 'N/A'
                    }}</span
                >
            </p>

            <p class="text-sm flex items-center">
                <StarIcon class="mr-1" />
                <template v-if="reviewsLocal.length > 0">
                    <span class="font-medium mr-1">{{ overallRating }}/5</span>
                    <Button
                        variant="link"
                        class="font-medium text-blue-600 underline flex items-center p-0"
                        @click="onOpenReviewsDialog"
                        >Read reviews</Button
                    >
                </template>
                <template v-else>
                    <span class="text-muted-foreground">No reviews yet.</span>
                </template>
            </p>
        </CardContent>
        <CardContent class="space-y-2">
            <p class="text-sm flex items-center">
                <CameraIcon class="mr-1" />
                <a
                    :href="appointment.meeting_url"
                    target="_blank"
                    class="font-medium text-blue-600 underline flex items-center"
                    >{{ appointment.meeting_url }}</a
                >
            </p>
        </CardContent>
        <CardFooter class="flex justify-between px-6 space-x-2 pb-6 pt-5">
            <span class="text-xs text-muted-foreground"
                >Created at
                {{ formatDate(appointment.created_at, 'distance') }}</span
            >
            <div class="flex justify-end space-x-2">
                <Button
                    variant="destructive"
                    @click="onChangeStatus('cancelled')"
                    :disabled="loading"
                    v-if="showRejectButton"
                >
                    <template v-if="loading">
                        <BaseLoadingSpinner class="mr-2 w-4 h-4" />
                    </template>
                    Reject</Button
                >
                <Button
                    @click="onChangeStatus('upcoming')"
                    :disabled="loading"
                    v-if="showAcceptButton"
                >
                    <template v-if="loading">
                        <BaseLoadingSpinner class="mr-2 w-4 h-4" />
                    </template>
                    Accept</Button
                >
                <Button
                    @click="onChangeStatus('for_review')"
                    :disabled="loading"
                    v-if="showDoneButton"
                >
                    <template v-if="loading">
                        <BaseLoadingSpinner class="mr-2 w-4 h-4" />
                    </template>
                    Done</Button
                >
                <Button
                    :disabled="loading || doesReviewed"
                    @click="onOpenFormDialog"
                    v-if="showReviewButton"
                >
                    <template v-if="loading">
                        <BaseLoadingSpinner class="mr-2 w-4 h-4" />
                    </template>
                    {{ doesReviewed ? 'Reviewed' : 'Review' }}</Button
                >
            </div>
        </CardFooter>

        <Dialog :open="isFormDialogOpen" @update:open="onCloseFormDialog">
            <DialogContent class="sm:max-w-[500px]">
                <DialogHeader>
                    <DialogTitle>Feedback form</DialogTitle>
                    <DialogDescription
                        >Create a feedback for
                        <span class="text-capitalize">{{
                            information.name
                        }}</span
                        >.
                    </DialogDescription>
                </DialogHeader>

                <div class="grid items-center w-full gap-4">
                    <template v-if="!!formError">
                        <Alert variant="destructive">
                            <ExclamationTriangleIcon class="w-4 h-4" />
                            <AlertTitle>Request Error</AlertTitle>
                            <AlertDescription>
                                {{ formError }}
                            </AlertDescription>
                        </Alert>
                    </template>

                    <div class="flex flex-col space-y-1.5">
                        <Label for="comment">Your personal review</Label>
                        <Textarea id="comment" v-model="form.comment" />
                    </div>
                    <div class="flex flex-col space-y-1.5">
                        <Label for="rating">Rating</Label>
                        <Select id="rating" v-model="form.rating">
                            <SelectTrigger>
                                <SelectValue
                                    placeholder="Select rating 1 out 5"
                                />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectGroup>
                                    <template v-for="n in 5" :key="n">
                                        <SelectItem :value="`${n}`">
                                            {{ n }}
                                        </SelectItem>
                                    </template>
                                </SelectGroup>
                            </SelectContent>
                        </Select>
                    </div>
                </div>

                <DialogFooter>
                    <Button
                        :disabled="!isFormValid || formLoading"
                        @click="onFormSubmit"
                    >
                        <template v-if="formLoading">
                            <BaseLoadingSpinner class="mr-2 w-4 h-4" />
                        </template>
                        Save
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>

        <Dialog :open="isReviewsDialogOpen" @update:open="onCloseReviewsDialog">
            <DialogContent
                class="sm:max-w-[650px] max-h-[800px] overflow-y-auto"
            >
                <DialogHeader>
                    <DialogTitle class="d-flex items-center"
                        >Reviews
                        <Badge variant="outline" class="ml-1"
                            ><StarIcon class="mr-1" />
                            {{ overallRating }}/5</Badge
                        ></DialogTitle
                    >
                </DialogHeader>

                <template v-for="(review, index) in reviewsLocal" :key="index">
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
    </Card>
</template>
