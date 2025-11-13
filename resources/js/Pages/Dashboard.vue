<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { Head, Link, usePage } from "@inertiajs/vue3";
import { computed } from "vue";
import { Pie } from "vue-chartjs";
import {
    Chart as ChartJS,
    Title,
    Tooltip,
    Legend,
    ArcElement,
    CategoryScale,
} from "chart.js";

// --- TAMBAHAN 1: Impor plugin datalabels ---
import ChartDataLabels from "chartjs-plugin-datalabels";

// Daftarkan komponen Chart.js YANG BARU
ChartJS.register(
    Title,
    Tooltip,
    Legend,
    ArcElement,
    CategoryScale,
    ChartDataLabels
);
// ------------------------------------

// Menerima props
const props = defineProps({
    totalDocuments: Number,
    totalTemplates: Number,
    masterDocData: Array,
    masterToData: Array,
});

// Mengambil nama admin
const adminName = computed(
    () => usePage().props.auth.user.full_name || "Admin"
);

// --- Data untuk Grafik (Tidak Berubah) ---
const pieColors1 = ["#14b8a6", "#f97316", "#3b82f6", "#ec4899", "#fde047"];
const pieColors2 = ["#3b82f6", "#a855f7", "#ef4444", "#eab308", "#22c55e"];

const masterDocChartData = computed(() => ({
    labels: props.masterDocData.map((item) => item.type_name),
    datasets: [
        {
            label: "Total Penggunaan",
            backgroundColor: props.masterDocData.map(
                (_, i) => pieColors1[i % pieColors1.length]
            ),
            data: props.masterDocData.map((item) => item.documents_count),
        },
    ],
}));

const masterToChartData = computed(() => ({
    labels: props.masterToData.map((item) => item.name),
    datasets: [
        {
            label: "Total Dokumen",
            backgroundColor: props.masterToData.map(
                (_, i) => pieColors2[i % pieColors2.length]
            ),
            data: props.masterToData.map((item) => item.documents_count),
        },
    ],
}));
// ------------------------------------

// --- TAMBAHAN 3: Perbarui chartOptions ---
const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            position: "right", // Legenda di kanan
        },
        // Konfigurasi baru untuk menampilkan angka
        datalabels: {
            display: true,
            color: "#ffffff", // Warna angka (putih)
            font: {
                weight: "bold",
                size: 14,
            },
            // Formatter untuk menampilkan nilainya saja
            formatter: (value, context) => {
                return value;
            },
        },
    },
};
// ------------------------------------
</script>

<template>
    <AppLayout title="Dashboard">
        <template #header>
            <div class="flex justify-between items-center w-full">
                <h2 class="text-xl text-gray-700">
                    Welcome, {{ adminName }} !
                </h2>
                <Link
                    :href="route('document.create')"
                    class="bg-white text-gray-800 font-semibold py-2 px-4 border border-gray-300 rounded-lg shadow-sm hover:bg-gray-50 inline-flex items-center"
                >
                    <svg
                        class="w-4 h-4 mr-2"
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                    >
                        <path
                            d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z"
                        />
                    </svg>
                    Buat Dokumen Baru
                </Link>
            </div>
        </template>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div class="bg-white p-6 rounded-lg shadow-sm">
                <div class="flex justify-between items-start">
                    <div>
                        <h3 class="text-md font-semibold text-gray-900 mb-4">
                            Total Document
                        </h3>
                        <p class="text-4xl font-bold text-teal-600 mt-2">
                            {{ totalDocuments }}
                        </p>
                    </div>
                    <div class="p-2">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            width="36"
                            height="36"
                            viewBox="0 0 24 24"
                        >
                            <path
                                fill="#14B8A6"
                                d="M4 22V2h10l6 6v2.1l-8 7.975V22zm10 0v-3.075l6.575-6.55l3.075 3.05L17.075 22zm6.575-5.6l.925-.975l-.925-.925l-.95.95zM13 9h5l-5-5l5 5l-5-5z"
                            />
                        </svg>
                    </div>
                </div>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-sm">
                <div class="flex justify-between items-start">
                    <div>
                        <h3 class="text-md font-semibold text-gray-900 mb-4">
                            Total Template
                        </h3>
                        <p class="text-4xl font-bold text-teal-600 mt-2">
                            {{ totalTemplates }}
                        </p>
                    </div>
                    <div class="p-2">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            width="36"
                            height="36"
                            viewBox="0 0 16 16"
                        >
                            <path
                                fill="#14B8A6"
                                d="M4.5 2A2.5 2.5 0 0 0 2 4.5v6A2.5 2.5 0 0 0 4.5 13H5V9.5A2.5 2.5 0 0 1 7.5 7H13V4.5A2.5 2.5 0 0 0 10.5 2zm0 2.5A.5.5 0 0 1 5 4h5a.5.5 0 0 1 0 1H5a.5.5 0 0 1-.5-.5m1.502 4.929A1.5 1.5 0 0 1 7.5 8h6a1.5 1.5 0 0 1 1.498 1.429L10.5 11.928zm4.74 3.508L15 10.572V13.5a1.5 1.5 0 0 1-1.5 1.5h-6A1.5 1.5 0 0 1 6 13.5v-2.928l4.257 2.365a.5.5 0 0 0 .486 0"
                            />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                        Grafik Master Document (Data)
                    </h3>
                    <div style="height: 300px">
                        <Pie
                            v-if="masterDocData.length"
                            :data="masterDocChartData"
                            :options="chartOptions"
                        />
                        <p v-else class="text-center text-gray-500 pt-16">
                            Belum ada data untuk ditampilkan.
                        </p>
                    </div>
                </div>
            </div>
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                        Grafik Master To (Data)
                    </h3>
                    <div style="height: 300px">
                        <Pie
                            v-if="masterToData.length"
                            :data="masterToChartData"
                            :options="chartOptions"
                        />
                        <p v-else class="text-center text-gray-500 pt-16">
                            Belum ada data untuk ditampilkan.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
