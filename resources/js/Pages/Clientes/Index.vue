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

                <!-- Tabla Responsive -->
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <ResponsiveTable 
                        :columns="columns" 
                        :items="clientes" 
                        :hasActions="true"
                    >
                        <!-- Plantilla para email con valor por defecto -->
                        <template #email="{ item: cliente }">
                            {{ cliente.email || '-' }}
                        </template>
                        
                        <!-- Plantilla para dirección con valor por defecto -->
                        <template #nif="{ item: cliente }">
                            {{ cliente.nif || '-' }}
                        </template>
                        
                        <template #direccion="{ item: cliente }">
                            {{ cliente.direccion || '-' }}
                        </template>
                        
                        <!-- Plantilla para estado con estilo condicional -->
                        <template #estado="{ item: cliente }">
                            <span 
                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                :class="{
                                    'bg-green-100 text-green-800': cliente.estado === 'activo',
                                    'bg-red-100 text-red-800': cliente.estado === 'inactivo'
                                }"
                            >
                                {{ cliente.estado }}
                            </span>
                        </template>
                        
                        <!-- Plantilla para las acciones -->
                        <template #actions="{ item: cliente }">
                            <!-- En vista móvil, los botones son más grandes y más fáciles de tocar -->
                            <Link 
                                :href="`/clientes/${cliente.id}/edit`"
                                class="md:text-indigo-600 md:hover:text-indigo-900 md:mr-3 px-3 py-2 md:p-0 bg-indigo-100 md:bg-transparent text-indigo-700 rounded-md inline-flex items-center"
                            >
                                <svg class="md:hidden w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                Editar
                            </Link>
                            
                            <button
                                @click="eliminarCliente(cliente)"
                                class="md:text-red-600 md:hover:text-red-900 px-3 py-2 md:p-0 bg-red-100 md:bg-transparent text-red-700 rounded-md inline-flex items-center mt-2 md:mt-0"
                            >
                                <svg class="md:hidden w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                Eliminar
                            </button>
                        </template>
                    </ResponsiveTable>
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
import ResponsiveTable from '@/Components/ResponsiveTable.vue';

const props = defineProps({
    clientes: Object,
    filters: Object
});

const search = ref(props.filters.search || '');
const estado = ref(props.filters.estado || 'todos');

// Definición de columnas para la tabla responsiva
const columns = [
    { key: 'nombre', label: 'Nombre' },
    { key: 'email', label: 'Email' },
    { key: 'telefono', label: 'Teléfono' },
    { key: 'nif', label: 'NIF' },
    { key: 'direccion', label: 'Dirección' },
    { key: 'estado', label: 'Estado' }
];

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