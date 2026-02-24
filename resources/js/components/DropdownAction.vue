<template>
  <DropdownMenu>
    <DropdownMenuTrigger as-child>
      <Button variant="ghost">⋮</Button>
    </DropdownMenuTrigger>
    <DropdownMenuContent>
      <DropdownMenuItem 
        v-for="action in visibleActions" 
        :key="action.key"
        @click="handleAction(action.key)"
      >
        {{ action.label }}
      </DropdownMenuItem>
    </DropdownMenuContent>
  </DropdownMenu>
</template>

<script setup>
import { computed } from 'vue'
import {
  DropdownMenu,
  DropdownMenuTrigger,
  DropdownMenuContent,
  DropdownMenuItem,
} from '@/components/ui/dropdown-menu'
import { Button } from '@/components/ui/button'

const props = defineProps({
  rowData: Object,
  actions: {
    type: Array,
    default: () => []
  }
})

const emit = defineEmits(['edits', 'delete', 'previews', 'realisasi', 'bkuPajak', 'bku', 'downloads'])

// Filter actions that should be visible
const visibleActions = computed(() => {
  return props.actions.filter(action => action.visible !== false)
})

function handleAction(actionKey) {
  const action = props.actions.find(a => a.key === actionKey)
  if (!action) return

  const emitEvent = action.emit || actionKey
  
  // Determine what data to emit based on the action
  if (actionKey === 'edits' || actionKey === 'delete' || actionKey === 'downloads') {
    emit(emitEvent, props.rowData?.id)
  } else {
    emit(emitEvent, props.rowData)
  }
}
</script>