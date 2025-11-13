<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { Head, Link, usePage } from "@inertiajs/vue3";
import { computed } from "vue";
import PrimaryButton from "@/Components/PrimaryButton.vue"; // <-- Menggunakan PrimaryButton

const props = defineProps({
    templates: Array,
});

// PERBAIKAN: Menggunakan optional chaining (?.) agar tidak crash jika flash null
const successMessage = computed(() => usePage().props.flash?.success);
const errorMessage = computed(() => usePage().props.flash?.error);
</script>

<template>
    <Head title="Templates" />

    <AppLayout title="Templates">
        <div
            v-if="successMessage"
            class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg shadow-sm"
        >
            {{ successMessage }}
        </div>

        <div
            v-if="errorMessage"
            class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg shadow-sm"
        >
            {{ errorMessage }}
        </div>

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold">Manajemen Template</h3>
                    <Link
                        :href="route('templates.create')"
                        class="bg-teal-500 text-white font-semibold py-2 px-4 border border-gray-300 rounded-lg shadow-sm hover:bg-teal-700 inline-flex items-center"
                    >
                        + Buat Template Baru
                    </Link>
                </div>

                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th
                                scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                            >
                                Nama Template
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
                        <tr v-if="templates.length === 0">
                            <td
                                colspan="3"
                                class="px-6 py-4 text-center text-gray-500"
                            >
                                Belum ada template.
                            </td>
                        </tr>
                        <tr v-for="template in templates" :key="template.id">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ template.name }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-500">
                                    {{ template.created_at }}
                                </div>
                            </td>
                            <td
                                class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2"
                            >
                                <Link
                                    :href="route('templates.edit', template.id)"
                                    class="text-indigo-600 hover:text-indigo-900"
                                    >Edit</Link
                                >
                                <Link
                                    :href="
                                        route('templates.destroy', template.id)
                                    "
                                    method="delete"
                                    as="button"
                                    class="text-red-600 hover:text-red-900"
                                    onclick="return confirm('Apakah Anda yakin ingin menghapus template ini?')"
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
