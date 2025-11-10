<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { Link, router } from "@inertiajs/vue3";

defineProps({
    templates: {
        type: Array,
        default: () => [],
    },
});

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

const deleteTemplate = (id, name) => {
    if (
        window.confirm(`Apakah Anda yakin ingin menghapus riwayat '${name}'?`)
    ) {
        router.delete(route("templates.destroy", id), {});
    }
};

</script>

<template>
    <AppLayout title="Template Document">
        <div class="flex justify-between items-center mb-6">
            <Link
                :href="route('templates.create')"
                class="bg-teal-600 text-white font-semibold py-2 px-4 rounded-lg shadow-sm hover:bg-teal-700 inline-flex items-center"
            >
                <svg
                    class="w-5 h-5 mr-2"
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                >
                    <path
                        d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z"
                    />
                </svg>
                Add Template
            </Link>
            <div class="relative w-full max-w-xs">
                <input
                    type="text"
                    placeholder="Search..."
                    class="w-full py-2 pl-4 pr-10 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500"
                />
                <button
                    class="absolute inset-y-0 right-0 flex items-center px-3 bg-teal-600 rounded-r-lg text-white hover:bg-teal-700"
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

        <div class="bg-white overflow-hidden rounded-lg shadow-sm">
            <div
                class="grid grid-cols-12 gap-4 text-sm font-semibold text-gray-600 bg-gray-50 p-4 border-b"
            >
                <div class="col-span-1 flex items-center">
                    <input type="checkbox" class="rounded border-gray-300" />
                </div>
                <div class="col-span-5">Title</div>
                <div class="col-span-3">Created</div>
                <div class="col-span-3">Action</div>
            </div>

            <div v-if="templates && templates.length > 0">
                <div
                    v-for="template in templates"
                    :key="template.id"
                    class="grid grid-cols-12 gap-4 items-center p-4 border-b last:border-b-0 hover:bg-gray-50"
                >
                    <div class="col-span-1 flex items-center">
                        <input
                            type="checkbox"
                            class="rounded border-gray-300"
                        />
                    </div>
                    <div class="col-span-5 font-medium text-gray-800">
                        {{ template.name }}
                    </div>
                    <div class="col-span-3 text-gray-500">
                        {{ formatDate(template.created_at) }}
                    </div>

                    <div class="col-span-3 flex items-center space-x-2">
                        <Link
                            :href="route('generator.show', template.id)"
                            class="text-green-600 hover:text-green-800 p-1 rounded hover:bg-green-100"
                            title="Gunakan Template Ini"
                        >
                            <svg
                                class="w-5 h-5"
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                            >
                                <path
                                    fill-rule="evenodd"
                                    d="M4.25 5.5a.75.75 0 00-.75.75v8.5c0 .414.336.75.75.75h8.5a.75.75 0 00.75-.75v-4a.75.75 0 011.5 0v4A2.25 2.25 0 0112.75 17H4.25A2.25 2.25 0 012 14.75v-8.5A2.25 2.25 0 014.25 4H8a.75.75 0 010 1.5H4.25a.75.75 0 00-.75.75z"
                                    clip-rule="evenodd"
                                />
                                <path
                                    fill-rule="evenodd"
                                    d="M6.194 12.753a.75.75 0 001.06.053L16.5 4.44v2.81a.75.75 0 001.5 0v-4.5a.75.75 0 00-.75-.75h-4.5a.75.75 0 000 1.5h2.553l-9.056 8.19a.75.75 0 00-.053 1.06z"
                                    clip-rule="evenodd"
                                />
                            </svg>
                        </Link>
                        <button
                            class="text-blue-500 hover:text-blue-700 p-1 rounded hover:bg-blue-100"
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
                            @click="deleteTemplate(template.id, template.name)"
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
                Belum ada template yang dibuat.
            </div>
        </div>
    </AppLayout>
</template>
