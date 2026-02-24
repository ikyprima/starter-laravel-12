<script setup lang="ts">
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref, onMounted, computed, watch } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { LoaderCircle, Check, Search, ChevronsUpDown } from 'lucide-vue-next';
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
import axios from 'axios';
import { cn } from "@/lib/utils"

const props = defineProps<{
    user?: any;
    isEdit: boolean;
}>();

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Users', href: '/admin/users' },
    { title: props.isEdit ? 'Edit' : 'Create', href: '#' },
];

const form = useForm({
    name: props.user?.name || '',
    email: props.user?.email || '',
    password: '',
    password_confirmation: '',
    roles: props.user?.roles ? props.user.roles.map((r: any) => r.name) : [], // Start with array of strings
    kode_sub_skpd: props.user?.kode_sub_skpd || '',
});

// Role Selection Logic
const availableRoles = ref<any[]>([]);
const selectedRoles = ref<any[]>([]); 
// Note: Combobox usually selects one item. For multiple select, we might need multiple badges or a different UI.
// However, standard Combobox in the example seems to be single select? "selectedWilayah"
// User Management typically needs Multiple Roles.
// If using single select for now as per "Role lookup" request implying a dropdown.
// Let's assume single role for simplicity in this "Lookup" pattern, OR array of selected items.
// Using default HTML multiple select or Checkboxes is easier for multiple.
// But user requested "Role lookup to table roles", likely implying a Combobox.
// I'll implement a Multi-Select or just Single Select if User usually has one role.
// Most systems: User has one Role (Group). Spatie allows multiple.
// Let's implement Single Role selection via Combobox for now to match the "Lookup" feel of MasterAsesor.
// If multiple needed, we can adapt.
const selectedRole = ref<any>(null);

const openRolePicker = ref(false);

async function fetchRoles() {
    try {
        const res = await axios.get(route('admin.get-roles'));
        availableRoles.value = res.data;
        
        // If editing, set selectedRole
        if (props.isEdit && props.user?.roles?.length > 0) {
            // Find the role object that matches
             // Assuming first role for single-select UI
            const userRoleName = props.user.roles[0].name;
            selectedRole.value = availableRoles.value.find(r => r.value === userRoleName);
            
            // Sync form
             if (selectedRole.value) {
                form.roles = [selectedRole.value.value];
            }
        }
    } catch (e) {
        console.error(e);
    }
}

watch(selectedRole, (newVal) => {
    if (newVal) {
        form.roles = [newVal.value];
    } else {
        form.roles = [];
    }
});


const isDialogKonfirmasi = ref(false);

const submit = () => {
    if (props.isEdit) {
        form.put(route('admin.users.update', props.user.id), {
            onSuccess: () => {
                 isDialogKonfirmasi.value = true;
            }
        });
    } else {
        form.post(route('admin.users.store'), {
            onSuccess: () => {
                isDialogKonfirmasi.value = true;
                form.reset();
                selectedRole.value = null;
            }
        });
    }
};

const back = () => {
    router.visit(route('admin.users.index'));
};

// SKPD Selection Logic
const availableSubSkpd = ref<any[]>([]);
const selectedSubSkpd = ref<any>(null);
const openSubSkpdPicker = ref(false);
const isLoadingSubSkpd = ref(false);

const isSkpdRoleSelected = computed(() => {
    return form.roles.some((role: string) => role.toUpperCase().includes('SKPD'));
});

async function fetchSubSkpds() {
    if (availableSubSkpd.value.length > 0) return;
    isLoadingSubSkpd.value = true;
    try {
        const res = await axios.get(route('admin.get-sub-skpd'));
        availableSubSkpd.value = res.data;
        
        // If editing, set selectedSubSkpd
        if (props.isEdit && props.user?.kode_sub_skpd) {
            selectedSubSkpd.value = availableSubSkpd.value.find(s => s.value === props.user.kode_sub_skpd);
        }
    } catch (e) {
        console.error('Failed to fetch SKPDs', e);
    } finally {
        isLoadingSubSkpd.value = false;
    }
}

watch(selectedSubSkpd, (newVal) => {
    if (newVal) {
        form.kode_sub_skpd = newVal.value;
    } else {
        form.kode_sub_skpd = '';
    }
});

watch(isSkpdRoleSelected, (newVal) => {
    if (newVal) {
        fetchSubSkpds();
    } else {
        form.kode_sub_skpd = '';
        selectedSubSkpd.value = null;
    }
}, { immediate: true });

const closeModalKonfirmasi = () => {
    // Reset form if adding new
    form.reset();
    form.clearErrors();
    selectedRole.value = null;
};

onMounted(() => {
    fetchRoles();
});
</script>

