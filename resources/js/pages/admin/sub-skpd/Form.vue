<script setup lang="ts">
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { LoaderCircle, Check, ChevronsUpDown } from 'lucide-vue-next';
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
import {
    Popover,
    PopoverContent,
    PopoverTrigger,
} from '@/components/ui/popover';
import {
    Command,
    CommandEmpty,
    CommandGroup,
    CommandInput,
    CommandItem,
    CommandList,
} from '@/components/ui/command';
import { cn } from "@/lib/utils"

const props = defineProps<{
    subSkpd?: any;
    isEdit: boolean;
    skpds: any[];
}>();

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Sub SKPD', href: '/admin/sub-skpd' },
    { title: props.isEdit ? 'Edit' : 'Create', href: '#' },
];

const form = useForm({
    kode_skpd: props.subSkpd?.kode_skpd || '',
    kode_sub_skpd: props.subSkpd?.kode_sub_skpd || '',
    nama_sub_skpd: props.subSkpd?.nama_sub_skpd || '',
    tahun: props.subSkpd?.tahun || '',
});

const isDialogKonfirmasi = ref(false);
const openSkpdPicker = ref(false);

const selectedSkpd = computed(() => {
    return props.skpds.find(s => s.kode_skpd === form.kode_skpd);
});

const submit = () => {
    if (props.isEdit) {
        form.put(route('admin.sub-skpd.update', props.subSkpd.id), {
            onSuccess: () => {
                 isDialogKonfirmasi.value = true;
            }
        });
    } else {
        form.post(route('admin.sub-skpd.store'), {
            onSuccess: () => {
                isDialogKonfirmasi.value = true;
            }
        });
    }
};

const back = () => {
    router.visit(route('admin.sub-skpd.index'));
};

const closeModalKonfirmasi = () => {
    if (!props.isEdit) {
        form.reset();
        form.clearErrors();
    }
};
</script>

<template>
    <Head :title="isEdit ? 'Edit Sub SKPD' : 'Tambah Sub SKPD'" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <Card class="relative w-full flex-1">
                <CardHeader>
                    <CardTitle class="uppercase">{{ isEdit ? 'Edit Sub SKPD' : 'Tambah Sub SKPD' }}</CardTitle>
                    <CardDescription>Isi formulir untuk {{ isEdit ? 'merubah' : 'menambah' }} data Sub SKPD.</CardDescription>
                </CardHeader>

                <CardContent>
                    <form @submit.prevent="submit" class="flex flex-col gap-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="grid gap-2">
                                <Label>SKPD Induk</Label>
                                <Popover v-model:open="openSkpdPicker">
                                    <PopoverTrigger as-child>
                                        <Button
                                            variant="outline"
                                            role="combobox"
                                            :aria-expanded="openSkpdPicker"
                                            class="w-full justify-between font-normal h-auto py-2 text-left"
                                        >
                                            <span class="truncate">
                                                {{ selectedSkpd ? selectedSkpd.nama_skpd : "Pilih SKPD..." }}
                                            </span>
                                            <ChevronsUpDown class="ml-2 h-4 w-4 shrink-0 opacity-50" />
                                        </Button>
                                    </PopoverTrigger>
                                    <PopoverContent class="w-[--radix-popover-trigger-width] p-0" align="start">
                                        <Command>
                                            <CommandInput class="h-9" placeholder="Cari SKPD..." />
                                            <CommandEmpty>SKPD tidak ditemukan.</CommandEmpty>
                                            <CommandList>
                                                <CommandGroup>
                                                    <CommandItem
                                                        v-for="skpd in skpds"
                                                        :key="skpd.kode_skpd"
                                                        :value="skpd.nama_skpd"
                                                        @select="() => {
                                                            form.kode_skpd = skpd.kode_skpd
                                                            openSkpdPicker = false
                                                        }"
                                                    >
                                                        {{ skpd.nama_skpd }}
                                                        <Check
                                                            :class="cn(
                                                                'ml-auto h-4 w-4',
                                                                form.kode_skpd === skpd.kode_skpd ? 'opacity-100' : 'opacity-0',
                                                            )"
                                                        />
                                                    </CommandItem>
                                                </CommandGroup>
                                            </CommandList>
                                        </Command>
                                    </PopoverContent>
                                </Popover>
                                <InputError :message="form.errors.kode_skpd" />
                            </div>

                            <div class="grid gap-2">
                                <Label for="kode_sub_skpd">Kode Sub SKPD</Label>
                                <Input id="kode_sub_skpd" type="text" v-model="form.kode_sub_skpd" required placeholder="Contoh: 1.01.0.00.0.00.01.0000" />
                                <InputError :message="form.errors.kode_sub_skpd" />
                            </div>
                            
                            <div class="grid gap-2">
                                <Label for="nama_sub_skpd">Nama Sub SKPD</Label>
                                <Input id="nama_sub_skpd" type="text" v-model="form.nama_sub_skpd" required placeholder="Contoh: DINAS PENDIDIKAN" />
                                <InputError :message="form.errors.nama_sub_skpd" />
                            </div>

                            <div class="grid gap-2">
                                <Label for="tahun">Tahun</Label>
                                <Input id="tahun" type="text" v-model="form.tahun" required placeholder="Contoh: 2026" />
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
