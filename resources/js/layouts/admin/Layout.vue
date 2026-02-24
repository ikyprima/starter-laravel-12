<script setup lang="ts">
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { Separator } from '@/components/ui/separator';
import { type NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { PageProps as InertiaPageProps } from '@inertiajs/core';

interface PageProps extends InertiaPageProps {
    ziggy?: {
        location: string;
    };
}

const sidebarNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: '/admin/dashboards',
    },
    {
        title: 'Users',
        href: '/admin/users',
    },
    {
        title: 'Roles',
        href: '/admin/roles',
    },
    {
        title: 'Permissions',
        href: '/admin/permissions',
    },
    {
        title: 'SKPD',
        href: '/admin/skpd',
    },
    {
        title: 'Sub SKPD',
        href: '/admin/sub-skpd',
    },
    // {
    //     title: 'BKU Pajak',
    //     href: '/admin/bku-pajak',
    // },
    // {
    //     title: 'Laporan Realisasi',
    //     href: '/admin/laporan-realisasi',
    // },
    {
        title: 'Master Akun Pajak',
        href: '/admin/master-akun-pajak',
    },
    {
        title: 'SP2D NPWP',
        href: '/admin/sp2d-npwp',
    },
];

const page = usePage<PageProps>();

const currentPath = page.props.ziggy?.location ? new URL(page.props.ziggy.location).pathname : '';
</script>

<template>
    <div class="px-4 py-6">
        <Heading title="Administrator" description="Manajemen Konfigurasi Sistem dan Master Data" />

        <div class="flex flex-1 flex-col space-y-8 md:space-y-0 lg:flex-row lg:space-x-12 lg:space-y-0 min-w-0">
            <aside class="w-full max-w-xl lg:w-48 flex-none">
                <nav class="flex flex-col space-x-0 space-y-1">
                    <Button
                        v-for="item in sidebarNavItems"
                        :key="item.href"
                        variant="ghost"
                        :class="['w-full justify-start', { 'bg-muted': currentPath === item.href }]"
                        as-child
                    >
                        <Link :href="item.href ?? ''">
                            {{ item.title }}
                        </Link>
                    </Button>
                </nav>
            </aside>

            <Separator class="my-6 md:hidden" />

            <div class="flex-1 min-w-0 overflow-hidden">
                <section class="max-w-full space-y-12">
                    <slot />
                </section>
            </div>
        </div>
    </div>
</template>
