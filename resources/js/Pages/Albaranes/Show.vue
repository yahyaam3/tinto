<template>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <!-- Encabezado -->
                <div class="flex justify-between items-start mb-8">
                    <div>
                        <h2 class="text-2xl font-semibold text-gray-800">Albarán {{ albaran.numero }}</h2>
                        <p class="text-gray-600">Fecha: {{ formatDate(albaran.fecha) }}</p>
                    </div>
                    <div class="flex space-x-3">
                        <Link :href="route('albaranes.pdf', albaran.id)" 
                              class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded inline-flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                            PDF Completo
                        </Link>
                        <Link :href="route('albaranes.ticket', albaran.id)" 
                              class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded inline-flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                            </svg>
                            Ticket
                        </Link>
                        <Link :href="route('albaranes.edit', albaran.id)" 
                              class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                            Editar
                        </Link>
                    </div>
                </div>

                <!-- Estado -->
                <div class="mb-8">
                    <span :class="{
                        'px-3 py-1 text-sm font-semibold rounded-full': true,
                        'bg-yellow-100 text-yellow-800': albaran.estado === 'pendiente',
                        'bg-blue-100 text-blue-800': albaran.estado === 'en_proceso',
                        'bg-green-100 text-green-800': albaran.estado === 'completado',
                        'bg-purple-100 text-purple-800': albaran.estado === 'entregado'
                    }">
                        {{ formatEstado(albaran.estado) }}
                    </span>
                </div>

                <!-- Información del Cliente -->
                <div class="bg-gray-50 p-4 rounded-lg mb-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Información del Cliente</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Nombre</p>
                            <p class="text-gray-900">{{ albaran.cliente.nombre }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Teléfono</p>
                            <p class="text-gray-900">{{ albaran.cliente.telefono }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Email</p>
                            <p class="text-gray-900">{{ albaran.cliente.email }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Dirección</p>
                            <p class="text-gray-900">{{ albaran.cliente.direccion }}</p>
                        </div>
                    </div>
                </div>

                <!-- Productos -->
                <div class="mb-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Productos</h3>
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
                            <tr v-for="producto in albaran.productos" :key="producto.id">
                                <td class="px-6 py-4 whitespace-nowrap">{{ producto.nombre }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ producto.pivot.cantidad }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ formatPrice(producto.precio) }}€</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ formatPrice(producto.precio * producto.pivot.cantidad) }}€</td>
                            </tr>
                        </tbody>
                        <tfoot class="bg-gray-50">
                            <tr>
                                <td colspan="3" class="px-6 py-4 text-right font-medium">Total:</td>
                                <td class="px-6 py-4 whitespace-nowrap font-bold">{{ formatPrice(albaran.total) }}€</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- Observaciones -->
                <div v-if="albaran.observaciones" class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Observaciones</h3>
                    <p class="text-gray-700 whitespace-pre-line">{{ albaran.observaciones }}</p>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    albaran: {
        type: Object,
        required: true
    }
});

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('es-ES');
};

const formatPrice = (price) => {
    return new Intl.NumberFormat('es-ES', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    }).format(price);
};

const formatEstado = (estado) => {
    const estados = {
        pendiente: 'Pendiente',
        en_proceso: 'En Proceso',
        completado: 'Completado',
        entregado: 'Entregado'
    };
    return estados[estado] || estado;
};
</script> 