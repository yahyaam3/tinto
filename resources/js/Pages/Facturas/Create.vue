<template>
    <SidebarLayout>
        <div class="p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-semibold text-gray-900">Nueva Factura</h2>
                <Link
                    href="/facturas"
                    class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 transition-colors"
                >
                    Volver
                </Link>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <form @submit.prevent="submit">
                    <!-- Mensajes de error -->
                    <div v-if="form.errors" class="mb-4">
                        <div v-for="(error, key) in form.errors" :key="key" class="text-red-600">
                            {{ error }}
                        </div>
                    </div>

                    <!-- Datos del Cliente -->
                    <div class="mb-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Datos del Cliente</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Cliente</label>
                                <select
                                    v-model="form.cliente_id"
                                    @change="cargarAlbaranes"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    required
                                >
                                    <option value="">Seleccione un cliente</option>
                                    <option v-for="cliente in clientes" :key="cliente.id" :value="cliente.id">
                                        {{ cliente.nombre }}
                                    </option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Fecha</label>
                                <input
                                    type="date"
                                    v-model="form.fecha"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    required
                                >
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Fecha de Vencimiento</label>
                                <input
                                    type="date"
                                    v-model="form.fecha_vencimiento"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    required
                                >
                            </div>
                        </div>
                    </div>

                    <!-- Albaranes -->
                    <div class="mb-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Albaranes</h3>
                        <div v-if="cargandoAlbaranes" class="text-center py-4">
                            <p class="text-gray-500">Cargando albaranes...</p>
                        </div>
                        <div v-else-if="albaranesSinFacturar.length > 0" class="space-y-4">
                            <div v-for="albaran in albaranesSinFacturar" :key="albaran.id" 
                                class="p-4 border rounded-lg">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <input
                                            type="checkbox"
                                            :value="albaran.id"
                                            v-model="form.albaran_ids"
                                            class="mr-2"
                                        >
                                        <span class="font-medium">Albarán #{{ albaran.numero }}</span>
                                    </div>
                                    <div class="text-gray-600">
                                        Fecha: {{ formatDate(albaran.fecha) }} - Total: {{ formatPrice(albaran.total) }}€
                                    </div>
                                </div>
                                <div class="mt-2 text-sm text-gray-500">
                                    {{ albaran.productos.length }} productos
                                </div>
                                <!-- Lista de productos -->
                                <div class="mt-2 pl-6">
                                    <div v-for="producto in albaran.productos" :key="producto.id" class="text-sm text-gray-600">
                                        - {{ producto.nombre }} ({{ producto.cantidad }} x {{ formatPrice(producto.precio_unitario) }}€)
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-else class="text-gray-500 text-center py-4">
                            {{ form.cliente_id ? 'No hay albaranes sin facturar para este cliente' : 'Seleccione un cliente para ver sus albaranes' }}
                        </div>
                    </div>

                    <!-- Botones -->
                    <div class="flex justify-end gap-4">
                        <Link
                            href="/facturas"
                            class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 transition-colors"
                        >
                            Cancelar
                        </Link>
                        <button
                            type="submit"
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors"
                            :disabled="form.processing || form.albaran_ids.length === 0"
                        >
                            {{ form.processing ? 'Creando...' : 'Crear Factura' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </SidebarLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Link, useForm } from '@inertiajs/vue3';
import SidebarLayout from '@/Layouts/SidebarLayout.vue';

const props = defineProps({
    clientes: Array,
    albaranes: Array
});

const albaranesSinFacturar = ref([]);
const cargandoAlbaranes = ref(false);

const form = useForm({
    cliente_id: '',
    fecha: new Date().toISOString().split('T')[0],
    fecha_vencimiento: new Date(Date.now() + 30 * 24 * 60 * 60 * 1000).toISOString().split('T')[0],
    albaran_ids: []
});

const cargarAlbaranes = async () => {
    if (form.cliente_id) {
        try {
            cargandoAlbaranes.value = true;
            form.albaran_ids = []; // Limpiar albaranes seleccionados
            const response = await fetch(`/api/clientes/${form.cliente_id}/albaranes-sin-facturar`);
            if (!response.ok) {
                throw new Error('Error al cargar los albaranes');
            }
            const data = await response.json();
            albaranesSinFacturar.value = data;
        } catch (error) {
            console.error('Error:', error);
            albaranesSinFacturar.value = [];
        } finally {
            cargandoAlbaranes.value = false;
        }
    } else {
        albaranesSinFacturar.value = [];
    }
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString();
};

const formatPrice = (price) => {
    return Number(price).toFixed(2);
};

const submit = () => {
    if (!form.cliente_id) {
        form.setError('cliente_id', 'Debe seleccionar un cliente');
        return;
    }

    if (!form.fecha) {
        form.setError('fecha', 'Debe seleccionar una fecha');
        return;
    }

    if (!form.fecha_vencimiento) {
        form.setError('fecha_vencimiento', 'Debe seleccionar una fecha de vencimiento');
        return;
    }

    if (form.albaran_ids.length === 0) {
        form.setError('albaran_ids', 'Debe seleccionar al menos un albarán');
        return;
    }

    form.post('/facturas', {
        onSuccess: () => {
            // La redirección se maneja automáticamente por el backend
        },
        onError: (errors) => {
            console.error('Errores:', errors);
        }
    });
};
</script> 