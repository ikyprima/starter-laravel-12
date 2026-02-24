<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import MasterLayout from '@/layouts/admin/LayoutFull.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Checkbox } from '@/components/ui/checkbox';
import { Select, SelectContent, SelectGroup, SelectItem, SelectLabel, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Trash2, Plus, Clock, ShieldAlert } from 'lucide-vue-next';

interface TypeDefinition {
    name: string;
    category: string;
    [key: string]: any;
}

interface Column {
    name: string;
    oldName?: string;
    type: TypeDefinition;
    length: number | undefined;
    precision: number | undefined;
    scale: number | undefined;
    fixed: boolean;
    unsigned: boolean;
    autoincrement: boolean;
    notnull: boolean;
    default: any;
    index?: 'PRIMARY' | 'UNIQUE' | 'INDEX' | null;
}

const props = defineProps<{
    db: {
        action: 'create' | 'update';
        table: {
            name: string;
            oldName?: string;
            columns: Column[];
            indexes: any[];
            foreignKeys: any[];
            options: any;
        };
        types: Record<string, TypeDefinition[]>;
        platform: string;
    };
}>();

const breadcrumbs = [
    { title: 'Admin', href: '/admin/dashboards' },
    { title: 'Database Manager', href: '/admin/database-manager' },
    { title: props.db.action === 'update' ? 'Edit Table' : 'New Table', href: '#' },
];

const form = useForm({
    name: props.db.table.name === 'New Table' ? '' : props.db.table.name,
    oldName: props.db.table.oldName || (props.db.table.name === 'New Table' ? '' : props.db.table.name),
    columns: props.db.table.columns.map(c => {
        // Find existing index for this column
        let indexType: any = null;
        if (props.db.table.indexes) {
            const idx = props.db.table.indexes.find(i => i.columns.includes(c.name));
            if (idx) indexType = idx.type;
        }
        return {
            ...c,
            index: indexType
        };
    }),
    indexes: props.db.table.indexes || [],
    foreignKeys: props.db.table.foreignKeys || [],
    options: props.db.table.options || { create_options: [] },
});

const handleTypeChange = (index: number, typeName: any) => {
    if (!typeName) return;
    // Find the full type object from props.db.types
    for (const category in props.db.types) {
        const found = props.db.types[category].find(t => t.name === typeName);
        if (found) {
            form.columns[index].type = { ...found };
            break;
        }
    }
};

const addColumn = () => {
    form.columns.push({
        name: '',
        type: { name: 'integer', category: 'Numbers' },
        length: undefined,
        precision: undefined,
        scale: undefined,
        fixed: false,
        unsigned: false,
        autoincrement: false,
        notnull: false,
        default: null,
        index: null,
    });
};

const removeColumn = (index: number) => {
    form.columns.splice(index, 1);
};

const addTimestamp = () => {
    const hasCreatedAt = form.columns.some(c => c.name === 'created_at');
    const hasUpdatedAt = form.columns.some(c => c.name === 'updated_at');

    if (!hasCreatedAt) {
        form.columns.push({
            name: 'created_at',
            type: { name: 'timestamp', category: 'Date and Time' },
            length: undefined,
            precision: undefined,
            scale: undefined,
            fixed: false,
            unsigned: false,
            autoincrement: false,
            notnull: true,
            default: 'CURRENT_TIMESTAMP',
            index: null,
        });
    }
    if (!hasUpdatedAt) {
        form.columns.push({
            name: 'updated_at',
            type: { name: 'timestamp', category: 'Date and Time' },
            length: undefined,
            precision: undefined,
            scale: undefined,
            fixed: false,
            unsigned: false,
            autoincrement: false,
            notnull: true,
            default: 'CURRENT_TIMESTAMP',
            index: null,
        });
    }
};

const addSoftDelete = () => {
    const hasDeletedAt = form.columns.some(c => c.name === 'deleted_at');
    if (!hasDeletedAt) {
        form.columns.push({
            name: 'deleted_at',
            type: { name: 'timestamp', category: 'Date and Time' },
            length: undefined,
            precision: undefined,
            scale: undefined,
            fixed: false,
            unsigned: false,
            autoincrement: false,
            notnull: false,
            default: null,
            index: null,
        });
    }
};

