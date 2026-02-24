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
    item?: any;
    isEdit: boolean;
}>();

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Master Akun Pajak', href: route('pajak.master-akun.index') },
    { title: props.isEdit ? 'Edit' : 'Tambah', href: '#' },
];

const form = useForm({
    kode_akun_pajak: props.item?.kode_akun_pajak || '',
    jenis_pajak: props.item?.jenis_pajak || '',
});

const isDialogKonfirmasi = ref(false);

const submit = () => {
    if (props.isEdit) {
        form.put(route('pajak.master-akun.update', props.item.id), {
            onSuccess: () => {
                 isDialogKonfirmasi.value = true;
            }
        });
    } else {
        form.post(route('pajak.master-akun.store'), {
            onSuccess: () => {
                isDialogKonfirmasi.value = true;
                form.reset();
            }
        });
    }
};

const back = () => {
    router.visit(route('pajak.master-akun.index'));
};

const closeModalKonfirmasi = () => {
    form.reset();
    form.clearErrors();
};
</script>

<template>
    <Head :title="isEdit ? 'Edit Akun Pajak' : 'Tambah Akun Pajak'" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <Card class="relative w-full max-w-2xl mx-auto">
                <CardHeader>
                    <CardTitle class="uppercase">{{ isEdit ? 'Edit Akun Pajak' : 'Tambah Akun Pajak' }}</CardTitle>
                    <CardDescription>Masukkan rincian akun pajak di bawah ini.</CardDescription>
                </CardHeader>

                <CardContent>
                    <form @submit.prevent="submit" class="flex flex-col gap-6">
                        <div class="grid gap-4">
                            <div class="grid gap-2">
                                <Label for="kode_akun_pajak">Kode Akun Pajak</Label>
                                <Input id="kode_akun_pajak" type="text" v-model="form.kode_akun_pajak" required autofocus placeholder="Contoh: 411121" />
                                <InputError :message="form.errors.kode_akun_pajak" />
                            </div>

                            <div class="grid gap-2">
                                <Label for="jenis_pajak">Jenis Pajak</Label>
                                <Input id="jenis_pajak" type="text" v-model="form.jenis_pajak" required placeholder="Contoh: PPh Pasal 21" />
                                <InputError :message="form.errors.jenis_pajak" />
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
                            {{ isEdit ? 'Data berhasil diperbarui.' : 'Data berhasil disimpan. Apakah Anda ingin menambah data lagi?' }}
                        </DialogDescription>
                    </DialogHeader>
                    <DialogFooter>
                         <div class="flex w-full justify-center gap-2">
                             <DialogClose as-child v-if="!isEdit">
                                <Button type="button" @click="closeModalKonfirmasi">Ya, Tambah Lagi</Button>
                            </DialogClose>
                            <Button type="button" @click="back" :variant="isEdit ? 'default' : 'destructive'">
                                {{ isEdit ? 'Selesai' : 'Tidak, Kembali' }}
                            </Button>
                        </div>
                    </DialogFooter>
                </DialogContent>
            </Dialog>
        </div>
    </AppLayout>
</template>
