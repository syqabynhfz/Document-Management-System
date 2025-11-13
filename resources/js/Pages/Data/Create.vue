<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";

// Inisialisasi form
const form = useForm({
    name: "",
    address: "",
    contact_person: "",
});

// Fungsi submit
const submit = () => {
    form.post(route("data.store"));
};
</script>

<template>
    <Head title="Tambah Vendor Baru" />

    <AppLayout title="Tambah Vendor Baru">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Tambah Vendor (Master To) Baru
            </h2>
        </template>

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <form
                @submit.prevent="submit"
                class="p-6 space-y-6 max-w-lg mx-auto"
            >
                <div>
                    <InputLabel for="name" value="Nama Vendor" />
                    <TextInput
                        id="name"
                        type="text"
                        class="mt-1 block w-full"
                        v-model="form.name"
                        required
                        autofocus
                    />
                    <InputError class="mt-2" :message="form.errors.name" />
                </div>

                <div>
                    <InputLabel for="address" value="Alamat" />
                    <TextInput
                        id="address"
                        type="text"
                        class="mt-1 block w-full"
                        v-model="form.address"
                    />
                    <InputError class="mt-2" :message="form.errors.address" />
                </div>

                <div>
                    <InputLabel for="contact_person" value="Contact Person" />
                    <TextInput
                        id="contact_person"
                        type="text"
                        class="mt-1 block w-full"
                        v-model="form.contact_person"
                    />
                    <InputError
                        class="mt-2"
                        :message="form.errors.contact_person"
                    />
                </div>

                <div class="flex items-center gap-4">
                    <PrimaryButton :disabled="form.processing"
                        >Simpan Vendor</PrimaryButton
                    >
                    <Link
                        :href="route('data.index')"
                        class="text-sm text-gray-600 hover:text-gray-900"
                    >
                        Batal
                    </Link>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
