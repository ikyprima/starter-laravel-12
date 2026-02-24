<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import DataTable from '@/components/DataTable.vue';
import axios from 'axios';
import AdminLayout from '@/layouts/admin/Layout.vue';
import { Skeleton } from '@/components/ui/skeleton';
import { LoaderCircle, AlertTriangle, CheckCircle2 } from 'lucide-vue-next';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Button } from '@/components/ui/button';

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
    { title: 'SKPD', href: '/admin/skpd' },
];

const isLoading = ref(false);
const skpds = ref([]);
const dataResponse = ref<PaginationResponse | null>(null);
const isImporting = ref(false);

// Dialog states
const isConfirmDelete = ref(false);
const isConfirmImport = ref(false);
const isShowResult = ref(false);
const resultMessage = ref('');
const deleteId = ref<number | null>(null);

const fields = [
    { key: 'kode_skpd', label: 'Kode SKPD', type: 'string', sortable: true },
    { key: 'nama_skpd', label: 'Nama SKPD', type: 'string', sortable: true },
    { key: 'tahun', label: 'Tahun', type: 'string', sortable: true },
];

async function loadData() {
    try {
        isLoading.value = true;
        const res = await axios.get(route('admin.skpd.data'));
        dataResponse.value = res.data.data;
        skpds.value = res.data.data.data;
    } catch (error) {
        console.error('Failed to load data:', error);
    } finally {
        isLoading.value = false;
    }
}

const buttonDinamis = [
   
    {
        label: "Import SKPD",
        variant: "outline" as const,
        onClick: "handleImport"
    }, {
        label: "Tambah SKPD",
        variant: "primary" as const,
        onClick: "handleCreate"
    }
];

function handleCreate() {
    router.visit(route('admin.skpd.create'));
}

function handleEdit(id: number) {
    router.visit(route('admin.skpd.edit', id));
}

function handleDelete(id: number) {
    deleteId.value = id;
    isConfirmDelete.value = true;
}



function handleBkuPajak(data: any) {
    router.visit(route('admin.skpd.bku-pajak', data.id));
}

function handleBku(data: any) {
    router.visit(route('admin.skpd.bku', data.id));
}

function confirmDelete() {
    if (!deleteId.value) return;
    
    router.delete(route('admin.skpd.destroy', deleteId.value), {
        onSuccess: () => {
            isConfirmDelete.value = false;
            loadData();
        }
    });
}

function handleImport() {
    isConfirmImport.value = true;
}

async function startImport() {
    isConfirmImport.value = false;
    isImporting.value = true;
    try {
        const res = await axios.get(route('admin.skpd.import'));
        if (res.data.success) {
            resultMessage.value = res.data.message;
            isShowResult.value = true;
            loadData();
        }
    } catch (e: any) {
        resultMessage.value = e.response?.data?.message || 'Gagal mengimpor data.';
        isShowResult.value = true;
    } finally {
        isImporting.value = false;
    }
}

function handleSearch(search: string) {
    isLoading.value = true;
    axios.get(route('admin.skpd.data'), { params: { search } })
        .then((res) => {
            dataResponse.value = res.data.data;
            skpds.value = res.data.data.data;
        })
        .finally(() => isLoading.value = false);
}

const handleClickPaging = async (link: string) => {
    try {
        isLoading.value = true;
        const res = await axios.get(link);
        dataResponse.value = res.data.data;
        skpds.value = res.data.data.data;
    } catch (error) {
        console.error('Failed load data:', error);
    } finally {
        isLoading.value = false;
    }
}


const dropdownActions = ref([
    {
        key: 'bku',
        label: "BKU SKPD",
        variant: "outline" as const,
        emit: "bku"
    },
    {
        key: 'bkuPajak',
        label: "BKU Pajak",
        variant: "outline" as const,
        emit: "bkuPajak"
    },
]);

