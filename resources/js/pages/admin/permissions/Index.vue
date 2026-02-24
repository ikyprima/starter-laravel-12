<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import DataTable from '@/components/DataTable.vue';
import axios from 'axios';
import { Skeleton } from '@/components/ui/skeleton';
import AdminLayout from '@/layouts/admin/Layout.vue';

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

interface PaginationResponse {
    current_page: number;
    data: any[];
    first_page_url: string;
    from: number;
    last_page: number;
    last_page_url: string;
    links: PaginationLink[];
    next_page_url: string | null;
    path: string;
    per_page: number;
    prev_page_url: string | null;
    to: number;
    total: number;
}

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Permissions', href: '/admin/permissions' },
];

const isLoading = ref(false);
const permissions = ref([]);
const dataResponse = ref<PaginationResponse | null>(null);

const fields = [
    { key: 'name', label: 'Name', type: 'string', sortable: true },
];

async function loadData() {
    try {
        isLoading.value = true;
        await axios.get(route('admin.permissions.data'))
            .then((res) => {
                dataResponse.value = res.data.data;
                permissions.value = res.data.data.data;
            })
            .finally(() => {
                isLoading.value = false;
            });
    } catch (error) {
        console.error('Failed to load data:', error);
    }
}

const buttonDinamis = [
    {
        label: "Create Permission",
        variant: "primary",
        onClick: "handleCreate"
    }
];

function handleCreate() {
    router.visit(route('admin.permissions.create'));
}

function handleEdit(id: number) {
    router.visit(route('admin.permissions.edit', id));
}

function handleSearch(search: string) {
    isLoading.value = true;
    axios.get(route('admin.permissions.data'), { params: { search } })
        .then((res) => {
             dataResponse.value = res.data.data;
             permissions.value = res.data.data.data;
        })
        .finally(() => isLoading.value = false);
}

const handleClickPaging = async (link: string) => {
      try {
        isLoading.value = true
        await axios.get(link)
          .then((res) => {
              dataResponse.value = res.data.data
              permissions.value = res.data.data.data
          })
          .finally(() => {
              isLoading.value = false
          });   
      } catch (error) {
        console.error('Failed load data:', error)
      }
}

const handlers: Record<string, Function> = {
    handleCreate
};

function dispatchMethod(value: { action: string; value: any }) {
    if (handlers[value.action]) {
        handlers[value.action](value.value);
    }
}

onMounted(() => {
    loadData();
});
</script>

<template>
    <Head title="Permissions" />
    
    <AppLayout :breadcrumbs="breadcrumbs">
        <AdminLayout>
            <div class="flex h-full flex-1 flex-col gap-4">
                 <div class="relative min-h-[100vh] flex-1 p-4 rounded-xl border border-sidebar-border/70 dark:border-sidebar-border md:min-h-min bg-card">
                    <DataTable v-if="!isLoading"
                        :data="permissions"
                        :fieldsFromDB="fields"
                        :currentPage="dataResponse?.current_page ?? 0"
                        :totalItems="dataResponse?.total ?? 0"
                        :perPage="dataResponse?.per_page ?? 0"
                        :paginationLinks="dataResponse?.links ?? []"
                        :firstPageUrl="dataResponse?.first_page_url ?? ''"
                        :lastPageUrl="dataResponse?.last_page_url ?? ''"
                        :buttonDinamis="buttonDinamis"
                        @edits="handleEdit"
                        @clickPaging="handleClickPaging"
                        @search="handleSearch"
                        @button-click="dispatchMethod"
                    />

                    <div v-if="isLoading" class="overflow-x-auto w-full border rounded-md">
                          <table class="w-full table-auto border-collapse">
                            <thead>
                              <tr class="border-b">
                                <th v-for="n in 1" :key="n" class="px-4 py-2 text-left">
                                  <Skeleton class="h-4 w-24" />
                                </th>
                              </tr>
                            </thead>
                            <tbody>
                              <tr v-for="row in 5" :key="row" class="border-b">
                                <td v-for="col in 1" :key="col" class="p-4">
                                    <Skeleton class="h-6 w-full" />
                                </td>
                              </tr>
                            </tbody>
                          </table>
                    </div>
                </div>
            </div>
        </AdminLayout>
    </AppLayout>
</template>
