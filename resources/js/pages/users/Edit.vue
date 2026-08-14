<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import users from '@/routes/users';

type User = {
    id: number;
    code: string;
    name: string;
    last_name: string | null;
    phone: string | null;
    email: string;
    role: string;
    specialty: string | null;
    birth_date: string | null;
};

type RoleOption = {
    value: string;
    label: string;
};

const props = defineProps<{
    user: User;
    roles: RoleOption[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Usuarios',
                href: users.index(),
            },
            {
                title: 'Editar',
                href: users.index(),
            },
        ],
    },
});
</script>

<template>
    <Head title="Editar usuario" />

    <div class="space-y-6 p-4">
        <div class="flex items-start justify-between gap-4">
            <Heading
                title="Editar usuario"
                description="Actualiza algunos datos del usuario"
            />

            <Button variant="secondary" as-child>
                <Link :href="users.index()">Volver</Link>
            </Button>
        </div>

        <Card>
            <CardHeader class="pb-1">
                <CardTitle>Datos del usuario</CardTitle>
            </CardHeader>
            <CardContent>
                <Form
                    v-bind="users.update.form(props.user.id)"
                    class="grid grid-cols-1 gap-4 md:grid-cols-2"
                    v-slot="{ errors, processing }"
                >
                    <div class="grid gap-2">
                        <Label for="code">Matrícula</Label>
                        <Input id="code" :default-value="props.user.code" disabled />
                    </div>

                    <div class="grid gap-2">
                        <Label for="role">Rol</Label>
                        <Input
                            id="role"
                            :default-value="
                                props.roles.find(
                                    (option) => option.value === props.user.role,
                                )?.label ?? props.user.role
                            "
                            disabled
                        />
                    </div>

                    <div class="grid gap-2">
                        <Label for="name">Nombre</Label>
                        <Input
                            id="name"
                            name="name"
                            :default-value="props.user.name"
                        />
                        <InputError :message="errors.name" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="last_name">Apellidos</Label>
                        <Input
                            id="last_name"
                            name="last_name"
                            :default-value="props.user.last_name ?? ''"
                        />
                        <InputError :message="errors.last_name" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="email">Email</Label>
                        <Input
                            id="email"
                            name="email"
                            type="text"
                            :default-value="props.user.email"
                        />
                        <InputError :message="errors.email" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="phone">Teléfono</Label>
                        <Input
                            id="phone"
                            name="phone"
                            :default-value="props.user.phone ?? ''"
                        />
                        <InputError :message="errors.phone" />
                    </div>

                    <div
                        v-if="props.user.role === 'teacher'"
                        class="grid gap-2 md:col-span-2"
                    >
                        <Label for="specialty">Especialidad</Label>
                        <Input
                            id="specialty"
                            name="specialty"
                            :default-value="props.user.specialty ?? ''"
                            required
                        />
                        <InputError :message="errors.specialty" />
                    </div>

                    <div
                        v-if="props.user.role === 'student'"
                        class="grid gap-2 md:col-span-2"
                    >
                        <Label for="birth_date">Fecha de nacimiento</Label>
                        <Input
                            id="birth_date"
                            name="birth_date"
                            type="date"
                            :default-value="props.user.birth_date ?? ''"
                        />
                        <InputError :message="errors.birth_date" />
                    </div>

                    <div class="md:col-span-2 flex justify-end gap-2">
                        <Link
                            :href="users.destroy(props.user.id)"
                            as="button"
                            class="inline-flex h-9 items-center justify-center rounded-md bg-destructive px-4 text-sm font-medium text-white transition-colors hover:bg-destructive/90"
                        >
                            Eliminar
                        </Link>

                        <Button type="submit" :disabled="processing">
                            Guardar cambios
                        </Button>
                    </div>
                </Form>
            </CardContent>
        </Card>
    </div>
</template>
