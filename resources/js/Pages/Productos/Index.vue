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

                <!-- Tabla Responsive -->
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <ResponsiveTable 
                        :columns="columns" 
                        :items="productos" 
                        :hasActions="true"
                    >
                        <!-- Plantilla personalizada para precio -->
                        <template #precio="{ item: producto }">
                            {{ formatPrice(producto.precio) }}€
                        </template>
                        
                        <!-- Plantilla para las acciones -->
                        <template #actions="{ item: producto }">
                            <!-- En vista móvil, los botones son más grandes y más fáciles de tocar -->
                            <Link 
                                :href="`/productos/${producto.id}/edit`"
                                class="md:text-indigo-600 md:hover:text-indigo-900 md:mr-3 px-3 py-2 md:p-0 bg-indigo-100 md:bg-transparent text-indigo-700 rounded-md inline-flex items-center"
                            >
                                <svg class="md:hidden w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                Editar
                            </Link>
                            
                            <button
                                @click="eliminarProducto(producto)"
                                class="md:text-red-600 md:hover:text-red-900 px-3 py-2 md:p-0 bg-red-100 md:bg-transparent text-red-700 rounded-md inline-flex items-center mt-2 md:mt-0"
                            >
                                <svg class="md:hidden w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                Eliminar
                            </button>
                        </template>
                    </ResponsiveTable>
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
import ResponsiveTable from '@/Components/ResponsiveTable.vue';

const props = defineProps({
    productos: Object,
    filters: Object
});

const search = ref(props.filters.search || '');

// Definición de columnas para la tabla responsiva
const columns = [
    { key: 'id', label: 'ID' },
    { key: 'nombre', label: 'Nombre' },
    { key: 'precio', label: 'Precio' }
];

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