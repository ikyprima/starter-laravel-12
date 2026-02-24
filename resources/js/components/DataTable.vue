<!-- components/DataTable.vue -->
<script setup lang="ts">
import type {
  ColumnDef,
  ColumnFiltersState,
  ExpandedState,
  SortingState,
  VisibilityState,
} from '@tanstack/vue-table'
import {
  getCoreRowModel,
  getExpandedRowModel,
  getFilteredRowModel,
  getPaginationRowModel,
  getSortedRowModel,
  useVueTable,
  FlexRender
} from '@tanstack/vue-table'

import { h, ref, watchEffect, computed } from 'vue'

// ... existing imports


import { valueUpdater } from '@/components/ui/table/utils'
import { Button } from '@/components/ui/button'
import { Checkbox } from '@/components/ui/checkbox'
import DropdownAction from '@/components/DropdownAction.vue'
import {
  DropdownMenu,
  DropdownMenuCheckboxItem,
  DropdownMenuContent,
  DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu'
import { Input } from '@/components/ui/input'
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table'
import { ArrowUpDown, ChevronDown, Loader2 } from 'lucide-vue-next'

import {
  Pagination,
  PaginationContent,
  PaginationEllipsis,
  PaginationItem,
  PaginationNext,
  PaginationPrevious,
  PaginationFirst,
  PaginationLast
} from '@/components/ui/pagination'
import {
  Tooltip,
  TooltipContent,
  TooltipProvider,
  TooltipTrigger,
} from '@/components/ui/tooltip'

const props = defineProps<{
  data: any[],
  fieldsFromDB: any[],
  currentPage: number,
  totalItems: number,
  perPage: number,
  paginationLinks : any[]
  firstPageUrl: string,
  lastPageUrl: string,
  buttonTambah?: any[]
  buttonDinamis?: readonly DynamicButton[]
  dropdownActions?: any[]
//   columns: ColumnDef<any>[]
}>()

// Extend TanStack Table types
import '@tanstack/vue-table'
declare module '@tanstack/vue-table' {
  interface ColumnMeta<TData, TValue> {
    width?: string
  }
}

interface ColumnMeta {
  width?: string
}


// Gunakan computed untuk memastikan reaktivitas actions
const resolvedActions = computed(() => props.dropdownActions || [])

export interface DynamicButton {
  label: string
  variant?: 'default' | 'destructive' | 'outline' | 'secondary' | 'ghost' | 'link' | 'primary' | null
  value?: string
  onClick?: string
  loading?: boolean
}


const dynamicColumns = computed<ColumnDef<any>[]>(() => {
  return props.fieldsFromDB.map(field => {
    // Shared column building logic
    const buildColumn = (cellRenderer: (context: any) => any): ColumnDef<any> => ({
      accessorKey: field.key,
      id: field.key,
      header: field.sortable
        ? ({ column }: { column: any }) =>
            h(Button, {
              variant: 'ghost',
              onClick: () => column.toggleSorting(column.getIsSorted() === 'asc'),
            }, () => [field.label, h(ArrowUpDown, { class: 'ml-2 h-4 w-4' })])
        : () => h('div', { class: 'text-left font-medium text-xs px-2' }, field.label),
      cell: cellRenderer,
      enableSorting: field.sortable,
      meta: {
        width: field.width
      }
    });

    // Case 1: Special Type 'two-line'
    if (field.type === 'two-line') {
      return buildColumn(({ row }: { row: any }) => {
        const item = row.original;
        const getValue = (keys: any) => {
          if (!keys) return null;
          const keyList = Array.isArray(keys) ? keys : [keys];
          for (const k of keyList) {
            if (item[k] !== undefined && item[k] !== null && item[k] !== '') return item[k];
          }
          return null;
        };

        const top = getValue(field.topKey) || '-';
        const bottom = getValue(field.bottomKey) || '-';

        return h('div', { class: 'flex flex-col min-w-0' }, [
          h('span', { class: 'text-[10px] font-mono leading-tight truncate' }, String(top)),
          h('span', { class: 'text-[10px] font-mono text-muted-foreground leading-tight truncate' }, String(bottom))
        ]);
      });
    }

    // Case 2: Special Keys
    switch (field.key) {
      case 'select':
        return {
          id: 'select',
          header: ({ table }) =>
            h(Checkbox, {
              modelValue: table.getIsAllPageRowsSelected() || (table.getIsSomePageRowsSelected() && 'indeterminate'),
              'onUpdate:modelValue': value => table.toggleAllPageRowsSelected(!!value),
            }),
          cell: ({ row }) =>
            h(Checkbox, {
              modelValue: row.getIsSelected(),
              'onUpdate:modelValue': value => row.toggleSelected(!!value),
            }),
          enableSorting: false,
          enableHiding: false,
        }
      case 'status':
        return buildColumn(({ row }: { row: any }) => h('div', { class: 'capitalize' }, row.getValue(field.key)));

      case 'email':
        return buildColumn(({ row }: { row: any }) => h('div', { class: 'lowercase' }, row.getValue(field.key)));

      case 'amount':
        return {
          accessorKey: field.key,
          id: field.key,
          header: field.sortable
            ? ({ column }: { column: any }) =>
              h(Button, {
                variant: 'ghost',
                onClick: () => column.toggleSorting(column.getIsSorted() === 'asc'),
              }, () => [field.label, h(ArrowUpDown, { class: 'ml-2 h-4 w-4 text-right' })])
            : () => h('div', { class: 'text-right' }, field.label),

          cell: ({ row }: { row: any }) => {
            const amount = Number(row.getValue(field.key))
            const formatted = new Intl.NumberFormat('en-US', {
              style: 'currency',
              currency: 'USD',
            }).format(amount)
            return h('div', { class: 'text-right font-medium' }, formatted)
          },
          enableSorting: field.sortable,
        }

      case 'keterangan_dokumen':
        return buildColumn(({ row }: { row: any }) => {
          const item = row.original;
          const getValue = (keys: any) => {
            if (!keys) return null;
            const keyList = Array.isArray(keys) ? keys : [keys];
            for (const k of keyList) {
              if (item[k]) return item[k];
            }
            return null;
          };

          const nomor = getValue(field.topKey) || getValue(['nomor_dokumen', 'nomor_tbp', 'nomor_sp2d']) || '-';
          const uraian = getValue(field.bottomKey) || getValue(['uraian_belanja', 'uraian']) || '-';

          return h('div', { class: 'flex flex-col gap-1.5' }, [
            h('span', { class: 'font-bold text-sm tracking-tight whitespace-nowrap' }, nomor),
            h('div', { class: 'max-w-[400px]' }, [
              h(TooltipProvider, {}, {
                default: () => h(Tooltip, { delayDuration: 200 }, {
                  default: () => [
                    h(TooltipTrigger, { asChild: true }, {
                      default: () => h('span', {
                        class: 'truncate text-xs text-muted-foreground line-clamp-2 cursor-help leading-relaxed block overflow-hidden'
                      }, uraian)
                    }),
                    h(TooltipContent, {
                      side: 'bottom',
                      class: 'max-w-[400px] p-3 text-xs bg-popover text-popover-foreground shadow-md border'
                    }, {
                      default: () => h('div', { class: 'flex flex-col gap-1' }, [
                        h('p', { class: 'font-bold border-b pb-1 mb-1' }, nomor),
                        h('p', {}, uraian)
                      ])
                    })
                  ]
                })
              })
            ])
          ])
        });

      default:
        return buildColumn(({ row }: { row: any }) => {
          const value = row.getValue(field.key) as string
          if (!value) return h('div', {}, '-')

          return h(TooltipProvider, {}, {
            default: () => h(Tooltip, { delayDuration: 300 }, {
              default: () => [
                h(TooltipTrigger, { asChild: true }, {
                  default: () => h('div', {
                    class: 'break-words max-w-[300px] whitespace-normal line-clamp-2 cursor-help text-sm'
                  }, value)
                }),
                h(TooltipContent, { class: 'max-w-[300px] break-words' }, {
                  default: () => value
                })
              ]
            })
          })
        });
    }
  })
});

const columns = computed<ColumnDef<any, ColumnMeta>[]>(() => [
  ...dynamicColumns.value,
  {
    id: 'actions',
    cell: ({ row }) => {
      return h(DropdownAction, {
        rowData: row.original,
        actions: props.dropdownActions || [],
        onExpand: row.toggleExpanded,
        onEdits: (id: string) => handleEdit(id),
        onDelete: (id: string) => handleDelete(id),
        onPreviews: (id: Object) => handePreview(id),
        onRealisasi: (data: any) => handleRealisasi(data),
        onBkuPajak: (data: any) => handleBkuPajak(data),
        onBku: (data: any) => handleBku(data),
      })
    },
  },
])

const sorting = ref<SortingState>([])
const columnFilters = ref<ColumnFiltersState>([])
const columnVisibility = ref<VisibilityState>({})
const rowSelection = ref({})
const expanded = ref<ExpandedState>({}) // Keep expanded ref if it's used elsewhere, but remove from table config
const searchQuery = ref('')

const table = useVueTable({
  data: props.data,
  get columns() { return columns.value },
  getCoreRowModel: getCoreRowModel(),
  getPaginationRowModel: getPaginationRowModel(),
  getSortedRowModel: getSortedRowModel(),
  getFilteredRowModel: getFilteredRowModel(),
  // getExpandedRowModel: getExpandedRowModel(), // Removed as per instruction
  onSortingChange: updaterOrValue => valueUpdater(updaterOrValue, sorting),
  onColumnFiltersChange: updaterOrValue => valueUpdater(updaterOrValue, columnFilters),
  onColumnVisibilityChange: updaterOrValue => valueUpdater(updaterOrValue, columnVisibility),
  onRowSelectionChange: updaterOrValue => valueUpdater(updaterOrValue, rowSelection),
  // onExpandedChange: updaterOrValue => valueUpdater(updaterOrValue, expanded), // Removed as per instruction
  state: {
    get sorting() { return sorting.value },
    get columnFilters() { return columnFilters.value },
    get columnVisibility() { return columnVisibility.value },
    get rowSelection() { return rowSelection.value },
    // get expanded() { return expanded.value }, // Removed as per instruction
  },
})

const isEllipsis = (label: string) => {
  return label.includes('...')
}

const emit = defineEmits(['previews','downloads','edits', 'delete','clickPaging','search','button-click', 'realisasi', 'bkuPajak', 'bku'])
function handePreview(id: Object) {
  emit('previews', id) 
}

function handleRealisasi(data: any) {
  emit('realisasi', data)
}

function handleBkuPajak(data: any) {
  emit('bkuPajak', data)
}

function handleBku(data: any) {
  emit('bku', data)
}


function handleClick(handlerName?: string, value?: any) {
  if (!handlerName) return
  emit("button-click",{
    action: handlerName,
    value
  } )
}
function handleEdit(id: string) {
   emit('edits', id) 
  // console.log('Edit item with ID:', id)
  // Navigasi, buka dialog, dsb
}

function handleDelete(id: string) {
  emit('delete', id)
}
function handlePaging(link: string) {
  emit('clickPaging', link)  
}
function handleSearch() {
  emit('search', searchQuery.value) // Emit ke parent (jika diperlukan)
  table.setGlobalFilter?.(searchQuery.value) // Jika pakai filtering global TanStack
}

watchEffect(() => {
  if (Array.isArray(props.data)) {
    table.setOptions((prev) => ({
      ...prev,
      data: props.data,
    }))
  }
})
// function handleClick(handlerName?: string) {
//   if (!handlerName) return

//   const handlers: Record<string, () => void> = {
//     handleTambah: () => console.log('Tambah diklik'),
//     handleHapus: () => console.log('Hapus diklik'),
//   }

//   if (handlers[handlerName]) {
//     handlers[handlerName]()
//   } else {
//     console.warn(`Handler ${handlerName} tidak ditemukan.`)
//   }
// }

</script>

<template>
  <div class="w-full">
    <div class="flex items-center py-4">
      <Input
        class="max-w-sm"
        placeholder="Pencarian ..."
        v-model="searchQuery"
        @keyup.enter="handleSearch"
      />
        <DropdownMenu>
        <DropdownMenuTrigger as-child>
          <Button variant="outline" class="ml-auto">
            Columns <ChevronDown class="ml-2 h-4 w-4" />
          </Button>
        </DropdownMenuTrigger>
        <DropdownMenuContent align="end">
          <DropdownMenuCheckboxItem
            v-for="column in table.getAllColumns().filter((column) => column.getCanHide())"
            :key="column.id"
            class="capitalize"
            :model-value="column.getIsVisible()"
            @update:model-value="(value) => column.toggleVisibility(!!value)"
          >
            {{ column.id }}
          </DropdownMenuCheckboxItem>
        </DropdownMenuContent>
      </DropdownMenu>

      <Button class="gap-2 ml-2" v-if="props.buttonTambah && props.buttonTambah?.length > 0" >Tambah</Button>
      <div v-if="props.buttonDinamis">
        <Button
          class="gap-2 ml-2"
            v-for="(btn, idx) in props.buttonDinamis"
            :key="idx"
            :variant="btn.variant === 'primary' ? 'default' : (btn.variant as any)"
            :disabled="btn.loading"
            @click="handleClick(btn.onClick, btn.value??'')"
          >
          <Loader2 v-if="btn.loading" class="h-4 w-4 animate-spin" />
          {{ btn.label }}
        </Button>
      </div>
    
    </div>
    <div class="rounded-md border w-full overflow-x-auto">
      <Table class="">
        <TableHeader>
          <TableRow v-for="headerGroup in table.getHeaderGroups()" :key="headerGroup.id">
            <TableHead 
              v-for="header in headerGroup.headers" 
              :key="header.id"
              :style="{ width: (header.column.columnDef.meta as any)?.width }"
            >
              <FlexRender v-if="!header.isPlaceholder" :render="header.column.columnDef.header" :props="header.getContext()" />
            </TableHead>
          </TableRow>
        </TableHeader>
        <TableBody>
          <template v-if="table.getRowModel().rows?.length">
            <template v-for="row in table.getRowModel().rows" :key="row.id">
              <TableRow :data-state="row.getIsSelected() && 'selected'">
                <TableCell v-for="cell in row.getVisibleCells()" :key="cell.id"  >
                  <FlexRender :render="cell.column.columnDef.cell" :props="cell.getContext()" />
                </TableCell>
              </TableRow>
              <TableRow v-if="row.getIsExpanded()">
                <TableCell :colspan="row.getAllCells().length">
                  {{ JSON.stringify(row.original) }}
                </TableCell>
              </TableRow>
            </template>
          </template>
          <TableRow v-else>
            <TableCell :colspan="columns.length" class="h-24 text-center">
              No results.
            </TableCell>
          </TableRow>
        </TableBody>
      </Table>
    </div>
    <div class="flex items-center justify-end space-x-2 py-4">
      <div class="flex-1 text-sm text-muted-foreground">
        {{ table.getFilteredSelectedRowModel().rows.length }} of
        {{ table.getFilteredRowModel().rows.length }} row(s) selected.
      </div>
      <div class="space-x-2">
        <!-- <Button variant="outline" size="sm" :disabled="!table.getCanPreviousPage()" @click="table.previousPage()">Previous</Button>
        <Button variant="outline" size="sm" :disabled="!table.getCanNextPage()" @click="table.nextPage()">Next</Button> -->
          <Pagination v-slot="{ page }" :items-per-page=props.perPage :total=props.totalItems :default-page=props.currentPage>

            <PaginationContent>
              <PaginationFirst 
                :disabled="props.currentPage === 1 || !props.firstPageUrl"
                @click="handlePaging(props.firstPageUrl)"/>
              <template v-for="(link, index) in props.paginationLinks" :key="index">
                <!-- Previous -->
                <PaginationPrevious
                  v-if="index === 0"
                  :disabled="!link.url"
                  @click="handlePaging(link.url)"
                />
                <!-- Next -->
                <PaginationNext
                  v-else-if="index === props.paginationLinks.length - 1"
                  :disabled="!link.url"
                  @click="handlePaging(link.url)"
                />
                <!-- Ellipsis -->
                <PaginationEllipsis
                  v-else-if="isEllipsis(link.label)"
                />

                <!-- Page Number -->
                <PaginationItem
                  v-else
                  :is-active="link.active"
                  :value=parseInt(link.label)
                  @click="handlePaging(link.url)"
                >
                
                  {{ link.label }}
                </PaginationItem>
              </template>
              <PaginationLast @click="handlePaging(props.lastPageUrl)"/> 
            </PaginationContent>
          </Pagination>
      </div>
    </div>
  </div>
</template>
