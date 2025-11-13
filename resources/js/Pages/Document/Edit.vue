<script setup>
import { Head, Link, useForm, usePage } from "@inertiajs/vue3";
import { computed } from "vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import Editor from "@tinymce/tinymce-vue";
import axios from "axios";

// Menerima data (termasuk 'pages' dan 'template')
const props = defineProps({
    document: Object,
    templates: Array,
    vendors: Array,
    docTypes: Array,
});

const successMessage = computed(() => usePage().props.flash?.success);

// Konfigurasi TinyMCE
const tinyMceApiKey = "gkuqcbrv8r2wl46hyw02bydp4wt97r6qcsw3iex6ir3cdao2";
const tinyMceConfig = {
    height: "100%", // Tinggi standar editor
    menubar: true,
    plugins: [
        "lists",
        "link",
        "image",
        "code",
        "table",
        "wordcount",
        "media",
        "pagebreak",
    ],
    toolbar:
        "undo redo | blocks | bold italic underline | " +
        "alignleft aligncenter alignright alignjustify | " +
        "bullist numlist | link image media | table | code | pagebreak",
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
};

// Inisialisasi form
const form = useForm({
    title: props.document.title,
    template_id: props.document.template_id,
    vendor_id: props.document.vendor_id,
    doctype_id: props.document.doctype_id,
    pages: props.document.pages, // Array halaman
});

// --- Logika Header/Footer ---
const selectedTemplate = computed(() => {
    // Kita gunakan data dari 'document.template' yang sudah di-load
    return props.document.template;
});

const selectedHeader = computed(() => {
    return selectedTemplate.value
        ? selectedTemplate.value.header_html || ""
        : "";
});

const selectedFooter = computed(() => {
    return selectedTemplate.value
        ? selectedTemplate.value.footer_html || ""
        : "";
});
// -----------------------------

// --- Logika Multi-Halaman ---
const addPage = () => {
    form.pages.push({
        id: null, // ID null menandakan halaman baru
        content: `<p>Ini adalah halaman ${form.pages.length + 1}</p>`,
    });
};

const removePage = (index) => {
    if (form.pages.length <= 1) {
        alert("Tidak bisa menghapus halaman terakhir.");
        return;
    }
    if (confirm("Apakah Anda yakin ingin menghapus halaman ini?")) {
        form.pages.splice(index, 1);
    }
};
// --------------------------

// Fungsi submit
const submit = () => {
    form.patch(route("document.update", props.document.id));
};
</script>

<style>
.page-container {
    width: 100%;
    padding: 2rem;
    background-color: #f3f4f6;
}
.page-a4 {
    width: 21cm;
    height: 29.7cm; /* <-- PERBAIKAN 1: Diubah dari min-height */
    padding: 1cm;
    margin: 0 auto 2rem auto; /* Jarak antar kertas */
    background: white;
    box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
    overflow: hidden; /* Konten yang meluap akan di-handle oleh .page-a4-body */
    display: flex;
    flex-direction: column;
}
.page-a4-body {
    flex: 1;
    margin-top: 0cm; /* Jarak dari header (sesuai PDF) */
    overflow-y: auto; /* <-- PERBAIKAN 2: Tambahkan scroll di SINI */
    position: relative; /* <-- Tambahan untuk stabilitas editor */
}
.page-a4 .prose {
    max-width: 100%;
}
</style>

<template>
    <Head :title="'Edit Dokumen: ' + form.title" />

    <div class="flex flex-col h-screen bg-gray-100 font-sans">
        <header
            class="bg-white p-4 flex justify-between items-center shrink-0 border-b border-gray-200 z-10"
        >
            <div class="flex items-center space-x-4">
                <Link
                    :href="route('document.index')"
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
                    <span class="text-sm font-medium">Kembali ke Dokumen</span>
                </Link>
            </div>

            <div class="flex items-center space-x-4">
                <h1 class="text-lg font-semibold text-gray-800">
                    {{ form.title }}
                </h1>
            </div>

            <div class="flex items-center space-x-4">
                <div
                    v-if="successMessage"
                    class="text-sm font-medium text-green-600"
                >
                    {{ successMessage }}
                </div>
                <PrimaryButton @click="submit" :disabled="form.processing">
                    {{ form.processing ? "Menyimpan..." : "Update Dokumen" }}
                </PrimaryButton>
            </div>
        </header>

        <div
            class="flex-1 grid grid-cols-1 lg:grid-cols-4 gap-0 overflow-hidden"
        >
            <div class="page-container overflow-y-auto lg:col-span-3">
                <div
                    v-for="(page, index) in form.pages"
                    :key="page.id || `new-${index}`"
                >
                    <div class="page-a4">
                        <header v-if="selectedHeader">
                            <div
                                v-html="selectedHeader"
                                class="prose max-w-none"
                            ></div>
                        </header>

                        <div class="page-a4-body">
                            <Editor
                                :id="`editor-${page.id || index}`"
                                :api-key="tinyMceApiKey"
                                v-model="page.content"
                                :init="tinyMceConfig"
                            />
                        </div>

                        <footer v-if="selectedFooter" class="mt-auto pt-4">
                            <div
                                v-html="selectedFooter"
                                class="prose max-w-none"
                            ></div>
                        </footer>
                    </div>

                    <div class="text-center my-4" v-if="form.pages.length > 1">
                        <DangerButton @click="removePage(index)">
                            Hapus Halaman {{ index + 1 }}
                        </DangerButton>
                    </div>
                </div>

                <div class="flex justify-center mt-4">
                    <SecondaryButton @click="addPage">
                        + Tambah Halaman Baru
                    </SecondaryButton>
                </div>
            </div>

            <div class="bg-white overflow-y-auto shadow-sm lg:col-span-1">
                <form @submit.prevent="submit" class="p-6 space-y-6">
                    <div>
                        <InputLabel for="title" value="Judul Dokumen" />
                        <TextInput
                            id="title"
                            type="text"
                            class="mt-1 block w-full"
                            v-model="form.title"
                            required
                        />
                        <InputError class="mt-2" :message="form.errors.title" />
                    </div>

                    <div>
                        <InputLabel for="template_id" value="Select Template" />
                        <select
                            id="template_id"
                            v-model="form.template_id"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                            required
                        >
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
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                            required
                        >
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
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                            required
                        >
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
                </form>
            </div>
        </div>
    </div>
</template>
