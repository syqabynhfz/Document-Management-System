<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { Head, Link, router } from "@inertiajs/vue3";

defineProps({
    documentHistories: {
        type: Array,
        default: () => [],
    },
});

// 2. Fungsi untuk format tanggal
const formatDate = (dateString) => {
    if (!dateString) return "N/A";
    const options = {
        day: "numeric",
        month: "short",
        year: "numeric",
        hour: "2-digit",
        minute: "2-digit",
    };
    return new Date(dateString).toLocaleDateString("id-ID", options);
};

const deleteHistory = (id, name) => {
    if (
        window.confirm(`Apakah Anda yakin ingin menghapus riwayat '${name}'?`)
    ) {
        router.delete(route("history.destroy", id), {});
    }
};
</script>

<template>
    <AppLayout title="History">
        <div class="flex justify-between items-center mb-6">
            <button
                class="bg-white text-gray-800 font-semibold py-2 px-4 border border-gray-300 rounded-lg shadow-sm hover:bg-gray-50 inline-flex items-center"
            >
                Eksport
                <svg
                    class="w-4 h-4 ml-2"
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                >
                    <path
                        fill-rule="evenodd"
                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                        clip-rule="evenodd"
                    />
                </svg>
            </button>
            <div class="flex items-center space-x-2">
                <button
                    class="bg-white text-gray-800 font-semibold py-2 px-4 border border-gray-300 rounded-lg shadow-sm hover:bg-gray-50 inline-flex items-center"
                >
                    Tanggal
                    <svg
                        class="w-4 h-4 ml-2"
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                    >
                        <path
                            fill-rule="evenodd"
                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                            clip-rule="evenodd"
                        />
                    </svg>
                </button>
                <div class="relative">
                    <input
                        type="text"
                        placeholder="Search..."
                        class="w-full py-2 pl-4 pr-10 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500"
                    />
                    <button
                        class="absolute inset-y-0 right-0 flex items-center px-3 bg-teal-600 rounded-r-lg text-white"
                    >
                        <svg
                            class="w-5 h-5"
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 20 20"
                            fill="currentColor"
                        >
                            <path
                                fill-rule="evenodd"
                                d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z"
                                clip-rule="evenodd"
                            />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden rounded-lg shadow-sm">
            <div
                class="grid grid-cols-12 gap-4 text-sm font-semibold text-gray-600 bg-gray-50 p-4 border-b"
            >
                <div class="col-span-1">No</div>
                <div class="col-span-4">Name</div>
                <div class="col-span-3">Template</div>
                <div class="col-span-2">Created Date</div>
                <div class="col-span-2 text-right">Action</div>
            </div>

            <div v-if="documentHistories.length > 0">
                <div
                    v-for="(history, index) in documentHistories"
                    :key="history.id"
                    class="grid grid-cols-12 gap-4 items-center p-4 border-b last:border-b-0 hover:bg-gray-50"
                >
                    <div class="col-span-1 text-gray-500">{{ index + 1 }}</div>
                    <div class="col-span-4 font-medium text-gray-800">
                        {{ history.name }}
                    </div>
                    <div class="col-span-3 text-gray-500">
                        {{ history.template ? history.template.name : "N/A" }}
                    </div>
                    <div class="col-span-2 text-gray-500">
                        {{ formatDate(history.created_at) }}
                    </div>
                    <div class="col-span-2 flex justify-end space-x-2">
                        <Link
                            :href="route('history.download', history.id)"
                            class="text-green-600 hover:text-green-800 p-1"
                            title="Download"
                        >
                            <svg
                                class="w-5 h-5"
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                            >
                                <path
                                    d="M10.75 2.75a.75.75 0 00-1.5 0v8.614L6.28 8.28a.75.75 0 10-1.06 1.06l4.25 4.25a.75.75 0 001.06 0l4.25-4.25a.75.75 0 10-1.06-1.06l-2.97 2.97V2.75z"
                                />
                                <path
                                    d="M3.5 12.75a.75.75 0 00-1.5 0v2.5A2.75 2.75 0 004.75 18h10.5A2.75 2.75 0 0018 15.25v-2.5a.75.75 0 00-1.5 0v2.5c0 .69-.56 1.25-1.25 1.25H4.75c-.69 0-1.25-.56-1.25-1.25v-2.5z"
                                />
                            </svg>
                        </Link>
                        <button
                            class="text-blue-500 hover:text-blue-700 p-1"
                            title="Edit"
                        >
                            <svg
                                class="w-5 h-5"
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                            >
                                <path
                                    d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z"
                                />
                                <path
                                    fill-rule="evenodd"
                                    d="M2 6a2 2 0 012-2h4a1 1 0 010 2H4v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"
                                    clip-rule="evenodd"
                                />
                            </svg>
                        </button>
                        <button
                            @click="deleteHistory(history.id, history.name)"
                            type="button"
                            class="text-red-500 hover:text-red-800 p-1 rounded hover:bg-red-100"
                            title="Delete"
                        >
                            <svg
                                class="w-5 h-5"
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                            >
                                <path
                                    fill-rule="evenodd"
                                    d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                    clip-rule="evenodd"
                                />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
            <div v-else class="p-6 text-center text-gray-500 italic">
                Belum ada riwayat dokumen yang tersimpan.
            </div>
        </div>
    </AppLayout>
</template>
