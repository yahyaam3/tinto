<template>
    <div class="overflow-x-auto bg-white rounded-lg shadow">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th v-for="column in columns" 
                        :key="column.key"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                    >
                        {{ column.label }}
                    </th>
                    <th v-if="actions" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Acciones
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <tr v-if="!items?.data?.length">
                    <td :colspan="actions ? columns.length + 1 : columns.length" class="px-6 py-4 text-center text-gray-500">
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
                    <td v-if="actions" class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <slot name="actions" :item="item"></slot>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script setup>
const props = defineProps({
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
    actions: {
        type: Boolean,
        default: false
    }
});
</script> 