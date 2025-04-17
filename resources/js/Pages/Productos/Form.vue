<template>
    <SidebarLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ producto ? 'Editar Producto' : 'Nuevo Producto' }}
                </h2>
            </div>
        </template>

        <div class="py-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="bg-white rounded-lg shadow p-6">
                    <form @submit.prevent="submit">
                        <!-- Nombre -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Nombre</label>
                            <input 
                                type="text"
                                v-model="form.nombre"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                required
                            >
                        </div>

                        <!-- Precio -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Precio</label>
                            <div class="relative mt-1 rounded-md shadow-sm">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <span class="text-gray-500 sm:text-sm">€</span>
                                </div>
                                <input 
                                    type="number"
                                    v-model="form.precio"
                                    step="0.01"
                                    min="0"
                                    class="block w-full rounded-md border-gray-300 pl-7 pr-12 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                    required
                                >
                            </div>
                        </div>

                        <div class="flex justify-end space-x-2">
                            <Link 
                                href="/productos"
                                class="px-4 py-2 border rounded-md hover:bg-gray-100"
                            >
                                Cancelar
                            </Link>
                            <button 
                                type="submit"
                                class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700"
                            >
                                {{ producto ? 'Actualizar' : 'Crear' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </SidebarLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import SidebarLayout from '@/Layouts/SidebarLayout.vue';

const props = defineProps({
    producto: Object
});

const form = ref({
    nombre: props.producto?.nombre || '',
    precio: props.producto?.precio || '',
});

const submit = () => {
    if (props.producto) {
        router.put(`/productos/${props.producto.id}`, form.value);
    } else {
        router.post('/productos', form.value);
    }
};
</script> 