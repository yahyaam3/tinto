<template>
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
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
</script>
