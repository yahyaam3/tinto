<template>
    <SidebarLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold text-gray-900">
                    {{ factura ? 'Editar Factura' : 'Nueva Factura' }}
                </h2>
            </div>
        </template>

        <div class="p-6 bg-white rounded-lg shadow">
            <form @submit.prevent="submit">
                <!-- Cliente y Fecha -->
                <div class="grid grid-cols-1 gap-6 mb-6 md:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Cliente</label>
                        <select 
                            v-model="form.cliente_id"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            required
                        >
                            <option value="">Seleccionar cliente</option>
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
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            required
                        >
                    </div>
                </div>

                <!-- Estado -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700">Estado</label>
                    <select 
                        v-model="form.estado"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        required
                    >
                        <option value="pendiente">Pendiente</option>
                        <option value="pagada">Pagada</option>
                    </select>
                </div>

                <!-- Productos -->
                <div class="mb-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Productos</h3>
                        <button 
                            type="button"
                            @click="agregarProducto"
                            class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700"
                        >
                            Agregar Producto
                        </button>
                    </div>

                    <div v-for="(producto, index) in form.productos" :key="index" class="grid grid-cols-12 gap-4 mb-4">
                        <div class="col-span-6">
                            <select 
                                v-model="producto.producto_id"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                required
                                @change="actualizarPrecio(index)"
                            >
                                <option value="">Seleccionar producto</option>
                                <option v-for="p in productos" :key="p.id" :value="p.id">
                                    {{ p.nombre }} - {{ formatPrice(p.precio) }}€
                                </option>
                            </select>
                        </div>
                        <div class="col-span-2">
                            <input 
                                type="number" 
                                v-model="producto.cantidad"
                                min="1"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                required
                                @input="calcularTotal"
                            >
                        </div>
                        <div class="col-span-3">
                            <div class="text-right py-2">
                                {{ formatPrice(calcularSubtotal(index)) }}€
                            </div>
                        </div>
                        <div class="col-span-1">
                            <button 
                                type="button"
                                @click="eliminarProducto(index)"
                                class="text-red-600 hover:text-red-900"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Total -->
                <div class="flex justify-end mb-6">
                    <div class="text-xl font-bold">
                        Total: {{ formatPrice(total) }}€
                    </div>
                </div>

                <!-- Botones -->
                <div class="flex justify-end gap-4">
                    <Link
                        href="/facturas"
                        class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50"
                    >
                        Cancelar
                    </Link>
                    <button
                        type="submit"
                        class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700"
                        :disabled="processing"
                    >
                        {{ factura ? 'Actualizar' : 'Crear' }} Factura
                    </button>
                </div>
            </form>
        </div>
    </SidebarLayout>
</template>

<script>
import { ref, computed } from 'vue';
import { Link, router, useForm } from '@inertiajs/vue3';
import SidebarLayout from '@/Layouts/SidebarLayout.vue';

export default {
    components: {
        Link,
        SidebarLayout
    },

    props: {
        factura: Object,
        clientes: Array,
        productos: Array
    },

    setup(props) {
        const form = useForm({
            cliente_id: props.factura?.cliente_id || '',
            fecha: props.factura?.fecha || new Date().toISOString().split('T')[0],
            estado: props.factura?.estado || 'pendiente',
            productos: props.factura?.productos?.map(p => ({
                producto_id: p.id,
                cantidad: p.pivot.cantidad
            })) || []
        });

        const total = ref(0);
        const processing = ref(false);

        const agregarProducto = () => {
            form.productos.push({
                producto_id: '',
                cantidad: 1
            });
        };

        const eliminarProducto = (index) => {
            form.productos.splice(index, 1);
            calcularTotal();
        };

        const actualizarPrecio = (index) => {
            calcularTotal();
        };

        const calcularSubtotal = (index) => {
            const producto = form.productos[index];
            if (!producto.producto_id) return 0;
            
            const productoInfo = props.productos.find(p => p.id === producto.producto_id);
            return productoInfo ? productoInfo.precio * producto.cantidad : 0;
        };

        const calcularTotal = () => {
            total.value = form.productos.reduce((sum, _, index) => sum + calcularSubtotal(index), 0);
        };

        const formatPrice = (price) => {
            return Number(price).toFixed(2);
        };

        const submit = () => {
            if (form.productos.length === 0) {
                alert('Debe agregar al menos un producto');
                return;
            }

            processing.value = true;
            
            if (props.factura) {
                form.put(`/facturas/${props.factura.id}`, {
                    onSuccess: () => {
                        processing.value = false;
                    },
                    onError: () => {
                        processing.value = false;
                    }
                });
            } else {
                form.post('/facturas', {
                    onSuccess: () => {
                        processing.value = false;
                    },
                    onError: () => {
                        processing.value = false;
                    }
                });
            }
        };

        // Calcular total inicial si hay productos
        if (form.productos.length > 0) {
            calcularTotal();
        }

        return {
            form,
            total,
            processing,
            agregarProducto,
            eliminarProducto,
            actualizarPrecio,
            calcularSubtotal,
            calcularTotal,
            formatPrice,
            submit
        };
    }
};
</script> 