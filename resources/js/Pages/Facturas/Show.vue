<template>
    <SidebarLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold text-gray-900">
                    Factura {{ factura.numero }}
                </h2>
                <div class="flex gap-2">
                    <Link
                        :href="`/facturas/${factura.id}/pdf`"
                        class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700"
                    >
                        Descargar PDF
                    </Link>
                    <Link
                        :href="`/facturas/${factura.id}/edit`"
                        class="px-4 py-2 bg-yellow-600 text-white rounded-md hover:bg-yellow-700"
                    >
                        Editar
                    </Link>
                </div>
            </div>
        </template>

        <div class="p-6 bg-white rounded-lg shadow">
            <!-- Información de la factura -->
            <div class="grid grid-cols-1 gap-6 mb-6 md:grid-cols-2">
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Información de la Factura</h3>
                    <dl class="grid grid-cols-1 gap-2">
                        <div class="flex justify-between py-2 border-b">
                            <dt class="font-medium text-gray-500">Número:</dt>
                            <dd>{{ factura.numero }}</dd>
                        </div>
                        <div class="flex justify-between py-2 border-b">
                            <dt class="font-medium text-gray-500">Fecha:</dt>
                            <dd>{{ formatDate(factura.fecha) }}</dd>
                        </div>
                        <div class="flex justify-between py-2 border-b">
                            <dt class="font-medium text-gray-500">Estado:</dt>
                            <dd>
                                <span :class="estadoClase(factura.estado)" class="px-2 py-1 text-xs rounded-full">
                                    {{ factura.estado }}
                                </span>
                            </dd>
                        </div>
                    </dl>
                </div>

                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Información del Cliente</h3>
                    <dl class="grid grid-cols-1 gap-2">
                        <div class="flex justify-between py-2 border-b">
                            <dt class="font-medium text-gray-500">Nombre:</dt>
                            <dd>{{ factura.cliente.nombre }}</dd>
                        </div>
                        <div class="flex justify-between py-2 border-b" v-if="factura.cliente.email">
                            <dt class="font-medium text-gray-500">Email:</dt>
                            <dd>{{ factura.cliente.email }}</dd>
                        </div>
                        <div class="flex justify-between py-2 border-b" v-if="factura.cliente.telefono">
                            <dt class="font-medium text-gray-500">Teléfono:</dt>
                            <dd>{{ factura.cliente.telefono }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Productos -->
            <div class="mb-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Productos</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Producto
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Cantidad
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Precio Unitario
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Subtotal
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="producto in factura.productos" :key="producto.id">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    {{ producto.nombre }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    {{ producto.pivot.cantidad }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    {{ formatPrice(producto.pivot.precio_unitario) }}€
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    {{ formatPrice(producto.pivot.subtotal) }}€
                                </td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" class="px-6 py-4 text-right font-medium">
                                    Total:
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap font-bold">
                                    {{ formatPrice(factura.total) }}€
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- Botón Volver -->
            <div class="flex justify-end">
                <Link
                    href="/facturas"
                    class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50"
                >
                    Volver a Facturas
                </Link>
            </div>
        </div>
    </SidebarLayout>
</template>

<script>
import { Link } from '@inertiajs/vue3';
import SidebarLayout from '@/Layouts/SidebarLayout.vue';

export default {
    components: {
        Link,
        SidebarLayout
    },

    props: {
        factura: Object
    },

    setup() {
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
            formatDate,
            formatPrice,
            estadoClase
        };
    }
};
</script> 