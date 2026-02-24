# DropdownAction Component - Dynamic Actions Guide

## Overview
The `DropdownAction` component has been refactored to support dynamic actions based on the parent component's requirements. This allows different pages to show different action menus in the DataTable dropdown.

## How It Works

### 1. DropdownAction Component
The component now accepts an `actions` prop that defines which menu items should appear:

```vue
<DropdownAction 
  :rowData="rowData"
  :actions="customActions"
  @edits="handleEdit"
  @delete="handleDelete"
  @previews="handlePreview"
/>
```

### 2. Action Configuration Format

Each action is an object with the following properties:

```typescript
{
  key: string,        // Unique identifier for the action
  label: string,      // Display text in the dropdown
  emit: string,       // Event name to emit (optional, defaults to key)
  visible: boolean    // Whether to show this action (optional, defaults to true)
}
```

### 3. Default Actions

**Important:** The component now requires you to explicitly define the actions you need. If no `actions` prop is provided, the dropdown will be empty.

This design ensures that each parent component is intentional about which actions to display, preventing unnecessary or irrelevant actions from appearing.

```javascript
// You MUST define actions explicitly
const dropdownActions = [
  { key: 'edits', label: 'Edit', emit: 'edits' },
  { key: 'delete', label: 'Delete', emit: 'delete' },
];
```

## Usage Examples

### Example 1: Simple Edit/Delete Only (Transaksi GU)

```vue
<script setup>
// Define only the actions you need
const dropdownActions = [
  { key: 'edits', label: 'Edit', emit: 'edits' },
  { key: 'delete', label: 'Delete', emit: 'delete' },
];
</script>

<template>
  <DataTable
    :data="items"
    :fieldsFromDB="fields"
    :dropdownActions="dropdownActions"
    @edits="handleEdit"
    @delete="handleDelete"
  />
</template>
```

### Example 2: Full Actions (Transaksi LS)

```vue
<script setup>
const dropdownActions = [
  { key: 'previews', label: 'Preview', emit: 'previews' },
  { key: 'bku', label: 'BKU', emit: 'bku' },
  { key: 'bkuPajak', label: 'BKU Pajak', emit: 'bkuPajak' },
  { key: 'realisasi', label: 'Realisasi', emit: 'realisasi' },
  { key: 'edits', label: 'Edit', emit: 'edits' },
  { key: 'delete', label: 'Delete', emit: 'delete' },
];
</script>

<template>
  <DataTable
    :data="items"
    :fieldsFromDB="fields"
    :dropdownActions="dropdownActions"
    @previews="handlePreview"
    @bku="handleBku"
    @bkuPajak="handleBkuPajak"
    @realisasi="handleRealisasi"
    @edits="handleEdit"
    @delete="handleDelete"
  />
</template>
```

### Example 3: Conditional Actions

```vue
<script setup>
import { computed } from 'vue';

const userRole = ref('admin');

// Show different actions based on user role
const dropdownActions = computed(() => [
  { key: 'previews', label: 'Preview', emit: 'previews' },
  { key: 'edits', label: 'Edit', emit: 'edits', visible: userRole.value === 'admin' },
  { key: 'delete', label: 'Delete', emit: 'delete', visible: userRole.value === 'admin' },
]);
</script>
```

### Example 4: Custom Action Names

```vue
<script setup>
const dropdownActions = [
  { key: 'view', label: 'Lihat Detail', emit: 'previews' },
  { key: 'edit', label: 'Ubah Data', emit: 'edits' },
  { key: 'remove', label: 'Hapus', emit: 'delete' },
  { key: 'download', label: 'Unduh PDF', emit: 'downloads' },
];
</script>
```

## Migration Guide

### Before (Hardcoded Actions)
```vue
<!-- All pages showed all actions -->
<DataTable
  :data="items"
  :fieldsFromDB="fields"
  @edits="handleEdit"
  @delete="handleDelete"
/>
```

### After (Dynamic Actions)
```vue
<script setup>
// Define only what you need
const dropdownActions = [
  { key: 'edits', label: 'Edit' },
  { key: 'delete', label: 'Delete' },
];
</script>

<template>
  <DataTable
    :data="items"
    :fieldsFromDB="fields"
    :dropdownActions="dropdownActions"
    @edits="handleEdit"
    @delete="handleDelete"
  />
</template>
```

## Available Action Keys

The following action keys are supported by default:

- `previews` - Emits full row data
- `bku` - Emits full row data
- `bkuPajak` - Emits full row data
- `realisasi` - Emits full row data
- `edits` - Emits row ID only
- `delete` - Emits row ID only
- `downloads` - Emits row ID only

## Notes

1. **Required Configuration**: You **MUST** pass the `dropdownActions` prop to define which actions should appear. If not provided, the dropdown will be empty.

2. **Event Handling**: Make sure to add event listeners (`@edits`, `@delete`, etc.) for all actions you include in your `dropdownActions` array.

3. **Data Emission**: 
   - Actions like `edits`, `delete`, and `downloads` emit only the row ID
   - Other actions emit the full row data object

4. **Visibility Control**: Use the `visible` property to conditionally show/hide actions based on permissions, roles, or data state.

