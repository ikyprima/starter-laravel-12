<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
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
    { title: 'Users', href: '/admin/users' },
];

const isLoading = ref(false);
const users = ref([]);
const dataResponse = ref<PaginationResponse | null>(null);

const fieldsFromDB = [
    { key: 'name', label: 'Name', type: 'string', sortable: true },
    { key: 'email', label: 'Email', type: 'string', sortable: true },
     // Custom render for roles handled in DataTable or we need to ensure data has flat structure if strictly sticking to DataTable logic.
     // However, DataTable usually handles basic text. For arrays/roles, we might need a custom slot in DataTable or pre-process data.
     // Let's check how MasterAsesor handles 'wilayah'. It uses 'wilayah.nama' in field key probably?
     // No, MasterAsesor maps data to flat structure in loadData: 'asesor.email': item.asesor?.email
];

// We need to verify if DataTable supports custom slots for columns.
// Looking at MasterAsesor, it maps data in loadData.
// listAsesor.value = res.data.data.data.map(...)

async function loadData() {
    try {
        isLoading.value = true;
        await axios.get(route('admin.users.data'))
            .then((res) => {
                dataResponse.value = res.data.data;
                users.value = res.data.data.data.map((user: any) => ({
                    ...user,
                    roles_list: user.roles.map((r: any) => r.name).join(', ') // Flatten roles for display
                }));
            })
            .finally(() => {
                isLoading.value = false;
            });
    } catch (error) {
        console.error('Failed to load data:', error);
    }
}

// Update fields to use the flattened key
const fields = [
    { key: 'name', label: 'Name', type: 'string', sortable: true },
    { key: 'email', label: 'Email', type: 'string', sortable: true },
    { key: 'roles_list', label: 'Roles', type: 'string', sortable: false },
];

const buttonDinamis = [
    {
        label: "Create User",
        variant: "primary",
        onClick: "handleCreate"
    }
];

function handleCreate() {
    router.visit(route('admin.users.create'));
}

function handleEdit(id: number) {
    router.visit(route('admin.users.edit', id));
}

function handleSearch(search: string) {
    isLoading.value = true;
    axios.get(route('admin.users.data'), { params: { search } })
        .then((res) => {
             dataResponse.value = res.data.data;
                users.value = res.data.data.data.map((user: any) => ({
                    ...user,
                    roles_list: user.roles.map((r: any) => r.name).join(', ')
                }));
        })
        .finally(() => isLoading.value = false);
}

const handleClickPaging = async (link: string) => {
      try {
        isLoading.value = true
        await axios.get(link)
          .then((res) => {
              dataResponse.value = res.data.data
               users.value = res.data.data.data.map((user: any) => ({
                    ...user,
                    roles_list: user.roles.map((r: any) => r.name).join(', ')
                }));
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
    <Head title="Users" />
    
    <AppLayout :breadcrumbs="breadcrumbs">
        <AdminLayout>
            <div class="flex h-full flex-1 flex-col gap-4">
                 <div class="relative min-h-[100vh] flex-1 p-4 rounded-xl border border-sidebar-border/70 dark:border-sidebar-border md:min-h-min bg-card">
                    <DataTable v-if="!isLoading"
                        :data="users"
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
                         <!-- Skeleton Loader -->
                          <table class="w-full table-auto border-collapse">
                            <thead>
                              <tr class="border-b">
                                <th v-for="n in 3" :key="n" class="px-4 py-2 text-left">
                                  <Skeleton class="h-4 w-24" />
                                </th>
                              </tr>
                            </thead>
                            <tbody>
                              <tr v-for="row in 5" :key="row" class="border-b">
                                <td v-for="col in 3" :key="col" class="p-4">
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
