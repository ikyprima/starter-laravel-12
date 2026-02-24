<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { ref, onMounted, watch } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import DataTable from '@/components/DataTable.vue';
import axios from 'axios';
import { toast } from 'vue-sonner';
import { Skeleton } from '@/components/ui/skeleton';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Label } from '@/components/ui/label';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import { LoaderCircle, Check, ChevronsUpDown, LayoutGrid } from 'lucide-vue-next';
import InputError from '@/components/InputError.vue';
import {
    Command,
    CommandEmpty,
    CommandGroup,
    CommandInput,
    CommandItem,
    CommandList,
} from '@/components/ui/command';
import {
    Popover,
    PopoverContent,
    PopoverTrigger,
} from '@/components/ui/popover';
import { cn } from "@/lib/utils";
import { AlertCircle } from 'lucide-vue-next';

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Transaksi GU', href: '#' },
];

const props = defineProps<{
    skpds?: any[],
    isAdmin?: boolean
}>();

const selectedYear = ref(new Date().getFullYear().toString());
const selectedMonth = ref<number | null>(null);
const selectedSkpd = ref<string | null>(null);
const openSkpdPicker = ref(false); // State for SKPD popover

const years = Array.from({ length: 5 }, (_, i) => (new Date().getFullYear() - 2 + i).toString());
const months = [
    'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
];

const isLoading = ref(false);
const items = ref([]);
const dataResponse = ref<any>(null);

const fields = [
    {
        key: 'keterangan_dokumen', 
        label: 'Keterangan Dokumen', 
        type: 'string',
        sortable: false,
        width: '500px',
        topKey: ['nomor_tbp'],
        bottomKey: ['uraian_belanja'], 
    },
    { key: 'nilai_belanja_fmt', label: 'Nilai Belanja', type: 'string', sortable: false, width: '150px' },
    { key: 'dpp_fmt', label: 'DPP', type: 'string', sortable: false, width: '150px' },
    { key: 'jumlah_pajak_fmt', label: 'Jumlah Pajak', type: 'string', sortable: false, width: '150px' },
    {
        key: 'rekanan_info', 
        label: 'Rekanan / NPWP', 
        type: 'two-line',
        topKey: 'npwp',
        bottomKey: 'nama_rekanan', 
    },
    { 
        key: 'pajak_info', 
        label: 'Jenis Pajak', 
        type: 'two-line',
        topKey: 'jenis_pajak',
        bottomKey: 'kode_akun_pajak' 
    },
    { 
        key: 'billing_info', 
        label: 'ID Billing / NTPN', 
        type: 'two-line',
        topKey: 'id_billing',
        bottomKey: 'ntpn' 
    },
];

async function loadData(page: number | string = 1) {
    if (selectedMonth.value === null) return;
    try {
        isLoading.value = true;
        const res = await axios.get(route('pajak.gu.data'), {
            params: {
                tahun: selectedYear.value,
                bulan: selectedMonth.value,
                kode_skpd: selectedSkpd.value,
                page: page
            }
        });
        dataResponse.value = res.data.data;
        items.value = res.data.data.data.map((item: any) => ({
            ...item,
            kode_akun_pajak: item.master_akun_pajak?.kode_akun_pajak,
            jenis_pajak: item.master_akun_pajak?.jenis_pajak,
            nilai_belanja_fmt: new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(item.nilai_belanja),
            dpp_fmt: new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(item.dpp),
            jumlah_pajak_fmt: new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(item.jumlah_pajak),
        }));
    } catch (error) {
        console.error('Failed to load data:', error);
    } finally {
        isLoading.value = false;
    }
}

function handleMonthClick(index: number) {
    selectedMonth.value = index + 1;
    loadData(1);
}

function handlePageClick(link: string | null) {
    if (link) {
        try {
            const url = new URL(link);
            const page = url.searchParams.get('page');
            if (page) loadData(page);
        } catch (e) {
            console.error('Invalid URL:', link);
        }
    }
}

