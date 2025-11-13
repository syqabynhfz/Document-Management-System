<script setup>
import { Head, Link, usePage } from "@inertiajs/vue3";
import { computed } from "vue";
import { Icon } from "@iconify/vue"; // <-- 1. IMPORT KOMPONEN ICONIFY

defineProps({
    isCollapsed: Boolean,
});

const emit = defineEmits(["toggleSidebar"]);

const adminName = computed(
    () => usePage().props.auth.user?.full_name || "Admin"
);

const navItems = [
    {
        name: "dashboard",
        label: "Dashboard",
        icon: "mdi:view-dashboard-outline",
    },
    {
        name: "data.index",
        label: "Data (Master To)",
        icon: "mdi:account-group-outline",
    },
    {
        name: "templates.index",
        label: "Templates",
        icon: "mdi:file-document-outline",
    },
    {
        name: "master-doc.index",
        label: "Master Doc",
        icon: "mdi:file-multiple-outline",
    },
    {
        name: "document.index",
        label: "Document",
        icon: "mdi:file-plus-outline",
    },
    {
        name: "history.index",
        label: "History",
        icon: "mdi:history",
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
                    'bg-teal-500 text-white shadow-md': route().current(
                        item.name.replace('.index', '.*')
                    ),
                    'text-teal-600 hover:bg-gray-100': !route().current(
                        item.name.replace('.index', '.*')
                    ),
                    'justify-center': isCollapsed,
                }"
                :title="item.label"
            >
                <Icon :icon="item.icon" class="w-6 h-6 shrink-0" />

                <span v-if="!isCollapsed" class="ml-4 font-semibold text-sm">{{
                    item.label
                }}</span>
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
