<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { ref, onMounted, reactive } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import AdminLayout from '@/layouts/admin/Layout.vue';
import DataTable from '@/components/DataTable.vue';
import axios from 'axios';
import { Skeleton } from '@/components/ui/skeleton';
import { toast } from 'vue-sonner';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { LoaderCircle } from 'lucide-vue-next';
import InputError from '@/components/InputError.vue';

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
    { title: 'Dashboard', href: '/admin/dashboards' },
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
        const res = await axios.get(route('admin.master-akun-pajak.data'));
        dataResponse.value = res.data.data;
        items.value = res.data.data.data;
    } catch (error) {
        console.error('Failed to load data:', error);
        toast.error('Gagal mengambil data');
    } finally {
        isLoading.value = false;
    }
}

const isModalOpen = ref(false);
const isEdit = ref(false);
const isProcessing = ref(false);
const selectedId = ref<number | null>(null);

const form = reactive({
    kode_akun_pajak: '',
    jenis_pajak: '',
    errors: {} as Record<string, string>,
});
const dropdownActions = ref([
    { key: 'edits', label: 'Edit', emit: 'edits' },
    { key: 'delete', label: 'Delete', emit: 'delete' },
]);
function openCreateModal() {
    isEdit.value = false;
    selectedId.value = null;
    form.kode_akun_pajak = '';
    form.jenis_pajak = '';
    form.errors = {};
    isModalOpen.value = true;
}

function openEditModal(idOrItem: any) {
    const item = typeof idOrItem === 'object' ? idOrItem : items.value.find((i: any) => i.id === idOrItem);
    if (!item) return;

    isEdit.value = true;
    selectedId.value = item.id;
    form.kode_akun_pajak = item.kode_akun_pajak;
    form.jenis_pajak = item.jenis_pajak;
    form.errors = {};
    isModalOpen.value = true;
}

async function submitForm() {
    try {
        isProcessing.value = true;
        form.errors = {};

        if (isEdit.value && selectedId.value) {
            await axios.put(route('admin.master-akun-pajak.update', selectedId.value), {
                kode_akun_pajak: form.kode_akun_pajak,
                jenis_pajak: form.jenis_pajak,
            });
            toast.success('Data berhasil diperbarui');
        } else {
            await axios.post(route('admin.master-akun-pajak.store'), {
                kode_akun_pajak: form.kode_akun_pajak,
                jenis_pajak: form.jenis_pajak,
            });
            toast.success('Data berhasil ditambahkan');
        }

        isModalOpen.value = false;
        loadData();
    } catch (error: any) {
        if (error.response?.status === 422) {
            form.errors = error.response.data.errors;
        } else {
            toast.error('Terjadi kesalahan sistem');
        }
    } finally {
        isProcessing.value = false;
    }
}

const isDeleteModalOpen = ref(false);
const itemToDelete = ref<number | null>(null);
const isDeleting = ref(false);

function handleDelete(id: number) {
    itemToDelete.value = id;
    isDeleteModalOpen.value = true;
}

async function confirmDelete() {
    if (!itemToDelete.value) return;
    
    isDeleting.value = true;
    try {
        await axios.delete(route('admin.master-akun-pajak.destroy', itemToDelete.value));
        toast.success('Data berhasil dihapus');
        loadData();
        isDeleteModalOpen.value = false;
        itemToDelete.value = null;
    } catch (error) {
        console.error('Failed to delete:', error);
        toast.error('Gagal menghapus data');
    } finally {
        isDeleting.value = false;
    }
}

function handleSearch(search: string) {
    isLoading.value = true;
    axios.get(route('admin.master-akun-pajak.data'), { params: { search } })
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

const buttonDinamis = [
    {
        label: "Tambah Akun",
        variant: "primary",
        onClick: "handleCreate"
    }
] as const;

const handlers: Record<string, Function> = {
    handleCreate: openCreateModal
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
                        @edits="openEditModal"
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
        </AdminLayout>

        <!-- Modal Create/Edit -->
        <Dialog v-model:open="isModalOpen">
            <DialogContent class="sm:max-w-[425px]">
                <DialogHeader>
                    <DialogTitle>{{ isEdit ? 'Edit Akun Pajak' : 'Tambah Akun Pajak' }}</DialogTitle>
                    <DialogDescription>
                        Lengkapi informasi akun pajak di bawah ini.
                    </DialogDescription>
                </DialogHeader>
                <form @submit.prevent="submitForm">
                    <div class="grid gap-4 py-4">
                        <div class="grid gap-2">
                            <Label for="kode_akun_pajak">Kode Akun Pajak</Label>
                            <Input id="kode_akun_pajak" v-model="form.kode_akun_pajak" placeholder="Contoh: 411121" />
                            <InputError :message="form.errors.kode_akun_pajak" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="jenis_pajak">Jenis Pajak</Label>
                            <Input id="jenis_pajak" v-model="form.jenis_pajak" placeholder="Contoh: PPh Pasal 21" />
                            <InputError :message="form.errors.jenis_pajak" />
                        </div>
                    </div>
                    <DialogFooter>
                        <Button type="button" variant="outline" @click="isModalOpen = false">Batal</Button>
                        <Button type="submit" :disabled="isProcessing">
                            <LoaderCircle v-if="isProcessing" class="h-4 w-4 animate-spin mr-2" />
                            {{ isEdit ? 'Simpan Perubahan' : 'Tambah Data' }}
                        </Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>

        <!-- Modal Konfirmasi Hapus -->
        <Dialog v-model:open="isDeleteModalOpen">
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>Konfirmasi Hapus</DialogTitle>
                    <DialogDescription>
                        Apakah Anda yakin ingin menghapus data akun pajak ini? Data yang dihapus tidak dapat dikembalikan.
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
    </AppLayout>
</template>
