<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import users from '@/routes/users';

type RoleOption = {
    value: string;
    label: string;
};

defineProps<{
    roles: RoleOption[];
}>();

const selectedRole = ref('student');

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Usuarios',
                href: users.index(),
            },
            {
                title: 'Crear',
                href: users.create(),
            },
        ],
    },
});
</script>

<template>
    <Head title="Crear usuario" />

    <div class="space-y-6 p-4">
        <div class="flex items-start justify-between gap-4">
            <Heading
                title="Crear usuario"
                description="Se generará una matrícula única y la contraseña inicial será esa misma matrícula"
            />

            <Button variant="secondary" as-child>
                <Link :href="users.index()">Volver</Link>
            </Button>
        </div>

        <Card>
            <CardHeader class="pb-1">
                <CardTitle>Datos de usuario</CardTitle>
            </CardHeader>
            <CardContent>
                <Form
                    v-bind="users.store.form()"
                    class="grid grid-cols-1 gap-4 md:grid-cols-2"
                    v-slot="{ errors, processing }"
                >
                    <div class="grid gap-2">
                        <Label for="name">Nombre</Label>
                        <Input id="name" name="name" placeholder="Nombre del usuario" />
                        <InputError :message="errors.name" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="last_name">Apellidos</Label>
                        <Input id="last_name" name="last_name" placeholder="Apellidos del usuario" />
                        <InputError :message="errors.last_name" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="email">Email</Label>
                        <Input id="email" name="email" type="text" placeholder="Correo electrónico" />
                        <InputError :message="errors.email" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="phone">Teléfono</Label>
                        <Input id="phone" name="phone" placeholder="Teléfono" />
                        <InputError :message="errors.phone" />
                    </div>

                    <div class="grid gap-2 md:col-span-2">
                        <Label for="role">Rol</Label>
                        <select
                            id="role"
                            name="role"
                            v-model="selectedRole"
                            class="border-input bg-background h-9 rounded-md border px-3 text-sm"
                        >
                            <option
                                v-for="role in roles"
                                :key="role.value"
                                :value="role.value"
                            >
                                {{ role.label }}
                            </option>
                        </select>
                        <InputError :message="errors.role" />
                    </div>

                    <div
                        v-if="selectedRole === 'teacher'"
                        class="grid gap-2 md:col-span-2"
                    >
                        <Label for="specialty">Especialidad</Label>
                        <Input id="specialty" name="specialty" />
                        <InputError :message="errors.specialty" />
                    </div>

                    <div
                        v-if="selectedRole === 'student'"
                        class="grid gap-2 md:col-span-2"
                    >
                        <Label for="birth_date">Fecha de nacimiento</Label>
                        <Input id="birth_date" name="birth_date" type="date" />
                        <InputError :message="errors.birth_date" />
                    </div>

                    <div class="md:col-span-2 flex justify-end">
                        <Button type="submit" :disabled="processing">
                            Crear usuario
                        </Button>
                    </div>
                </Form>
            </CardContent>
        </Card>
    </div>
</template>
