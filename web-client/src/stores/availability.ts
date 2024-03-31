import { defineStore } from 'pinia';
import apiClient from '@/lib/api-client.ts';
import { CreateServiceRequest } from '@/stores/service.ts';

export interface ListAvailabilityRequest {
    day_of_week?: number | null;
}
export interface CreateAvailabilityRequest {
    day_of_week?: number | null;
    time_from?: string | null;
    time_to?: string | null;
}
export interface UpdateAvailabilityRequest {
    availability_id?: number | null;
    day_of_week?: number | null;
    time_from?: string | null;
    time_to?: string | null;
    _method?: string | null;
}
export interface ChangeAvailabilityStatusRequest {
    availability_id?: number | null;
    is_active?: number | null;
    _method?: string | null;
}

export const useAvailabilityStore = defineStore('availability', {
    actions: {
        async list(payload: ListAvailabilityRequest) {
            try {
                const parameters = apiClient.toURLSearchParams(payload);
                const response = await apiClient.get({
                    route: `availability?${parameters}`,
                });
                return await apiClient.toReadableResponse('complete', response);
            } catch (e) {
                return await apiClient.toReadableResponse('error', e);
            }
        },

        async create(payload: CreateAvailabilityRequest) {
            try {
                const response = await apiClient.post({
                    route: `availability`,
                    body: payload,
                });
                return await apiClient.toReadableResponse('complete', response);
            } catch (e) {
                return await apiClient.toReadableResponse('error', e);
            }
        },

        async update(payload: UpdateAvailabilityRequest) {
            try {
                payload._method = 'PUT';
                const response = await apiClient.post({
                    route: `availability/${payload.availability_id}`,
                    body: payload,
                });
                return await apiClient.toReadableResponse('complete', response);
            } catch (e) {
                return await apiClient.toReadableResponse('error', e);
            }
        },

        async delete(availabilityID: number) {
            try {
                const response = await apiClient.delete({
                    route: `availability/${availabilityID}`,
                });
                return await apiClient.toReadableResponse('complete', response);
            } catch (e) {
                return await apiClient.toReadableResponse('error', e);
            }
        },

        async changeStatus(payload: ChangeAvailabilityStatusRequest) {
            try {
                payload._method = 'PUT';
                const response = await apiClient.post({
                    route: `availability/active/${payload.availability_id}`,
                    body: payload,
                });
                return await apiClient.toReadableResponse('complete', response);
            } catch (e) {
                return await apiClient.toReadableResponse('error', e);
            }
        },
    },
});
