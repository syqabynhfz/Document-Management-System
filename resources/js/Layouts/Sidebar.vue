<script setup>
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    isCollapsed: Boolean,
});

const emit = defineEmits(['toggleSidebar']);

const navItems = [
    { 
        name: 'dashboard', 
        label: 'Dashboard',
        svgPath: 'M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z' 
    },
    { 
        name: 'templates.index', 
        label: 'Templates',
        svgPath: 'M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z' 
    },
    { 
        name: 'history.index', 
        label: 'History',
        svgPath: 'M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0011.664 0l3.18-3.182m-3.182-4.991v4.99' 
    },
    { 
        name: 'ai.importer.show',
        label: 'AI Importer',
        svgPath: 'M9.75 3.104v5.714a2.25 2.25 0 01-.5 1.591L5.328 15.36a2.25 2.25 0 01-1.84 1.14H2.25a2.25 2.25 0 01-2.25-2.25v-6.75a2.25 2.25 0 01.984-1.837l4.87-3.409a2.25 2.25 0 012.896.315zM14.25 3.104v5.714a2.25 2.25 0 00.5 1.591l3.922 4.954a2.25 2.25 0 001.84 1.14h1.238a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-.984-1.837l-4.87-3.409a2.25 2.25 0 00-2.896.315z' // Contoh ikon
    },
];
</script>

<template>
    <aside
        class="relative flex flex-col h-full bg-white rounded-r-xl shadow-xl transition-all duration-300 p-3"
        :class="isCollapsed ? 'w-20' : 'w-64'"
    >
        <nav class="flex-1 flex flex-col w-full space-y-2">
            <Link
                v-for="item in navItems"
                :key="item.name"
                :href="route(item.name)"
                class="flex items-center p-3 rounded-xl transition-colors duration-200"
                :class="{
                    'bg-teal-500 text-white shadow-md': route().current(item.name.replace('.index', '.*')),
                    'text-gray-500 hover:bg-gray-100': !route().current(item.name.replace('.index', '.*')),
                    'justify-center': isCollapsed
                }"
                :title="item.label"
            >
                <svg class="w-6 h-6 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" :d="item.svgPath" />
                </svg>
                <span v-if="!isCollapsed" class="ml-4 font-semibold text-sm">{{ item.label }}</span>
            </Link>
        </nav>

        <button
            @click="$emit('toggleSidebar')"
            class="absolute top-1/2 -right-3.5 transform -translate-y-1/2 bg-white w-7 h-7 rounded-full shadow-lg border border-gray-200 flex items-center justify-center text-gray-600 hover:bg-gray-50 transition-transform duration-300 z-10 hidden lg:flex"
            :class="{ 'rotate-180': isCollapsed }"
            title="Toggle Sidebar"
        >
            <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
                stroke-width="2"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M15 19l-7-7 7-7"
                />
            </svg>
        </button>
         </aside>
</template>