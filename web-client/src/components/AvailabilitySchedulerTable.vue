<script setup lang="ts">
import { Switch } from '@/components/ui/switch';
import {
    Tooltip,
    TooltipContent,
    TooltipProvider,
    TooltipTrigger,
} from '@/components/ui/tooltip';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuGroup,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import {
    Pencil1Icon,
    TrashIcon,
    PlusIcon,
    DotsHorizontalIcon,
    ExclamationTriangleIcon,
    PauseIcon,
} from '@radix-icons/vue';
import {
    Table,
    TableBody,
    TableCaption,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { ref, computed } from 'vue';
import {
    ListAvailabilityRequest,
    useAvailabilityStore,
} from '@/stores/availability';
import { useToast } from '@/components/ui/toast';
import { useDatetime } from '@/composables/datetime';
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';
import { Input } from '@/components/ui/input';
import {
    Dialog,
    DialogContent,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Label } from '@/components/ui/label';
import BaseLoadingSpinner from '@/components/base/BaseLoadingSpinner.vue';
import {
    Select,
    SelectContent,
    SelectGroup,
    SelectItem,
    SelectLabel,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';

const props = defineProps({
    dayOfWeek: {
        type: Number,
        required: true,
    },
});

const availabilityStore = useAvailabilityStore();
const { toast } = useToast();
const { formatTime, formatDate, generateTimeslots } = useDatetime();

const isDialogOpen = ref(false);
const error = ref(null);
const formLoading = ref(false);
const defaultForm = {
    availability_id: null,
    time_from: null,
    time_to: null,
    is_active: true,
};
const form = ref(Object.assign({}, defaultForm));
const tableLoading = ref(false);
const availabilities = ref([]);
const operation = ref('create');
const timeslots = ref(generateTimeslots());

const isFormValid = computed(() => {
    const { time_from, time_to } = form.value;
    return time_from && time_to;
});
const dayLabel = computed(() => {
    const labels = {
        1: 'Monday',
        2: 'Tuesday',
        3: 'Wednesday',
        4: 'Thursday',
        5: 'Friday',
        6: 'Saturday',
        7: 'Sunday',
    };

    return labels[props.dayOfWeek];
});

const onOpenDialog = (_operation: string, availability: any = null) => {
    if (_operation === 'update' && !!availability) {
        const { id, time_from, time_to, is_active } = availability;
        form.value.availability_id = id;
        form.value.time_from = time_from;
        form.value.time_to = time_to;
        operation.value = 'update';
        isDialogOpen.value = true;

        return;
    }
    operation.value = 'create';
    isDialogOpen.value = true;
};
const onCloseDialog = () => {
    if (operation.value === 'update') {
        form.value = Object.assign({}, defaultForm);
        error.value = null;
    }
    isDialogOpen.value = false;
};
const onFormSubmit = async () => {
    formLoading.value = true;
    const payload = {
        ...form.value,
        day_of_week: props.dayOfWeek,
    };
    const result = await availabilityStore[operation.value](payload);
    if (result.success) {
        toast({
            title:
                operation.value === 'create'
                    ? 'New availability created'
                    : 'Availability updated',
            description:
                operation.value === 'create'
                    ? 'Congratulations! You have new availability created in your list.'
                    : 'Nice! You have updated a availability in your list.',
        });
        isDialogOpen.value = false;
        await listAvailabilities();
        form.value = Object.assign({}, defaultForm);
        formLoading.value = false;
        error.value = null;
        operation.value = 'create';

        return;
    }
    formLoading.value = false;
    error.value = result.message;
};
const listAvailabilities = async () => {
    tableLoading.value = true;
    const payload: ListAvailabilityRequest = {
        day_of_week: props.dayOfWeek,
    };
    const result = await availabilityStore.list(payload);
    if (result.success) {
        availabilities.value = result.data;
        tableLoading.value = false;
        return;
    }
    toast({
        variant: 'destructive',
        title: 'Server error.',
        description: result.message,
    });
};
const onDeleteAvailability = async (availabilityID) => {
    tableLoading.value = true;
    const result = await availabilityStore.delete(availabilityID);
    if (result.success) {
        toast({
            title: 'Availability deleted',
            description:
                'Oh no. Availability is successfully deleted. The action is permanent.',
        });
        await listAvailabilities();

        return;
    }
    tableLoading.value = false;
    toast({
        variant: 'destructive',
        title: 'Server error.',
        description: result.message,
    });
};
const onChangeAvailabilityStatus = async (availability) => {
    tableLoading.value = true;
    const payload = {
        availability_id: availability.id,
        is_active: availability.is_active ? 0 : 1,
    };
    const result = await availabilityStore.changeStatus(payload);
    if (result.success) {
        toast({
            title: 'Availability status changed',
            description: 'Availability status is successfully changed.',
        });
        await listAvailabilities();

        return;
    }
    tableLoading.value = false;
    toast({
        variant: 'destructive',
        title: 'Server error.',
        description: result.message,
    });
};

listAvailabilities();
</script>

<template>
    <div>
        <div class="flex justify-between items-center">
            <div></div>
            <div class="flex">
                <DropdownMenu>
                    <DropdownMenuTrigger as-child>
                        <Button variant="ghost" size="icon">
                            <DotsHorizontalIcon />
                        </Button>
                    </DropdownMenuTrigger>
                    <DropdownMenuContent class="w-56">
                        <DropdownMenuLabel
                            >{{ dayLabel }} actions</DropdownMenuLabel
                        >
                        <DropdownMenuSeparator />
                        <DropdownMenuGroup>
                            <DropdownMenuItem @click="onOpenDialog">
                                <PlusIcon class="mr-2 h-4 w-4" />
                                <span>Add new availability</span>
                            </DropdownMenuItem>
                        </DropdownMenuGroup>
                        <DropdownMenuSeparator />
                        <DropdownMenuGroup>
                            <DropdownMenuItem>
                                <Switch class="mr-2" />
                                <span>Accept appointments</span>
                            </DropdownMenuItem>
                        </DropdownMenuGroup>
                    </DropdownMenuContent>
                </DropdownMenu>
            </div>
        </div>
        <template v-if="tableLoading">
            <div class="flex flex-col justify-center items-center">
                <img class="w-auto h-40" src="/nyan-cat.gif" alt="Auth GIF" />
            </div>
        </template>
        <Table v-else>
            <!--
              <TableCaption>A list of your recent invoices.</TableCaption>

-->
            <TableHeader>
                <TableRow>
                    <TableHead>Time from</TableHead>
                    <TableHead>Time to</TableHead>
                    <TableHead>Active</TableHead>
                    <TableHead>Date Added</TableHead>
                    <TableHead class="text-right">Actions</TableHead>
                </TableRow>
            </TableHeader>
            <TableBody>
                <template v-if="availabilities.length > 0">
                    <template
                        v-for="(availability, index) in availabilities"
                        :key="index"
                    >
                        <TableRow>
                            <TableCell>
                                {{ formatTime(availability.time_from) }}
                            </TableCell>
                            <TableCell>
                                {{ formatTime(availability.time_to) }}
                            </TableCell>
                            <TableCell>
                                <Badge
                                    :variant="
                                        availability.is_active
                                            ? 'default'
                                            : 'destructive'
                                    "
                                    >{{
                                        availability.is_active ? 'Yes' : 'No'
                                    }}</Badge
                                >
                            </TableCell>
                            <TableCell>
                                {{
                                    formatDate(
                                        availability.created_at,
                                        'formatted'
                                    )
                                }}
                            </TableCell>
                            <TableCell class="text-right">
                                <TooltipProvider>
                                    <Tooltip>
                                        <TooltipTrigger as-child>
                                            <Button
                                                size="icon"
                                                variant="ghost"
                                                @click="
                                                    onChangeAvailabilityStatus(
                                                        availability
                                                    )
                                                "
                                            >
                                                <PauseIcon
                                                    :class="`font-bold ${
                                                        availability.is_active
                                                            ? 'text-muted-foreground'
                                                            : 'text-red-600'
                                                    }`"
                                                />
                                            </Button>
                                        </TooltipTrigger>
                                        <TooltipContent>
                                            <p>
                                                {{
                                                    availability.is_active
                                                        ? 'Disable'
                                                        : 'Enable'
                                                }}
                                            </p>
                                        </TooltipContent>
                                    </Tooltip>
                                </TooltipProvider>
                                <Button
                                    size="icon"
                                    variant="ghost"
                                    :disabled="
                                        index === availabilities.length - 1
                                    "
                                    @click="
                                        onOpenDialog('update', availability)
                                    "
                                >
                                    <Pencil1Icon />
                                </Button>
                                <Button
                                    size="icon"
                                    variant="ghost"
                                    :disabled="
                                        index === availabilities.length - 1
                                    "
                                    @click="
                                        onDeleteAvailability(availability.id)
                                    "
                                >
                                    <TrashIcon />
                                </Button>
                            </TableCell>
                        </TableRow>
                    </template>
                </template>
                <template v-else>
                    <TableCaption
                        >No created availabilities to show.</TableCaption
                    >
                </template>
            </TableBody>
        </Table>

        <Dialog :open="isDialogOpen" @update:open="onCloseDialog">
            <DialogContent class="sm:max-w-[425px]">
                <DialogHeader>
                    <DialogTitle>
                        {{ operation === 'create' ? 'Create new' : 'Update' }}
                        Availability
                    </DialogTitle>
                </DialogHeader>

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
                        <Label for="time_from">Time From</Label>
                        <Select id="time_from" v-model="form.time_from">
                            <SelectTrigger>
                                <SelectValue placeholder="Select time slot" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectGroup>
                                    <template
                                        v-for="(timeslot, index) in timeslots"
                                        :key="index"
                                    >
                                        <SelectItem :value="timeslot.value">
                                            {{ timeslot.text }}
                                        </SelectItem>
                                    </template>
                                </SelectGroup>
                            </SelectContent>
                        </Select>
                    </div>
                    <div class="flex flex-col space-y-1.5">
                        <Label for="time_to">Time To</Label>
                        <Select id="time_to" v-model="form.time_to">
                            <SelectTrigger>
                                <SelectValue placeholder="Select time slot" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectGroup>
                                    <SelectLabel>Time slots</SelectLabel>
                                    <template
                                        v-for="(timeslot, index) in timeslots"
                                        :key="index"
                                    >
                                        <SelectItem :value="timeslot.value">
                                            {{ timeslot.text }}
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
    </div>
</template>
