<template>
    <div :style="style" :class="$props.class">
      <label :for="name" class="text-sm text-gray-500" :class="{ 'sr-only': !showLabel }">
        <input type="checkbox" :id="name" :checked="modelValue" @change="onChange" class="mr-2">
        {{ label }}
        <span class="text-red-600" v-if="$props.required">*</span>
      </label>
    </div>
  </template>

  <script>
  import { defineComponent } from "vue";

  export default defineComponent({
    inheritAttrs: false,
    props: {
      class: String,
      style: [String, Object],
      name: {
        type: String,
        required: true,
      },
      label: {
        type: String,
        default: "",
      },
      modelValue: {
        default: false,
        type: Boolean,
      },
      showLabel: {
        type: Boolean,
        default: true,
      },
      required: {
        type: Boolean,
        default: false,
      },
    },
    emits: ['update:modelValue'],
    setup(props, { emit }) {
      function onChange(event) {
        emit("update:modelValue", event.target.checked);
      }

      return {
        onChange
      }
    }
  });
  </script>
