<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import usersRoutes from '@/routes/users';

type ManagedUser = {
    id: number;
    name: string;
    last_name: string | null;
    email: string;
    phone: string | null;
    role: string;
    code: string;
};

type PaginationLink = {
    url: string | null;
    label: string;
    active: boolean;
};

type PaginatedUsers = {
    data: ManagedUser[];
    from: number | null;
    to: number | null;
    total: number;
    links: PaginationLink[];
};

defineProps<{
    users: PaginatedUsers;
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Usuarios',
                href: usersRoutes.index(),
            },
        ],
    },
});
</script>

<template>
    <Head title="Usuarios" />

    <div class="space-y-6 p-4">
        <div class="flex items-start justify-between gap-4">
            <Heading
                title="Gestión de usuarios"
                description="Administra cuentas de administrativos, docentes y alumnos"
            />

            <Button as-child>
                <Link :href="usersRoutes.create()">Crear usuario</Link>
            </Button>
        </div>

        <Card>
            <CardHeader class="pb-1">
                <CardTitle>Listado</CardTitle>
            </CardHeader>
            <CardContent class="space-y-4">
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[760px] text-sm">
                        <thead class="text-left text-muted-foreground">
                            <tr class="border-b">
                                <th class="px-2 py-3 font-medium">Matrícula</th>
                                <th class="px-2 py-3 font-medium">Nombre</th>
                                <th class="px-2 py-3 font-medium">Email</th>
                                <th class="px-2 py-3 font-medium">Rol</th>
                                <th class="px-2 py-3 font-medium">Teléfono</th>
                                <th class="px-2 py-3 text-right font-medium">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="user in users.data"
                                :key="user.id"
                                class="border-b last:border-b-0"
                            >
                                <td class="px-2 py-3 font-mono text-xs">
                                    {{ user.code }}
                                </td>
                                <td class="px-2 py-3">
                                    {{ user.name }} {{ user.last_name ?? '' }}
                                </td>
                                <td class="px-2 py-3">{{ user.email }}</td>
                                <td class="px-2 py-3 capitalize">{{ user.role }}</td>
                                <td class="px-2 py-3">{{ user.phone ?? 'N/P' }}</td>
                                <td class="px-2 py-3">
                                    <div class="flex justify-end gap-2">
                                        <Button variant="secondary" size="sm" as-child>
                                            <Link :href="usersRoutes.edit(user.id)">Editar</Link>
                                        </Button>

                                        <Form
                                            v-bind="usersRoutes.destroy.form(user.id)"
                                            v-slot="{ processing }"
                                            class="inline"
                                        >
                                            <Button
                                                type="submit"
                                                variant="destructive"
                                                size="sm"
                                                :disabled="processing"
                                            >
                                                Eliminar
                                            </Button>
                                        </Form>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="flex items-center justify-between gap-4 text-sm text-muted-foreground">
                    <p>
                        Mostrando {{ users.from ?? 0 }}-{{ users.to ?? 0 }} de
                        {{ users.total }} usuarios.
                    </p>

                    <div class="flex items-center gap-2">
                        <template v-for="link in users.links" :key="link.label">
                            <span
                                v-if="link.url === null"
                                class="rounded-md border px-2 py-1 opacity-50"
                                v-html="link.label"
                            />
                            <Link
                                v-else
                                :href="link.url"
                                class="rounded-md border px-2 py-1"
                                :class="{ 'bg-muted text-foreground': link.active }"
                                v-html="link.label"
                            />
                        </template>
                    </div>
                </div>
            </CardContent>
        </Card>
    </div>
</template>
