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
        title: 'Transaksi LS',
        href: '/pajak-ls',
    },
    {
        title: 'Transaksi GU',
        href: '/pajak-gu',
    },
    {
        title: 'Master Akun Pajak',
        href: '/master-akun-pajak',
    },
    {
        title: 'SP2D NPWP',
        href: '/sp2d-npwp',
    },
];

const page = usePage<PageProps>();

const currentPath = page.props.ziggy?.location ? new URL(page.props.ziggy.location).pathname : '';
</script>

<template>
    <div class="px-4 py-6">
        <Heading title="Pajak" description="Manajemen Transaksi dan Akun Pajak" />

        <div class="flex flex-col space-y-8 md:space-y-0 lg:flex-row lg:space-x-12 lg:space-y-0">
            <aside class="w-full max-w-xl lg:w-48 lg:sticky lg:top-6 lg:self-start">
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

            <div class="flex-1 w-full min-w-0 overflow-hidden">
                <section class="max-w-5xl space-y-6">
                    <slot />
                </section>
            </div>
        </div>
    </div>
</template>
