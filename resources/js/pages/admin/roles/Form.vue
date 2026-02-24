<script setup lang="ts">
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Checkbox } from '@/components/ui/checkbox';
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
    role: any;
    permissions: Record<string, any[]>;
    isEdit: boolean;
}>();

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Roles', href: '/admin/roles' },
    { title: props.isEdit ? 'Edit' : 'Create', href: '#' },
];

const form = useForm({
    name: props.role.name || '',
    permissions: props.role.permissions ? props.role.permissions.map((p: any) => p.name) : [],
});

const isDialogKonfirmasi = ref(false);

const submit = () => {
    if (props.isEdit) {
        form.put(route('admin.roles.update', props.role.id), {
            onSuccess: () => {
                 isDialogKonfirmasi.value = true;
            }
        });
    } else {
        form.post(route('admin.roles.store'), {
            onSuccess: () => {
                isDialogKonfirmasi.value = true;
                form.reset();
            }
        });
    }
};

const back = () => {
    router.visit(route('admin.roles.index'));
};

const closeModalKonfirmasi = () => {
    form.reset();
    form.clearErrors();
};
</script>

<template>
    <Head :title="isEdit ? 'Edit Role' : 'Create Role'" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <Card class="relative w-full flex-1">
                <CardHeader>
                    <CardTitle class="uppercase">{{ isEdit ? 'Edit Role' : 'Create Role' }}</CardTitle>
                    <CardDescription>Fill in the form to {{ isEdit ? 'edit' : 'create' }} a role and assign permissions.</CardDescription>
                </CardHeader>

                <CardContent>
                    <form @submit.prevent="submit" class="flex flex-col gap-6">
                        <div class="grid gap-2">
                            <Label for="name">Name</Label>
                            <Input id="name" v-model="form.name" required autofocus placeholder="Role Name" />
                            <InputError :message="form.errors.name" />
                        </div>

                        <div class="space-y-4">
                            <Label>Permissions</Label>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                <div v-for="(group, groupName) in permissions" :key="groupName" class="border p-4 rounded-md bg-muted/20">
                                    <h3 class="font-semibold capitalize mb-3 border-b pb-2">{{ groupName }}</h3>
                                    <div class="space-y-2">
                                        <div v-for="permission in group" :key="permission.id" class="flex items-center space-x-2">
                                            <Checkbox 
                                                :id="'perm-' + permission.id" 
                                                :checked="form.permissions.includes(permission.name)"
                                                @update:checked="(checked: boolean) => {
                                                    if (checked) form.permissions.push(permission.name);
                                                    else form.permissions = form.permissions.filter((p: string) => p !== permission.name);
                                                }"
                                            />
                                            <Label :for="'perm-' + permission.id" class="text-sm font-normal cursor-pointer">{{ permission.name }}</Label>
                                        </div>
                                    </div>
                                </div>
                            </div>
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
