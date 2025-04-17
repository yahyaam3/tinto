<template>
    <Head title="Bienvenido" />
    <SidebarLayout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Sección de Inicio -->
                <div class="text-center mb-16">
                    <h2 class="page-header">
                        ¡Bienvenido a tu Sistema de Gestión de Tintorería!
                    </h2>
                    <p class="text-xl text-gray-500">
                        Gestiona tu tintorería de manera eficiente y profesional
                    </p>
                </div>

                <!-- Acciones Rápidas -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-16">
                    <div class="card">
                        <h3 class="section-title">Últimos Albaranes</h3>
                        <div v-if="ultimosAlbaranes && ultimosAlbaranes.length > 0" class="space-y-4">
                            <div v-for="albaran in ultimosAlbaranes" :key="albaran.id" class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                <div>
                                    <p class="font-medium">{{ albaran.cliente.nombre }}</p>
                                    <!-- Aquí podrías añadir más detalles si los pasas desde el controlador -->
                                    <p class="text-sm text-gray-500">Nº: {{ albaran.numero }}</p> 
                                </div>
                                <span 
                                    class="badge"
                                    :class="{
                                        'badge-warning': albaran.estado === 'pendiente',
                                        'badge-success': albaran.estado === 'completado'
                                    }"
                                >
                                    {{ albaran.estado }}
                                </span>
                            </div>
                        </div>
                         <div v-else class="text-gray-500 italic">
                             No hay albaranes recientes para mostrar.
                         </div>
                        <div class="mt-4">
                            <Link href="/albaranes" class="nav-link text-indigo-600 hover:text-indigo-500">
                                Ver todos los albaranes
                                <svg class="ml-2 h-5 w-5 text-indigo-500 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </Link>
                        </div>
                    </div>

                    <div class="card">
                        <h3 class="section-title">Acciones Rápidas</h3>
                        <div class="space-y-4">
                            <Link href="/clientes/create" 
                                  method="get"
                                  class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 active:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Nuevo Cliente
                            </Link>
                            <Link href="/albaranes/create" 
                                  method="get"
                                  class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500 active:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Nuevo Albarán
                            </Link>
                             <Link href="/facturas/create" 
                                  method="get"
                                  class="inline-flex items-center px-4 py-2 bg-purple-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-500 active:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Nueva Factura
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </SidebarLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3';
import SidebarLayout from '@/Layouts/SidebarLayout.vue';

defineProps({
    ultimosAlbaranes: Array // Definir la prop
});
</script>

<style>
.page-header {
    @apply text-3xl font-bold text-gray-900 mb-4;
}

.stats-card {
    @apply bg-white p-6 rounded-lg shadow-sm;
}

.stats-title {
    @apply text-sm font-medium text-gray-500;
}

.stats-value {
    @apply text-2xl font-semibold text-gray-900 mt-2;
}

.card {
    @apply bg-white p-6 rounded-lg shadow-sm;
}

.section-title {
    @apply text-lg font-medium text-gray-900 mb-4;
}

.badge {
    @apply px-2 py-1 text-xs font-medium rounded-full;
}

.badge-warning {
    @apply bg-yellow-100 text-yellow-800;
}

.badge-success {
    @apply bg-green-100 text-green-800;
}

.nav-link {
    @apply inline-flex items-center text-sm font-medium;
}
</style> 