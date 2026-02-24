<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import AdminLayout from '@/layouts/admin/Layout.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { LoaderCircle, CheckCircle2, AlertCircle } from 'lucide-vue-next';
import axios from 'axios';
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table';

const props = defineProps({
    skpd: Object,
    initialSyncStatus: Object
});

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'SKPD', href: '/admin/skpd' },
    { title: 'Realisasi Bulan', href: '#' },
];

const months = [
    { id: 1, name: 'Januari' },
    { id: 2, name: 'Februari' },
    { id: 3, name: 'Maret' },
    { id: 4, name: 'April' },
    { id: 5, name: 'Mei' },
    { id: 6, name: 'Juni' },
    { id: 7, name: 'Juli' },
    { id: 8, name: 'Agustus' },
    { id: 9, name: 'September' },
    { id: 10, name: 'Oktober' },
    { id: 11, name: 'November' },
    { id: 12, name: 'Desember' },
];

const syncingMonth = ref<number | null>(null);
const syncStatus = ref<Record<number, { success?: boolean; message?: string; loading?: boolean }>>(props.initialSyncStatus || {});

async function syncRealisasi(monthId: number) {
    syncingMonth.value = monthId;
    syncStatus.value[monthId] = { loading: true };
    
    try {
        const res = await axios.post(route('admin.skpd.realisasi.sync'), {
            id_skpd: props.skpd?.id_skpd,
            bulan: monthId
        });
        
        syncStatus.value[monthId] = { 
            success: res.data.status, 
            message: res.data.message,
            loading: false 
        };
    } catch (error: any) {
        syncStatus.value[monthId] = { 
            success: false, 
            message: error.response?.data?.message || 'Gagal sinkronisasi data.',
            loading: false 
        };
    } finally {
        syncingMonth.value = null;
    }
}
</script>

<template>
    <Head :title="'Realisasi - ' + skpd?.nama_skpd" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <AdminLayout>
            <div class="flex h-full flex-1 flex-col gap-4">
                <Card>
                    <CardHeader>
                        <CardTitle>Daftar Realisasi Per Bulan</CardTitle>
                        <CardDescription>
                            SKPD: {{ skpd?.nama_skpd }} ({{ skpd?.kode_skpd }})
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="rounded-md border">
                            <Table>
                                <TableHeader>
                                    <TableRow>
                                        <TableHead class="w-[50px]">No</TableHead>
                                        <TableHead>Bulan</TableHead>
                                        <TableHead>Status Sinkronisasi</TableHead>
                                        <TableHead class="text-right">Aksi</TableHead>
                                    </TableRow>
                                </TableHeader>
                                <TableBody>
                                    <TableRow v-for="(month, index) in months" :key="month.id">
                                        <TableCell>{{ index + 1 }}</TableCell>
                                        <TableCell class="font-medium">{{ month.name }}</TableCell>
                                        <TableCell>
                                            <div v-if="syncStatus[month.id]" class="flex items-center gap-2">
                                                <div v-if="syncStatus[month.id].loading" class="flex items-center gap-2 text-muted-foreground">
                                                    <LoaderCircle class="h-4 w-4 animate-spin" />
                                                    <span class="text-xs">Sinkronisasi...</span>
                                                </div>
                                                <div v-else-if="syncStatus[month.id].success" class="flex items-center gap-2 text-emerald-600">
                                                    <CheckCircle2 class="h-4 w-4" />
                                                    <span class="text-xs">{{ syncStatus[month.id].message }}</span>
                                                </div>
                                                <div v-else class="flex items-center gap-2 text-destructive">
                                                    <AlertCircle class="h-4 w-4" />
                                                    <span class="text-xs">{{ syncStatus[month.id].message }}</span>
                                                </div>
                                            </div>
                                            <span v-else class="text-xs text-muted-foreground italic">Belum disinkronkan</span>
                                        </TableCell>
                                        <TableCell class="text-right">
                                            <div class="flex justify-end gap-2">
                                                <Button 
                                                    variant="secondary"
                                                    size="sm"
                                                    @click="router.visit(route('admin.skpd.realisasi.detail', { skpd: props.skpd?.id, bulan: month.id }))"
                                                >
                                                    Detail
                                                </Button>
                                                <Button 
                                                    :disabled="syncingMonth !== null"
                                                    variant="outline"
                                                    size="sm"
                                                    @click="syncRealisasi(month.id)"
                                                >
                                                    <LoaderCircle v-if="syncingMonth === month.id" class="h-3 w-3 animate-spin mr-2" />
                                                    Sinkron Realisasi
                                                </Button>
                                            </div>
                                        </TableCell>
                                    </TableRow>
                                </TableBody>
                            </Table>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </AdminLayout>
    </AppLayout>
</template>
