<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, type SharedData } from '@/types';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { Receipt, FileText, ArrowRight, LayoutDashboard } from 'lucide-vue-next';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import PlaceholderPattern from '../components/PlaceholderPattern.vue';

const page = usePage<SharedData>();
const userRoles = page.props.auth.roles || [];
const isSkpd = computed(() => userRoles.some(role => role.toLowerCase().includes('skpd')));

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];

const skpdMenu = [
    {
        title: 'Transaksi LS',
        description: 'Kelola data transaksi pajak Langsung (LS).',
        icon: Receipt,
        href: '/pajak-ls',
        color: 'text-blue-500',
        bgColor: 'bg-blue-500/10',
    },
    {
        title: 'Transaksi GU',
        description: 'Kelola data transaksi pajak Ganti Uang (GU).',
        icon: FileText,
        href: '/pajak-gu',
        color: 'text-emerald-500',
        bgColor: 'bg-emerald-500/10',
    }
];
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div v-if="isSkpd" class="flex h-full flex-1 flex-col gap-6 p-4">
            <div class="flex flex-col gap-2">
                <h1 class="text-2xl font-bold tracking-tight">Selamat Datang, {{ page.props.auth.user.name }}</h1>
                <p class="text-muted-foreground">Silakan pilih modul di bawah untuk mulai mengelola transaksi pajak.</p>
            </div>

            <div class="grid gap-6 md:grid-cols-2">
                <Link v-for="item in skpdMenu" :key="item.title" :href="item.href" class="group">
                    <Card class="transition-all duration-300 hover:shadow-lg hover:-translate-y-1 border-sidebar-border/70 dark:border-sidebar-border h-full">
                        <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle class="text-sm font-bold uppercase tracking-wider text-muted-foreground">{{ item.title }}</CardTitle>
                            <div :class="['p-2 rounded-lg transition-colors group-hover:bg-opacity-20', item.bgColor]">
                                <component :is="item.icon" :class="['size-5', item.color]" />
                            </div>
                        </CardHeader>
                        <CardContent>
                            <CardDescription class="text-sm text-muted-foreground mb-4">
                                {{ item.description }}
                            </CardDescription>
                            <div class="flex items-center text-xs font-semibold text-primary opacity-0 -translate-x-2 transition-all duration-300 group-hover:opacity-100 group-hover:translate-x-0">
                                Masuk ke Modul <ArrowRight class="ml-2 size-3" />
                            </div>
                        </CardContent>
                    </Card>
                </Link>
            </div>

            <Card class="border-dashed border-sidebar-border/70 dark:border-sidebar-border mt-auto">
                <CardHeader>
                    <CardTitle class="text-lg flex items-center gap-2">
                        <LayoutDashboard class="size-5" />
                        Informasi Session
                    </CardTitle>
                </CardHeader>
                <CardContent>
                    <p class="text-sm">Tahun Anggaran Aktif: <span class="font-bold underline">{{ page.props.auth.tahun }}</span></p>
                </CardContent>
            </Card>
        </div>

        <div v-else class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="grid auto-rows-min gap-4 md:grid-cols-3">
                <div class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                    <PlaceholderPattern />
                </div>
                <div class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                    <PlaceholderPattern />
                </div>
                <div class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                    <PlaceholderPattern />
                </div>
            </div>
            <div class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 dark:border-sidebar-border md:min-h-min">
                <PlaceholderPattern />
            </div>
        </div>
    </AppLayout>
</template>
