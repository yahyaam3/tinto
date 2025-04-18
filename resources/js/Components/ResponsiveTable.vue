<template>
    <div>
        <!-- Vista de escritorio - Tabla completa -->
        <div class="hidden md:block overflow-x-auto bg-white rounded-lg shadow">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th v-for="column in columns" 
                            :key="column.key"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                        >
                            {{ column.label }}
                        </th>
                        <th v-if="hasActions" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Acciones
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <tr v-if="!items?.data?.length">
                        <td :colspan="hasActions ? columns.length + 1 : columns.length" class="px-6 py-4 text-center text-gray-500">
                            No hay registros para mostrar
                        </td>
                    </tr>
                    <tr v-else v-for="item in items.data" :key="item.id">
                        <td v-for="column in columns" 
                            :key="column.key"
                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"
                        >
                            <slot :name="column.key" :item="item">
                                {{ item[column.key] }}
                            </slot>
                        </td>
                        <td v-if="hasActions" class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <slot name="actions" :item="item"></slot>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Vista móvil - Cards -->
        <div class="md:hidden space-y-4">
            <div v-if="!items?.data?.length" class="bg-white rounded-lg shadow p-4 text-center text-gray-500">
                No hay registros para mostrar
            </div>
            <div v-else v-for="item in items.data" :key="item.id" class="bg-white rounded-lg shadow p-4 border-l-4 border-indigo-500">
                <div v-for="column in columns" :key="column.key" class="mb-2">
                    <div class="text-xs text-gray-500 uppercase">{{ column.label }}</div>
                    <div class="text-sm font-medium">
                        <slot :name="column.key" :item="item">
                            {{ item[column.key] }}
                        </slot>
                    </div>
                </div>

                <!-- Acciones en mobile: se muestran como botones grandes y fáciles de tocar -->
                <div v-if="hasActions" class="mt-4 pt-3 border-t flex flex-wrap gap-2">
                    <slot name="actions" :item="item"></slot>
                </div>
            </div>
        </div>

        <!-- Paginación -->
        <div v-if="items.links && items.data.length > 0" class="mt-4">
            <slot name="pagination" :links="items.links">
                <Pagination :links="items.links" />
            </slot>
        </div>
    </div>
</template>

<script setup>
import Pagination from '@/Components/Pagination.vue';

defineProps({
    columns: {
        type: Array,
        required: true,
        validator: (value) => {
            return value.every(column => 
                typeof column === 'object' && 
                'key' in column && 
                'label' in column
            );
        }
    },
    items: {
        type: Object,
        default: () => ({
            data: []
        })
    },
    hasActions: {
        type: Boolean,
        default: false
    }
});
</script>
