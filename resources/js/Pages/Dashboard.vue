<template>
  <AuthenticatedLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Dashboard General
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Tarjetas de estadísticas -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
          <!-- Clientes -->
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
            <h3 class="text-sm font-medium text-gray-500">Total Clientes</h3>
            <p class="text-2xl font-semibold text-gray-900 mt-1">{{ totalClientes }}</p>
          </div>
          <!-- Ingresos -->
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
            <h3 class="text-sm font-medium text-gray-500">Ingresos (Este Mes)</h3>
            <p class="text-2xl font-semibold text-green-600 mt-1">{{ formatCurrency(ingresosMes) }}</p>
            <p class="text-sm text-gray-500 mt-1">Basado en facturas pagadas</p>
          </div>
          <!-- Facturas por Cobrar -->
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
            <h3 class="text-sm font-medium text-gray-500">Facturas por Cobrar</h3>
            <p class="text-2xl font-semibold text-red-600 mt-1">{{ facturasPendientes }}</p>
            <p class="text-sm text-gray-500 mt-1">{{ formatCurrency(totalPendiente) }} total</p>
          </div>
          <!-- Facturas Mes -->
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
            <h3 class="text-sm font-medium text-gray-500">Facturas (Este Mes)</h3>
            <p class="text-2xl font-semibold text-gray-900 mt-1">{{ facturasMes }}</p>
          </div>
        </div>

        <!-- Tarjetas de Albaranes -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
           <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
            <h3 class="text-sm font-medium text-gray-500">Albaranes Pendientes</h3>
            <p class="text-2xl font-semibold text-yellow-600 mt-1">{{ albaranesPendientesCount }}</p>
            <p class="text-sm text-gray-500 mt-1">{{ formatCurrency(albaranesPendientesTotal) }} valor total</p>
          </div>
           <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
            <h3 class="text-sm font-medium text-gray-500">Albaranes Completados (Mes)</h3>
            <p class="text-2xl font-semibold text-blue-600 mt-1">{{ albaranesCompletadosMes }}</p>
            <p class="text-sm text-gray-500 mt-1">Marcados este mes</p>
          </div>
        </div>

        <!-- Acciones rápidas -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mb-8">
          <h3 class="text-lg font-semibold text-gray-700 mb-4">Acciones Rápidas</h3>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <Link :href="route('dashboard.clientes.create')" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition-colors text-center">
              Nuevo Cliente
            </Link>
            <Link :href="route('dashboard.productos.create')" class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 transition-colors text-center">
              Nuevo Producto
            </Link>
            <Link :href="route('dashboard.facturas.create')" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors text-center">
              Nueva Factura
            </Link>
             <!-- Podrías añadir un botón 'Nuevo Albarán' aquí si quieres -->
          </div>
        </div>

        <!-- Historial: Últimas facturas -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
          <h3 class="text-lg font-semibold text-gray-700 mb-4">Últimas Facturas</h3>
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Número</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cliente</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="factura in ultimasFacturas" :key="factura.id">
                  <td class="px-6 py-4 whitespace-nowrap">{{ factura.numero }}</td>
                  <td class="px-6 py-4 whitespace-nowrap">{{ factura.cliente.nombre }}</td>
                  <td class="px-6 py-4 whitespace-nowrap">{{ formatDate(factura.fecha) }}</td>
                  <td class="px-6 py-4 whitespace-nowrap">{{ formatCurrency(factura.total) }}</td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <Link :href="route('facturas.pdf', factura.id)" class="text-indigo-600 hover:text-indigo-900 mr-2">
                      PDF
                    </Link>
                    <!-- Otras acciones -->
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Gráficos de Ventas -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
          <!-- Gráfico de Ventas Diarias -->
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-700 mb-4">Ventas Diarias (Este Mes)</h3>
            <canvas ref="ventasDiariasChart"></canvas>
          </div>
          <!-- Gráfico de Ventas Mensuales -->
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-700 mb-4">Ventas Mensuales (Este Año)</h3>
            <canvas ref="ventasMensualesChart"></canvas>
          </div>
        </div>

        <!-- Productos más solicitados y Clientes más frecuentes -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
          <!-- Productos más solicitados -->
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-700 mb-4">Productos Más Solicitados</h3>
            <div class="space-y-4">
              <div v-for="(producto, index) in productosMasSolicitados" :key="index" class="flex items-center justify-between">
                <div class="flex items-center">
                  <span class="text-lg font-medium text-gray-900">{{ index + 1 }}.</span>
                  <span class="ml-2 text-gray-800">{{ producto.nombre }}</span>
                </div>
                <span class="text-gray-600">{{ producto.total_solicitado }} unidades</span>
              </div>
            </div>
          </div>
          <!-- Clientes más frecuentes -->
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-700 mb-4">Clientes Más Frecuentes</h3>
            <div class="space-y-4">
              <div v-for="(cliente, index) in clientesMasFrecuentes" :key="index" class="flex items-center justify-between">
                <div class="flex items-center">
                  <span class="text-lg font-medium text-gray-900">{{ index + 1 }}.</span>
                  <span class="ml-2 text-gray-800">{{ cliente.nombre }}</span>
                </div>
                <span class="text-gray-600">{{ cliente.albaranes_count }} albaranes</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { format } from 'date-fns';
