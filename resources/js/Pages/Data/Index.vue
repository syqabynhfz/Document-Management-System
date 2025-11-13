<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { Head, Link, usePage } from "@inertiajs/vue3";
import { computed } from "vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";

const props = defineProps({
    vendors: Array,
});

const successMessage = computed(() => usePage().props.flash?.success);
const errorMessage = computed(() => usePage().props.flash?.error);
</script>

<template>
    <Head title="Data (Master To)" />

    <AppLayout title="Data (Master To)">
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
                    <h3 class="text-lg font-semibold">
                        Manajemen Vendor (Master To)
                    </h3>
                    <Link
                        :href="route('data.create')"
                        class="bg-teal-500 text-white font-semibold py-2 px-4 border border-gray-300 rounded-lg shadow-sm hover:bg-teal-700 inline-flex items-center"
                    >
                        + Tambah Vendor Baru
                    </Link>
                </div>

                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th
                                scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                            >
                                Nama Vendor
                            </th>
                            <th
                                scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                            >
                                Alamat
                            </th>
                            <th
                                scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                            >
                                Kontak
                            </th>
                            <th
                                scope="col"
                                class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider"
                            >
                                Total Dokumen (TO)
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
                        <tr v-if="vendors.length === 0">
                            <td
                                colspan="4"
                                class="px-6 py-4 text-center text-gray-500"
                            >
                                Belum ada data vendor.
                            </td>
                        </tr>
                        <tr v-for="vendor in vendors" :key="vendor.id">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ vendor.name }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-500">
                                    {{ vendor.address || "-" }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-500">
                                    {{ vendor.contact_person || "-" }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <div
                                    class="text-sm font-semibold text-gray-900"
                                >
                                    {{ vendor.to_count }}
                                </div>
                            </td>
                            <td
                                class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2"
                            >
                                <Link
                                    :href="route('data.edit', vendor.id)"
                                    class="text-indigo-600 hover:text-indigo-900"
                                    >Edit</Link
                                >
                                <Link
                                    :href="route('data.destroy', vendor.id)"
                                    method="delete"
                                    as="button"
                                    class="text-red-600 hover:text-red-900"
                                    onclick="return confirm('Apakah Anda yakin ingin menghapus vendor ini?')"
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