// Form State
const isModalOpen = ref(false);
const isEdit = ref(false);
const editingId = ref<number | null>(null);
const isSubmitting = ref(false);
const isSyncing = ref(false);
const isSyncDialogOpen = ref(false);
const forceBku = ref(false);
const forceBkuPajak = ref(false);
const isDeletingAll = ref(false);
const availableAkun = ref<any[]>([]);
const openAkunPicker = ref(false);
const selectedAkun = ref<any>(null);
const isUploadDialogOpen = ref(false);
const bkuFile = ref<File | null>(null);
const bkuPajakFile = ref<File | null>(null);
const isUploading = ref(false);
const isApiOnline = ref(true);
const isCheckingConnection = ref(false);
const apiErrorMessage = ref('');

const form = ref({
    nomor_tbp: '',
    nilai_belanja: 0,
    uraian_belanja: '',
    dpp: 0,
    master_akun_pajak_id: null as number | null,
    jumlah_pajak: 0,
    npwp: '',
    ntpn: '',
    jenis_pajak: '',
    nama_rekanan: '',
});

const isDeleteDialogOpen = ref(false);
const itemToDeleteId = ref<number | null>(null);

const errors = ref<any>({});

async function fetchAkunPajak() {
    try {
        const res = await axios.get(route('pajak.master-akun.data'));
        availableAkun.value = res.data.data.data.map((a: any) => ({
            value: a.id,
            label: `${a.kode_akun_pajak} - ${a.jenis_pajak}`,
            kode: a.kode_akun_pajak,
            jenis: a.jenis_pajak
        }));
    } catch (e) {
        console.error(e);
    }
}

function handleCreate() {
    isEdit.value = false;
    editingId.value = null;
    form.value = {
        nomor_tbp: '',
        nilai_belanja: 0,
        uraian_belanja: '',
        dpp: 0,
        master_akun_pajak_id: null,
        jumlah_pajak: 0,
        npwp: '',
        ntpn: '',
        jenis_pajak: '',
        nama_rekanan: '',
    };
    selectedAkun.value = null;
    errors.value = {};
    isModalOpen.value = true;
}

function handleEdit(id: number) {
    const item = items.value.find((i: any) => i.id === id) as any;
    if (item) {
        isEdit.value = true;
        editingId.value = id;

        // Populate selectedAkun
        const foundAkun = availableAkun.value.find(a => a.value === item.master_akun_pajak_id);
        if (foundAkun) {
            selectedAkun.value = foundAkun;
        } else if (item.master_akun_pajak_id) {
            selectedAkun.value = {
                value: item.master_akun_pajak_id,
                label: `${item.kode_akun_pajak} - ${item.jenis_pajak}`,
                kode: item.kode_akun_pajak,
                jenis: item.jenis_pajak
            };
        } else {
            selectedAkun.value = null;
        }

        form.value = {
            nomor_tbp: item.nomor_tbp,
            nilai_belanja: item.nilai_belanja,
            uraian_belanja: item.uraian_belanja,
            dpp: item.dpp,
            master_akun_pajak_id: item.master_akun_pajak_id,
            jumlah_pajak: item.jumlah_pajak,
            npwp: item.npwp,
            ntpn: item.ntpn,
            jenis_pajak: item.jenis_pajak,
            nama_rekanan: item.nama_rekanan
        };
        errors.value = {};
        isModalOpen.value = true;
    }
}

async function handleSubmit() {
    isSubmitting.value = true;
    errors.value = {};

    if (props.isAdmin && !selectedSkpd.value) {
        toast.error('Silakan pilih Unit Kerja (SKPD) terlebih dahulu.');
        isSubmitting.value = false;
        return;
    }

    try {
        const payload: any = {
            ...form.value,
            tahun: selectedYear.value,
            bulan: selectedMonth.value,
        };

        if (props.isAdmin && selectedSkpd.value) {
            payload.kode_skpd = selectedSkpd.value;
        }

        if (isEdit.value && editingId.value) {
            await axios.put(route('pajak.gu.update', editingId.value), payload);
        } else {
            await axios.post(route('pajak.gu.store'), payload);
        }
        isModalOpen.value = false;
        loadData();
    } catch (error: any) {
        if (error.response?.status === 422) {
            errors.value = error.response.data.errors;
        }
    } finally {
        isSubmitting.value = false;
    }
}

