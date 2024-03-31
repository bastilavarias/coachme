<script setup lang="ts">
import {
    Card,
    CardHeader,
    CardDescription,
    CardContent,
} from '@/components/ui/card';
import {
    Pencil1Icon,
    TrashIcon,
    PlusIcon,
    DotsHorizontalIcon,
    ExclamationTriangleIcon,
} from '@radix-icons/vue';
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
    Table,
    TableBody,
    TableCaption,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import BaseLoadingSpinner from '@/components/base/BaseLoadingSpinner.vue';
import {
    Dialog,
    DialogContent,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Button } from '@/components/ui/button';
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';
import { computed, ref } from 'vue';
import { Label } from '@/components/ui/label';
import { Input } from '@/components/ui/input';
import { useToast } from '@/components/ui/toast';
import { useServiceStore } from '@/stores/service.ts';
import { useDatetime } from '@/composables/datetime.ts';

const { toast } = useToast();
const serviceStore = useServiceStore();
const { formatDate } = useDatetime();

const isDialogOpen = ref(false);
const error = ref(null);
const formLoading = ref(false);
const defaultForm = {
    title: null,
    service_id: null,
};
const form = ref(Object.assign({}, defaultForm));
const tableLoading = ref(false);
const services = ref([]);
const operation = ref('create');

const isFormValid = computed(() => {
    return !!form.value.title;
});

const onOpenDialog = (_operation: string, service: any = null) => {
    if (_operation === 'update' && !!service) {
        const { id, title } = service;
        form.value.service_id = id;
        form.value.title = title;
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
    const result = await serviceStore[operation.value](form.value);
    if (result.success) {
        toast({
            title:
                operation.value === 'create'
                    ? 'New service created'
                    : 'Service updated',
            description:
                operation.value === 'create'
                    ? 'Congratulations! You have new service created in your list.'
                    : 'Nice! You have updated a service in your list.',
        });
        isDialogOpen.value = false;
        await listServices();
        form.value = Object.assign({}, defaultForm);
        formLoading.value = false;
        error.value = null;
        operation.value = 'create';

        return;
    }
    formLoading.value = false;
    error.value = result.message;
};
const listServices = async () => {
    tableLoading.value = true;
    const result = await serviceStore.list();
    if (result.success) {
        services.value = result.data;
        tableLoading.value = false;
        return;
    }
    toast({
        variant: 'destructive',
        title: 'Server error.',
        description: result.message,
    });
};
const onDeleteService = async (serviceID: number) => {
    tableLoading.value = true;
    const result = await serviceStore.delete(serviceID);
    if (result.success) {
        toast({
            title: 'Service deleted',
            description:
                'Oh no. Service is successfully deleted. The action is permanent.',
        });
        await listServices();

        return;
    }
    tableLoading.value = false;
    toast({
        variant: 'destructive',
        title: 'Server error.',
        description: result.message,
    });
};

listServices();
</script>

<template>
    <div class="space-y-4">
        <Card>
            <CardHeader class="pb-0">
                <CardDescription>Manage your services</CardDescription>
            </CardHeader>
            <CardContent>
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
                                    >Services actions</DropdownMenuLabel
                                >
                                <DropdownMenuSeparator />
                                <DropdownMenuGroup>
                                    <DropdownMenuItem @click="onOpenDialog">
                                        <PlusIcon class="mr-2 h-4 w-4" />
                                        <span>Add new service</span>
                                    </DropdownMenuItem>
                                </DropdownMenuGroup>
                            </DropdownMenuContent>
                        </DropdownMenu>
                    </div>
                </div>
                <template v-if="tableLoading">
                    <div class="flex flex-col justify-center items-center">
                        <img
                            class="w-auto h-40"
                            src="/nyan-cat.gif"
                            alt="Auth GIF"
                        />
                    </div>
                </template>
                <Table v-else>
                    <TableHeader>
                        <TableRow>
                            <TableHead>Title</TableHead>
                            <TableHead>Date Added</TableHead>
                            <TableHead class="text-right">Actions</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <template v-if="services.length > 0">
                            <template
                                v-for="(service, index) in services"
                                :key="index"
                            >
                                <TableRow>
                                    <TableCell>
                                        {{ service.title }}
                                    </TableCell>
                                    <TableCell>
                                        {{
                                            formatDate(
                                                service.created_at,
                                                'formatted'
                                            )
                                        }}
                                    </TableCell>
                                    <TableCell class="text-right">
                                        <Button
                                            size="icon"
                                            variant="ghost"
                                            :disabled="
                                                index === services.length - 1
                                            "
                                            @click="
                                                onOpenDialog('update', service)
                                            "
                                        >
                                            <Pencil1Icon />
                                        </Button>
                                        <Button
                                            size="icon"
                                            variant="ghost"
                                            :disabled="
                                                index === services.length - 1
                                            "
                                            @click="onDeleteService(service.id)"
                                        >
                                            <TrashIcon />
                                        </Button>
                                    </TableCell>
                                </TableRow>
                            </template>
                        </template>
                        <template v-else>
                            <TableCaption
                                >No created services to show.</TableCaption
                            >
                        </template>
                    </TableBody>
                </Table>
            </CardContent>
        </Card>

        <Dialog :open="isDialogOpen" @update:open="onCloseDialog">
            <DialogContent class="sm:max-w-[425px]">
                <DialogHeader>
                    <DialogTitle>
                        {{ operation === 'create' ? 'Create new' : 'Update' }}
                        Service
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
                        <Label for="service">Service title</Label>
                        <Input id="service" v-model="form.title" />
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
