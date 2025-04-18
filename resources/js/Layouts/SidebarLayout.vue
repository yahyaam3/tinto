<template>
    <div class="min-h-screen bg-gray-100">
        <!-- Barra superior móvil -->
        <div class="lg:hidden bg-indigo-600 text-white p-4 flex items-center justify-between">
            <a href="/" class="text-xl font-bold">tintorería Pro-E 7'S</a>
            <button @click="isMobileMenuOpen = !isMobileMenuOpen" class="text-white focus:outline-none">
                <svg v-if="!isMobileMenuOpen" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
                <svg v-else class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Sidebar para escritorio -->
        <aside class="hidden lg:flex fixed inset-y-0 left-0 w-64 bg-indigo-600 text-white overflow-y-auto flex-col">
            <!-- Logo -->
            <div class="p-6 border-b border-indigo-500">
                <a href="/" class="flex items-center">
                    <span class="text-2xl font-bold">tintorería Pro-Ecològica 7'S</span>
                </a>
            </div>

            <!-- Navegación escritorio -->
            <nav class="space-y-1 px-2 py-4 flex-grow">
                <!-- Enlaces de navegación para escritorio -->
                <SidebarNavLinks />
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

        <!-- Menú móvil desplegable -->
        <div v-if="isMobileMenuOpen" class="lg:hidden fixed inset-0 z-40 flex">
            <!-- Fondo oscuro -->
            <div class="fixed inset-0 bg-gray-600 bg-opacity-75" @click="isMobileMenuOpen = false"></div>
            
            <!-- Panel lateral -->
            <div class="relative flex-1 flex flex-col max-w-xs w-full bg-indigo-600 text-white">
                <div class="absolute top-0 right-0 -mr-12 pt-2">
                    <button @click="isMobileMenuOpen = false" class="ml-1 flex items-center justify-center h-10 w-10 rounded-full focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white">
                        <span class="sr-only">Cerrar menú</span>
                        <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                
                <!-- Navegación móvil -->
                <div class="flex-1 h-0 pt-5 pb-4 overflow-y-auto">
                    <div class="flex-shrink-0 flex items-center px-4">
                        <span class="text-xl font-bold">tintorería Pro-Ecològica 7'S</span>
                    </div>
                    <nav class="mt-5 px-2 space-y-1">
                        <!-- Enlaces de navegación para móvil -->
                        <SidebarNavLinks />
                    </nav>
                </div>
                
                <!-- Botón de Admin en la parte inferior móvil -->
                <div class="flex-shrink-0 flex border-t border-indigo-500 p-4">
                    <a href="/login" class="flex items-center px-4 py-2 text-indigo-100 hover:bg-indigo-700 rounded-md transition-colors opacity-50 hover:opacity-100">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </a>
                </div>
            </div>
            <div class="flex-shrink-0 w-14">
                <!-- Espacio en blanco para permitir cerrar haciendo clic -->
            </div>
        </div>

        <!-- Contenido Principal -->
        <div class="lg:pl-64">
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
import SidebarNavLinks from '@/Components/SidebarNavLinks.vue';
import { ref } from 'vue';

// Estado para controlar la visibilidad del menú móvil
const isMobileMenuOpen = ref(false);
</script>

<style scoped>
.router-link-active {
    @apply bg-indigo-700;
}
</style>