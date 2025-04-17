<template>
    <SidebarLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ cliente ? 'Editar Cliente' : 'Nuevo Cliente' }}
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

                        <!-- Email (Opcional) -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Email (Opcional)</label>
                            <input 
                                type="email"
                                v-model="form.email"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >
                        </div>

                        <!-- Teléfono -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Teléfono</label>
                            <input 
                                type="text"
                                v-model="form.telefono"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                required
                            >
                        </div>

                        <!-- Dirección (Opcional) -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Dirección </label>
                            <input 
                                type="text"
                                v-model="form.direccion"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >
                        </div>

                        <div class="flex justify-end space-x-2">
                            <Link 
                                href="/clientes"
                                class="px-4 py-2 border rounded-md hover:bg-gray-100"
                            >
                                Cancelar
                            </Link>
                            <button 
                                type="submit"
                                class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700"
                            >
                                {{ cliente ? 'Actualizar' : 'Crear' }}
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
    cliente: {
        type: Object,
        default: null
    },
    errors: {
        type: Object,
        default: () => ({})
    }
});

const form = ref({
    nombre: props.cliente?.nombre || '',
    email: props.cliente?.email || '',
    telefono: props.cliente?.telefono || '',
    direccion: props.cliente?.direccion || '',
    estado: 'activo'
});

const submit = () => {
    if (props.cliente) {
        router.put(`/clientes/${props.cliente.id}`, form.value);
    } else {
        router.post('/clientes', form.value);
    }
};
</script> 