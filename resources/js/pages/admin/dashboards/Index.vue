<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { Users, ShieldCheck, Key, ArrowRight, Building2, Landmark } from 'lucide-vue-next';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import AdminLayout from '@/layouts/admin/Layout.vue';

const props = defineProps<{
    counts: {
        skpd: number;
        sub_skpd: number;
        users: number;
        master_akun_pajak: number;
    }
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/admin/dashboards',
    },
];

const menuItems = [
    {
        title: 'Users',
        description: 'Manage system users and their account details.',
        icon: Users,
        href: route('admin.users.index'),
        color: 'text-blue-500',
        bgColor: 'bg-blue-500/10',
        count: props.counts.users
    },
    {
        title: 'Roles',
        description: 'Define and manage user roles and their associated permissions.',
        icon: ShieldCheck,
        href: route('admin.roles.index'),
        color: 'text-purple-500',
        bgColor: 'bg-purple-500/10',
    },
    {
        title: 'Permissions',
        description: 'Manage granular access control permissions.',
        icon: Key,
        href: route('admin.permissions.index'),
        color: 'text-orange-500',
        bgColor: 'bg-orange-500/10',
    },
    {
        title: 'SKPD',
        description: 'Master data for Satuan Kerja Perangkat Daerah.',
        icon: Building2,
        href: route('admin.skpd.index'),
        color: 'text-emerald-500',
        bgColor: 'bg-emerald-500/10',
        count: props.counts.skpd
    },
    {
        title: 'Sub SKPD',
        description: 'Master data for Unit Satuan Kerja Perangkat Daerah.',
        icon: Landmark,
        href: route('admin.sub-skpd.index'),
        color: 'text-rose-500',
        bgColor: 'bg-rose-500/10',
        count: props.counts.sub_skpd
    },
    {
        title: 'Akun Pajak',
        description: 'Referesi Kode dan Jenis Akun Pajak.',
        icon: Landmark, // You can change this to something else like Receipt
        href: route('admin.master-akun-pajak.index'),
        color: 'text-cyan-500',
        bgColor: 'bg-cyan-500/10',
        count: props.counts.master_akun_pajak
    },
];
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <AdminLayout>
            <div class="flex h-full flex-1 flex-col gap-6">
                <!-- <h1 class="text-3xl font-bold tracking-tight">Admin Dashboard</h1> -->
                
                <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                    <Link v-for="item in menuItems" :key="item.title" :href="item.href" class="group">
                        <Card class="transition-all duration-300 hover:shadow-lg hover:-translate-y-1 border-sidebar-border/70 dark:border-sidebar-border h-full">
                            <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                                <CardTitle class="text-sm font-bold uppercase tracking-wider text-muted-foreground">{{ item.title }}</CardTitle>
                                <div :class="['p-2 rounded-lg transition-colors group-hover:bg-opacity-20', item.bgColor]">
                                    <component :is="item.icon" :class="['size-5', item.color]" />
                                </div>
                            </CardHeader>
                            <CardContent>
                                <div v-if="item.count !== undefined" class="text-3xl font-bold mb-1">
                                    {{ item.count }}
                                </div>
                                <CardDescription class="text-xs text-muted-foreground mb-4 line-clamp-2">
                                    {{ item.description }}
                                </CardDescription>
                                <div class="flex items-center text-xs font-semibold text-primary opacity-0 -translate-x-2 transition-all duration-300 group-hover:opacity-100 group-hover:translate-x-0">
                                    Manage Data <ArrowRight class="ml-2 size-3" />
                                </div>
                            </CardContent>
                        </Card>
                    </Link>
                </div>
                
                <!-- <div class="relative min-h-[300px] flex-1 rounded-xl border border-dashed border-sidebar-border/70 dark:border-sidebar-border flex items-center justify-center p-8 text-center">
                    <div class="max-w-md space-y-2">
                        <h3 class="text-xl font-semibold">Panel Administrator</h3>
                        <p class="text-muted-foreground">Silakan gunakan navigasi di sebelah kiri atau kartu statistik di atas untuk mengelola berbagai modul sistem.</p>
                    </div>
                </div> -->
            </div>
        </AdminLayout>
    </AppLayout>
</template>
