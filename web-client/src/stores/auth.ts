import { defineStore } from 'pinia';
import apiClient from '@/lib/api-client.ts';
import { LocationQueryValue } from 'vue-router';

export interface AuthRegisterPayload {
    name?: string | null;
    email?: string | null;
    password?: string | null;
    password_confirmation?: string | null;
    level?: string | null;
    image?: File | null;
}
export interface AuthLoginPayload {
    email?: string | null;
    password?: string | null;
}
export interface OAuthLoginPayload {
    level?: LocationQueryValue | null;
    code?: string | null;
    provider?: string | null;
}

export const useAuthStore = defineStore('auth', {
    state: () => ({
        isAuthenticated: false,
        accessToken: null,
        user: null,
    }),

    actions: {
        async establishAuth({ user, accessToken }) {
            this.user = Object.assign({}, user);
            this.accessToken = accessToken;
            this.isAuthenticated = true;
            window.localStorage.setItem('access_token', accessToken);
            window.localStorage.setItem('user', JSON.stringify(user));
        },
        disableAuth() {
            this.isAuthenticated = false;
        },
        removeAuth() {
            window.localStorage.removeItem('access_token');
            window.localStorage.removeItem('user');
            this.accessToken = null;
            this.user = null;
        },

        async register(payload: AuthRegisterPayload) {
            try {
                const response = await apiClient.post({
                    route: 'auth/register',
                    body: payload,
                    transform: 'form-data',
                });
                const authData = response.data;
                this.establishAuth({
                    user: authData.user,
                    accessToken: authData.access_token,
                });
                return await apiClient.toReadableResponse('complete', response);
            } catch (e) {
                return await apiClient.toReadableResponse('error', e);
            }
        },
        async login(payload: AuthLoginPayload) {
            try {
                const response = await apiClient.post({
                    route: 'auth/login',
                    body: payload,
                });
                const { user, access_token } = response.data;
                const authData = response.data;
                this.establishAuth({
                    user: authData.user,
                    accessToken: authData.access_token,
                });
                return await apiClient.toReadableResponse('complete', response);
            } catch (e) {
                return await apiClient.toReadableResponse('error', e);
            }
        },
        async oAuthentication(payload: OAuthLoginPayload) {
            try {
                const response = await apiClient.post({
                    route: `auth/oauth`,
                    body: payload,
                });
                const authData = response.data;
                this.establishAuth({
                    user: authData.user,
                    accessToken: authData.access_token,
                });
                return await apiClient.toReadableResponse('complete', response);
            } catch (e) {
                return await apiClient.toReadableResponse('error', e);
            }
        },

        async refresh() {
            try {
                const response = await apiClient.get({
                    route: `auth/refresh`,
                });
                const authData = response.data;
                this.establishAuth({
                    user: authData.user,
                    accessToken: authData.access_token,
                });
                return await apiClient.toReadableResponse('complete', response);
            } catch (e) {
                this.disableAuth();
                this.removeAuth();
                return await apiClient.toReadableResponse('error', e);
            }
        },

        async logout() {
            try {
                const response = await apiClient.get({
                    route: `auth/logout`,
                });
                return await apiClient.toReadableResponse('complete', response);
            } catch (e) {
                return await apiClient.toReadableResponse('error', e);
            }
        },
    },
});
