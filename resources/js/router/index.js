import { createWebHistory, createRouter } from "vue-router";

import routes from "@/router/routes";

import { useAuthStore } from "@/stores/auth";

const router = createRouter({
    history: createWebHistory(),
    linkActiveClass: "active",
    routes,
});

router.beforeEach(async (to, from, next) => {
    const authStore = useAuthStore();
    const requiresAuth = to?.meta?.requiresAuth;

    if (!authStore.user) {
        await authStore.getCurrentUser();
    }
    if (!authStore.user) {
        authStore.clearBrowserData();
        if (requiresAuth) {
            next({ name: "login" });
        }
    }

    if (to?.meta?.isPublicAuthPage && authStore.user) {
        next({ name: "dashboard" });
        return;
    }

    next();
});

export default router;