<template>
    <Head :title="isEdit ? 'Edit User' : 'Create User'" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <Card class="relative w-full flex-1">
                <CardHeader>
                    <CardTitle class="uppercase">{{ isEdit ? 'Edit User' : 'Create User' }}</CardTitle>
                    <CardDescription>Fill in the form to {{ isEdit ? 'edit' : 'create' }} a user.</CardDescription>
                </CardHeader>

                <CardContent>
                    <form @submit.prevent="submit" class="flex flex-col gap-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="grid gap-2">
                                <Label for="name">Name</Label>
                                <Input id="name" type="text" v-model="form.name" required autofocus placeholder="Full Name" />
                                <InputError :message="form.errors.name" />
                            </div>

                            <div class="grid gap-2">
                                <Label for="email">Email</Label>
                                <Input id="email" type="email" v-model="form.email" required placeholder="name@example.com" />
                                <InputError :message="form.errors.email" />
                            </div>
                            
                            <div class="grid gap-2">
                                <Label for="password">Password {{ isEdit ? '(Leave blank to keep)' : '' }}</Label>
                                <Input id="password" type="password" v-model="form.password" :required="!isEdit" />
                                <InputError :message="form.errors.password" />
                            </div>

                            <div class="grid gap-2">
                                <Label for="password_confirmation">Confirm Password</Label>
                                <Input id="password_confirmation" type="password" v-model="form.password_confirmation" :required="!isEdit" />
                            </div>

                             <div class="grid gap-2">
                                <Label>Role</Label>
                                 <Popover v-model:open="openRolePicker">
                                    <PopoverTrigger as-child>
                                        <Button
                                            variant="outline"
                                            role="combobox"
                                            :aria-expanded="openRolePicker"
                                            class="w-full justify-between font-normal"
                                        >
                                            {{ selectedRole ? selectedRole.label : "Select role..." }}
                                            <ChevronsUpDown class="ml-2 h-4 w-4 shrink-0 opacity-50" />
                                        </Button>
                                    </PopoverTrigger>
                                    <PopoverContent class="w-[--radix-popover-trigger-width] p-0" align="start">
                                        <Command>
                                            <CommandInput class="h-9" placeholder="Search role..." />
                                            <CommandEmpty>No role found.</CommandEmpty>
                                            <CommandList>
                                                <CommandGroup>
                                                    <CommandItem
                                                        v-for="role in availableRoles"
                                                        :key="role.value"
                                                        :value="role.label"
                                                        @select="() => {
                                                            selectedRole = role
                                                            openRolePicker = false
                                                        }"
                                                    >
                                                        {{ role.label }}
                                                        <Check
                                                            :class="cn(
                                                                'ml-auto h-4 w-4',
                                                                selectedRole?.value === role.value ? 'opacity-100' : 'opacity-0',
                                                            )"
                                                        />
                                                    </CommandItem>
                                                </CommandGroup>
                                            </CommandList>
                                        </Command>
                                    </PopoverContent>
                                </Popover>
                                <InputError :message="form.errors.roles" />
                            </div>

                            <div v-if="isSkpdRoleSelected" class="grid gap-2 border-t pt-4 col-span-1 md:col-span-2">
                                <Label>Sub SKPD</Label>
                                <Popover v-model:open="openSubSkpdPicker">
                                    <PopoverTrigger as-child>
                                        <Button
                                            variant="outline"
                                            role="combobox"
                                            :aria-expanded="openSubSkpdPicker"
                                            class="w-full justify-between font-normal h-auto py-2 text-left"
                                        >
                                            <span class="truncate">
                                                {{ selectedSubSkpd ? selectedSubSkpd.label : (isLoadingSubSkpd ? "Loading..." : "Select SKPD...") }}
                                            </span>
                                            <ChevronsUpDown class="ml-2 h-4 w-4 shrink-0 opacity-50" />
                                        </Button>
                                    </PopoverTrigger>
                                    <PopoverContent class="w-[--radix-popover-trigger-width] p-0" align="start">
                                        <Command>
                                            <CommandInput class="h-9" placeholder="Search SKPD..." />
                                            <CommandEmpty>No SKPD found.</CommandEmpty>
                                            <CommandList>
                                                <CommandGroup>
                                                    <CommandItem
                                                        v-for="skpd in availableSubSkpd"
                                                        :key="skpd.value"
                                                        :value="skpd.label"
                                                        @select="() => {
                                                            selectedSubSkpd = skpd
                                                            openSubSkpdPicker = false
                                                        }"
                                                    >
                                                        {{ skpd.label }}
                                                        <Check
                                                            :class="cn(
                                                                'ml-auto h-4 w-4',
                                                                selectedSubSkpd?.value === skpd.value ? 'opacity-100' : 'opacity-0',
                                                            )"
                                                        />
                                                    </CommandItem>
                                                </CommandGroup>
                                            </CommandList>
                                        </Command>
                                    </PopoverContent>
                                </Popover>
                                <InputError :message="form.errors.kode_sub_skpd" />
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
