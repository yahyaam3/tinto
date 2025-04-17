<template>
    <ClienteForm :cliente="cliente" />
</template>

<script setup>
import ClienteForm from './Form.vue';

defineProps({
    cliente: {
        type: Object,
        required: true
    }
});
</script> 