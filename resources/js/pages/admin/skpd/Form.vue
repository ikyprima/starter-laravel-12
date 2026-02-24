<script setup lang="ts">
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { LoaderCircle } from 'lucide-vue-next';
import InputError from '@/components/InputError.vue';
import {
    Card,
    CardContent,
    CardDescription,
    CardFooter,
    CardHeader,
    CardTitle,
} from "@/components/ui/card"
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';

const props = defineProps<{
    skpd?: any;
    isEdit: boolean;
}>();

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'SKPD', href: '/admin/skpd' },
    { title: props.isEdit ? 'Edit' : 'Create', href: '#' },
];

const form = useForm({
    id_skpd: props.skpd?.id_skpd || '',
    kode_skpd: props.skpd?.kode_skpd || '',
    nama_skpd: props.skpd?.nama_skpd || '',
    tahun: props.skpd?.tahun || '',
});

const isDialogKonfirmasi = ref(false);

const submit = () => {
    if (props.isEdit) {
        form.put(route('admin.skpd.update', props.skpd.id), {
            onSuccess: () => {
                 isDialogKonfirmasi.value = true;
            }
        });
    } else {
        form.post(route('admin.skpd.store'), {
            onSuccess: () => {
                isDialogKonfirmasi.value = true;
            }
        });
    }
};

const back = () => {
    router.visit(route('admin.skpd.index'));
};

const closeModalKonfirmasi = () => {
    if (!props.isEdit) {
        form.reset();
        form.clearErrors();
    }
};
</script>

<template>
    <Head :title="isEdit ? 'Edit SKPD' : 'Tambah SKPD'" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <Card class="relative w-full flex-1">
                <CardHeader>
                    <CardTitle class="uppercase">{{ isEdit ? 'Edit SKPD' : 'Tambah SKPD' }}</CardTitle>
                    <CardDescription>Isi formulir untuk {{ isEdit ? 'merubah' : 'menambah' }} data SKPD.</CardDescription>
                </CardHeader>

                <CardContent>
                    <form @submit.prevent="submit" class="flex flex-col gap-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="grid gap-2">
                                <Label for="id_skpd">ID SKPD</Label>
                                <Input id="id_skpd" type="number" v-model="form.id_skpd" required autofocus placeholder="Contoh: 2559" />
                                <InputError :message="form.errors.id_skpd" />
                            </div>

                            <div class="grid gap-2">
                                <Label for="kode_skpd">Kode SKPD</Label>
                                <Input id="kode_skpd" type="text" v-model="form.kode_skpd" required placeholder="Contoh: 1.01.0.00.0.00.01.0000" />
                                <InputError :message="form.errors.kode_skpd" />
                            </div>
                            
                            <div class="grid gap-2">
                                <Label for="nama_skpd">Nama SKPD</Label>
                                <Input id="nama_skpd" type="text" v-model="form.nama_skpd" required placeholder="Contoh: DINAS PENDIDIKAN" />
                                <InputError :message="form.errors.nama_skpd" />
                            </div>

                            <div class="grid gap-2">
                                <Label for="tahun">Tahun</Label>
                                <Input id="tahun" type="text" v-model="form.tahun" required placeholder="Contoh: 2025" />
                                <InputError :message="form.errors.tahun" />
                            </div>
                        </div>

                         <div class="flex justify-end gap-2">
                            <Button type="button" variant="secondary" @click="back" :disabled="form.processing">
                                Batal
                            </Button>
                            <Button type="submit" :disabled="form.processing">
                                <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin mr-2" />
                                Simpan
                            </Button>
                        </div>
                    </form>
                </CardContent>
            </Card>

             <Dialog v-model:open="isDialogKonfirmasi">
                <DialogContent class="sm:max-w-[425px]">
                    <DialogHeader>
                        <DialogTitle class="text-center">Berhasil</DialogTitle>
                        <DialogDescription class="text-center">
                            {{ isEdit ? 'Data berhasil diperbaharui.' : 'Data berhasil disimpan. Apakah ingin menambah data lain?' }}
                        </DialogDescription>
                    </DialogHeader>
                    <DialogFooter>
                         <div class="flex w-full justify-center gap-2">
                             <DialogClose as-child v-if="!isEdit">
                                <Button type="button" @click="closeModalKonfirmasi">Ya, Tambah Lagi</Button>
                            </DialogClose>
                            <Button type="button" @click="back" :variant="isEdit ? 'default' : 'destructive'">
                                {{ isEdit ? 'OK' : 'Kembali' }}
                            </Button>
                        </div>
                    </DialogFooter>
                </DialogContent>
            </Dialog>
        </div>
    </AppLayout>
</template>
