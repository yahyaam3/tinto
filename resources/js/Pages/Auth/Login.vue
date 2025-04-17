<template>
    <div class="min-h-screen flex items-center justify-center bg-gray-50">
        <div class="w-full max-w-sm p-8">
            <div class="flex justify-center mb-8">
                <img src="/images/logo.svg" alt="Logo" class="w-16 h-16">
            </div>

            <form @submit.prevent="submit" class="space-y-6">
                <div>
                    <label class="block text-sm text-gray-600" for="email">
                        Email
                    </label>
                    <input
                        id="email"
                        type="email"
                        class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-lg text-sm shadow-sm placeholder-gray-400
                        focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500"
                        v-model="form.email"
                        required
                        autofocus
                    >
                </div>

                <div>
                    <label class="block text-sm text-gray-600" for="password">
                        Contraseña
                    </label>
                    <input
                        id="password"
                        type="password"
                        class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-lg text-sm shadow-sm placeholder-gray-400
                        focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500"
                        v-model="form.password"
                        required
                        autocomplete="current-password"
                    >
                </div>

                <div class="flex items-center">
                    <input 
                        type="checkbox" 
                        id="remember"
                        class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
                        name="remember"
                    >
                    <label for="remember" class="ml-2 block text-sm text-gray-600">
                        Recordarme
                    </label>
                </div>

                <div>
                    <button 
                        type="submit"
                        class="w-full flex justify-center py-2 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                        :class="{ 'opacity-75 cursor-not-allowed': form.processing }" 
                        :disabled="form.processing"
                    >
                        INICIAR SESIÓN
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3';

const form = useForm({
    email: '',
    password: '',
});

const submit = () => {
    form.post(route('login'), {
        onSuccess: (page) => {
            console.log('Inertia login onSuccess: Navegación automática debería ocurrir.', page);
            // Normalmente no se necesita un visit() manual aquí si el backend redirige
        },
        onError: (errors) => {
            console.error('Inertia login onError:', errors);
            // Si hay errores aquí, podrían impedir la redirección visual
        },
        onFinish: () => {
            console.log('Inertia login onFinish: Petición completada.');
            // Se ejecuta siempre, después de onSuccess o onError
        }
    });
};
</script>

<style>
.bg-gray-50 {
    background-color: #F9FAFB;
}

input:-webkit-autofill,
input:-webkit-autofill:hover,
input:-webkit-autofill:focus {
    -webkit-text-fill-color: inherit;
    -webkit-box-shadow: 0 0 0px 1000px #ffffff inset;
    transition: background-color 5000s ease-in-out 0s;
}
</style> 