function handleDelete(id: number) {
    itemToDeleteId.value = id;
    isDeleteDialogOpen.value = true;
}

async function confirmDelete() {
    if (!itemToDeleteId.value) return;
    try {
        await axios.delete(route('pajak.gu.destroy', itemToDeleteId.value));
        toast.success('Transaksi berhasil dihapus');
        loadData();
    } catch (error) {
        console.error('Failed to delete:', error);
        toast.error('Gagal menghapus transaksi');
    } finally {
        isDeleteDialogOpen.value = false;
        itemToDeleteId.value = null;
    }
}

async function handleSyncBku() {
    if (!selectedMonth.value) return;
    if (props.isAdmin && !selectedSkpd.value) {
        toast.error('Silakan pilih SKPD terlebih dahulu.');
        return;
    }
    
    // Reset and check connection
    isCheckingConnection.value = true;
    apiErrorMessage.value = '';
    try {
        const res = await axios.get(route('pajak.webservice.cek-koneksi'));
        isApiOnline.value = res.data.status === 200;
        if (!isApiOnline.value) {
            apiErrorMessage.value = res.data.message;
        }
    } catch (error: any) {
        isApiOnline.value = false;
        apiErrorMessage.value = error.response?.data?.message || 'Webservice Mati';
    } finally {
        isCheckingConnection.value = false;
    }

    forceBku.value = false;
    forceBkuPajak.value = false;
    isSyncDialogOpen.value = true;
}

async function confirmSync() {
    isSyncDialogOpen.value = false;
    isSyncing.value = true;
    try {
        const payload: any = {
            tahun: selectedYear.value,
            bulan: selectedMonth.value,
            force_bku: forceBku.value,
            force_bku_pajak: forceBkuPajak.value,
        };
        if (props.isAdmin && selectedSkpd.value) {
             payload.kode_skpd = selectedSkpd.value;
        }

        await Promise.all([
            axios.post(route('pajak.gu.sync-bku'), payload),
            axios.post(route('pajak.ls.sync-bku'), payload)
        ]);

        toast.success('Data BKU GU & LS Berhasil Disinkronkan');
        loadData(1);
    } catch (error: any) {
        console.error('Sync failed:', error);
        toast.error(error.response?.data?.message || 'Gagal menyinkronkan data.');
    } finally {
        isSyncing.value = false;
    }
}

const isDeleteAllModalOpen = ref(false);

function handleDeleteAll() {
    isDeleteAllModalOpen.value = true;
}

async function confirmDeleteAll() {
    isDeletingAll.value = true;
    try {
        const payload: any = {
            tahun: selectedYear.value,
            bulan: selectedMonth.value,
        };
        if (props.isAdmin && selectedSkpd.value) {
            payload.kode_skpd = selectedSkpd.value;
        }

        const res = await axios.post(route('pajak.gu.destroy-by-period'), payload);
        toast.success(res.data.message);
        isDeleteAllModalOpen.value = false;
        loadData();
    } catch (error: any) {
        console.error('Delete all failed:', error);
        toast.error(error.response?.data?.message || 'Gagal menghapus data.');
    } finally {
        isDeletingAll.value = false;
    }
}

function handleExport() {
    if (!selectedMonth.value) {
        toast.error('Pilih bulan pelaporan terlebih dahulu.');
        return;
    }

    const params = new URLSearchParams({
        tahun: selectedYear.value,
        bulan: selectedMonth.value.toString(),
    });

    if (selectedSkpd.value) {
        params.append('kode_skpd', selectedSkpd.value);
    }

    const url = route('pajak.gu.export') + '?' + params.toString();
    window.location.href = url;
}

function handleUploadButtonClick() {
    if (!selectedMonth.value) {
        toast.error('Silakan pilih bulan terlebih dahulu.');
        return;
    }
    if (props.isAdmin && !selectedSkpd.value) {
        toast.error('Silakan pilih SKPD terlebih dahulu.');
        return;
    }
    isUploadDialogOpen.value = true;
}

