<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { Head, Link } from "@inertiajs/vue3";

const props = defineProps({
    document: Object, // Menerima dokumen DAN revisinya
});

// Helper untuk format tanggal
const formatDate = (dateString) => {
    const options = {
        year: "numeric",
        month: "long",
        day: "numeric",
        hour: "2-digit",
        minute: "2-digit",
    };
    return new Date(dateString).toLocaleDateString("id-ID", options);
};
</script>

<style>
.preview-container {
    width: 100%;
    padding: 1rem;
    background-color: #f3f4f6;
}
.preview-a4 {
    width: 21cm;
    height: 29.7cm;
    padding: 1cm;
    margin: 0 auto;
    background: white;
    box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
    overflow: auto; /* Dibuat scrollable */
    display: flex;
    flex-direction: column;
}
.preview-body {
    flex: 1;
    margin-top: 2cm;
    word-wrap: break-word;
    overflow-wrap: break-word;
}
.preview-a4 .prose {
    max-width: 100%;
}
</style>

<template>
    <Head :title="'Riwayat: ' + document.title" />

    <AppLayout :title="'Riwayat: ' + document.title">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Riwayat Revisi untuk: {{ document.title }}
            </h2>
        </template>

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div
                v-for="revision in document.revisions"
                :key="revision.id"
                class="bg-white overflow-hidden shadow-sm sm:rounded-lg"
            >
                <details class="group">
                    <summary
                        class="flex justify-between items-center p-4 cursor-pointer group-open:border-b"
                    >
                        <div>
                            <span class="font-semibold text-gray-900"
                                >Revisi pada:
                                {{ formatDate(revision.created_at) }}</span
                            >
                            <span class="ml-4 text-gray-600"
                                >oleh: {{ revision.admin.full_name }}</span
                            >
                        </div>
                        <span class="text-indigo-600 group-open:hidden"
                            >Klik untuk melihat</span
                        >
                        <span class="text-indigo-600 hidden group-open:block"
                            >Menampilkan</span
                        >
                    </summary>

                    <div class="p-4 bg-gray-50">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <h3 class="font-bold text-lg text-red-600 mb-2">
                                    SEBELUM
                                </h3>
                                <div
                                    class="preview-container rounded-md border"
                                >
                                    <div
                                        class="preview-a4"
                                        style="
                                            height: 60vh;
                                            min-height: 400px;
                                            padding: 1cm;
                                        "
                                    >
                                        <div
                                            class="preview-body prose"
                                            v-html="revision.content_before"
                                        ></div>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <h3
                                    class="font-bold text-lg text-green-600 mb-2"
                                >
                                    SESUDAH
                                </h3>
                                <div
                                    class="preview-container rounded-md border"
                                >
                                    <div
                                        class="preview-a4"
                                        style="
                                            height: 60vh;
                                            min-height: 400px;
                                            padding: 1cm;
                                        "
                                    >
                                        <div
                                            class="preview-body prose"
                                            v-html="revision.content_after"
                                        ></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </details>
            </div>
        </div>
    </AppLayout>
</template>
