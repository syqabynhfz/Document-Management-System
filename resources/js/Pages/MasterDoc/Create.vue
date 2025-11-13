<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";

// Inisialisasi form
const form = useForm({
    type_name: "",
    product: "",
});

// Fungsi submit
const submit = () => {
    form.post(route("master-doc.store"));
};
</script>

<template>
    <Head title="Tambah Tipe Dokumen" />

    <AppLayout title="Tambah Tipe Dokumen">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Tambah Tipe Dokumen (Master Doc) Baru
            </h2>
        </template>

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <form
                @submit.prevent="submit"
                class="p-6 space-y-6 max-w-lg mx-auto"
            >
                <div>
                    <InputLabel for="type_name" value="Nama Tipe Dokumen" />
                    <TextInput
                        id="type_name"
                        type="text"
                        class="mt-1 block w-full"
                        v-model="form.type_name"
                        required
                        autofocus
                    />
                    <InputError class="mt-2" :message="form.errors.type_name" />
                </div>

                <div>
                    <InputLabel for="product" value="Produk (Opsional)" />
                    <TextInput
                        id="product"
                        type="text"
                        class="mt-1 block w-full"
                        v-model="form.product"
                    />
                    <InputError class="mt-2" :message="form.errors.product" />
                </div>

                <div class="flex items-center gap-4">
                    <PrimaryButton :disabled="form.processing"
                        >Simpan Tipe Dokumen</PrimaryButton
                    >
                    <Link
                        :href="route('master-doc.index')"
                        class="text-sm text-gray-600 hover:text-gray-900"
                    >
                        Batal
                    </Link>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
