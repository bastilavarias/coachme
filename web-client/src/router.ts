import { createRouter, createWebHistory } from 'vue-router';
import { useAuthStore } from '@/stores/auth.ts';
import { useToast } from '@/components/ui/toast/use-toast';

const { toast } = useToast();

const router = createRouter({
    history: createWebHistory(),
    routes: [
        {
            path: '/',
            component: () => import('@/layouts/PublicLayout.vue'),
            children: [
                {
                    path: '',
                    name: 'home-page',
                    component: () => import('@/pages/HomePage.vue'),
                },
                {
                    path: 'login',
                    name: 'login-page',
                    component: () => import('@/pages/LoginPage.vue'),
                },
                {
                    path: 'register',
                    name: 'register-page',
                    component: () => import('@/pages/RegisterPage.vue'),
                },
                {
                    path: 'callback/:provider',
                    name: 'auth-callback-page',
                    component: () => import('@/pages/AuthCallbackPage.vue'),
                },
            ],
        },

        {
            path: '/dashboard',
            component: () => import('@/layouts/DashboardLayout.vue'),
            children: [
                {
                    path: '',
                    name: 'bookings-page',
                    component: () => import('@/pages/BookingsPage.vue'),
                    meta: {
                        requiresAuth: true,
                        users: ['student', 'instructor'],
                    },
                },
                {
                    path: 'appointment',
                    component: () => import('@/layouts/MirrorLayout.vue'),
                    children: [
                        {
                            path: '',
                            name: 'appointment-page',
                            component: () =>
                                import('@/pages/AppointmentPage.vue'),
                            meta: {
                                requiresAuth: true,
                                users: ['student'],
                            },
                        },
                        {
                            path: 'calendar/:userID',
                            name: 'appointment-calendar-page',
                            component: () =>
                                import('@/pages/AppointmentCalendarPage.vue'),
                            meta: {
                                requiresAuth: true,
                                users: ['student'],
                            },
                        },
                    ],
                },
                {
                    path: 'settings',
                    name: 'settings-page',
                    component: () => import('@/pages/SettingsPage.vue'),
                    meta: {
                        requiresAuth: true,
                        users: ['student', 'instructor'],
                    },
                },
            ],
        },
    ],
});

router.beforeEach(async (to, from, next) => {
    const authStore = useAuthStore();
    const isProtectedRoute = to.matched.some(
        (record) => record.meta.requiresAuth
    );
    await authStore.refresh();
    const isAuthenticated = authStore.isAuthenticated;
    if (isProtectedRoute) {
        const allowedUsers = to.meta.users || [];
        if (!isAuthenticated) {
            toast({
                title: 'Session expired.',
                description:
                    'Your session expired. Please login again to use CoachMe. Thankyou.',
            });
            return next({ name: 'login-page' });
        }
        if (!allowedUsers.includes(authStore.user.level)) {
            return next({ name: 'bookings-page' });
        }
    }
    if (
        isAuthenticated &&
        [
            'home-page',
            'register-page',
            'login-page',
            'auth-callback-page',
        ].includes(to.name)
    ) {
        return next({ name: 'bookings-page' });
    }
    next();
});

export default router;
