<script setup lang="ts">
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { ref, onMounted, watch } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import AdminLayout from '@/layouts/admin/Layout.vue';
import DataTable from '@/components/DataTable.vue';
import axios from 'axios';
import { toast } from 'vue-sonner';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Skeleton } from '@/components/ui/skeleton';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { LoaderCircle, Upload, Trash2 } from 'lucide-vue-next';
import InputError from '@/components/InputError.vue';

const page = usePage<any>();

const months = [
    'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
];

watch(() => page.props.flash, (flash: any) => {
    if (flash?.success) {
        toast.success(flash.success);
    }
    if (flash?.error) {
        toast.error(flash.error);
    }
}, { deep: true });

const breadcrumbs = [
    { title: 'Dashboard', href: '/admin/dashboards' },
    { title: 'SP2D NPWP', href: '#' },
];

const fields = [
    { key: 'tahun', label: 'Tahun', type: 'string', sortable: true },
    { key: 'bulan_name', label: 'Bulan', type: 'string', sortable: true },
    { key: 'sp2d_number', label: 'Nomor SP2D', type: 'string', sortable: true },
    { key: 'npwp_bud', label: 'NPWP BUD', type: 'string' },
    { key: 'npwp_skpd', label: 'NPWP SKPD', type: 'string' },
    { key: 'npwp_penerima', label: 'NPWP Penerima', type: 'string' },
    { key: 'nama_penerima', label: 'Nama Penerima', type: 'string' },
];

const isLoading = ref(false);
const items = ref([]);
const dataResponse = ref<any>(null);
const search = ref('');

async function loadData(url: string | null = null, pageNumber: number = 1) {
    try {
        isLoading.value = true;
        const targetUrl = url || route('admin.sp2d-npwp.data');
        const params = url ? {} : {
            page: pageNumber,
            search: search.value
        };

        const res = await axios.get(targetUrl, { params });
        dataResponse.value = res.data.data;
        items.value = res.data.data.data;
    } catch (error) {
        console.error('Failed to load data:', error);
        toast.error('Gagal mengambil data.');
    } finally {
        isLoading.value = false;
    }
}

function handleClickPaging(link: any) {
    if (typeof link === 'string') {
        loadData(link);
    } else if (link?.url) {
        loadData(link.url);
    }
}

function handleSearch(s: string) {
    search.value = s;
    loadData(null, 1);
}

// Import logic
const isImportOpen = ref(false);
const importForm = useForm({
    file: null as File | null,
    bulan: null as string | null,
});

function handleImport() {
    isImportOpen.value = true;
}

function submitImport() {
    if (!importForm.file) {
        toast.error('Pilih file terlebih dahulu');
        return;
    }
    if (!importForm.bulan) {
        toast.error('Pilih bulan terlebih dahulu');
        return;
    }

    importForm.post(route('admin.sp2d-npwp.import'), {
        onSuccess: () => {
            isImportOpen.value = false;
            importForm.reset();
            toast.success('File berhasil diproses');
            loadData();
        },
        onError: (err) => {
            toast.error(Object.values(err)[0] as string || 'Gagal mengimport data');
        }
    });
}

// Delete logic
const isDeleteDialogOpen = ref(false);
const itemToDeleteId = ref<number | null>(null);

function handleDelete(id: number) {
    itemToDeleteId.value = id;
    isDeleteDialogOpen.value = true;
}

async function confirmDelete() {
    if (!itemToDeleteId.value) return;
    try {
        await axios.delete(route('admin.sp2d-npwp.destroy', itemToDeleteId.value));
        loadData();
    } catch (error) {
        toast.error('Gagal menghapus data');
    } finally {
        isDeleteDialogOpen.value = false;
        itemToDeleteId.value = null;
    }
}

const buttonDinamis = [
    { label: 'Import Excel', variant: 'primary', onClick: 'handleImport' }
] as const;

const dropdownActions = [
    { key: 'delete', label: 'Hapus', emit: 'delete' }
];

const handlers: Record<string, Function> = {
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
    <Head title="SP2D NPWP" />

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
                        :dropdownActions="dropdownActions"
                        @delete="handleDelete"
                        @search="handleSearch"
                        @clickPaging="handleClickPaging"
                        @button-click="dispatchMethod"
                    />

                    <div v-if="isLoading" class="p-8">
                        <Skeleton class="h-[300px] w-full" />
                    </div>
                </div>
            </div>

            <!-- Import Dialog -->
            <Dialog v-model:open="isImportOpen">
                <DialogContent>
                    <DialogHeader>
                        <DialogTitle>Import Data SP2D NPWP</DialogTitle>
                        <DialogDescription>
                            Pilih bulan dan upload file Excel (.xlsx, .xls) untuk sheet DTH.
                        </DialogDescription>
                    </DialogHeader>
                    
                    <div class="grid gap-4 py-4">
                        <div class="grid gap-2">
                            <Label for="bulan">Bulan Pelaporan</Label>
                            <Select v-model="importForm.bulan">
                                <SelectTrigger>
                                    <SelectValue placeholder="Pilih Bulan" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem v-for="(m, i) in months" :key="i" :value="(i + 1).toString()">
                                        {{ m }}
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                            <InputError :message="importForm.errors.bulan" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="file">File Excel</Label>
                            <Input 
                                id="file" 
                                type="file" 
                                accept=".xlsx,.xls,.csv"
                                @change="importForm.file = ($event.target as HTMLInputElement).files?.[0] || null"
                            />
                            <InputError :message="importForm.errors.file" />
                        </div>
                    </div>

                    <DialogFooter>
                        <Button variant="secondary" @click="isImportOpen = false">Batal</Button>
                        <Button :disabled="importForm.processing" @click="submitImport">
                            <LoaderCircle v-if="importForm.processing" class="mr-2 h-4 w-4 animate-spin" />
                            <Upload v-else class="mr-2 h-4 w-4" />
                            Import Data
                        </Button>
                    </DialogFooter>
                </DialogContent>
            </Dialog>

            <!-- Delete Dialog -->
            <Dialog v-model:open="isDeleteDialogOpen">
                <DialogContent>
                    <DialogHeader>
                        <DialogTitle>Konfirmasi Hapus</DialogTitle>
                        <DialogDescription>
                            Apakah Anda yakin ingin menghapus data ini?
                        </DialogDescription>
                    </DialogHeader>
                    <DialogFooter>
                        <Button variant="secondary" @click="isDeleteDialogOpen = false">Batal</Button>
                        <Button variant="destructive" @click="confirmDelete">
                            <Trash2 class="mr-2 h-4 w-4" />
                            Hapus
                        </Button>
                    </DialogFooter>
                </DialogContent>
            </Dialog>
        </AdminLayout>
    </AppLayout>
</template>
