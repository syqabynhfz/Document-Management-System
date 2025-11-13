<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";

// Terima data 'docType' dari controller
const props = defineProps({
    docType: Object,
});

// Inisialisasi form dengan data yang ada
const form = useForm({
    type_name: props.docType.type_name,
    product: props.docType.product || "",
});

// Fungsi submit untuk update (PATCH)
const submit = () => {
    form.patch(route("master-doc.update", props.docType.id));
};
</script>

<template>
    <Head :title="'Edit Tipe: ' + form.type_name" />

    <AppLayout :title="'Edit Tipe: ' + form.type_name">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Edit Tipe Dokumen (Master Doc)
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
                        >Update Tipe Dokumen</PrimaryButton
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
