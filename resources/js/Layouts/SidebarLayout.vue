<template>
    <div class="min-h-screen bg-gray-100">
        <!-- Sidebar -->
        <aside class="fixed inset-y-0 left-0 w-64 bg-indigo-600 text-white overflow-y-auto flex flex-col">
            <!-- Logo -->
            <div class="p-6 border-b border-indigo-500">
                <a href="/" class="flex items-center">
                    <span class="text-2xl font-bold">tintorería Pro-Ecològica 7'S</span>
                </a>
            </div>

            <!-- Navegación -->
            <nav class="space-y-1 px-2 py-4 flex-grow">
                <!-- Clientes -->
                <Link href="/clientes"
                    class="flex items-center px-4 py-3 text-sm font-medium rounded-md transition-colors"
                    :class="{'bg-indigo-700 text-white': $page.url.startsWith('/clientes'), 'text-indigo-100 hover:bg-indigo-700': !$page.url.startsWith('/clientes')}"
                >
                    <svg class="mr-3 h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    Clientes
                </Link>

                <!-- Productos -->
                <Link href="/productos"
                    class="flex items-center px-4 py-3 text-sm font-medium rounded-md transition-colors"
                    :class="{'bg-indigo-700 text-white': $page.url.startsWith('/productos'), 'text-indigo-100 hover:bg-indigo-700': !$page.url.startsWith('/productos')}"
                >
                    <svg class="mr-3 h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                    Productos
                </Link>

                <!-- Albaranes -->
                <Link href="/albaranes"
                    class="flex items-center px-4 py-3 text-sm font-medium rounded-md transition-colors"
                    :class="{'bg-indigo-700 text-white': $page.url.startsWith('/albaranes'), 'text-indigo-100 hover:bg-indigo-700': !$page.url.startsWith('/albaranes')}"
                >
                    <svg class="mr-3 h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Albaranes
                </Link>

                <!-- Facturas -->
                <Link href="/facturas"
                    class="flex items-center px-4 py-3 text-sm font-medium rounded-md transition-colors"
                    :class="{'bg-indigo-700 text-white': $page.url.startsWith('/facturas'), 'text-indigo-100 hover:bg-indigo-700': !$page.url.startsWith('/facturas')}"
                >
                    <svg class="mr-3 h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    Facturas
                </Link>
            </nav>

            <!-- Botón de Admin en la parte inferior -->
            <div class="p-4 border-t border-indigo-500 mt-auto">
                <a href="/login" class="flex items-center px-4 py-2 text-indigo-100 hover:bg-indigo-700 rounded-md transition-colors opacity-50 hover:opacity-100">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </a>
            </div>
        </aside>

        <!-- Contenido Principal -->
        <div class="pl-64">
            <main class="py-6">
                <div class="mx-auto px-4 sm:px-6 md:px-8">
                    <!-- Mensaje Flash -->
                    <div v-if="$page.props.flash.success" class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded relative" role="alert">
                        <span class="block sm:inline">{{ $page.props.flash.success }}</span>
                        <button @click="$page.props.flash.success = null" class="absolute top-0 bottom-0 right-0 px-4 py-3">
                            <svg class="fill-current h-6 w-6 text-green-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><title>Cerrar</title><path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/></svg>
                        </button>
                    </div>
                    
                    <slot></slot>
                </div>
            </main>
        </div>
    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<style scoped>
.router-link-active {
    @apply bg-indigo-700;
}
</style>