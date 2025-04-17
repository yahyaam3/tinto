<template>
    <SidebarLayout>
        <div class="py-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-semibold">Clientes</h2>
                    <Link 
                        href="/clientes/create"
                        class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700"
                    >
                        Nuevo Cliente
                    </Link>
                </div>

                <!-- Filtros -->
                <div class="bg-white rounded-lg shadow mb-6 p-4 flex gap-4">
                    <div class="flex-1">
                        <input
                            type="text"
                            v-model="search"
                            placeholder="Buscar clientes..."
                            class="w-full rounded-md border-gray-300"
                        >
                    </div>
                    <div class="w-48">
                        <select
                            v-model="estado"
                            class="w-full rounded-md border-gray-300"
                        >
                            <option value="todos">Todos los estados</option>
                            <option value="activo">Activo</option>
                            <option value="inactivo">Inactivo</option>
                        </select>
                    </div>
                </div>

                <!-- Tabla -->
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Teléfono</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dirección</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="cliente in clientes.data" :key="cliente.id">
                                <td class="px-6 py-4 whitespace-nowrap">{{ cliente.nombre }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ cliente.email || '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ cliente.telefono }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ cliente.direccion || '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span 
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                        :class="{
                                            'bg-green-100 text-green-800': cliente.estado === 'activo',
                                            'bg-red-100 text-red-800': cliente.estado === 'inactivo'
                                        }"
                                    >
                                        {{ cliente.estado }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <Link 
                                        :href="`/clientes/${cliente.id}/edit`"
                                        class="text-indigo-600 hover:text-indigo-900 mr-3"
                                    >
                                        Editar
                                    </Link>
                                    <button
                                        @click="eliminarCliente(cliente)"
                                        class="text-red-600 hover:text-red-900"
                                    >
                                        Eliminar
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="clientes.data.length === 0">
                                <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                    No hay registros para mostrar
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Paginación -->
                <div class="mt-4" v-if="clientes.data.length > 0">
                    <Pagination :links="clientes.links" />
                </div>
            </div>
        </div>
    </SidebarLayout>
</template>

<script setup>
import { ref, watch } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import SidebarLayout from '@/Layouts/SidebarLayout.vue';
import Pagination from '@/Components/Pagination.vue';

const props = defineProps({
    clientes: Object,
    filters: Object
});

const search = ref(props.filters.search || '');
const estado = ref(props.filters.estado || 'todos');

watch([search, estado], ([newSearch, newEstado]) => {
    router.get('/clientes', 
        { 
            search: newSearch, 
            estado: newEstado 
        }, 
        { 
            preserveState: true,
            preserveScroll: true,
            replace: true
        }
    );
});

const eliminarCliente = (cliente) => {
    if (confirm('¿Estás seguro de que deseas eliminar este cliente?')) {
        router.delete(`/clientes/${cliente.id}`);
    }
};
</script> 