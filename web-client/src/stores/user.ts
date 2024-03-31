import { defineStore } from 'pinia';
import apiClient from '@/lib/api-client.ts';

export interface GetOneUserPayload {
    user_id?: number | null;
    with_availabilities?: boolean | number | null;
    with_services?: boolean | number | null;
}
export interface ListUserPayload {
    user_id?: number | null;
    level?: string | null;
    sort_by?: string | null;
    search?: string | null;
}
export interface UpdateUserPayload {
    user_id?: number | null;
    name?: string | null;
    email?: string | null;
    mobile_number?: string | null;
    bio?: string | null;
    image?: File | null;
    _method?: string | null;
}

export const useUserStore = defineStore('user', {
    actions: {
        async list(payload: ListUserPayload) {
            try {
                const parameters = apiClient.toURLSearchParams(payload);
                const response = await apiClient.get({
                    route: `user?${parameters}`,
                });
                return await apiClient.toReadableResponse('complete', response);
            } catch (e) {
                return await apiClient.toReadableResponse('error', e);
            }
        },

        async getOne(payload: GetOneUserPayload) {
            try {
                const parameters = apiClient.toURLSearchParams(payload);
                const response = await apiClient.get({
                    route: `user/${payload.user_id}?${parameters}`,
                });
                return await apiClient.toReadableResponse('complete', response);
            } catch (e) {
                return await apiClient.toReadableResponse('error', e);
            }
        },
        async update(payload: UpdateUserPayload) {
            try {
                payload._method = 'PUT';
                const response = await apiClient.post({
                    route: `user/${payload.user_id}`,
                    body: payload,
                    transform: 'form-data',
                });
                return await apiClient.toReadableResponse('complete', response);
            } catch (e) {
                return await apiClient.toReadableResponse('error', e);
            }
        },
    },
});
