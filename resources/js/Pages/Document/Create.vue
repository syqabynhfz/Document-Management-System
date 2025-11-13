<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";

// Menerima data master dari DocumentController@create
const props = defineProps({
    templates: Array,
    vendors: Array,
    docTypes: Array,
});

// Inisialisasi form (tanpa body_content)
const form = useForm({
    title: "",
    template_id: null,
    vendor_id: null,
    doctype_id: null,
});

// Fungsi submit (akan redirect ke halaman Edit)
const submit = () => {
    form.post(route("document.store"));
};
</script>

<template>
    <Head title="Buat Dokumen Baru" />

    <AppLayout title="Buat Dokumen Baru (Langkah 1 dari 2)">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Detail Dokumen
            </h2>
        </template>

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <form
                @submit.prevent="submit"
                class="p-6 space-y-6 max-w-2xl mx-auto"
            >
                <p class="text-sm text-gray-600">
                    Isi detail dokumen terlebih dahulu. Anda akan mengisi konten
                    (isi surat) di langkah berikutnya.
                </p>

                <div>
                    <InputLabel for="title" value="Judul Dokumen" />
                    <TextInput
                        id="title"
                        type="text"
                        class="mt-1 block w-full"
                        v-model="form.title"
                        required
                        autofocus
                    />
                    <InputError class="mt-2" :message="form.errors.title" />
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <InputLabel for="template_id" value="Select Template" />
                        <select
                            id="template_id"
                            v-model="form.template_id"
                            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                            required
                        >
                            <option :value="null" disabled>
                                -- Pilih Template --
                            </option>
                            <option
                                v-for="template in templates"
                                :key="template.id"
                                :value="template.id"
                            >
                                {{ template.name }}
                            </option>
                        </select>
                        <InputError
                            class="mt-2"
                            :message="form.errors.template_id"
                        />
                    </div>

                    <div>
                        <InputLabel
                            for="vendor_id"
                            value="Select To (Vendor)"
                        />
                        <select
                            id="vendor_id"
                            v-model="form.vendor_id"
                            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                            required
                        >
                            <option :value="null" disabled>
                                -- Pilih Vendor --
                            </option>
                            <option
                                v-for="vendor in vendors"
                                :key="vendor.id"
                                :value="vendor.id"
                            >
                                {{ vendor.name }}
                            </option>
                        </select>
                        <InputError
                            class="mt-2"
                            :message="form.errors.vendor_id"
                        />
                    </div>

                    <div>
                        <InputLabel
                            for="doctype_id"
                            value="Select Tipe (Master Doc)"
                        />
                        <select
                            id="doctype_id"
                            v-model="form.doctype_id"
                            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                            required
                        >
                            <option :value="null" disabled>
                                -- Pilih Tipe Dokumen --
                            </option>
                            <option
                                v-for="docType in docTypes"
                                :key="docType.id"
                                :value="docType.id"
                            >
                                {{ docType.type_name }}
                            </option>
                        </select>
                        <InputError
                            class="mt-2"
                            :message="form.errors.doctype_id"
                        />
                    </div>
                </div>

                <div class="flex items-center gap-4 border-t pt-6">
                    <PrimaryButton :disabled="form.processing">
                        {{
                            form.processing
                                ? "Menyimpan..."
                                : "Lanjut ke Editor"
                        }}
                    </PrimaryButton>
                    <Link
                        :href="route('document.index')"
                        class="text-sm text-gray-600 hover:text-gray-900"
                    >
                        Batal
                    </Link>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
