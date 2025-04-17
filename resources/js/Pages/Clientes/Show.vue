<template>
    <SidebarLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold text-gray-900">
                    Detalles del Cliente
                </h2>
                <div class="flex space-x-3">
                    <Button 
                        @click="navigateTo(`/clientes/${cliente.id}/edit`)"
                        variant="secondary"
                    >
                        Editar
                    </Button>
                    <Button 
                        @click="confirmarEliminacion"
                        variant="danger"
                    >
                        Eliminar
                    </Button>
                </div>
            </div>
        </template>

        <div class="p-6">
            <div class="overflow-hidden bg-white shadow sm:rounded-lg">
                <div class="px-4 py-6 sm:px-6">
                    <div class="flex items-center">
                        <div class="h-12 w-12 flex-shrink-0">
                            <span class="inline-flex h-12 w-12 items-center justify-center rounded-full bg-gray-500">
                                <span class="text-xl font-medium leading-none text-white">
                                    {{ cliente.nombre.charAt(0).toUpperCase() }}
                                </span>
                            </span>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-medium leading-6 text-gray-900">
                                {{ cliente.nombre }}
                            </h3>
                            <p class="mt-1 text-sm text-gray-500">
                                Cliente desde {{ formatDate(cliente.created_at) }}
                            </p>
                        </div>
                    </div>
                </div>
                <div class="border-t border-gray-200 px-4 py-5 sm:p-0">
                    <dl class="sm:divide-y sm:divide-gray-200">
                        <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6 sm:py-5">
                            <dt class="text-sm font-medium text-gray-500">Email</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">
                                {{ cliente.email }}
                            </dd>
                        </div>
                        <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6 sm:py-5">
                            <dt class="text-sm font-medium text-gray-500">Teléfono</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">
                                {{ cliente.telefono }}
                            </dd>
                        </div>
                        <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6 sm:py-5">
                            <dt class="text-sm font-medium text-gray-500">Estado</dt>
                            <dd class="mt-1 text-sm sm:col-span-2 sm:mt-0">
                                <span :class="[
                                    'inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium',
                                    cliente.estado === 'activo' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
                                ]">
                                    {{ cliente.estado.charAt(0).toUpperCase() + cliente.estado.slice(1) }}
                                </span>
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Historial de Facturas -->
            <div class="mt-8">
                <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">
                    Historial de Facturas
                </h3>
                <Table :columns="columns" :items="facturas">
                    <template #numero="{ item }">
                        <Link :href="`/facturas/${item.id}`" class="text-indigo-600 hover:text-indigo-900">
                            {{ item.numero }}
                        </Link>
                    </template>
                    <template #fecha="{ item }">
                        {{ formatDate(item.fecha) }}
                    </template>
                    <template #estado="{ item }">
                        <span :class="[
                            'inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium',
                            item.estado === 'pagada' ? 'bg-green-100 text-green-800' : 
                            item.estado === 'pendiente' ? 'bg-yellow-100 text-yellow-800' : 
                            'bg-red-100 text-red-800'
                        ]">
                            {{ item.estado.charAt(0).toUpperCase() + item.estado.slice(1) }}
                        </span>
                    </template>
                    <template #total="{ item }">
                        {{ formatPrice(item.total) }}
                    </template>
                </Table>
            </div>
        </div>

        <!-- Modal de confirmación -->
        <Modal :show="showDeleteModal" @close="closeDeleteModal">
            <div class="sm:flex sm:items-start">
                <div class="mt-3 text-center sm:mt-0 sm:text-left">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">
                        Confirmar eliminación
                    </h3>
                    <div class="mt-2">
                        <p class="text-sm text-gray-500">
                            ¿Estás seguro que deseas eliminar este cliente? Esta acción no se puede deshacer.
                        </p>
                    </div>
                </div>
            </div>
            <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                <Button
                    @click="eliminarCliente"
                    variant="danger"
                    :loading="processing"
                    class="sm:ml-3"
                >
                    Eliminar
                </Button>
                <Button
                    @click="closeDeleteModal"
                    variant="secondary"
                >
                    Cancelar
                </Button>
            </div>
        </Modal>
    </SidebarLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import SidebarLayout from '@/Layouts/SidebarLayout.vue';
import Button from '@/Components/Button.vue';
import Modal from '@/Components/Modal.vue';
import Table from '@/Components/Table.vue';

const props = defineProps({
    cliente: {
        type: Object,
        required: true
    },
    facturas: {
        type: Array,
        required: true
    }
});

const columns = [
    { key: 'numero', label: 'Número' },
    { key: 'fecha', label: 'Fecha' },
    { key: 'estado', label: 'Estado' },
    { key: 'total', label: 'Total' }
];

const showDeleteModal = ref(false);
const processing = ref(false);

const confirmarEliminacion = () => {
    showDeleteModal.value = true;
};

const closeDeleteModal = () => {
    showDeleteModal.value = false;
};

const eliminarCliente = () => {
    processing.value = true;
    router.delete(`/clientes/${props.cliente.id}`, {
        onSuccess: () => {
            closeDeleteModal();
            processing.value = false;
        },
        onError: () => {
            processing.value = false;
        }
    });
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('es-ES');
};

const formatPrice = (price) => {
    return new Intl.NumberFormat('es-ES', {
        style: 'currency',
        currency: 'EUR'
    }).format(price);
};

const navigateTo = (url) => {
    router.visit(url, {
        preserveState: true,
        preserveScroll: true
    });
};
</script> 