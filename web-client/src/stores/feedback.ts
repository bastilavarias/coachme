import { defineStore } from 'pinia';
import apiClient from '@/lib/api-client.ts';

export interface CreateFeedbackRequest {
    reviewee_id?: number | null;
    appointment_id?: number | null;
    rating?: number | null;
    comment?: string | null;
}

export const useFeedbackStore = defineStore('feedback', {
    actions: {
        async create(payload: CreateFeedbackRequest) {
            try {
                const response = await apiClient.post({
                    route: `feedback`,
                    body: payload,
                });
                return await apiClient.toReadableResponse('complete', response);
            } catch (e) {
                return await apiClient.toReadableResponse('error', e);
            }
        },
    },
});
