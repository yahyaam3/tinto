<template>
    <SidebarLayout>
        <div class="py-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-semibold">Albaranes</h2>
                    <Link 
                        href="/albaranes/create"
                        class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700"
                    >
                        Nuevo Albarán
                    </Link>
                </div>

                <!-- Filtros -->
                <div class="bg-white rounded-lg shadow mb-6 p-4 flex gap-4">
                    <div class="flex-1">
                        <input
                            type="text"
                            v-model="search"
                            placeholder="Buscar albaranes..."
                            class="w-full rounded-md border-gray-300"
                        >
                    </div>
                    <div class="w-48">
                        <select
                            v-model="estado"
                            class="w-full rounded-md border-gray-300"
                        >
                            <option value="todos">Todos los estados</option>
                            <option value="pendiente">Pendiente</option>
                            <option value="completado">Completado</option>
                        </select>
                    </div>
                </div>

                <!-- Tabla Responsive -->
                <ResponsiveTable 
                    :columns="columns" 
                    :items="albaranes" 
                    :hasActions="true"
                >
                    <!-- Plantillas para las celdas personalizadas -->
                    <template #productos="{ item: albaran }">
                        <div v-for="producto in albaran.productos" :key="producto.id" class="text-sm">
                            {{ producto.cantidad }}x {{ producto.nombre }} 
                            <span class="text-gray-500">
                                ({{ formatPrice(producto.pivot.precio_unitario) }}€)
                            </span>
                        </div>
                    </template>
                    
                    <template #estado="{ item: albaran }">
                        <span 
                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                            :class="{
                                'bg-yellow-100 text-yellow-800': albaran.estado === 'pendiente',
                                'bg-green-100 text-green-800': albaran.estado === 'completado'
                            }"
                        >
                            {{ albaran.estado }}
                        </span>
                    </template>
                    
                    <template #total="{ item: albaran }">
                        {{ formatPrice(albaran.total) }}€
                    </template>
                    
                    <template #fecha="{ item: albaran }">
                        {{ formatDate(albaran.fecha) }}
                    </template>
                    
                    <!-- Plantilla para las acciones -->
                    <template #actions="{ item: albaran }">
                        <!-- En vista móvil, los botones son más grandes y más fáciles de tocar -->
                        <Link 
                            :href="`/albaranes/${albaran.id}/edit`"
                            class="md:text-indigo-600 md:hover:text-indigo-900 md:mr-3 px-3 py-2 md:p-0 bg-indigo-100 md:bg-transparent text-indigo-700 rounded-md inline-flex items-center"
                        >
                            <svg class="md:hidden w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                            Editar
                        </Link>
                        
                        <button
                            v-if="albaran.estado === 'pendiente'" 
                            @click="marcarComoCompletado(albaran)"
                            class="md:text-green-600 md:hover:text-green-900 md:mr-3 px-3 py-2 md:p-0 bg-green-100 md:bg-transparent text-green-700 rounded-md inline-flex items-center mt-2 md:mt-0"
                        >
                            <svg class="md:hidden w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            Completado
                        </button>
                        
                        <button
                            @click="imprimirTicket(albaran)"
                            class="md:text-gray-600 md:hover:text-gray-900 md:mr-3 px-3 py-2 md:p-0 bg-gray-100 md:bg-transparent text-gray-700 rounded-md inline-flex items-center mt-2 md:mt-0"
                        >
                            <svg class="md:hidden w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                            Ticket
                        </button>
                        
                        <button
                            @click="eliminarAlbaran(albaran)"
                            class="md:text-red-600 md:hover:text-red-900 px-3 py-2 md:p-0 bg-red-100 md:bg-transparent text-red-700 rounded-md inline-flex items-center mt-2 md:mt-0"
                        >
                            <svg class="md:hidden w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            Eliminar
                        </button>
                    </template>
                </ResponsiveTable>

                <!-- Paginación -->
                <div class="mt-4" v-if="albaranes.data.length > 0">
                    <Pagination :links="albaranes.links" />
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
    albaranes: Object,
    filters: Object
});

const search = ref(props.filters.search || '');
const estado = ref(props.filters.estado || 'todos');

// Definición de columnas para la tabla responsiva
const columns = [
    { key: 'numero', label: 'Nº Albarán' },
    { key: 'cliente.nombre', label: 'Cliente' },
    { key: 'fecha', label: 'Fecha' },
    { key: 'productos', label: 'Productos' },
    { key: 'total', label: 'Total' },
    { key: 'estado', label: 'Estado' }
];

watch([search, estado], ([newSearch, newEstado]) => {
    router.get('/albaranes', 
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

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('es-ES');
};

const formatPrice = (price) => {
    return Number(price).toFixed(2);
};

const marcarComoCompletado = (albaran) => {
    if (confirm(`¿Marcar el albarán #${albaran.numero} como completado?`)) {
        router.patch(route('albaranes.marcarCompletado', albaran.id), {}, {
            preserveScroll: true,
            onSuccess: () => { },
            onError: (errors) => {
                console.error('Error al marcar como completado:', errors);
                alert('Hubo un error al marcar el albarán como completado.');
            }
        });
    }
};

const imprimirTicket = (albaran) => {
    window.open(`/albaranes/${albaran.id}/ticket`, '_blank');
};

const eliminarAlbaran = (albaran) => {
    if (confirm('¿Estás seguro de que deseas eliminar este albarán?')) {
        router.delete(`/albaranes/${albaran.id}`);
    }
};
</script> 