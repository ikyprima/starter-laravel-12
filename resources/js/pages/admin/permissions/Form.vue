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
    permission: any;
    isEdit: boolean;
}>();

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Permissions', href: '/admin/permissions' },
    { title: props.isEdit ? 'Edit' : 'Create', href: '#' },
];

const form = useForm({
    name: props.permission.name || '',
});

const isDialogKonfirmasi = ref(false);

const submit = () => {
    if (props.isEdit) {
        form.put(route('admin.permissions.update', props.permission.id), {
            onSuccess: () => {
                 isDialogKonfirmasi.value = true;
            }
        });
    } else {
        form.post(route('admin.permissions.store'), {
            onSuccess: () => {
                isDialogKonfirmasi.value = true;
                form.reset();
            }
        });
    }
};

const back = () => {
    router.visit(route('admin.permissions.index'));
};

const closeModalKonfirmasi = () => {
    form.reset();
    form.clearErrors();
};
</script>

<template>
    <Head :title="isEdit ? 'Edit Permission' : 'Create Permission'" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <Card class="relative w-full flex-1 max-w-2xl">
                <CardHeader>
                    <CardTitle class="uppercase">{{ isEdit ? 'Edit Permission' : 'Create Permission' }}</CardTitle>
                    <CardDescription>Fill in the form to {{ isEdit ? 'edit' : 'create' }} a permission.</CardDescription>
                </CardHeader>

                <CardContent>
                    <form @submit.prevent="submit" class="flex flex-col gap-6">
                        <div class="grid gap-2">
                            <Label for="name">Name</Label>
                            <Input id="name" v-model="form.name" required autofocus placeholder="Permission Name" />
                            <InputError :message="form.errors.name" />
                        </div>

                        <div class="flex justify-end gap-2">
                            <Button type="button" variant="secondary" @click="back" :disabled="form.processing">
                                Cancel
                            </Button>
                            <Button type="submit" :disabled="form.processing">
                                <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin mr-2" />
                                Save
                            </Button>
                        </div>
                    </form>
                </CardContent>
            </Card>

             <Dialog v-model:open="isDialogKonfirmasi">
                <DialogContent class="sm:max-w-[425px]">
                    <DialogHeader>
                        <DialogTitle class="text-center">Success</DialogTitle>
                        <DialogDescription class="text-center">
                            {{ isEdit ? 'Data updated successfully.' : 'Data saved successfully. Do you want to add another?' }}
                        </DialogDescription>
                    </DialogHeader>
                    <DialogFooter>
                         <div class="flex w-full justify-center gap-2">
                             <DialogClose as-child v-if="!isEdit">
                                <Button type="button" @click="closeModalKonfirmasi">Yes, Add Another</Button>
                            </DialogClose>
                            <Button type="button" @click="back" :variant="isEdit ? 'default' : 'destructive'">
                                {{ isEdit ? 'OK' : 'No, Go Back' }}
                            </Button>
                        </div>
                    </DialogFooter>
                </DialogContent>
            </Dialog>
        </div>
    </AppLayout>
</template>
