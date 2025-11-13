<script setup>
import { Head, Link, useForm } from "@inertiajs/vue3";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import Editor from "@tinymce/tinymce-vue";
import axios from "axios";

// --- PERUBAHAN 1: Menerima props 'template' dari controller ---
const props = defineProps({
    template: Object,
});

// Konfigurasi TinyMCE (Sama seperti Create.vue)
const tinyMceApiKey = "gkuqcbrv8r2wl46hyw02bydp4wt97r6qcsw3iex6ir3cdao2"; // Key Anda
const tinyMceConfig = (height = 300) => ({
    height: height,
    menubar: false,
    plugins: ["lists", "link", "image", "code", "table", "wordcount", "media"],
    toolbar:
        "undo redo | blocks | bold italic | alignleft aligncenter alignright | bullist numlist | link image media | code",
    automatic_uploads: true,
    images_upload_handler: (blobInfo, success, failure) => {
        const formData = new FormData();
        formData.append("file", blobInfo.blob(), blobInfo.filename());

        return axios
            .post(route("templates.upload_image"), formData)
            .then((res) => {
                success(res.data.location);
                return res.data.location;
            })
            .catch((err) => {
                failure("Upload gagal: " + err.message);
                return Promise.reject("Upload gagal: " + err.message);
            });
    },
});

// --- PERUBAHAN 2: Inisialisasi form dengan data dari props ---
const form = useForm({
    name: props.template.name,
    header_html: props.template.header_html || "", // Gunakan || '' untuk mengatasi nilai null
    footer_html: props.template.footer_html || "",
});

// --- PERUBAHAN 3: Fungsi submit diubah menjadi .patch() untuk update ---
const submit = () => {
    form.patch(route("templates.update", props.template.id), {
        onError: (errors) => {
            console.error("Error saat update:", errors);
            alert("Gagal update. Cek konsol.");
        },
    });
};
</script>

<style>
/* CSS A4 Preview (Sama seperti Create.vue) */
.page-container {
    width: 100%;
    padding: 2rem;
    background-color: #f3f4f6; /* Latar belakang abu-abu */
}
.page-a4 {
    width: 21cm; /* Lebar A4 */
    min-height: 29.7cm; /* Tinggi A4 */
    padding: 2cm; /* Margin standar dokumen */
    margin: 0 auto; /* Centering */
    background: white;
    box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}
.page-a4 .prose {
    max-width: 100%;
}
</style>

<template>
    <Head :title="'Edit Template: ' + form.name" />

    <div class="flex flex-col h-screen bg-gray-100 font-sans">
        <header
            class="bg-white p-4 flex justify-between items-center shrink-0 border-b border-gray-200 z-10"
        >
            <div class="flex items-center space-x-4">
                <Link
                    :href="route('templates.index')"
                    class="flex items-center space-x-2 text-gray-600 hover:text-gray-900"
                >
                    <svg
                        class="w-5 h-5"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke-width="1.5"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M15.75 19.5 8.25 12l7.5-7.5"
                        />
                    </svg>
                    <span class="text-sm font-medium"
                        >Kembali ke Templates</span
                    >
                </Link>
            </div>

            <div class="flex items-center space-x-4">
                <TextInput
                    id="name"
                    type="text"
                    class="text-lg font-semibold text-gray-800"
                    v-model="form.name"
                    required
                    autofocus
                    placeholder="Beri nama template..."
                />
                <InputError class="mt-2" :message="form.errors.name" />

                <PrimaryButton @click="submit" :disabled="form.processing">
                    {{ form.processing ? "Menyimpan..." : "Update Template" }}
                </PrimaryButton>
            </div>
        </header>

        <div
            class="flex-1 grid grid-cols-1 lg:grid-cols-4 gap-0 overflow-hidden"
        >
            <div class="page-container overflow-y-auto lg:col-span-3">
                <div class="page-a4">
                    <header>
                        <div
                            v-if="!form.header_html"
                            class="text-center text-gray-400 italic py-10 border-b border-dashed"
                        >
                            Header Preview
                        </div>
                        <div
                            v-else
                            v-html="form.header_html"
                            class="prose max-w-none"
                        ></div>
                    </header>

                    <div class="flex-1 text-gray-400 italic my-10">
                        <p>
                            (Konten dokumen Anda akan muncul di sini saat Anda
                            menggunakan template ini di menu "Document").
                        </p>
                    </div>

                    <footer>
                        <div
                            v-if="!form.footer_html"
                            class="text-center text-gray-400 italic py-10 border-t border-dashed"
                        >
                            Footer Preview
                        </div>
                        <div
                            v-else
                            v-html="form.footer_html"
                            class="prose max-w-none"
                        ></div>
                    </footer>
                </div>
            </div>

            <div class="bg-white overflow-y-auto shadow-sm lg:col-span-1">
                <div class="p-6 space-y-6">
                    <div>
                        <InputLabel
                            for="header_html"
                            value="Header HTML (Kop Surat)"
                        />
                        <p class="text-sm text-gray-500 mb-2">
                            Anda bisa upload gambar atau menempelkan URL gambar
                            langsung di editor.
                        </p>
                        <Editor
                            :api-key="tinyMceApiKey"
                            v-model="form.header_html"
                            :init="tinyMceConfig(300)"
                        />
                        <InputError
                            class="mt-2"
                            :message="form.errors.header_html"
                        />
                    </div>

                    <div>
                        <InputLabel
                            for="footer_html"
                            value="Footer HTML (Kaki Surat)"
                        />
                        <p class="text-sm text-gray-500 mb-2">
                            Anda bisa upload gambar atau menempelkan URL gambar
                            langsung di editor.
                        </p>
                        <Editor
                            :api-key="tinyMceApiKey"
                            v-model="form.footer_html"
                            :init="tinyMceConfig(200)"
                        />
                        <InputError
                            class="mt-2"
                            :message="form.errors.footer_html"
                        />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