import { es } from 'date-fns/locale';
import { onMounted, ref } from 'vue';
import Chart from 'chart.js/auto';

const props = defineProps({
  totalClientes: Number,
  facturasMes: Number,
  ingresosMes: Number,
  facturasPendientes: Number,
  totalPendiente: Number,
  albaranesPendientesCount: Number,
  albaranesPendientesTotal: Number,
  albaranesCompletadosMes: Number,
  ultimasFacturas: Array,
  ventasDiarias: Array,
  ventasMensuales: Array,
  productosMasSolicitados: Array,
  clientesMasFrecuentes: Array
});

const ventasDiariasChart = ref(null);
const ventasMensualesChart = ref(null);

onMounted(() => {
  // Gráfico de ventas diarias
  const ctxDiario = ventasDiariasChart.value.getContext('2d');
  new Chart(ctxDiario, {
    type: 'line',
    data: {
      labels: props.ventasDiarias.map(venta => format(new Date(venta.dia), 'dd/MM')),
      datasets: [{
        label: 'Ventas €',
        data: props.ventasDiarias.map(venta => venta.total),
        borderColor: 'rgb(75, 192, 192)',
        tension: 0.1
      }]
    },
    options: {
      responsive: true,
      plugins: {
        legend: {
          position: 'top',
        },
        title: {
          display: false
        }
      }
    }
  });

  // Gráfico de ventas mensuales
  const ctxMensual = ventasMensualesChart.value.getContext('2d');
  new Chart(ctxMensual, {
    type: 'bar',
    data: {
      labels: props.ventasMensuales.map(venta => {
        const fecha = new Date(2024, venta.mes - 1, 1);
        return format(fecha, 'MMMM', { locale: es });
      }),
      datasets: [{
        label: 'Ventas €',
        data: props.ventasMensuales.map(venta => venta.total),
        backgroundColor: 'rgba(75, 192, 192, 0.2)',
        borderColor: 'rgb(75, 192, 192)',
        borderWidth: 1
      }]
    },
    options: {
      responsive: true,
      plugins: {
        legend: {
          position: 'top',
        },
        title: {
          display: false
        }
      }
    }
  });
});

const formatDate = (date) => {
  if (!date) return 'N/A';
  try {
    return format(new Date(date), 'dd/MM/yyyy', { locale: es });
  } catch (error) {
    console.error("Error formatting date:", date, error);
    return date; // Devuelve la fecha original si falla
  }
};

const formatCurrency = (value) => {
  const numValue = parseFloat(value);
  if (isNaN(numValue)) {
    return '0,00 €'; // O 'N/A' o lo que prefieras
  }
  return numValue.toLocaleString('es-ES', { style: 'currency', currency: 'EUR' });
};
</script> 