const handlers: Record<string, Function> = {
    handleCreate,
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
    <Head title="SKPD" />
    
    <AppLayout :breadcrumbs="breadcrumbs">
        <AdminLayout>
            <div class="flex h-full flex-1 flex-col gap-4">
                 <div v-if="isImporting" class="mb-4 p-4 bg-indigo-50 border border-indigo-200 rounded-lg flex items-center gap-3">
                    <LoaderCircle class="animate-spin text-indigo-600" />
                    <span class="text-indigo-900 font-medium">Sedang mengimpor data dari API SIPD...</span>
                </div>
                 <div class="relative min-h-[100vh] flex-1 p-4 rounded-xl border border-sidebar-border/70 dark:border-sidebar-border md:min-h-min bg-card">
                    <DataTable v-if="!isLoading"
                        :data="skpds"
                        :fieldsFromDB="fields"
                        :currentPage="dataResponse?.current_page ?? 0"
                        :totalItems="dataResponse?.total ?? 0"
                        :perPage="dataResponse?.per_page ?? 0"
                        :paginationLinks="dataResponse?.links ?? []"
                        :firstPageUrl="dataResponse?.first_page_url ?? ''"
                        :lastPageUrl="dataResponse?.last_page_url ?? ''"
                        :buttonDinamis="buttonDinamis"
                         :dropdownActions="dropdownActions"
                        @edits="handleEdit"
                        @delete="handleDelete"
                        @clickPaging="handleClickPaging"
                        @search="handleSearch"
                        @button-click="dispatchMethod"
                        @bkuPajak="handleBkuPajak"
                        @bku="handleBku"
                    />

                    <!-- Skeleton Loading State -->
                    <div v-if="isLoading" class="overflow-x-auto w-full border rounded-md">
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

            <!-- Delete Confirmation Dialog -->
            <Dialog v-model:open="isConfirmDelete">
                <DialogContent>
                    <DialogHeader>
                        <DialogTitle class="flex items-center gap-2">
                            <AlertTriangle class="h-5 w-5 text-destructive" />
                            Konfirmasi Hapus
                        </DialogTitle>
                        <DialogDescription>
                            Apakah Anda yakin ingin menghapus data SKPD ini? Tindakan ini tidak dapat dibatalkan.
                        </DialogDescription>
                    </DialogHeader>
                    <DialogFooter>
                        <Button variant="outline" @click="isConfirmDelete = false">Batal</Button>
                        <Button variant="destructive" @click="confirmDelete">Hapus Sekarang</Button>
                    </DialogFooter>
                </DialogContent>
            </Dialog>

            <!-- Import Confirmation Dialog -->
            <Dialog v-model:open="isConfirmImport">
                <DialogContent>
                    <DialogHeader>
                        <DialogTitle>Konfirmasi Impor</DialogTitle>
                        <DialogDescription>
                            Sistem akan mengambil data dari API External. Data yang sudah ada dengan kode yang sama akan diperbarui. Lanjutkan?
                        </DialogDescription>
                    </DialogHeader>
                    <DialogFooter>
                        <Button variant="outline" @click="isConfirmImport = false">Batal</Button>
                        <Button @click="startImport">Ya, Mulai Impor</Button>
                    </DialogFooter>
                </DialogContent>
            </Dialog>

            <!-- Result Dialog -->
            <Dialog v-model:open="isShowResult">
                <DialogContent>
                    <DialogHeader>
                        <DialogTitle class="flex items-center gap-2">
                            <CheckCircle2 class="h-5 w-5 text-emerald-500" />
                            Hasil Proses
                        </DialogTitle>
                        <DialogDescription>
                            {{ resultMessage }}
                        </DialogDescription>
                    </DialogHeader>
                    <DialogFooter>
                        <Button @click="isShowResult = false">Tutup</Button>
                    </DialogFooter>
                </DialogContent>
            </Dialog>
        </AdminLayout>
    </AppLayout>
</template>
