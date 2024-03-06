<template>
  <Page
    :title="page.title"
    :breadcrumbs="page.breadcrumbs"
    :actions="page.actions"
    @action="onAction"
  >
    <Panel>
      <Form id="create-message" @submit.prevent="onSubmit">
          <TextInput
          class="mb-4"
          type="textarea"
          :required="true"
          name="message"
          v-model="form.message"
          :label="trans('messages.labels.message')"
        />
        <TextInput
          class="mb-4"
          type="datetime-local"
          :required="true"
          name="openingTime"
          v-model="form.scheduled_opening_time"
          :label="trans('messages.labels.opentime')"
        />

      </Form>
    </Panel>
  </Page>
</template>

<script>
import { defineComponent, reactive } from "vue";
import { trans } from "@/helpers/i18n";
import TextInput from "@/views/components/input/TextInput";
import Panel from "@/views/components/Panel";
import Page from "@/views/layouts/Page";
import MessageService from "@/services/MessageService";
import { clearObject, reduceProperties } from "@/helpers/data";
import { toUrl } from "@/helpers/routing";
import Form from "@/views/components/Form";

export default defineComponent({
  components: {
    Form,
    Panel,
    TextInput,
    Page,
  },
  setup() {
    const form = reactive({
      first_name: "",
      last_name: "",
      middle_name: "",
      email: "",
      password: "",
    });


    const page = reactive({
      id: "create_messages",
      title: trans("global.pages.messages_create"),
      filters: false,
      breadcrumbs: [
        {
          name: trans("global.pages.messages"),
          to: toUrl("/messages/list"),
        },
        {
          name: trans("global.pages.messages_create"),
          active: true,
        },
      ],
      actions: [
        {
          id: "back",
          name: trans("global.buttons.back"),
          icon: "fa fa-angle-left",
          to: toUrl("/messages/list"),
          theme: "outline",
        },
        {
          id: "submit",
          name: trans("global.buttons.save"),
          icon: "fa fa-save",
          type: "submit",
        },
      ],
    });

    const service = new MessageService();

    function onAction(data) {
      switch (data.action.id) {
        case "submit":
          onSubmit();
          break;
      }
    }

    function onSubmit() {
      service.handleCreate('create-message', reduceProperties(form, 'id')).then(() => {
          clearObject(form)
      })
      return false;
    }

    return {
      trans,
      form,
      page,
      onSubmit,
      onAction,
    };
  },
});
</script>

<style scoped>
</style>
