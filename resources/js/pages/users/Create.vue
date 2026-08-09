<script setup lang="ts">
import { Head } from '@inertiajs/vue3';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Usuarios',
                href: '/users',
            },
            {
                title: 'Crear',
                href: '/users/create',
            },
        ],
    },
});
</script>

<template>
    <Head title="Crear usuario" />

    <div class="space-y-2 p-4">
        <h1 class="text-xl font-semibold">Crear usuario</h1>
        <p class="text-sm text-muted-foreground">
            Formulario en construcci\u00f3n. En la siguiente fase implementaremos
            validaciones y persistencia.
        </p>
    </div>
</template>
