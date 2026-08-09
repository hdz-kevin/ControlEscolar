<script setup lang="ts">
import { Head } from '@inertiajs/vue3';

type User = {
    id: number;
    name: string;
    email: string;
};

defineProps<{
    user: User;
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Usuarios',
                href: '/users',
            },
            {
                title: 'Editar',
                href: '/users',
            },
        ],
    },
});
</script>

<template>
    <Head title="Editar usuario" />

    <div class="space-y-2 p-4">
        <h1 class="text-xl font-semibold">Editar usuario</h1>
        <p class="text-sm text-muted-foreground">
            Edici\u00f3n en construcci\u00f3n. En la siguiente fase conectaremos el
            formulario con validaci\u00f3n del backend.
        </p>
    </div>
</template>
