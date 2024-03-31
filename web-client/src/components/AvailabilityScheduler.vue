<script setup lang="ts">
import { Switch } from '@/components/ui/switch';

import {
    Card,
    CardHeader,
    CardDescription,
    CardContent,
} from '@/components/ui/card';
import {
    Accordion,
    AccordionContent,
    AccordionItem,
    AccordionTrigger,
} from '@/components/ui/accordion';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Label } from '@/components/ui/label';
import AvailabilitySchedulerTable from '@/components/AvailabilitySchedulerTable.vue';

const defaultValue = '1';
const accordionItems = [
    {
        value: '1',
        title: 'Monday',
    },
    {
        value: '2',
        title: 'Tuesday',
    },
    {
        value: '3',
        title: 'Wednesday',
    },
    {
        value: '4',
        title: 'Thursday',
    },
    {
        value: '5',
        title: 'Friday',
    },
    {
        value: '6',
        title: 'Saturday',
    },
    {
        value: '7',
        title: 'Sunday',
    },
];
</script>

<template>
    <div class="space-y-4">
        <div>
            <form class="w-1/4 space-y-4">
                <div class="flex items-center space-x-1.5">
                    <Switch />
                    <Label for="account-type">Accept appointments</Label>
                </div>
                <div class="flex flex-col space-y-1.5">
                    <Label for="account-type">Timezone</Label>
                    <Select>
                        <SelectTrigger id="account-type">
                            <SelectValue placeholder="Asia/Manila" />
                        </SelectTrigger>
                        <SelectContent position="popper">
                            <SelectItem value="instructor"
                                >Asia/Manila</SelectItem
                            >
                        </SelectContent>
                    </Select>
                </div>
            </form>
        </div>

        <Card>
            <CardHeader class="pb-0">
                <CardDescription>Days of week</CardDescription>
            </CardHeader>
            <CardContent>
                <Accordion
                    type="single"
                    class="w-full"
                    collapsible
                    :default-value="defaultValue"
                >
                    <template
                        v-for="item in accordionItems"
                        :key="item.value"
                        :value="item.value"
                    >
                        <AccordionItem :value="item.value">
                            <AccordionTrigger>
                                <span class="flex items-center">
                                    {{ item.title }}
                                </span>
                            </AccordionTrigger>
                            <AccordionContent>
                                <AvailabilitySchedulerTable
                                    :day-of-week="parseInt(item.value)"
                                />
                            </AccordionContent>
                        </AccordionItem>
                    </template>
                </Accordion>
            </CardContent>
        </Card>
    </div>
</template>
