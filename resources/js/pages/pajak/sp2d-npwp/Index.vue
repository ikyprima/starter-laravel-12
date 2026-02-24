<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import PajakLayout from '@/layouts/pajak/Layout.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import DataTable from '@/components/DataTable.vue';
import axios from 'axios';
import { toast } from 'vue-sonner';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { LoaderCircle, Upload, Trash2 } from 'lucide-vue-next';

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Pajak', href: '/pajak-ls' },
    { title: 'SP2D NPWP', href: '#' },
];

const fields = [
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

async function loadData(page = 1) {
    try {
        isLoading.value = true;
        const res = await axios.get(route('pajak.sp2d-npwp.data'), {
            params: {
                page,
                search: search.value
            }
        });
        dataResponse.value = res.data.data;
        items.value = res.data.data.data;
    } catch (error) {
        console.error('Failed to load data:', error);
        toast.error('Gagal mengambil data.');
    } finally {
        isLoading.value = false;
    }
}

// Import logic
const isImportOpen = ref(false);
const fileInput = ref<HTMLInputElement | null>(null);
const importForm = useForm({
    file: null as File | null,
});

function handleImport() {
    isImportOpen.value = true;
}

function submitImport() {
    if (!importForm.file) {
        toast.error('Pilih file terlebih dahulu');
        return;
    }

    importForm.post(route('pajak.sp2d-npwp.import'), {
        onSuccess: () => {
            isImportOpen.value = false;
            toast.success('Data berhasil diimport');
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
        await axios.delete(route('pajak.sp2d-npwp.destroy', itemToDeleteId.value));
        toast.success('Data berhasil dihapus');
        loadData();
    } catch (error) {
        toast.error('Gagal menghapus data');
    } finally {
        isDeleteDialogOpen.value = false;
        itemToDeleteId.value = null;
    }
}

onMounted(() => {
    loadData();
});
</script>

<template>
    <Head title="SP2D NPWP" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <PajakLayout>
            <div class="space-y-6">
                <HeadingSmall title="Master Data SP2D NPWP" description="Manajemen data NPWP berdasarkan nomor SP2D." />

                <div class="bg-card rounded-xl border p-4">
                    <DataTable
                        :data="items"
                        :fieldsFromDB="fields"
                        :currentPage="dataResponse?.current_page ?? 0"
                        :totalItems="dataResponse?.total ?? 0"
                        :perPage="dataResponse?.per_page ?? 0"
                        :paginationLinks="dataResponse?.links ?? []"
                        :firstPageUrl="dataResponse?.first_page_url ?? ''"
                        :lastPageUrl="dataResponse?.last_page_url ?? ''"
                        :buttonDinamis="[
                            { label: 'Import Excel', variant: 'primary', onClick: 'handleImport' }
                        ]"
                        :dropdownActions="[
                            { key: 'delete', label: 'Hapus', emit: 'delete' }
                        ]"
                        @delete="handleDelete"
                        @search="(s) => { search = s; loadData(1); }"
                        @button-click="(v) => { if (v.action === 'handleImport') handleImport(); }"
                    />
                </div>
            </div>

            <!-- Import Dialog -->
            <Dialog v-model:open="isImportOpen">
                <DialogContent>
                    <DialogHeader>
                        <DialogTitle>Import Data SP2D NPWP</DialogTitle>
                        <DialogDescription>
                            Upload file Excel (.xlsx, .xls) dengan heading row: sp2d_number, npwp_bud, npwp_skpd, npwp_penerima, nama_penerima.
                        </DialogDescription>
                    </DialogHeader>
                    
                    <div class="grid gap-4 py-4">
                        <div class="grid gap-2">
                            <Label for="file">File Excel</Label>
                            <Input 
                                id="file" 
                                type="file" 
                                accept=".xlsx,.xls,.csv"
                                @input="importForm.file = ($event.target as HTMLInputElement).files?.[0] || null"
                            />
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
        </PajakLayout>
    </AppLayout>
</template>
