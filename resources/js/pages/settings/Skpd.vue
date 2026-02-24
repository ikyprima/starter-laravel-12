<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import HeadingSmall from '@/components/HeadingSmall.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { type BreadcrumbItem } from '@/types';
import { toast } from 'vue-sonner';

interface Props {
    skpd: {
        id: number;
        nama_skpd: string;
        kode_skpd: string;
        npwp: string | null;
    } | null;
    subSkpd: {
        nama_sub_skpd: string;
        kode_sub_skpd: string;
    } | null;
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'SKPD settings',
        href: '/settings/skpd',
    },
];

const form = useForm({
    nama_skpd: props.skpd?.nama_skpd || '',
    npwp: props.skpd?.npwp || '',
});

const submit = () => {
    form.patch(route('settings.skpd.update'), {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Informasi SKPD berhasil diperbarui');
        },
    });
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="SKPD settings" />

        <SettingsLayout>
            <div class="flex flex-col space-y-6">
                <HeadingSmall 
                    title="Informasi Unit Kerja (SKPD)" 
                    description="Kelola informasi Satuan Kerja Perangkat Daerah Anda." 
                />

                <div v-if="!skpd" class="p-4 bg-muted/50 rounded-lg border border-dashed text-center text-muted-foreground">
                    Data SKPD tidak ditemukan untuk user ini.
                </div>

                <form v-else @submit.prevent="submit" class="space-y-6">
                    <div class="grid gap-2">
                        <Label for="kode_skpd">Kode SKPD</Label>
                        <Input id="kode_skpd" class="mt-1 block w-full bg-muted" :model-value="props.skpd?.kode_skpd" disabled />
                    </div>

                    <div class="grid gap-2">
                        <Label for="nama_skpd">Nama SKPD</Label>
                        <Input id="nama_skpd" class="mt-1 block w-full" v-model="form.nama_skpd" required />
                        <InputError class="mt-2" :message="form.errors.nama_skpd" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="npwp">NPWP SKPD</Label>
                        <Input id="npwp" class="mt-1 block w-full" v-model="form.npwp" placeholder="00.000.000.0-000.000" />
                        <InputError class="mt-2" :message="form.errors.npwp" />
                    </div>

                    <div v-if="subSkpd" class="grid gap-2">
                        <Label>Sub Unit Kerja</Label>
                        <div class="text-sm text-muted-foreground bg-muted/30 p-2 rounded border">
                            {{ subSkpd.kode_sub_skpd }} - {{ subSkpd.nama_sub_skpd }}
                        </div>
                    </div>

                    <div class="flex items-center gap-4">
                        <Button :disabled="form.processing">Simpan Perubahan</Button>

                        <Transition
                            enter-active-class="transition ease-in-out"
                            enter-from-class="opacity-0"
                            leave-active-class="transition ease-in-out"
                            leave-to-class="opacity-0"
                        >
                            <p v-show="form.recentlySuccessful" class="text-sm text-neutral-600">Berhasil disimpan.</p>
                        </Transition>
                    </div>
                </form>
            </div>
        </SettingsLayout>
    </AppLayout>
</template>
