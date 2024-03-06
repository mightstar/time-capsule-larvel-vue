<template>
  <Panel :title="trans('messages.labels.message')">Message: {{ $props.content }} </Panel>
</template>

<script>
import { defineComponent } from "vue";
import { trans } from "@/helpers/i18n";
import Panel from "@/views/components/Panel";

export default defineComponent({
  components: {
    Panel,
  },
  props: {
    content: {
      type: String,
      default: "",
    },
  },
  setup(props) {
    return {
      trans,
    };
  },
});
</script>