const submit = () => {
    // Sync indexes from columns back to form.indexes before submitting
    const newIndexes: any[] = [];
    form.columns.forEach(col => {
        if (col.index) {
            newIndexes.push({
                name: col.index === 'PRIMARY' ? 'primary' : `${form.name}_${col.name}_${col.index.toLowerCase()}`,
                columns: [col.name],
                type: col.index,
                table: form.name
            });
        }
    });
    form.indexes = newIndexes;

    if (props.db.action === 'update') {
        form.put(route('admin.database-manager.update', props.db.table.oldName || props.db.table.name));
    } else {
        form.post(route('admin.database-manager.store'));
    }
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head :title="db.action === 'update' ? 'Edit Table' : 'New Table'" />
        <MasterLayout>
            <form @submit.prevent="submit" class="space-y-6">
                <div class="bg-white p-6 rounded-lg shadow-sm border space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <Label for="table_name">Table Name</Label>
                            <Input id="table_name" v-model="form.name" placeholder="e.g. users, posts" required />
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-2">
                        <Button type="button" variant="outline" size="sm" @click="addColumn">
                            <Plus class="w-4 h-4 mr-2" /> Add Column
                        </Button>
                        <Button type="button" variant="outline" size="sm" @click="addTimestamp">
                            <Clock class="w-4 h-4 mr-2" /> Add Timestamps
                        </Button>
                        <Button type="button" variant="outline" size="sm" @click="addSoftDelete">
                            <ShieldAlert class="w-4 h-4 mr-2" /> Add Soft Deletes
                        </Button>
                    </div>

                    <div class="border rounded-md overflow-hidden">
                        <Table>
                            <TableHeader>
                                <TableRow class="bg-slate-50">
                                    <TableHead class="w-[180px]">Name</TableHead>
                                    <TableHead class="w-[180px]">Type</TableHead>
                                    <TableHead class="w-[150px]">Size / Params</TableHead>
                                    <TableHead class="w-[120px]">Index</TableHead>
                                    <TableHead class="text-center w-[70px]">Not Null</TableHead>
                                    <TableHead class="text-center w-[70px]">Unsigned</TableHead>
                                    <TableHead class="text-center w-[60px]">A/I</TableHead>
                                    <TableHead>Default</TableHead>
                                    <TableHead class="w-[40px]"></TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                <TableRow v-for="(column, index) in form.columns" :key="index">
                                    <TableCell>
                                        <Input v-model="column.name" placeholder="column_name" required class="h-8 text-sm" />
                                    </TableCell>
                                    <TableCell>
                                        <Select 
                                            :model-value="column.type.name" 
                                            @update:model-value="(val) => handleTypeChange(index, val)"
                                        >
                                            <SelectTrigger class="h-8 text-sm">
                                                <SelectValue placeholder="Type" />
                                            </SelectTrigger>
                                            <SelectContent>
                                                <SelectGroup v-for="(types, category) in db.types" :key="category">
                                                    <SelectLabel class="text-xs font-bold text-slate-500">{{ category }}</SelectLabel>
                                                    <SelectItem v-for="type in types" :key="type.name" :value="type.name" class="text-sm">
                                                        {{ type.name }}
                                                    </SelectItem>
                                                </SelectGroup>
                                            </SelectContent>
                                        </Select>
                                    </TableCell>
                                    <TableCell>
                                        <div v-if="column.type.category === 'Numbers' && (column.type.name === 'decimal' || column.type.name === 'float' || column.type.name === 'double')" class="flex gap-1">
                                            <Input v-model="column.precision" type="number" placeholder="P" class="h-8 text-xs w-1/2" title="Precision" />
                                            <Input v-model="column.scale" type="number" placeholder="S" class="h-8 text-xs w-1/2" title="Scale" />
                                        </div>
                                        <div v-else-if="column.type.category !== 'Date and Time' && column.type.name !== 'text' && column.type.name !== 'longtext'">
                                            <Input v-model="column.length" type="number" placeholder="Length" class="h-8 text-sm" />
                                        </div>
                                        <div v-else class="text-xs text-slate-400 text-center">-</div>
                                    </TableCell>
                                    <TableCell>
                                        <Select v-model="column.index">
                                            <SelectTrigger class="h-8 text-xs">
                                                <SelectValue placeholder="None" />
                                            </SelectTrigger>
                                            <SelectContent>
                                                <SelectItem value="PRIMARY" class="text-xs">PRIMARY</SelectItem>
                                                <SelectItem value="UNIQUE" class="text-xs">UNIQUE</SelectItem>
                                                <SelectItem value="INDEX" class="text-xs">INDEX</SelectItem>
                                                <SelectItem :value="null" class="text-xs">None</SelectItem>
                                            </SelectContent>
                                        </Select>
                                    </TableCell>
                                    <TableCell class="text-center">
                                        <Checkbox v-model:checked="column.notnull" />
                                    </TableCell>
                                    <TableCell class="text-center">
                                        <Checkbox v-model:checked="column.unsigned" />
                                    </TableCell>
                                    <TableCell class="text-center">
                                        <Checkbox v-model:checked="column.autoincrement" />
                                    </TableCell>
                                    <TableCell>
                                        <Input v-model="column.default" placeholder="NULL" class="h-8 text-sm" />
                                    </TableCell>
                                    <TableCell>
                                        <Button type="button" variant="ghost" size="icon" @click="removeColumn(index)" class="h-8 w-8 text-red-500 hover:text-red-700 hover:bg-red-50">
                                            <Trash2 class="w-4 h-4" />
                                        </Button>
                                    </TableCell>
                                </TableRow>
                            </TableBody>
                        </Table>
                    </div>

                    <div class="flex justify-end gap-3 pt-4 border-t">
                        <Button type="button" variant="ghost" as-child>
                            <Link :href="route('admin.database-manager.index')">Cancel</Link>
                        </Button>
                        <Button type="submit" :disabled="form.processing">
                            {{ db.action === 'update' ? 'Update Table' : 'Create Table' }}
                        </Button>
                    </div>
                </div>
            </form>
        </MasterLayout>
    </AppLayout>
</template>
