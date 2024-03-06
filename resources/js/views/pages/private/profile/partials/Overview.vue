<template>
    <Panel>
        <div class="flex">
            <div class="w-1/6 px-2">
                <div class="rounded-full">
                </div>
            </div>
            <div class="w-5/6 px-6 pt-2">
                <div class="items-center">
                    <ul class="mt-2">
                        <li class="mb-1 text-2xl font-bold">{{ authStore.user.full_name }}
                            <Badge theme="success" class="inline" v-if="authStore.user.email_verified_at">
                                {{ trans('users.status.verified') }}
                            </Badge>
                        </li>
                        <li class="text-gray-700"><i class="fa fa-envelope"></i> {{ authStore.user.email }}</li>
                        <li class="mt-5 text-gray-500">{{
                                trans('global.phrases.member_since', {date: authStore.user.created_at})
                            }}
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </Panel>
</template>

<script>
import AuthService from "@/services/AuthService";
import {getResponseError} from "@/helpers/api";

import {trans} from "@/helpers/i18n";

import {computed, defineComponent} from 'vue'
import {useAuthStore} from "@/stores/auth";
import {useAlertStore} from "@/stores";
import Button from "@/views/components/input/Button";
import Panel from "@/views/components/Panel";
import Badge from "@/views/components/Badge";

export default defineComponent({
    components: {
        Panel,
        Badge,
        Button
    },
    setup(props, {emit}) {
        const authService = new AuthService();
        const alertStore = useAlertStore();
        const authStore = useAuthStore()

        function onVerificationSend() {
            authService.sendVerification({user: authStore.user.id})
                .then((response) => (alertStore.success(trans('global.phrases.verification_sent'))))
                .catch((error) => (alertStore.error(getResponseError(error))));
        }

        return {
            authStore,
            onVerificationSend,
            trans,
        }
    }
});
</script>