async function onUploadBku(e: Event) {
    const target = e.target as HTMLInputElement;
    if (target.files && target.files[0]) {
        bkuFile.value = target.files[0];
    }
}

async function onUploadBkuPajak(e: Event) {
    const target = e.target as HTMLInputElement;
    if (target.files && target.files[0]) {
        bkuPajakFile.value = target.files[0];
    }
}

async function submitUpload() {
    if (!bkuFile.value && !bkuPajakFile.value) {
        toast.error('Silakan pilih setidaknya satu file untuk diunggah.');
        return;
    }

    isUploading.value = true;
    try {
        if (bkuFile.value) {
            const formData = new FormData();
            formData.append('file', bkuFile.value);
            formData.append('bulan', selectedMonth.value!.toString());
            formData.append('tahun', selectedYear.value);
            formData.append('kode_skpd', selectedSkpd.value || '');
            await axios.post(route('pajak.upload-bku'), formData);
            toast.success('Berhasil unggah BKU');
            bkuFile.value = null;
        }

        if (bkuPajakFile.value) {
            const formData = new FormData();
            formData.append('file', bkuPajakFile.value);
            formData.append('bulan', selectedMonth.value!.toString());
            formData.append('tahun', selectedYear.value);
            formData.append('kode_skpd', selectedSkpd.value || '');
            await axios.post(route('pajak.upload-bku-pajak'), formData);
            toast.success('Berhasil unggah BKU Pajak');
            bkuPajakFile.value = null;
        }
        
        isUploadDialogOpen.value = false;

        // Otomatis sinkronisasi setelah upload berhasil
        toast.info('Memulai sinkronisasi data...');
        const syncPayload = {
            bulan: selectedMonth.value,
            tahun: selectedYear.value,
            kode_skpd: selectedSkpd.value,
            force_bku: false,
            force_bku_pajak: false
        };

        await Promise.all([
            axios.post(route('pajak.gu.sync-bku'), syncPayload),
            axios.post(route('pajak.ls.sync-bku'), syncPayload)
        ]);

        toast.success('Data BKU GU & LS Berhasil Disinkronkan');
        loadData();
    } catch (error: any) {
        toast.error(error.response?.data?.message || 'Gagal mengunggah atau menyinkronkan data.');
    } finally {
        isUploading.value = false;
    }
}

function calculateDpp() {
    const kode = selectedAkun.value?.kode;
    const nilaiBelanja = Number(form.value.nilai_belanja) || 0;
    const jumlahPajak = Number(form.value.jumlah_pajak) || 0;

    if (kode == '411121') {
        form.value.dpp = nilaiBelanja;
    } else if (kode == '411122') {
        form.value.dpp = Math.round(nilaiBelanja - (11 / 111 * nilaiBelanja));
    } else if (kode == '411124') {
        form.value.dpp = Math.round((100 / 2) * jumlahPajak);
    } else if (kode == '411211') {
        form.value.dpp = Math.round((100 / 111) * nilaiBelanja);
    } else {
        form.value.dpp = 0;
    }
}

const dropdownActions = [
    { key: 'edits', label: 'Edit', emit: 'edits' },
    { key: 'delete', label: 'Hapus', emit: 'delete' },
];

watch(selectedAkun, (newVal) => {
    if (newVal) {
        form.value.master_akun_pajak_id = newVal.value;
        form.value.jenis_pajak = newVal.jenis;
        calculateDpp();
    }
});

watch(() => form.value.nilai_belanja, () => {
    calculateDpp();
});

watch(() => form.value.jumlah_pajak, () => {
    calculateDpp();
});

onMounted(() => {
    fetchAkunPajak();
});
</script>

