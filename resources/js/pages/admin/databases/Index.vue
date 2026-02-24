<script setup lang="ts">
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import MasterLayout from '@/layouts/admin/LayoutFull.vue';
import DataTable, { type DynamicButton } from '@/components/DataTable.vue';
import { type PaginatedResponse, type BreadcrumbItem, type SharedData } from '@/types';
import axios from 'axios';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Button } from '@/components/ui/button';
import { toast } from 'vue-sonner';

interface InterfaceListData {
    id: string;
    prefix: string;
    name: string;
    slug: string | null;
    dataTypeId: string | null;
}

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Admin', href: '/admin/dashboards' },
    { title: 'Database Manager', href: '/admin/database-manager' }
];

const listData = ref<InterfaceListData[]>([]);
const dataResponse = ref<PaginatedResponse<InterfaceListData> | null>(null);
const isLoading = ref(false);

const isDeleteDialogOpen = ref(false);
const itemToDeleteId = ref<string | null>(null);

const fieldsFromDB = [
    { key: 'name', label: 'Name Table', type: 'string', sortable: false },
    { key: 'prefix', label: 'Prefix', type: 'string', sortable: false },
    { key: 'slug', label: 'slug', type: 'string', sortable: false },
];

const buttonDinamis: DynamicButton[] = [
    {
        label: "Tambah",
        variant: "primary",
        onClick: "handleTambah"
    }
];

async function loadData() {
    try {
        isLoading.value = true;
        const res = await axios.get(route('admin.list-table'));
        listData.value = res.data.map((item: any) => ({
            ...item,
            id: item.name
        }));
    } catch (error) {
        console.error('Gagal load data:', error);
    } finally {
        isLoading.value = false;
    }
}

function handleTambah() {
    router.get(route('admin.database-manager.create'));
}

const handleEdit = (id: string) => {
    router.get(route('admin.database-manager.edit', id));
};

const handleDelete = (id: string) => {
    itemToDeleteId.value = id;
    isDeleteDialogOpen.value = true;
};

const confirmDelete = async () => {
    if (!itemToDeleteId.value) return;
    router.delete(route('admin.database-manager.destroy', itemToDeleteId.value), {
        onSuccess: () => {
            toast.success('Tabel berhasil dihapus');
            loadData();
        },
        onError: () => {
            toast.error('Gagal menghapus tabel');
        },
        onFinish: () => {
            isDeleteDialogOpen.value = false;
            itemToDeleteId.value = null;
        }
    });
};

const clickMethod = (value: { action: string; value: any }) => {
    if (value.action === 'handleTambah') {
        handleTambah();
    }
};

const handleClickPaging = async (link: string) => {
    try {
        isLoading.value = true;
        const res = await axios.get(link);
        listData.value = res.data.map((item: any) => ({
            ...item,
            id: item.name
        }));
    } catch (error) {
        console.error('Gagal load data:', error);
    } finally {
        isLoading.value = false;
    }
};

const handleSearch = async (search: string) => {
    isLoading.value = true;
    try {
        const res = await axios.get(route('admin.list-table'), {
            params: { search }
        });
        listData.value = res.data.map((item: any) => ({
            ...item,
            id: item.name
        }));
    } catch (error) {
        console.error('Search failed:', error);
    } finally {
        isLoading.value = false;
    }
};

onMounted(() => {
    loadData();
});
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Database Manager" />
        <MasterLayout>
            <div class="space-y-6">
                <DataTable v-if="!isLoading"
                    :data="listData" 
                    :fieldsFromDB="fieldsFromDB"
                    :currentPage="dataResponse?.current_page ?? 0"
                    :totalItems="dataResponse?.total ?? listData.length" 
                    :perPage="dataResponse?.per_page ?? 10"
                    :paginationLinks="dataResponse?.links ?? []"
                    :firstPageUrl="dataResponse?.first_page_url ?? ''"
                    :lastPageUrl="dataResponse?.last_page_url ?? ''"
                    :buttonDinamis="buttonDinamis"
                    @edits="handleEdit"
                    @delete="handleDelete"
                    @clickPaging="handleClickPaging"
                    @search="handleSearch"
                    @button-click="clickMethod"
                />
                <div v-else class="flex justify-center py-10">
                    <span>Loading...</span>
                </div>
            </div>
        </MasterLayout>

        <!-- Delete Confirmation Dialog -->
        <Dialog v-model:open="isDeleteDialogOpen">
            <DialogContent class="sm:max-w-[425px]">
                <DialogHeader>
                    <DialogTitle>Konfirmasi Hapus</DialogTitle>
                    <DialogDescription>
                        Apakah Anda yakin ingin menghapus tabel <strong>{{ itemToDeleteId }}</strong>? Tindakan ini tidak dapat dibatalkan.
                    </DialogDescription>
                </DialogHeader>
                <DialogFooter class="gap-2 sm:gap-0">
                    <Button variant="secondary" @click="isDeleteDialogOpen = false">Batal</Button>
                    <Button variant="destructive" @click="confirmDelete">Hapus</Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>
