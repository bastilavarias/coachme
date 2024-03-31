import { defineStore } from 'pinia';
import apiClient from '@/lib/api-client.ts';

export interface CreateServiceRequest {
    title?: string | null;
}
export interface UpdateServiceRequest {
    service_id?: number | null;
    title?: string | null;
    _method?: string | null;
}

export const useServiceStore = defineStore('service', {
    actions: {
        async create(payload: CreateServiceRequest) {
            try {
                const response = await apiClient.post({
                    route: `service`,
                    body: payload,
                });
                return await apiClient.toReadableResponse('complete', response);
            } catch (e) {
                return await apiClient.toReadableResponse('error', e);
            }
        },

        async list() {
            try {
                const response = await apiClient.get({
                    route: `service`,
                });
                return await apiClient.toReadableResponse('complete', response);
            } catch (e) {
                return await apiClient.toReadableResponse('error', e);
            }
        },

        async update(payload: UpdateServiceRequest) {
            try {
                payload._method = 'PUT';
                const response = await apiClient.post({
                    route: `service/${payload.service_id}`,
                    body: payload,
                });
                return await apiClient.toReadableResponse('complete', response);
            } catch (e) {
                return await apiClient.toReadableResponse('error', e);
            }
        },

        async delete(serviceID: number) {
            try {
                const response = await apiClient.delete({
                    route: `service/${serviceID}`,
                });
                return await apiClient.toReadableResponse('complete', response);
            } catch (e) {
                return await apiClient.toReadableResponse('error', e);
            }
        },
    },
});
