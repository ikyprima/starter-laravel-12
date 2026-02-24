<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import DataTable from '@/components/DataTable.vue';
import axios from 'axios';
import { Skeleton } from '@/components/ui/skeleton';
import AdminLayout from '@/layouts/admin/Layout.vue';
import type { DynamicButton } from '@/components/DataTable.vue';

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
    { title: 'Administrator', href: '/admin/dashboards' },
    { title: 'Laporan Realisasi', href: '#' },
];

const isLoading = ref(false);
const items = ref([]);
const dataResponse = ref<PaginationResponse | null>(null);

const fields = [
    { key: 'tanggal_dokumen', label: 'Tanggal Doc', type: 'string', sortable: true },
    { key: 'nomor_dokumen', label: 'No Dokumen', type: 'string', sortable: true },
    { key: 'kode_rekening', label: 'Rekening', type: 'string', sortable: true },
    { key: 'jenis_transaksi', label: 'Jenis', type: 'string', sortable: true },
    { key: 'nilai_realisasi', label: 'Realisasi', type: 'amount', sortable: true },
    { key: 'nilai_setoran', label: 'Setoran', type: 'amount', sortable: true },
    { key: 'nomor_sp2d', label: 'SP2D', type: 'string', sortable: true },
];

async function loadData() {
    try {
        isLoading.value = true;
        const res = await axios.get(route('admin.laporan-realisasi.data'));
        dataResponse.value = res.data.data;
        items.value = res.data.data.data;
    } catch (error) {
        console.error('Failed to load data:', error);
    } finally {
        isLoading.value = false;
    }
}

const buttonDinamis: DynamicButton[] = [
    {
        label: "Sinkron External API",
        variant: "outline",
        onClick: "handleSync"
    },
    {
        label: "Import Excel",
        variant: "outline",
        onClick: "handleImport"
    }
];

async function handleSync() {
    try {
        isLoading.value = true;
        const res = await axios.post(route('admin.laporan-realisasi.sync'));
        alert(res.data.message);
        loadData();
    } catch (error) {
        alert('Gagal sinkronisasi data');
    } finally {
        isLoading.value = false;
    }
}

async function handleImport() {
    try {
        isLoading.value = true;
        const res = await axios.post(route('admin.laporan-realisasi.import'));
        alert(res.data.message);
        loadData();
    } catch (error) {
        alert('Gagal import data');
    } finally {
        isLoading.value = false;
    }
}

function handleSearch(search: string) {
    isLoading.value = true;
    axios.get(route('admin.laporan-realisasi.data'), { params: { search } })
        .then((res) => {
            dataResponse.value = res.data.data;
            items.value = res.data.data.data;
        })
        .finally(() => isLoading.value = false);
}

const handleClickPaging = async (link: string) => {
    try {
        isLoading.value = true;
        const res = await axios.get(link);
        dataResponse.value = res.data.data;
        items.value = res.data.data.data;
    } catch (error) {
        console.error('Failed load data:', error);
    } finally {
        isLoading.value = false;
    }
}

const handlers: Record<string, Function> = {
    handleSync,
    handleImport
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
    <Head title="Laporan Realisasi" />
    
    <AppLayout :breadcrumbs="breadcrumbs">
        <AdminLayout>
            <div class="flex h-full flex-1 flex-col gap-4">
                <div class="relative min-h-[100vh] flex-1 p-4 rounded-xl border border-sidebar-border/70 dark:border-sidebar-border md:min-h-min bg-card">
                    <DataTable v-if="!isLoading"
                        :data="items"
                        :fieldsFromDB="fields"
                        :currentPage="dataResponse?.current_page ?? 0"
                        :totalItems="dataResponse?.total ?? 0"
                        :perPage="dataResponse?.per_page ?? 0"
                        :paginationLinks="dataResponse?.links ?? []"
                        :firstPageUrl="dataResponse?.first_page_url ?? ''"
                        :lastPageUrl="dataResponse?.last_page_url ?? ''"
                        :buttonDinamis="buttonDinamis"
                        @clickPaging="handleClickPaging"
                        @search="handleSearch"
                        @button-click="dispatchMethod"
                    />

                    <div v-if="isLoading" class="overflow-x-auto w-full border rounded-md">
                        <table class="w-full table-auto border-collapse">
                            <thead>
                                <tr class="border-b">
                                    <th v-for="n in 5" :key="n" class="px-4 py-2 text-left">
                                        <Skeleton class="h-4 w-24" />
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="row in 5" :key="row" class="border-b">
                                    <td v-for="col in 5" :key="col" class="p-4">
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
