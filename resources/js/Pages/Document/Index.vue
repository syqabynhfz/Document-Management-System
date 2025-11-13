<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { Head, Link, usePage } from "@inertiajs/vue3";
import { computed } from "vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";

const props = defineProps({
    documents: Array,
});

const successMessage = computed(() => usePage().props.flash?.success);
</script>

<template>
    <Head title="Document" />

    <AppLayout title="Document">
        <div
            v-if="successMessage"
            class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg shadow-sm"
        >
            {{ successMessage }}
        </div>

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-semibold">Manajemen Dokumen</h2>
                    <Link
                        :href="route('document.create')"
                        class="bg-teal-500 text-white font-semibold py-2 px-4 border border-gray-300 rounded-lg shadow-sm hover:bg-teal-700 inline-flex items-center"
                    >
                        + Buat Dokumen Baru
                    </Link>
                </div>
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th
                                scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                            >
                                Judul Dokumen
                            </th>
                            <th
                                scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                            >
                                Tipe (Master Doc)
                            </th>
                            <th
                                scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                            >
                                Penerima (Master To)
                            </th>
                            <th
                                scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                            >
                                Tanggal Dibuat
                            </th>
                            <th
                                scope="col"
                                class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider"
                            >
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-if="documents.length === 0">
                            <td
                                colspan="5"
                                class="px-6 py-4 text-center text-gray-500"
                            >
                                Belum ada dokumen yang dibuat.
                            </td>
                        </tr>
                        <tr v-for="doc in documents" :key="doc.id">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ doc.title }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-500">
                                    {{ doc.doctype_name }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-500">
                                    {{ doc.vendor_name }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-500">
                                    {{ doc.created_at }}
                                </div>
                            </td>
                            <td
                                class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2"
                            >
                                <a
                                    :href="route('document.download', doc.id)"
                                    class="text-green-600 hover:text-green-900"
                                >
                                    Download
                                </a>

                                <Link
                                    :href="route('document.edit', doc.id)"
                                    class="text-indigo-600 hover:text-indigo-900"
                                    >Edit</Link
                                >
                                <Link
                                    :href="route('document.destroy', doc.id)"
                                    method="delete"
                                    as="button"
                                    class="text-red-600 hover:text-red-900"
                                    onclick="return confirm('Apakah Anda yakin ingin menghapus dokumen ini?')"
                                >
                                    Hapus
                                </Link>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AppLayout>
</template>
