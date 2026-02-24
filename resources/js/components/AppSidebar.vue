<script setup lang="ts">
import { computed } from 'vue';
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavMainRecursive from '@/components/NavMainParentRecursive.vue';
import NavUser from '@/components/NavUser.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavItem, type SharedData } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { BookOpen, Folder, LayoutGrid, Calendar } from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';

const page = usePage<SharedData>();
const activeYear = page.props.auth.tahun;
const userRoles = page.props.auth.roles || [];

const isAdmin = computed(() => userRoles.some(role => role.toLowerCase() === 'admin'));
const isSkpd = computed(() => userRoles.some(role => role.toLowerCase().includes('skpd')) && !isAdmin.value);

const mainNavItems = computed<NavItem[]>(() => {
    if (isSkpd.value) {
        return [
            {
                title: 'Dashboard',
                href: '/dashboard',
                icon: LayoutGrid,
            },
            {
                title: 'Pajak',
                icon: LayoutGrid,
                children: [
                    {
                        title: 'Transaksi LS',
                        icon: LayoutGrid,
                        href: '/pajak-ls',
                    },
                    {
                        title: 'Transaksi GU',
                        icon: LayoutGrid,
                        href: '/pajak-gu',
                    },
                ]
            },
        ];
    }

    // Default or Admin view
    return [
        {
            title: 'Dashboard',
            href: '/dashboard',
            icon: LayoutGrid,
        },
        // {
        //     title: 'Dokumen',
        //     icon: LayoutGrid,
        //     children: [
        //         { 
        //             title: 'Semua Dokumen', 
        //             icon: LayoutGrid,
        //             children: [
        //                 { 
        //                     title: 'sub Dokumen 1', 
        //                     icon: LayoutGrid,
        //                     href: '/semua-dokumen' },
        //                 { 
        //                     title: 'sub Arsip 1',
        //                     icon: LayoutGrid,
        //                     href: '/dokumen-a' 
        //                 },
        //             ] 
        //         },
        //         { 
        //             title: 'Kategori',
        //             icon: LayoutGrid,
        //             href: '/dokumen/kategori' 
        //         },
        //     ]
        // },
        {
            title: 'Pajak',
            icon: LayoutGrid,
            children: [
                {
                    title: 'Transaksi LS',
                    icon: LayoutGrid,
                    href: '/pajak-ls',
                },
                {
                    title: 'Transaksi GU',
                    icon: LayoutGrid,
                    href: '/pajak-gu',
                },
            ]
        },
    ];
});

const footerNavItems = computed<NavItem[]>(() => {
    if (isAdmin.value) {
        return [
            {
                title: 'Administrator',
                href: '/admin/dashboards',
                icon: Folder,
            },
        ];
    }
    
    return [];
});
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="route('dashboard')">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMainRecursive :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <!-- <NavFooter :items="footerNavItems" /> -->
            <NavMain :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
