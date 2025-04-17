<template>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <h2 class="text-2xl font-semibold text-gray-800 mb-6">
                    {{ albaran ? 'Editar Albarán' : 'Nuevo Albarán' }}
                </h2>

                <form @submit.prevent="submit" class="space-y-6">
                    <!-- Cliente -->
                    <div>
                        <label for="cliente_id" class="block text-sm font-medium text-gray-700">Cliente</label>
                        <select id="cliente_id" 
                                v-model="form.cliente_id"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Seleccionar cliente</option>
                            <option v-for="cliente in clientes" :key="cliente.id" :value="cliente.id">
                                {{ cliente.nombre }}
                            </option>
                        </select>
                        <div v-if="form.errors.cliente_id" class="text-red-500 text-sm mt-1">
                            {{ form.errors.cliente_id }}
                        </div>
                    </div>

                    <!-- Fecha -->
                    <div>
                        <label for="fecha" class="block text-sm font-medium text-gray-700">Fecha</label>
                        <input type="date" 
                               id="fecha" 
                               v-model="form.fecha"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <div v-if="form.errors.fecha" class="text-red-500 text-sm mt-1">
                            {{ form.errors.fecha }}
                        </div>
                    </div>

                    <!-- Estado -->
                    <div>
                        <label for="estado" class="block text-sm font-medium text-gray-700">Estado</label>
                        <select id="estado" 
                                v-model="form.estado"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="pendiente">Pendiente</option>
                            <option value="en_proceso">En Proceso</option>
                            <option value="completado">Completado</option>
                            <option value="entregado">Entregado</option>
                        </select>
                        <div v-if="form.errors.estado" class="text-red-500 text-sm mt-1">
                            {{ form.errors.estado }}
                        </div>
                    </div>

                    <!-- Productos -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Productos</label>
                        <div class="space-y-4">
                            <div v-for="(producto, index) in form.productos" :key="index" class="flex items-center gap-4">
                                <div class="flex-1">
                                    <!-- Toggle entre búsqueda y desplegable -->
                                    <div class="flex items-center mb-2">
                                        <button 
                                            type="button" 
                                            @click="toggleSearchMode(index)"
                                            class="text-sm text-indigo-600 hover:text-indigo-800">
                                            {{ searchMode[index] ? 'Usar lista desplegable' : 'Usar buscador' }}
                                        </button>
                                    </div>

                                    <!-- Buscador con autocompletado -->
                                    <div v-if="searchMode[index]" class="relative">
                                        <input
                                            type="text"
                                            v-model="searchQueries[index]"
                                            @input="() => buscarProductos(index)"
                                            placeholder="Buscar producto por nombre o ID..."
                                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        />
                                        <!-- Resultados de búsqueda -->
                                        <div v-if="searchResults[index].length > 0" 
                                             class="absolute z-10 w-full mt-1 bg-white shadow-lg max-h-60 rounded-md py-1 text-base overflow-auto">
                                            <div v-for="result in searchResults[index]" 
                                                 :key="result.id"
                                                 @click="seleccionarProducto(result, index)"
                                                 class="cursor-pointer hover:bg-gray-100 px-4 py-2">
                                                {{ result.id }} - {{ result.nombre }} - {{ formatPrice(result.precio) }}€
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Selector desplegable -->
                                    <select v-else
                                            v-model="form.productos[index].producto_id"
                                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <option value="">Seleccionar producto</option>
                                        <option v-for="p in productos" :key="p.id" :value="p.id">
                                            {{ p.id }} - {{ p.nombre }} - {{ formatPrice(p.precio) }}€
                                        </option>
                                    </select>
                                </div>
                                <div class="w-32">
                                    <input type="number" 
                                           v-model="producto.cantidad"
                                           min="1"
                                           placeholder="Cantidad"
                                           class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                </div>
                                <button type="button" 
                                        @click="eliminarProducto(index)"
                                        class="text-red-600 hover:text-red-900">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <button type="button" 
                                @click="agregarProducto"
                                class="mt-2 inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            Agregar Producto
                        </button>
                    </div>

                    <!-- Observaciones -->
                    <div>
                        <label for="observaciones" class="block text-sm font-medium text-gray-700">Observaciones</label>
                        <textarea id="observaciones" 
                                  v-model="form.observaciones" 
                                  rows="3" 
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                    </div>

                    <!-- Total -->
                    <div class="border-t pt-4">
                        <div class="text-right">
                            <span class="text-lg font-semibold">Total: {{ formatPrice(calcularTotal()) }}€</span>
                        </div>
                    </div>

                    <!-- Botones -->
                    <div class="flex justify-end space-x-3">
                        <Link href="/albaranes" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                            Cancelar
                        </Link>
                        <button type="submit" 
                                class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                :disabled="form.processing">
                            {{ albaran ? 'Actualizar' : 'Crear' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Link, useForm } from '@inertiajs/vue3';
import { ref, reactive } from 'vue';

const props = defineProps({
    albaran: {
        type: Object,
        default: null
    },
    clientes: {
        type: Array,
        required: true
    },
    productos: {
        type: Array,
        required: true
    }
});

const form = useForm({
    cliente_id: props.albaran?.cliente_id || '',
    fecha: props.albaran?.fecha || new Date().toISOString().split('T')[0],
    estado: props.albaran?.estado || 'pendiente',
    productos: props.albaran?.productos || [],
    observaciones: props.albaran?.observaciones || ''
});

const searchQueries = ref([]);
const searchResults = ref([]);
const searchMode = ref([]);

const buscarProductos = (index) => {
    const query = searchQueries.value[index]?.toLowerCase();
    if (!query) {
        searchResults.value[index] = [];
        return;
    }

    searchResults.value[index] = props.productos.filter(producto => 
        producto.id.toString().includes(query) ||
        producto.nombre.toLowerCase().includes(query)
    ).slice(0, 10); // Limitamos a 10 resultados para mejor rendimiento
};

const seleccionarProducto = (producto, index) => {
    form.productos[index].producto_id = producto.id;
    searchQueries.value[index] = `${producto.id} - ${producto.nombre}`;
    searchResults.value[index] = [];
};

const agregarProducto = () => {
    form.productos.push({
        producto_id: '',
        cantidad: 1
    });
    searchQueries.value.push('');
    searchResults.value.push([]);
    searchMode.value.push(false);
};

const eliminarProducto = (index) => {
    form.productos.splice(index, 1);
    searchQueries.value.splice(index, 1);
    searchResults.value.splice(index, 1);
    searchMode.value.splice(index, 1);
};

const toggleSearchMode = (index) => {
    searchMode.value[index] = !searchMode.value[index];
};

const calcularTotal = () => {
    return form.productos.reduce((total, item) => {
        const producto = props.productos.find(p => p.id === item.producto_id);
        return total + (producto ? producto.precio * item.cantidad : 0);
    }, 0);
};

const formatPrice = (price) => {
    return new Intl.NumberFormat('es-ES', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    }).format(price);
};

const submit = () => {
    if (props.albaran) {
        form.put(`/albaranes/${props.albaran.id}`);
    } else {
        form.post('/albaranes');
    }
};
</script> 