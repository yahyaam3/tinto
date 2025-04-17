<template>
    <SidebarLayout>
        <div class="py-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-semibold">Productos</h2>
                    <Link 
                        href="/productos/create"
                        class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700"
                    >
                        Nuevo Producto
                    </Link>
                </div>

                <!-- Filtros -->
                <div class="bg-white rounded-lg shadow mb-6 p-4">
                    <div class="flex-1">
                        <input
                            type="text"
                            v-model="search"
                            placeholder="Buscar productos..."
                            class="w-full rounded-md border-gray-300"
                        >
                    </div>
                </div>

                <!-- Tabla -->
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Precio</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="producto in productos.data" :key="producto.id">
                                <td class="px-6 py-4 whitespace-nowrap">{{ producto.id }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ producto.nombre }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ formatPrice(producto.precio) }}€</td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <Link 
                                        :href="`/productos/${producto.id}/edit`"
                                        class="text-indigo-600 hover:text-indigo-900 mr-3"
                                    >
                                        Editar
                                    </Link>
                                    <button
                                        @click="eliminarProducto(producto)"
                                        class="text-red-600 hover:text-red-900"
                                    >
                                        Eliminar
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="productos.data.length === 0">
                                <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                                    No hay registros para mostrar
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Paginación -->
                <div class="mt-4" v-if="productos.data.length > 0">
                    <Pagination :links="productos.links" />
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
    productos: Object,
    filters: Object
});

const search = ref(props.filters.search || '');

watch(search, (newSearch) => {
    router.get('/productos', 
        { search: newSearch }, 
        { 
            preserveState: true,
            preserveScroll: true,
            replace: true
        }
    );
});

const formatPrice = (price) => {
    return Number(price).toFixed(2);
};

const eliminarProducto = (producto) => {
    if (confirm('¿Estás seguro de que deseas eliminar este producto?')) {
        router.delete(`/productos/${producto.id}`);
    }
};
</script> 