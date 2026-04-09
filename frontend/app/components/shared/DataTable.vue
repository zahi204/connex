<template>
  <div>
    <div v-if="loading">
      <SharedLoadingState :rows="4" />
    </div>
    <div v-else-if="!rows.length">
      <SharedEmptyState :title="emptyMessage" />
    </div>
    <div v-else class="cx-table-wrapper" style="overflow-x:auto;">
      <table class="cx-table">
        <thead>
          <tr>
            <th v-for="col in columns" :key="col.key" :style="col.width ? `width:${col.width}` : ''">
              {{ col.label }}
            </th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="(row, i) in rows"
            :key="row.id ?? i"
            :class="{ 'cx-table-row-clickable': !!onRowClick }"
            @click="onRowClick ? onRowClick(row) : undefined"
          >
            <td v-for="col in columns" :key="col.key">
              <slot :name="`cell-${col.key}`" :row="row" :value="row[col.key]">
                {{ row[col.key] ?? '—' }}
              </slot>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup lang="ts">
defineProps<{
  columns: Array<{ key: string; label: string; width?: string }>
  rows: any[]
  loading?: boolean
  emptyMessage?: string
  onRowClick?: ((row: any) => void) | null
}>()
</script>

<style scoped>
.cx-table-row-clickable { cursor: pointer; }
.cx-table-row-clickable:hover { background: var(--cx-bg-card-hover); }
</style>
