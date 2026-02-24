<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import PajakLayout from '@/layouts/pajak/Layout.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import DataTable from '@/components/DataTable.vue';
import axios from 'axios';
import { Skeleton } from '@/components/ui/skeleton';
import { Button } from '@/components/ui/button';
import { LoaderCircle } from 'lucide-vue-next';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';

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
    { title: 'Master Akun Pajak', href: '#' },
];

const isLoading = ref(false);
const items = ref([]);
const dataResponse = ref<PaginationResponse | null>(null);

const fields = [
    { key: 'kode_akun_pajak', label: 'Kode Akun Pajak', type: 'string', sortable: true },
    { key: 'jenis_pajak', label: 'Jenis Pajak', type: 'string', sortable: true },
];

async function loadData() {
    try {
        isLoading.value = true;
        const res = await axios.get(route('pajak.master-akun.data'));
        dataResponse.value = res.data.data;
        items.value = res.data.data.data;
    } catch (error) {
        console.error('Failed to load data:', error);
    } finally {
        isLoading.value = false;
    }
}

const buttonDinamis = [
    {
        label: "Tambah Akun",
        variant: "primary" as const,
        onClick: "handleCreate"
    }
];

const dropdownActions = ref([
    { key: 'edits', label: 'Edit', emit: 'edits' },
    { key: 'delete', label: 'Delete', emit: 'delete' },
]);

function handleCreate() {
    router.visit(route('pajak.master-akun.create'));
}

function handleEdit(id: number) {
    router.visit(route('pajak.master-akun.edit', id));
}

const isDeleteModalOpen = ref(false);
const itemToDelete = ref<number | null>(null);
const isDeleting = ref(false);

function handleDelete(id: number) {
    itemToDelete.value = id;
    isDeleteModalOpen.value = true;
}

async function confirmDelete() {
    if (itemToDelete.value !== null) {
        isDeleting.value = true;
        try {
            await router.delete(route('pajak.master-akun.destroy', itemToDelete.value));
            loadData();
            isDeleteModalOpen.value = false;
            itemToDelete.value = null;
        } catch (error) {
            console.error('Failed to delete:', error);
        } finally {
            isDeleting.value = false;
        }
    }
}

function handleSearch(search: string) {
    isLoading.value = true;
    axios.get(route('pajak.master-akun.data'), { params: { search } })
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
    <Head title="Master Akun Pajak" />
    
    <AppLayout :breadcrumbs="breadcrumbs">
        <PajakLayout>
            <div class="space-y-6">
                <HeadingSmall title="Master Akun Pajak" description="Kelola referensi kode dan jenis akun pajak." />
                
                <div class="relative min-h-[100vh] flex-1 p-4 rounded-xl border border-sidebar-border/70 dark:border-sidebar-border md:min-h-min">
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
                        @edits="handleEdit"
                        @delete="handleDelete"
                        @clickPaging="handleClickPaging"
                        @search="handleSearch"
                        @button-click="dispatchMethod"
                    />

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

            <Dialog v-model:open="isDeleteModalOpen">
                <DialogContent>
                    <DialogHeader>
                        <DialogTitle>Konfirmasi Hapus</DialogTitle>
                        <DialogDescription>
                            Apakah Anda yakin ingin menghapus data master akun pajak ini? Tindakan ini tidak dapat dibatalkan.
                        </DialogDescription>
                    </DialogHeader>
                    <DialogFooter>
                        <Button variant="outline" @click="isDeleteModalOpen = false" :disabled="isDeleting">
                            Batal
                        </Button>
                        <Button variant="destructive" @click="confirmDelete" :disabled="isDeleting">
                            <LoaderCircle v-if="isDeleting" class="mr-2 h-4 w-4 animate-spin" />
                            Hapus
                        </Button>
                    </DialogFooter>
                </DialogContent>
            </Dialog>
        </PajakLayout>
    </AppLayout>
</template>
