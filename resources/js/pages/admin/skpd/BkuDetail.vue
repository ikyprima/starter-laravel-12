<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import AdminLayout from '@/layouts/admin/Layout.vue';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { ArrowLeft } from 'lucide-vue-next';
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table';
import {
    Tooltip,
    TooltipContent,
    TooltipProvider,
    TooltipTrigger,
} from '@/components/ui/tooltip';

const props = defineProps({
    skpd: Object,
    bulan: Number,
    tahun: [String, Number],
    data: Array
});

const months = [
    '', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
];

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'SKPD', href: '/admin/skpd' },
    { title: 'BKU Bulan', href: route('admin.skpd.bku', props.skpd?.id) },
    { title: 'Detail', href: '#' },
];

function formatCurrency(value: any) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(value || 0);
}

function formatDate(dateString: string) {
    if (!dateString) return '-';
    const cleanDate = dateString.split(/[ T]/)[0];
    const parts = cleanDate.split('-');
    if (parts.length === 3) {
        return `${parts[2]}-${parts[1]}-${parts[0]}`;
    }
    return dateString;
}
</script>

<template>
    <Head :title="'Detail BKU - ' + months[bulan || 0]" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <AdminLayout>
            <div class="flex h-full flex-1 flex-col gap-4 w-full min-w-0 overflow-hidden">
                <div class="flex items-center justify-between">
                    <Button variant="ghost" size="sm" @click="router.visit(route('admin.skpd.bku', props.skpd?.id))">
                        <ArrowLeft class="mr-2 h-4 w-4" />
                        Kembali Ke Daftar Bulan
                    </Button>
                </div>

                <Card class="flex w-full flex-col min-w-0 overflow-hidden">
                    <CardHeader class="flex-none">
                        <CardTitle>Detail BKU {{ months[bulan || 0] }} {{ tahun }}</CardTitle>
                        <CardDescription>
                            SKPD: {{ skpd?.nama_skpd }} ({{ skpd?.kode_skpd }})
                        </CardDescription>
                    </CardHeader>
                    <CardContent class="flex-1 p-6 min-w-0 overflow-x-auto">
                        <div class="relative border rounded-md min-w-max">
                            <Table>
                                <TableHeader class="sticky top-0 bg-background z-10">
                                    <TableRow>
                                        <TableHead class="w-[90px] text-center text-xs">Tanggal</TableHead>
                                        <TableHead class="text-xs">Keterangan Dokumen</TableHead>
                                        <TableHead class="text-right text-xs">Penerimaan</TableHead>
                                        <TableHead class="text-right text-xs">Pengeluaran</TableHead>
                                        <TableHead class="text-right text-xs">Saldo</TableHead>
                                    </TableRow>
                                </TableHeader>
                                <TableBody>
                                    <TableRow v-for="item in (data as any[])" :key="item.id">
                                        <TableCell class="whitespace-nowrap text-xs">{{ formatDate(item.tanggal) }}</TableCell>
                                        <TableCell>
                                            <div class="flex flex-col gap-1.5">
                                                <span class="font-bold text-xs tracking-tight whitespace-nowrap">{{ item.nomor_dokumen }}</span>
                                                <div class="max-w-[400px]">
                                                    <TooltipProvider>
                                                        <Tooltip :delayDuration="200">
                                                            <TooltipTrigger as-child>
                                                                <span class="truncate text-[10px] text-muted-foreground line-clamp-2 cursor-help leading-relaxed block overflow-hidden">
                                                                    {{ item.uraian }}
                                                                </span>
                                                            </TooltipTrigger>
                                                            <TooltipContent side="bottom" class="max-w-[400px] p-3 text-xs bg-popover text-popover-foreground shadow-md border">
                                                                <div class="flex flex-col gap-1">
                                                                    <p class="font-bold border-b pb-1 mb-1 text-xs">{{ item.nomor_dokumen }}</p>
                                                                    <p class="text-xs">{{ item.uraian }}</p>
                                                                </div>
                                                            </TooltipContent>
                                                        </Tooltip>
                                                    </TooltipProvider>
                                                </div>
                                            </div>
                                        </TableCell>
                                        <TableCell class="text-right text-xs whitespace-nowrap">{{ formatCurrency(item.penerimaan) }}</TableCell>
                                        <TableCell class="text-right text-xs whitespace-nowrap">{{ formatCurrency(item.pengeluaran) }}</TableCell>
                                        <TableCell class="text-right text-xs whitespace-nowrap font-bold text-emerald-600 dark:text-emerald-500">{{ formatCurrency(item.saldo) }}</TableCell>
                                       
                                    </TableRow>
                                    <TableRow v-if="!data || data.length === 0">
                                        <TableCell colspan="6" class="text-center py-20 text-muted-foreground">
                                            Tidak ada data untuk bulan ini.
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
