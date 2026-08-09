<script setup lang="ts">
import { Head } from '@inertiajs/vue3';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Usuarios',
                href: '/users',
            },
        ],
    },
});
</script>

<template>
    <Head title="Usuarios" />

    <div class="space-y-2 p-4">
        <h1 class="text-xl font-semibold">Gesti\u00f3n de usuarios</h1>
        <p class="text-sm text-muted-foreground">
            M\u00f3dulo en construcci\u00f3n. En la siguiente fase agregaremos listado real,
            filtros y acciones CRUD.
        </p>
    </div>
</template>
