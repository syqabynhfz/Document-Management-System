<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'; 
import { Head, Link } from '@inertiajs/vue3';

defineProps
({
    userName: String,
    totalDocuments: Number,
    totalTemplates: Number,
    recentDocuments: Array,
});

</script>

<template>
    <AppLayout title="Dashboard">

        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl text-gray-700">Welcome, {{ userName }} !</h2>
           <Link
                :href="route('templates.index')"
                class="bg-white text-gray-800 font-semibold py-2 px-4 border border-gray-300 rounded-lg shadow-sm hover:bg-gray-50 inline-flex items-center"
            >
                <svg class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z" /></svg>
                Add New
            </Link>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-1 space-y-6">
                <div class="bg-white p-6 rounded-lg shadow-sm">
                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="text-gray-500">Total Document</h3>
                            <p class="text-4xl font-bold text-teal-600 mt-2">{{ totalDocuments }}</p>
                        </div>
                        <div class="p-2 bg-gray-100 rounded-lg">
                            <svg class="w-6 h-6 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M3.75 5.25h16.5m-16.5 4.5h16.5m-16.5 4.5h16.5m-16.5 4.5h16.5" />
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-sm">
                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="text-gray-500">Total Template</h3>
                            <p class="text-4xl font-bold text-teal-600 mt-2">{{ totalTemplates }}</p>
                        </div>
                        <div class="p-2 bg-gray-100 rounded-lg">
                            <svg class="w-6 h-6 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m5.231 13.481L15 17.25m-4.5-1.845l-1.259 1.259A2.25 2.25 0 0110.5 18h-1.5a2.25 2.25 0 01-2.25-2.25v-1.5a2.25 2.25 0 012.25-2.25H8.25m6.75 0l-1.259 1.259m0 0A2.25 2.25 0 0112 18h-1.5a2.25 2.25 0 01-2.25-2.25v-1.5a2.25 2.25 0 012.25-2.25H8.25" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-2 bg-white p-6 rounded-lg shadow-sm">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Daftar Document Terbaru</h3>
                <div class="overflow-x-auto">
                    <div class="grid grid-cols-3 gap-4 text-sm font-semibold text-gray-500 border-b pb-2">
                        <div class="col-span-1">Nama Dokumen</div>
                        <div class="col-span-1">Created Date</div>
                        <div class="col-span-1 text-right">Action</div>
                    </div>
                    <div class="mt-4 space-y-4">
                        <div v-for="doc in recentDocuments" :key="doc.id"
                            class="grid grid-cols-3 gap-4 items-center">
                            <div class="col-span-1 text-gray-800 font-medium">{{ doc.name }}</div>
                            <div class="col-span-1 text-gray-500">{{ doc.created_at }}</div>
                            <div class="col-span-1 text-right">
                                <button
                                    class="bg-red-500 text-white text-sm font-semibold py-2 px-4 rounded-lg hover:bg-red-600">
                                    Unduh PDF
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>