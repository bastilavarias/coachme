import { defineStore } from 'pinia';
import apiClient from '@/lib/api-client.ts';

export interface CreateAppointmentPayload {
    instructor_id?: number | null;
    availability_id?: number | null;
    date?: string | null;
    service_id?: number | null;
    meeting_url?: string | null;
}
export interface ListAppointmentsPayload {
    count_only?: boolean | number | null;
    order_by?: string | null;
    status?: string | null;
    tomorrow?: boolean | null;
}
export interface UpdateAppointmentPayload {
    appointment_id?: number | null;
    status?: string | null;
    _method?: string | null;
}

export const useAppointmentStore = defineStore('appointment', {
    actions: {
        async available() {
            try {
                const response = await apiClient.get({
                    route: `appointment/available`,
                });
                return await apiClient.toReadableResponse('complete', response);
            } catch (e) {
                return await apiClient.toReadableResponse('error', e);
            }
        },

        async create(payload: CreateAppointmentPayload) {
            try {
                const response = await apiClient.post({
                    route: `appointment`,
                    body: payload,
                });
                return await apiClient.toReadableResponse('complete', response);
            } catch (e) {
                return await apiClient.toReadableResponse('error', e);
            }
        },

        async list(payload: ListAppointmentsPayload) {
            try {
                const parameters = apiClient.toURLSearchParams(payload);
                const response = await apiClient.get({
                    route: `appointment?${parameters}`,
                    body: payload,
                });
                return await apiClient.toReadableResponse('complete', response);
            } catch (e) {
                return await apiClient.toReadableResponse('error', e);
            }
        },

        async update(payload: UpdateAppointmentPayload) {
            try {
                payload._method = 'PUT';
                const response = await apiClient.post({
                    route: `appointment/${payload.appointment_id}`,
                    body: payload,
                });
                return await apiClient.toReadableResponse('complete', response);
            } catch (e) {
                return await apiClient.toReadableResponse('error', e);
            }
        },
    },
});
