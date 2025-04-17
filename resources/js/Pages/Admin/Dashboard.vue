<template>
    <SidebarLayout>
        <div class="p-6">
            <h2 class="text-2xl font-semibold text-gray-900 mb-6">Panel de Administración</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Facturas del Mes -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-medium text-gray-700 mb-2">Facturas este Mes</h3>
                    <div class="flex items-baseline">
                        <span class="text-4xl font-bold text-gray-900">{{ stats.facturas_mes }}</span>
                        <span class="ml-2 text-sm text-gray-600">facturas</span>
                    </div>
                    <div class="mt-2 text-sm text-gray-600">
                        €{{ stats.total_mes }} total
                    </div>
                </div>

                <!-- Total Clientes -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-medium text-gray-700 mb-2">Total Clientes</h3>
                    <div class="flex items-baseline">
                        <span class="text-4xl font-bold text-gray-900">{{ stats.total_clientes }}</span>
                        <span class="ml-2 text-sm text-gray-600">clientes</span>
                    </div>
                    <div class="mt-2 text-sm text-green-600" v-if="stats.incremento_clientes > 0">
                        +{{ stats.incremento_clientes }}% desde el mes pasado
                    </div>
                </div>

                <!-- Ingresos Totales -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-medium text-gray-700 mb-2">Ingresos Totales</h3>
                    <div class="flex items-baseline">
                        <span class="text-4xl font-bold text-gray-900">€{{ stats.ingresos_totales }}</span>
                    </div>
                    <div class="mt-2 text-sm text-gray-600">
                        Este año
                    </div>
                </div>
            </div>

            <!-- Gráficos y más estadísticas se pueden añadir aquí -->
        </div>
    </SidebarLayout>
</template>

<script>
import { defineComponent } from 'vue';
import SidebarLayout from '@/Layouts/SidebarLayout.vue';

export default defineComponent({
    components: {
        SidebarLayout
    },

    props: {
        stats: Object
    }
});
</script> 