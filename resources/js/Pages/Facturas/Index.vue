<template>
    <SidebarLayout>
        <div class="p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-semibold text-gray-900">Facturas</h2>
                <Link
                    href="/facturas/create"
                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors"
                >
                    Nueva Factura
                </Link>
            </div>

            <div class="bg-white rounded-lg shadow">
                <!-- Filtros -->
                <div class="p-6 border-b border-gray-200">
                    <div class="flex gap-4">
                        <div class="flex-1">
                            <input
                                v-model="search"
                                type="text"
                                placeholder="Buscar por cliente..."
                                class="w-full px-4 py-2 border rounded-md"
                                @input="debouncedSearch"
                            >
                        </div>
                        <select
                            v-model="estado"
                            class="px-4 py-2 border rounded-md"
                            @change="filtrar"
                        >
                            <option value="todos">Todos los estados</option>
                            <option value="pendiente">Pendiente</option>
                            <option value="pagada">Pagada</option>
                        </select>
                    </div>
                </div>

                <!-- Tabla Responsive -->
                <ResponsiveTable 
                    :columns="columns" 
                    :items="facturas" 
                    :hasActions="true"
                >
                    <!-- Plantillas para las celdas personalizadas -->
                    <template #fecha="{ item: factura }">
                        {{ formatDate(factura.fecha) }}
                    </template>
                    
                    <template #total="{ item: factura }">
                        {{ formatPrice(factura.total) }}€
                    </template>
                    
                    <template #estado="{ item: factura }">
                        <span :class="estadoClase(factura.estado)" class="px-2 py-1 text-xs rounded-full">
                            {{ factura.estado }}
                        </span>
                    </template>
                    
                    <!-- Plantilla para las acciones -->
                    <template #actions="{ item: factura }">
                        <!-- En vista móvil, los botones son más grandes y más fáciles de tocar -->
                        <Link 
                            :href="route('facturas.pdf', factura.id)"
                            class="md:text-blue-600 md:hover:text-blue-900 md:mr-3 px-3 py-2 md:p-0 bg-blue-100 md:bg-transparent text-blue-700 rounded-md inline-flex items-center"
                            target="_blank"
                        >
                            <svg class="md:hidden w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            PDF
                        </Link>
                        
                        <Link 
                            :href="route('facturas.edit', factura.id)"
                            class="md:text-indigo-600 md:hover:text-indigo-900 md:mr-3 px-3 py-2 md:p-0 bg-indigo-100 md:bg-transparent text-indigo-700 rounded-md inline-flex items-center mt-2 md:mt-0"
                        >
                            <svg class="md:hidden w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                            Editar
                        </Link>
                        
                        <button 
                            v-if="factura.estado === 'pendiente'" 
                            @click="marcarComoPagada(factura)"
                            class="md:text-green-600 md:hover:text-green-900 md:mr-3 px-3 py-2 md:p-0 bg-green-100 md:bg-transparent text-green-700 rounded-md inline-flex items-center mt-2 md:mt-0"
                        >
                            <svg class="md:hidden w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            Pagada
                        </button>
                        
                        <button
                            @click="eliminarFactura(factura)"
                            class="md:text-red-600 md:hover:text-red-900 px-3 py-2 md:p-0 bg-red-100 md:bg-transparent text-red-700 rounded-md inline-flex items-center mt-2 md:mt-0"
                        >
                            <svg class="md:hidden w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            Eliminar
                        </button>
                    </template>
                </ResponsiveTable>

                <!-- Paginación -->
                <div class="p-6 border-t border-gray-200">
                    <Pagination :links="facturas.links" />
                </div>
            </div>
        </div>
    </SidebarLayout>
</template>

<script>
import { ref, watch } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import SidebarLayout from '@/Layouts/SidebarLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import ResponsiveTable from '@/Components/ResponsiveTable.vue';
import debounce from 'lodash/debounce';

export default {
    components: {
        Link,
        SidebarLayout,
        Pagination,
        ResponsiveTable
    },

    props: {
        facturas: Object,
        filters: Object
    },

    setup(props) {
        const search = ref(props.filters.search || '');
        const estado = ref(props.filters.estado || 'todos');
        
        // Definición de columnas para la tabla responsiva
        const columns = [
            { key: 'numero', label: 'Nº Factura' },
            { key: 'cliente.nombre', label: 'Cliente' },
            { key: 'fecha', label: 'Fecha' },
            { key: 'total', label: 'Total' },
            { key: 'estado', label: 'Estado' }
        ];

        const debouncedSearch = debounce(() => {
            router.get('/facturas', { search: search.value, estado: estado.value }, {
                preserveState: true,
                replace: true
            });
        }, 300);

        const filtrar = () => {
            router.get('/facturas', { search: search.value, estado: estado.value }, {
                preserveState: true,
                replace: true
            });
        };

        const marcarComoPagada = (factura) => {
            if (confirm(`¿Marcar la factura #${factura.numero} como pagada?`)) {
                router.patch(route('facturas.marcarPagada', factura.id), {}, {
                    preserveScroll: true, // Mantiene la posición de scroll
                    onSuccess: () => {
                        // Opcional: podrías mostrar una notificación local si no quieres depender solo del flash
                    },
                    onError: (errors) => {
                        console.error('Error al marcar como pagada:', errors);
                        alert('Hubo un error al marcar la factura como pagada.');
                    }
                });
            }
        };

        const eliminarFactura = (factura) => {
            if (confirm('¿Estás seguro de que deseas eliminar esta factura?')) {
                router.delete(`/facturas/${factura.id}`);
            }
        };

        const formatDate = (date) => {
            return new Date(date).toLocaleDateString();
        };

        const formatPrice = (price) => {
            return Number(price).toFixed(2);
        };

        const estadoClase = (estado) => {
            return {
                'pendiente': 'bg-yellow-100 text-yellow-800',
                'pagada': 'bg-green-100 text-green-800'
            }[estado] || 'bg-gray-100 text-gray-800';
        };

        return {
            search,
            estado,
            columns,
            debouncedSearch,
            filtrar,
            marcarComoPagada,
            eliminarFactura,
            formatDate,
            formatPrice,
            estadoClase
        };
    }
};
</script> 