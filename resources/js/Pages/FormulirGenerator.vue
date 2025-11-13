<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { useForm } from "@inertiajs/vue3";
import TextInput from "@/Components/TextInput.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextAreaInput from "@/Components/TextAreaInput.vue";
import InputError from "@/Components/InputError.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import DangerButton from "@/Components/DangerButton.vue";
import { ref } from "vue";

// Menerima data template dan custom fields dari controller
const props = defineProps({
    template: {
        type: Object,
        required: true,
    },
    customFields: {
        type: Array,
        default: () => [],
    },
});

// --- PERBAIKAN LOGIKA FORM ---
// Siapkan objek kosong untuk form data
const initialFormData = {};
// Isi objek form data berdasarkan custom fields yang diterima
props.customFields.forEach((field) => {
    if (field.field_type === "table") {
        // Jika tipenya tabel, inisialisasi sebagai array kosong
        initialFormData[field.field_name] = [];
    } else {
        // Jika tipe lain, inisialisasi sebagai string kosong
        initialFormData[field.field_name] = "";
    }
});

// Gunakan useForm dari Inertia untuk mengelola state isian formulir
// Kita bungkus semua data di dalam 'formData' agar rapi
const form = useForm({
    formData: initialFormData,
});

// --- LOGIKA TABEL INTERAKTIF ---
// PENTING: Sesuaikan nama kolom ('uraian', 'qty', 'harga') ini
// agar sama dengan yang ada di GeneratorController Anda
const addTableRow = (fieldName) => {
    form.formData[fieldName].push({
        uraian: "",
        qty: 1,
        harga: 0,
    });
};

const removeTableRow = (fieldName, index) => {
    form.formData[fieldName].splice(index, 1);
};

// --- LOGIKA SUBMIT FORM (POST) ---
// Ini adalah cara yang benar untuk men-submit form di Inertia
const submitForm = () => {
    form.post(route("generator.generate", props.template.id), {
        responseType: "blob", // Penting agar browser bisa menangani download PDF
        onSuccess: () => {
            // Browser akan otomatis memicu download
        },
        onError: (errors) => {
            console.error("Gagal men-generate dokumen:", errors);
        },
    });
};

// Ref untuk menyimpan HTML preview
// Ini hanya menampilkan HTML awal, tidak reaktif terhadap isian form.
// Membuatnya reaktif sangat kompleks dan butuh route preview khusus.
const previewHtml = ref(props.template.body_html);
</script>

