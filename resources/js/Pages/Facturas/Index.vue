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

                <!-- Tabla -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Número
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Cliente
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Fecha
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Total
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Estado
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Acciones
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="factura in facturas.data" :key="factura.id">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    {{ factura.numero }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    {{ factura.cliente.nombre }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    {{ formatDate(factura.fecha) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    {{ formatPrice(factura.total) }}€
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span :class="estadoClase(factura.estado)" class="px-2 py-1 text-xs rounded-full">
                                        {{ factura.estado }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <Link 
                                        :href="route('facturas.pdf', factura.id)"
                                        class="text-blue-600 hover:text-blue-900 mr-3"
                                        target="_blank"
                                    >
                                        PDF
                                    </Link>
                                    <Link 
                                        :href="route('facturas.edit', factura.id)"
                                        class="text-indigo-600 hover:text-indigo-900 mr-3"
                                    >
                                        Editar
                                    </Link>
                                    <button 
                                        v-if="factura.estado === 'pendiente'" 
                                        @click="marcarComoPagada(factura)"
                                        class="text-green-600 hover:text-green-900 mr-3"
                                    >
                                        Marcar Pagada
                                    </button>
                                    <button
                                        @click="eliminarFactura(factura)"
                                        class="text-red-600 hover:text-red-900"
                                    >
                                        Eliminar
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

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
import debounce from 'lodash/debounce';

export default {
    components: {
        Link,
        SidebarLayout,
        Pagination
    },

    props: {
        facturas: Object,
        filters: Object
    },

    setup(props) {
        const search = ref(props.filters.search || '');
        const estado = ref(props.filters.estado || 'todos');

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