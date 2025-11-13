<script setup>
import { ref, onMounted, watch } from "vue";
import { Head, Link } from "@inertiajs/vue3";
import Sidebar from "@/Layouts/Sidebar.vue";

defineProps({
    title: String,
});

const isCollapsed = ref(false);
const isDrawerOpen = ref(false);

function toggleSidebar() {
    isCollapsed.value = !isCollapsed.value;
}

onMounted(() => {
    const savedState = localStorage.getItem("sidebarCollapsed");
    if (savedState) {
        isCollapsed.value = savedState === "true";
    }
});

watch(isCollapsed, (newValue) => {
    localStorage.setItem("sidebarCollapsed", newValue ? "true" : "false");
});
</script>

<template>
    <Head :title="title" />

    <div class="h-screen flex flex-col bg-gray-100 font-sans overflow-hidden">
        <header
            class="bg-white shadow-sm p-4 flex justify-between items-center shrink-0 border-b border-gray-200 fixed top-0 left-0 right-0 z-20 h-16"
        >
            <div class="flex items-center space-x-10">
                <div class="w-12 h-8 shrink-0 ml-4">
                    <img
                        src="/images/logorimba.png"
                        alt="Logo Rimba"
                        class="w-full h-full object-contain"
                    />
                </div>
                <h1 class="text-xl font-semibold text-teal-600">{{ title }}</h1>
                <button
                    @click="isDrawerOpen = true"
                    class="p-2 rounded-md text-gray-600 lg:hidden"
                    title="Buka Menu"
                >
                    <svg
                        class="w-6 h-6"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M4 6h16M4 12h16m-7 6h7"
                        />
                    </svg>
                </button>
            </div>

            <div class="flex items-center">
                <div class="p-2">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        width="36"
                        height="36"
                        viewBox="0 0 512 512"
                    >
                        <path
                            fill="#14B8A6"
                            fill-rule="evenodd"
                            d="M256 42.667A213.333 213.333 0 0 1 469.334 256c0 117.821-95.513 213.334-213.334 213.334c-117.82 0-213.333-95.513-213.333-213.334C42.667 138.18 138.18 42.667 256 42.667m21.334 234.667h-42.667c-52.815 0-98.158 31.987-117.715 77.648c30.944 43.391 81.692 71.685 139.048 71.685s108.104-28.294 139.049-71.688c-19.557-45.658-64.9-77.645-117.715-77.645M256 106.667c-35.346 0-64 28.654-64 64s28.654 64 64 64s64-28.654 64-64s-28.653-64-64-64"
                        />
                    </svg>
                </div>
                <Link
                    :href="route('logout')"
                    method="post"
                    as="button"
                    class="p-2"
                    title="Logout"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        width="36"
                        height="36"
                        viewBox="0 0 36 36"
                    >
                        <path
                            fill="#14B8A6"
                            d="M23 4H7a2 2 0 0 0-2 2v24a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-6h-9.37a1 1 0 0 1-1-1a1 1 0 0 1 1-1H25V6a2 2 0 0 0-2-2"
                            class="clr-i-solid clr-i-solid-path-1"
                        />
                        <path
                            fill="#14B8A6"
                            d="M28.16 17.28a1 1 0 0 0-1.41 1.41L30.13 22H25v2h5.13l-3.38 3.46a1 1 0 1 0 1.41 1.41l5.84-5.8Z"
                            class="clr-i-solid clr-i-solid-path-2"
                        />
                        <path fill="none" d="M0 0h36v36H0z" />
                    </svg>
                </Link>
            </div>
        </header>

        <div class="flex flex-1 overflow-hidden pt-16">
            <div
                v-if="isDrawerOpen"
                @click="isDrawerOpen = false"
                class="fixed inset-0 bg-black opacity-50 z-20 lg:hidden"
            ></div>

            <div
                :class="[
                    'relative h-full pt-4 pr-4 transition-all duration-300 shrink-0', // Padding Anda (pt-4 pr-4) dipertahankan
                    isCollapsed ? 'w-28' : 'w-72',
                    'lg:translate-x-0', // Selalu tampil di desktop
                    isDrawerOpen
                        ? 'translate-x-0 fixed z-30'
                        : '-translate-x-full fixed z-30', // Logika drawer mobile
                ]"
            >
                <Sidebar
                    :is-collapsed="isCollapsed"
                    @toggle-sidebar="toggleSidebar"
                    class="h-full"
                />
            </div>

            <main class="flex-1 p-6 overflow-y-auto">
                <slot />
            </main>
        </div>
    </div>
</template>