<template>
    <AppLayout
        :title="
            'Generate Document : ' + (template ? template.name : 'Loading...')
        "
    >
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Formulir Generator: {{ template.name }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-screen-2xl mx-auto sm:px-6 lg:px-8">
                <!-- === TATA LETAK DUA PANEL (GRID) === -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- === PANEL KIRI: PREVIEW === -->
                    <div class="bg-white p-6 rounded-lg shadow-lg">
                        <h3 class="text-xl font-semibold mb-4 text-gray-900">
                            Letter Preview
                        </h3>
                        <div
                            class="border border-gray-300 rounded-lg p-6 h-[80vh] overflow-y-auto"
                        >
                            <!-- 
                                Ini hanya menampilkan HTML dasar dari template.
                                Untuk preview real-time, diperlukan logika yang jauh lebih kompleks.
                            -->
                            <div
                                v-html="previewHtml"
                                class="prose max-w-none"
                            ></div>
                        </div>
                    </div>

                    <!-- === PANEL KANAN: FORMULIR === -->
                    <div class="bg-white p-6 rounded-lg shadow-lg">
                        <h3 class="text-xl font-semibold mb-4 text-gray-900">
                            Document Creation Form
                        </h3>

                        <form @submit.prevent="submitForm" class="space-y-6">
                            <div v-for="field in customFields" :key="field.id">
                                <InputLabel :for="field.field_name">
                                    {{ field.field_label }}
                                    <span
                                        v-if="field.is_required"
                                        class="text-red-500 ml-1"
                                        >*</span
                                    >
                                </InputLabel>

                                <!-- === FORM INPUT BIASA (TEXT, DATE, NUMBER) === -->
                                <TextInput
                                    v-if="
                                        field.field_type === 'text' ||
                                        field.field_type === 'number' ||
                                        field.field_type === 'date'
                                    "
                                    :id="field.field_name"
                                    :type="
                                        field.field_type === 'date'
                                            ? 'date'
                                            : field.field_type === 'number'
                                            ? 'number'
                                            : 'text'
                                    "
                                    class="mt-1 block w-full"
                                    v-model="form.formData[field.field_name]"
                                    :required="field.is_required"
                                />

                                <!-- === FORM INPUT TEXTAREA === -->
                                <TextAreaInput
                                    v-else-if="field.field_type === 'textarea'"
                                    :id="field.field_name"
                                    class="mt-1 block w-full"
                                    v-model="form.formData[field.field_name]"
                                    :required="field.is_required"
                                    rows="4"
                                />

                                <!-- === FORM TABEL INTERAKTIF === -->
                                <div
                                    v-else-if="field.field_type === 'table'"
                                    class="border rounded-lg p-4 mt-1"
                                >
                                    <div class="overflow-x-auto">
                                        <table
                                            class="min-w-full divide-y divide-gray-200"
                                        >
                                            <thead class="bg-gray-50">
                                                <tr>
                                                    <!-- PENTING: Sesuaikan header tabel ini -->
                                                    <th
                                                        class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase"
                                                    >
                                                        Uraian
                                                    </th>
                                                    <th
                                                        class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase w-20"
                                                    >
                                                        Qty
                                                    </th>
                                                    <th
                                                        class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase w-36"
                                                    >
                                                        Harga
                                                    </th>
                                                    <th
                                                        class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase w-16"
                                                    >
                                                        Aksi
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody
                                                class="bg-white divide-y divide-gray-200"
                                            >
                                                <tr
                                                    v-if="
                                                        form.formData[
                                                            field.field_name
                                                        ].length === 0
                                                    "
                                                >
                                                    <td
                                                        colspan="4"
                                                        class="px-3 py-3 text-center text-sm text-gray-500"
                                                    >
                                                        Belum ada item.
                                                    </td>
                                                </tr>
                                                <tr
                                                    v-for="(row, index) in form
                                                        .formData[
                                                        field.field_name
                                                    ]"
                                                    :key="index"
                                                >
                                                    <!-- PENTING: Sesuaikan v-model properti ini (uraian, qty, harga) -->
                                                    <td class="px-1 py-1">
                                                        <TextInput
                                                            v-model="row.uraian"
                                                            class="w-full text-sm"
                                                            placeholder="Nama item..."
                                                        />
                                                    </td>
                                                    <td class="px-1 py-1">
                                                        <TextInput
                                                            v-model="row.qty"
                                                            type="number"
                                                            class="w-full text-sm"
                                                            placeholder="1"
                                                        />
                                                    </td>
                                                    <td class="px-1 py-1">
                                                        <TextInput
                                                            v-model="row.harga"
                                                            type="number"
                                                            class="w-full text-sm"
                                                            placeholder="0"
                                                        />
                                                    </td>
                                                    <td class="px-1 py-1">
                                                        <DangerButton
                                                            @click.prevent="
                                                                removeTableRow(
                                                                    field.field_name,
                                                                    index
                                                                )
                                                            "
                                                        >
                                                            X
                                                        </DangerButton>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <SecondaryButton
                                        @click.prevent="
                                            addTableRow(field.field_name)
                                        "
                                        class="mt-2"
                                    >
                                        + Tambah Baris
                                    </SecondaryButton>
                                </div>

                                <!-- Menampilkan error validasi -->
                                <InputError
                                    class="mt-1"
                                    :message="
                                        form.errors[
                                            `formData.${field.field_name}`
                                        ]
                                    "
                                />
                            </div>

                            <div class="flex justify-end pt-4 border-t mt-6">
                                <!-- Ganti <a> dengan <PrimaryButton> untuk submit form POST -->
                                <PrimaryButton
                                    type="submit"
                                    :class="{ 'opacity-25': form.processing }"
                                    :disabled="form.processing"
                                >
                                    {{
                                        form.processing
                                            ? "Memproses..."
                                            : "Generate PDF"
                                    }}
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
