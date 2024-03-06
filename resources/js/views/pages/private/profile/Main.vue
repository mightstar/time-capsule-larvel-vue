<template>
    <Page>
        <div class="w-full xl:w-2/3 ml-auto mr-auto">
            <Overview class="mb-4"/>
            <FormGeneral class="mb-4"/>
            <FormPassword class="mb-4"/>
        </div>
    </Page>

</template>

<script>
import {computed, defineComponent, ref} from "vue";
import {trans} from "@/helpers/i18n";
import {useAuthStore} from "@/stores/auth";
import FormPassword from "@/views/pages/private/profile/partials/FormPassword";
import FormGeneral from "@/views/pages/private/profile/partials/FormGeneral";
import Overview from "@/views/pages/private/profile/partials/Overview";
import Page from "@/views/layouts/Page";
import Modal from "@/views/components/Modal";
import Panel from "@/views/components/Panel";
import AuthService from "@/services/AuthService";
import {useAlertStore} from "@/stores";
import {getResponseError} from "@/helpers/api";
import Badge from "@/views/components/Badge";
import Button from "@/views/components/input/Button";

export default defineComponent({
    components: {
        Button,
        Badge,
        Panel,
        Modal,
        Page,
        Overview,
        FormGeneral,
        FormPassword,
    },
    setup() {

        const authService = new AuthService();
        const alertStore = useAlertStore();

        function onVerificationSend() {
            const {user} = useAuthStore();
            authService.sendVerification({user: user.id})
                .then((response) => (alertStore.success(trans('global.phrases.verification_sent'))))
                .catch((error) => (alertStore.error(getResponseError(error))));
        }

        return {
            onVerificationSend,
            trans
        }
    }
});
</script>
