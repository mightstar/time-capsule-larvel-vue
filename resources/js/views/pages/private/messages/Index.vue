<template>
  <Page
    :title="page.title"
    :breadcrumbs="page.breadcrumbs"
    :actions="page.actions"
    @action="onPageAction"
  >
    <template #filters v-if="page.toggleFilters">
      <Filters @clear="onFiltersClear">
        <FiltersRow>
          <FiltersCol>
            <TextInput
              name="message"
              :label="trans('messages.labels.message')"
              v-model="mainQuery.filters.message.value"
            ></TextInput>
          </FiltersCol>
          <FiltersCol>
            <Checkbox
              name="unopened"
              :label="trans('messages.labels.not_opened')"
              v-model="mainQuery.filters.un_opened.value"
            ></Checkbox>
          </FiltersCol>
        </FiltersRow>
      </Filters>
    </template>

    <template #default>
      <Table
        :id="page.id"
        v-if="table"
        :headers="table.headers"
        :sorting="table.sorting"
        :actions="table.actions"
        :records="table.records"
        :pagination="table.pagination"
        :is-loading="table.loading"
        @page-changed="onTablePageChange"
        @action="onTableAction"
        @sort="onTableSort"
        @record-click="onTableRecordClick"
      >
        <template v-slot:content-id="props">
          <div class="flex items-center">
            <div class="flex-shrink-0 h-10 w-10">
              <Message class="w-10 h-10 text-gray-400 rounded-full" />
            </div>
            <div class="ml-4">
              <div class="text-sm text-gray-500">
                {{ trans("messages.labels.id") + ": " + props.item.id }}
              </div>
            </div>
          </div>
        </template>
        <template v-slot:content-status="props">
          <span
            v-if="props.item.is_opened"
            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800"
            v-html="trans('messages.status.opened')"
          ></span>
          <span
            v-else
            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800"
            v-html="trans('messages.status.not_opened')"
          ></span>
          <br />
          <span
            v-if="isFuture(props.item.scheduled_opening_time)"
            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-300 text-black mt-2"
            v-html="trans('messages.status.locked')"
          ></span>
          <span
            v-else
            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-red-800 mt-2"
            v-html="trans('messages.status.not_locked')"
          ></span>
        </template>
      </Table>
    </template>
  </Page>

  <Modal
    :is-showing="isMessageModalShowing"
    @close="isMessageModalShowing = false"
  >
    <MessageBoard :content="messageContent" />
  </Modal>
</template>

<script>
import { trans } from "@/helpers/i18n";
import { watch, onMounted, defineComponent, reactive, ref } from "vue";
import { getResponseError, prepareQuery } from "@/helpers/api";
import { toUrl } from "@/helpers/routing";
import { useAlertStore } from "@/stores";
import alertHelpers from "@/helpers/alert";
import Page from "@/views/layouts/Page";
import Table from "@/views/components/Table";
import Message from "@/views/components/icons/Message";
import Modal from "@/views/components/Modal";
import Filters from "@/views/components/filters/Filters";
import FiltersRow from "@/views/components/filters/FiltersRow";
import FiltersCol from "@/views/components/filters/FiltersCol";
import TextInput from "@/views/components/input/TextInput";
import Checkbox from "@/views/components/input/Checkbox";
import MessageBoard from "@/views/pages/private/messages/partials/MessageBord";
import Dropdown from "@/views/components/input/Dropdown";
import MessageService from "@/services/MessageService";

export default defineComponent({
  components: {
    Dropdown,
    TextInput,
    FiltersCol,
    FiltersRow,
    Filters,
    Page,
    Table,
    Message,
    Checkbox,
    Modal,
    MessageBoard,
  },
  setup() {
    const service = new MessageService();
    const alertStore = useAlertStore();
    const isMessageModalShowing = ref(false);
    const messageContent = ref("");
    const mainQuery = reactive({
      page: 1,
      search: "",
      sort: "",
      filters: {
        scheduled_opening_time: {
          value: "",
          comparison: "=",
        },
        un_opened: {
          value: false,
          comparison: "=",
        },
      },
    });

    const page = reactive({
      id: "list_messages",
      title: trans("global.pages.messages"),
      breadcrumbs: [
        {
          name: trans("global.pages.messages"),
          to: toUrl("/messages"),
          active: true,
        },
      ],
      actions: [
        {
          id: "filters",
          name: trans("global.buttons.filters"),
          icon: "fa fa-filter",
          theme: "outline",
        },
        {
          id: "new",
          name: trans("global.buttons.add_new"),
          icon: "fa fa-plus",
          to: toUrl("/messages/create"),
        },
      ],
      toggleFilters: false,
    });

    const table = reactive({
      headers: {
        id: trans("messages.labels.id_pound"),
        scheduled_opening_time: trans("messages.labels.opentime"),
        status: trans("messages.labels.status"),
      },
      sorting: {
        scheduled_opening_time: true,
        status: false,
      },
      pagination: {
        meta: null,
        links: null,
      },
      actions: {
        edit: {
          id: "edit",
          name: trans("global.actions.edit"),
          icon: "fa fa-edit",
          showName: false,
          to: toUrl("/messages/{id}/edit"),
        },
        delete: {
          id: "delete",
          name: trans("global.actions.delete"),
          icon: "fa fa-trash",
          showName: false,
          danger: true,
        },
      },
      loading: false,
      records: null,
    });

    function isFuture(time) {
      return new Date(time) > new Date();
    }

    function onTableSort(params) {
      mainQuery.sort = params;
    }

    function onTablePageChange(page) {
      mainQuery.page = page;
    }

    function onTableRecordClick(record) {
      service.find(record.id).then((response) => {
        messageContent.value = response.data.model.message;
        isMessageModalShowing.value = true;
        page.loading = false;
      });
    }

    function onTableAction(params) {
      switch (params.action.id) {
        case "delete":
          alertHelpers.confirmDanger(function () {
            service
              .delete(params.item.id)
              .then(function (response) {
                alertStore.success("Delete message success!");
                fetchPage(mainQuery);
              })
              .catch((error) => {
                alertStore.error(getResponseError(error));
              });
          });
          break;
      }
    }

    function onPageAction(params) {
      switch (params.action.id) {
        case "filters":
          page.toggleFilters = !page.toggleFilters;
          break;
      }
    }

    function onFiltersClear() {
      let clonedFilters = mainQuery.filters;
      for (let key in clonedFilters) {
        clonedFilters[key].value = "";
      }
      mainQuery.filters = clonedFilters;
    }

    function fetchPage(params) {
      table.records = [];
      table.loading = true;
      let query = prepareQuery(params);

      service
        .index(query)
        .then((response) => {
          table.records = response.data.data;
          table.pagination.meta = response.data.meta;
          table.pagination.links = response.data.links;
          table.loading = false;
        })
        .catch((error) => {
          alertStore.error(getResponseError(error));
          table.loading = false;
        });
    }

    watch(mainQuery, (newTableState) => {
      fetchPage(mainQuery);
    });

    onMounted(() => {
      fetchPage(mainQuery);
    });

    return {
      trans,
      page,
      table,
      messageContent,
      onTablePageChange,
      onTableAction,
      onTableSort,
      onPageAction,
      onFiltersClear,
      mainQuery,
      isFuture,
      isMessageModalShowing,
      onTableRecordClick,
    };
  },
});
</script>
