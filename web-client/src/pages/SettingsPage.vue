<script setup lang="ts">
import { Card, CardContent } from '@/components/ui/card';
import {
    Accordion,
    AccordionContent,
    AccordionItem,
    AccordionTrigger,
} from '@/components/ui/accordion';
import { PersonIcon, CalendarIcon, BackpackIcon } from '@radix-icons/vue';
import AvailabilityScheduler from '@/components/AvailabilityScheduler.vue';
import AccountForm from '@/components/AccountForm.vue';
import ServicesTable from '@/components/ServicesTable.vue';
import { useAuthStore } from '@/stores/auth.ts';
import { onMounted, ref } from 'vue';

const { user } = useAuthStore();

const defaultValue = 'availability';

let accordionItems = ref([
    {
        value: 'account',
        title: 'Account',
        icon: PersonIcon,
        content: AccountForm,
    },
]);

onMounted(() => {
    if (user.level === 'instructor') {
        accordionItems.value = [
            {
                value: 'services',
                title: 'Services',
                icon: BackpackIcon,
                content: ServicesTable,
            },
            {
                value: 'availability',
                title: 'Availability',
                icon: CalendarIcon,
                content: AvailabilityScheduler,
            },
            ...accordionItems.value,
        ];
    }
});
</script>

<template>
    <div class="container space-y-4">
        <div>
            <h3 class="text-3xl font-bold">Settings</h3>
            <p class="text-muted-foreground">
                Configure your settings in this page
            </p>
        </div>
        <Card class="pt-6">
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
                                    <Component
                                        class="w-5 h-5"
                                        :is="item.icon"
                                    />
                                    <span class="ml-2 text-lg font-medium">
                                        {{ item.title }}
                                    </span>
                                </span>
                            </AccordionTrigger>
                            <AccordionContent>
                                <Component :is="item.content" />
                            </AccordionContent>
                        </AccordionItem>
                    </template>
                </Accordion>
            </CardContent>
        </Card>
    </div>
</template>
