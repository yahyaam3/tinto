<template>
    <button
        :type="type"
        :class="[
            'inline-flex items-center justify-center px-4 py-2 border rounded-md font-semibold text-xs uppercase tracking-widest transition ease-in-out duration-150',
            variant === 'primary' && 'border-transparent bg-indigo-600 text-white hover:bg-indigo-500 focus:bg-indigo-700',
            variant === 'secondary' && 'border-gray-300 bg-white text-gray-700 hover:text-gray-500 focus:border-indigo-300',
            variant === 'danger' && 'border-transparent bg-red-600 text-white hover:bg-red-500 focus:bg-red-700',
            variant === 'success' && 'border-transparent bg-green-600 text-white hover:bg-green-500 focus:bg-green-700',
            disabled && 'opacity-50 cursor-not-allowed',
            size === 'sm' && 'px-2 py-1 text-xs',
            size === 'lg' && 'px-6 py-3 text-base',
        ]"
        :disabled="disabled || loading"
        @click="$emit('click', $event)"
    >
        <svg v-if="loading" class="animate-spin -ml-1 mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <slot></slot>
    </button>
</template>

<script setup>
defineProps({
    type: {
        type: String,
        default: 'button'
    },
    variant: {
        type: String,
        default: 'primary',
        validator: value => ['primary', 'secondary', 'danger', 'success'].includes(value)
    },
    size: {
        type: String,
        default: 'md',
        validator: value => ['sm', 'md', 'lg'].includes(value)
    },
    disabled: {
        type: Boolean,
        default: false
    },
    loading: {
        type: Boolean,
        default: false
    }
});
</script> 