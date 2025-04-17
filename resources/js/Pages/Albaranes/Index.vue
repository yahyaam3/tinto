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

                <!-- Tabla -->
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nº Albarán</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cliente</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Productos</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="albaran in albaranes.data" :key="albaran.id">
                                <td class="px-6 py-4 whitespace-nowrap font-medium">{{ albaran.numero }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ albaran.cliente.nombre }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ formatDate(albaran.fecha) }}</td>
                                <td class="px-6 py-4">
                                    <div v-for="producto in albaran.productos" :key="producto.id" class="text-sm">
                                        {{ producto.cantidad }}x {{ producto.nombre }} 
                                        <span class="text-gray-500">
                                            ({{ formatPrice(producto.pivot.precio_unitario) }}€)
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap font-medium">
                                    {{ formatPrice(albaran.total) }}€
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span 
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                        :class="{
                                            'bg-yellow-100 text-yellow-800': albaran.estado === 'pendiente',
                                            'bg-green-100 text-green-800': albaran.estado === 'completado'
                                        }"
                                    >
                                        {{ albaran.estado }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <Link 
                                        :href="`/albaranes/${albaran.id}/edit`"
                                        class="text-indigo-600 hover:text-indigo-900 mr-3"
                                    >
                                        Editar
                                    </Link>
                                    <button
                                        v-if="albaran.estado === 'pendiente'" 
                                        @click="marcarComoCompletado(albaran)"
                                        class="text-green-600 hover:text-green-900 mr-3"
                                    >
                                        Marcar Completado
                                    </button>
                                    <button
                                        @click="imprimirTicket(albaran)"
                                        class="text-gray-600 hover:text-gray-900 mr-3"
                                    >
                                        Ticket
                                    </button>
                                    <button
                                        @click="eliminarAlbaran(albaran)"
                                        class="text-red-600 hover:text-red-900"
                                    >
                                        Eliminar
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="albaranes.data.length === 0">
                                <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                    No hay registros para mostrar
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

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

const props = defineProps({
    albaranes: Object,
    filters: Object
});

const search = ref(props.filters.search || '');
const estado = ref(props.filters.estado || 'todos');

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