<template>
    <Head title="Transaksi Pajak GU" />
    
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="px-4 py-6 space-y-6 flex-1 min-w-0 overflow-hidden">
                <HeadingSmall title="Pelaporan GU" description="Kelola transaksi penggantian uang per periode." />
                
                <!-- Modern Period Selector -->
                <div class="flex flex-col gap-6 bg-background/50 backdrop-blur-sm border border-sidebar-border/50 rounded-2xl p-6 shadow-sm">
                    <div class="flex flex-col md:flex-row md:items-center justify-end gap-4">
                        <div v-if="props.isAdmin" class="flex items-center gap-3 bg-muted/50 p-1.5 rounded-xl border border-sidebar-border/30">
                            <Popover v-model:open="openSkpdPicker">
                                <PopoverTrigger as-child>
                                    <Button variant="outline" role="combobox" :aria-expanded="openSkpdPicker" class="w-[400px] h-9 border-none bg-transparent focus:ring-0 shadow-none font-semibold justify-between">
                                        {{ selectedSkpd ? props.skpds?.find((s) => s.kode_skpd === selectedSkpd)?.nama_skpd : "Pilih Unit Kerja (SKPD)..." }}
                                        <ChevronsUpDown class="ml-2 h-4 w-4 shrink-0 opacity-50" />
                                    </Button>
                                </PopoverTrigger>
                                <PopoverContent class="w-[400px] p-0" align="start">
                                    <Command>
                                        <CommandInput placeholder="Cari SKPD..." />
                                        <CommandEmpty>Tidak ditemukan.</CommandEmpty>
                                        <CommandList>
                                            <CommandGroup>
                                                <CommandItem 
                                                    v-for="skpd in props.skpds" 
                                                    :key="skpd.kode_skpd" 
                                                    :value="skpd.nama_skpd" 
                                                    @select="() => {
                                                        selectedSkpd = skpd.kode_skpd;
                                                        openSkpdPicker = false;
                                                        loadData();
                                                    }"
                                                >
                                                    <Check :class="cn('mr-2 h-4 w-4', selectedSkpd === skpd.kode_skpd ? 'opacity-100' : 'opacity-0')" />
                                                    {{ skpd.nama_skpd }}
                                                </CommandItem>
                                            </CommandGroup>
                                        </CommandList>
                                    </Command>
                                </PopoverContent>
                            </Popover>
                        </div>

                        <div class="flex items-center gap-3 bg-muted/50 p-1.5 rounded-xl border border-sidebar-border/30">
                            <Select v-model="selectedYear" @update:modelValue="() => loadData(1)">
                                <SelectTrigger class="w-[110px] h-9 border-none bg-transparent focus:ring-0 shadow-none font-semibold">
                                    <SelectValue placeholder="Tahun" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem v-for="y in years" :key="y" :value="y">{{ y }}</SelectItem>
                                </SelectContent>
                            </Select>
                        </div>
                    </div>

                    <div class="overflow-x-auto pb-2 scrollbar-hide flex md:justify-center">
                        <div class="flex p-1 bg-muted/40 rounded-xl border border-sidebar-border/30 w-max md:w-full max-w-6xl">
                            <button 
                                v-for="(month, index) in months" 
                                :key="month"
                                @click="handleMonthClick(index)"
                                :class="cn(
                                    'flex-1 px-3 sm:px-5 py-2.5 rounded-lg text-sm font-medium transition-all duration-200 ease-in-out whitespace-nowrap text-center',
                                    selectedMonth === index + 1 
                                        ? 'bg-primary text-primary-foreground shadow-md shadow-primary/20 scale-[1.02]' 
                                        : 'text-muted-foreground hover:text-foreground hover:bg-background/50'
                                )"
                            >
                                {{ month }}
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Data Table Section -->
                <div v-if="selectedMonth" class="relative min-h-[400px] flex-1 p-4 rounded-xl border border-sidebar-border/70 dark:border-sidebar-border md:min-h-min overflow-hidden bg-card">
                    <DataTable v-if="!isLoading"
                        :data="items"
                        :fieldsFromDB="fields"
                        :currentPage="dataResponse?.current_page ?? 0"
                        :totalItems="dataResponse?.total ?? 0"
                        :perPage="dataResponse?.per_page ?? 0"
                        :paginationLinks="dataResponse?.links ?? []"
                        :firstPageUrl="dataResponse?.first_page_url ?? ''"
                        :lastPageUrl="dataResponse?.last_page_url ?? ''"
                        :buttonDinamis="[
                            // { label: 'Hapus Data', variant: 'destructive', onClick: 'handleDeleteAll', loading: isDeletingAll },
                            { label: 'Sync BKU', variant: 'outline', onClick: 'handleSyncBku', loading: isSyncing || isCheckingConnection },
                            { label: 'Upload BKU', variant: 'outline', onClick: 'handleUploadBku' },
                            { label: 'Export Excel', variant: 'outline', onClick: 'handleExport' },
                            { label: 'Tambah Transaksi GU', variant: 'primary', onClick: 'handleCreate' }
                        ] as const"
                        :dropdownActions="dropdownActions"
                        @edits="handleEdit"
                        @delete="handleDelete"
                        @search="(s) => { loadData(1) }"
                        @button-click="(v) => {
                            if (v.action === 'handleCreate') handleCreate();
                            if (v.action === 'handleSyncBku') handleSyncBku();
                            if (v.action === 'handleUploadBku') handleUploadButtonClick();
                            if (v.action === 'handleExport') handleExport();
                            // if (v.action === 'handleDeleteAll') handleDeleteAll();
                        }"
                        @clickPaging="handlePageClick"
                    />

                    <div v-if="isLoading" class="p-8">
                        <Skeleton class="h-[300px] w-full" />
                    </div>
                </div>

                <div v-else class="flex flex-col items-center justify-center py-24 px-4 text-center bg-muted/20 rounded-2xl border-2 border-dashed border-sidebar-border/50 transition-all duration-300">
                    <div class="bg-background p-4 rounded-full shadow-sm mb-4 border border-sidebar-border/30">
                        <LayoutGrid class="h-8 w-8 text-muted-foreground/60" />
                    </div>
                    <h3 class="text-xl font-semibold tracking-tight">Belum Ada Bulan Terpilih</h3>
                    <p class="text-muted-foreground max-w-xs mt-2">
                        Silakan pilih salah satu periode bulan di atas untuk mengelola rincian transaksi laporan pajak GU.
                    </p>
                </div>
            </div>


        <!-- Form Modal -->
        <Dialog v-model:open="isModalOpen">
            <DialogContent class="sm:max-w-[700px]">
                <DialogHeader>
                    <DialogTitle>{{ isEdit ? 'Edit' : 'Tambah' }} Transaksi GU</DialogTitle>
                    <DialogDescription>Isi detail transaksi pajak GU di bawah ini.</DialogDescription>
                </DialogHeader>
                
                <div class="grid grid-cols-2 gap-4 py-4">
                    <div class="grid gap-2 col-span-2">
                        <Label for="nomor_tbp">Nomor TBP</Label>
                        <Input id="nomor_tbp" v-model="form.nomor_tbp" />
                        <InputError :message="errors.nomor_tbp?.[0]" />
                    </div>
                    <div class="grid gap-2 col-span-2">
                        <Label for="uraian_belanja">Uraian Belanja</Label>
                        <Textarea id="uraian_belanja" v-model="form.uraian_belanja" />
                        <InputError :message="errors.uraian_belanja?.[0]" />
                    </div>
                    
                    <div class="grid gap-2">
                        <Label for="nilai_belanja">Nilai Belanja</Label>
                        <Input id="nilai_belanja" type="number" v-model="form.nilai_belanja" />
                        <InputError :message="errors.nilai_belanja?.[0]" />
                    </div>
                    <div /> <!-- Spacer -->

                    <div class="grid gap-2">
                        <Label>Kode Akun Pajak</Label>
                        <Popover v-model:open="openAkunPicker">
                            <PopoverTrigger as-child>
                                <Button variant="outline" role="combobox" class="w-full justify-between font-normal">
                                    {{ selectedAkun ? selectedAkun.label : "Pilih akun..." }}
                                    <ChevronsUpDown class="ml-2 h-4 w-4 shrink-0 opacity-50" />
                                </Button>
                            </PopoverTrigger>
                            <PopoverContent class="w-[--radix-popover-trigger-width] p-0" align="start">
                                <Command>
                                    <CommandInput placeholder="Cari kode/jenis pajak..." />
                                    <CommandEmpty>Tidak ditemukan.</CommandEmpty>
                                    <CommandList>
                                        <CommandGroup>
                                            <CommandItem v-for="akun in availableAkun" :key="akun.value" :value="akun.label" @select="() => { selectedAkun = akun; openAkunPicker = false; }">
                                                {{ akun.label }}
                                                <Check :class="cn('ml-auto h-4 w-4', selectedAkun?.value === akun.value ? 'opacity-100' : 'opacity-0')" />
                                            </CommandItem>
                                        </CommandGroup>
                                    </CommandList>
                                </Command>
                            </PopoverContent>
                        </Popover>
                        <InputError :message="errors.master_akun_pajak_id?.[0]" />
                    </div>
                    <div class="grid gap-2">
                        <Label>Jenis Pajak</Label>
                        <Input :value="selectedAkun?.jenis || ''" disabled class="bg-muted" v-model="form.jenis_pajak" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="jumlah_pajak">Jumlah Pajak</Label>
                        <Input id="jumlah_pajak" type="number" v-model="form.jumlah_pajak" />
                        <InputError :message="errors.jumlah_pajak?.[0]" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="dpp">DPP</Label>
                        <Input id="dpp" type="number" v-model="form.dpp" />
                        <InputError :message="errors.dpp?.[0]" />
                    </div>
                    <div class="grid gap-2 col-span-2">
                        <Label for="nama_rekanan">Nama Rekanan</Label>
                        <Input id="nama_rekanan" v-model="form.nama_rekanan" />
                        <InputError :message="errors.nama_rekanan?.[0]" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="npwp">NPWP</Label>
                        <Input id="npwp" v-model="form.npwp" />
                        <InputError :message="errors.npwp?.[0]" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="ntpn">NTPN (16 digit Alfanumerik)</Label>
                        <Input 
                            id="ntpn" 
                            v-model="form.ntpn" 
                            maxlength="16" 
                            @input="form.ntpn = form.ntpn.replace(/[^a-zA-Z0-9]/g, '').toUpperCase()"
                        />
                        <InputError :message="errors.ntpn?.[0]" />
                    </div>
                </div>

                <DialogFooter>
                    <Button variant="secondary" @click="isModalOpen = false">Batal</Button>
                    <Button :disabled="isSubmitting" @click="handleSubmit">
                        <LoaderCircle v-if="isSubmitting" class="mr-2 h-4 w-4 animate-spin" />
                        Simpan
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>

        <!-- Delete Confirmation Dialog -->
        <Dialog v-model:open="isDeleteDialogOpen">
            <DialogContent class="sm:max-w-[425px]">
                <DialogHeader>
                    <DialogTitle>Konfirmasi Hapus</DialogTitle>
                    <DialogDescription>
                        Apakah Anda yakin ingin menghapus transaksi ini? Tindakan ini tidak dapat dibatalkan.
                    </DialogDescription>
                </DialogHeader>
                <DialogFooter class="gap-2 sm:gap-2">
                    <Button variant="secondary" @click="isDeleteDialogOpen = false">Batal</Button>
                    <Button variant="destructive" @click="confirmDelete">Hapus</Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>

        <!-- Delete All Confirmation Dialog -->
        <Dialog v-model:open="isDeleteAllModalOpen">
            <DialogContent class="sm:max-w-[425px]">
                <DialogHeader>
                    <DialogTitle>Konfirmasi Hapus Semua</DialogTitle>
                    <DialogDescription>
                        Apakah Anda yakin ingin menghapus SEMUA data transaksi GU pada periode ini? Tindakan ini tidak dapat dibatalkan.
                    </DialogDescription>
                </DialogHeader>
                <DialogFooter class="gap-2 sm:gap-2">
                    <Button variant="secondary" @click="isDeleteAllModalOpen = false">Batal</Button>
                    <Button variant="destructive" @click="confirmDeleteAll" :disabled="isDeletingAll">
                        <LoaderCircle v-if="isDeletingAll" class="mr-2 h-4 w-4 animate-spin" />
                        Hapus Semua
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
        <!-- Sync Confirmation Dialog -->
        <Dialog v-model:open="isSyncDialogOpen">
            <DialogContent class="sm:max-w-[425px]">
                <DialogHeader>
                    <DialogTitle>Konfirmasi Sinkronisasi BKU</DialogTitle>
                    <DialogDescription>
                        Apakah Anda yakin ingin melakukan sinkronisasi data dari BKU ke Transaksi GU?
                    </DialogDescription>
                </DialogHeader>

                <div v-if="!isApiOnline" class="mt-4">
                    <div class="p-4 rounded-xl bg-destructive/5 border border-destructive/20 text-destructive flex items-start gap-3 shadow-sm">
                        <AlertCircle class="h-5 w-5 shrink-0 mt-0.5" />
                        <div>
                            <h5 class="font-bold leading-none mb-1.5">Webservice Mati</h5>
                            <p class="text-xs font-medium mt-2">
                                Koneksi ke API SIPD sedang terputus. Silakan gunakan fitur <button type="button" @click="isSyncDialogOpen = false; isUploadDialogOpen = true" class="underline font-bold hover:text-destructive/80">Upload BKU</button> secara manual jika Anda sudah memiliki file Excel.
                            </p>
                        </div>
                    </div>
                </div>

                <div v-if="isCheckingConnection" class="p-4 flex items-center justify-center gap-2 text-sm text-muted-foreground">
                    <LoaderCircle class="h-4 w-4 animate-spin" />
                    Mengecek koneksi webservice...
                </div>

                <div class="space-y-4 py-4" v-if="!isCheckingConnection">
                    <div class="flex items-center space-x-2">
                        <Checkbox id="force_bku" :checked="forceBku" @update:checked="(val: boolean) => forceBku = val" />
                        <div class="grid gap-1.5 leading-none">
                            <label for="force_bku" class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">
                                Force Sync BKU
                            </label>
                            <p class="text-xs text-muted-foreground">
                                Ambil ulang data BKU SIPD (Menghapus cache lokal BKU).
                            </p>
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-2">
                        <Checkbox id="force_bku_pajak" :checked="forceBkuPajak" @update:checked="(val: boolean) => forceBkuPajak = val" />
                        <div class="grid gap-1.5 leading-none">
                            <label for="force_bku_pajak" class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">
                                Force Sync BKU Pajak
                            </label>
                            <p class="text-xs text-muted-foreground">
                                Ambil ulang data BKU Pajak SIPD (Menghapus cache lokal BKU Pajak).
                            </p>
                        </div>
                    </div>
                </div>

                <DialogFooter>
                    <Button variant="secondary" @click="isSyncDialogOpen = false">Batal</Button>
                    <Button :disabled="isSyncing || !isApiOnline" @click="confirmSync">
                        <LoaderCircle v-if="isSyncing" class="mr-2 h-4 w-4 animate-spin" />
                        Mulai Sinkronisasi
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>

        <!-- Upload BKU Dialog -->
        <Dialog v-model:open="isUploadDialogOpen">
            <DialogContent class="sm:max-w-[425px]">
                <DialogHeader>
                    <DialogTitle>Upload BKU SIPD</DialogTitle>
                    <DialogDescription>
                        Unggah file Excel BKU dan BKU Pajak untuk periode {{ months[(selectedMonth || 1) - 1] }} {{ selectedYear }}.
                    </DialogDescription>
                </DialogHeader>

                <div class="grid gap-4 py-4">
                    <div class="grid gap-2">
                        <Label for="bku_file">File BKU</Label>
                        <Input id="bku_file" type="file" @change="onUploadBku" accept=".xlsx,.xls,.csv" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="bku_pajak_file">File BKU Pajak</Label>
                        <Input id="bku_pajak_file" type="file" @change="onUploadBkuPajak" accept=".xlsx,.xls,.csv" />
                    </div>
                </div>

                <DialogFooter>
                    <Button variant="secondary" @click="isUploadDialogOpen = false">Batal</Button>
                    <Button :disabled="isUploading" @click="submitUpload">
                        <LoaderCircle v-if="isUploading" class="mr-2 h-4 w-4 animate-spin" />
                        Unggah dan Sinkron
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>
