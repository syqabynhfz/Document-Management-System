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
    localStorage.setItem("sidebarCollapsed", newValue ? 'true' : 'false');
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
                <h1 class="text-xl font-semibold text-gray-800">{{ title }}</h1>
                <button
                    @click="isDrawerOpen = true"
                    class="p-2 rounded-md text-gray-600 lg:hidden"
                    title="Buka Menu"
                >
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" />
                    </svg>
                </button>
            </div>

            <div class="flex items-center space-x-4">
                <div class="p-2 bg-gray-100 rounded-full">
                    <svg
                        class="w-6 h-6 text-gray-600"
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke-width="1.5"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"
                        />
                    </svg>
                </div>
                <Link
                    :href="route('logout')"
                    method="post"
                    as="button"
                    class="p-2 bg-gray-100 rounded-full"
                    title="Logout"
                >
                    <svg
                        class="w-6 h-6 text-gray-600"
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke-width="1.5"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75"
                        />
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
                    isDrawerOpen ? 'translate-x-0 fixed z-30' : '-translate-x-full fixed z-30' // Logika drawer mobile
                ]"
            >

                <Sidebar :is-collapsed="isCollapsed"
                @toggle-sidebar="toggleSidebar"
                class="h-full" />
            </div>

            <main class="flex-1 p-6 overflow-y-auto">
                <slot />
            </main>
        </div>
    </div>
</template>