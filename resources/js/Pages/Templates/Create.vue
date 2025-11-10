<script setup>
import { ref } from "vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import Editor from "@tinymce/tinymce-vue";
import draggable from "vuedraggable";
import axios from "axios";

const form = useForm({
    name: "Template Baru (Klik untuk Mengedit)",
    body_html: "",
    custom_fields: [],
});

const tinyMceApiKey = "cv91g7yskmqat5acgisde00o4hsp8lf4r50348gl34v6jfa3";

const contentBlocks = ref([
    {
        id: "text",
        name: "Text",
        svgPath: "M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25H12", // Path SVG untuk Teks (Heroicons)
        defaultHtml: "<h2>Judul Baru</h2><p>Tulis paragraf Anda di sini...</p>",
    },
    {
        id: "image",
        name: "Image",
        svgPath:
            "M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm1.5-3.75a.75.75 0 11-1.5 0 .75.75 0 011.5 0z",
        defaultHtml:
            '<p><img src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAwIiBoZWlnaHQ9IjIwMCIgdmlld0JveD0iMCAwIDQwMCAyMDAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PHJlY3Qgd2lkdGg9IjEwMCUiIGhlaWdodD0iMTAwJSIgZmlsbD0iI2YxZjVmOSIvPjx0ZXh0IHg9IjUwJSIgeT0iNTAlIiBkb21pbmFudC1iYXNlbGluZT0ibWlkZGxlIiB0ZXh0LWFuY2hvcj0ibWlkZGxlIiBmb250LWZhbWlseT0ic2Fucy1zZXJpZiIgZm9udC13ZWlnaHQ9IjYwMCIgZm9udC1zaXplPSIxNiIgc3Ryb2tlPSIjZDFkNWQ5IiBmaWxsPSIjNmI3MjgwIj5Eb3VibGUtY2xpY2sgb3IgZHJhZyBpbWFnZSBoZXJlPC90ZXh0Pjwvc3ZnPg==" alt="Image Placeholder" width="400" height="200" /></p>',
    },
    {
        id: "table",
        name: "Table",
        svgPath:
            "M3.375 18h17.25c.621 0 1.125-.504 1.125-1.125V8.25c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v8.625c0 .621.504 1.125 1.125 1.125zM3.375 7.125h17.25c.621 0 1.125-.504 1.125-1.125V3.375c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v2.625c0 .621.504 1.125 1.125 1.125z", // Path SVG untuk Tabel
        defaultHtml:
            '<table border="1" style="width: 100%; border-collapse: collapse;"><tbody><tr><td style="padding: 5px;">Kolom 1</td><td style="padding: 5px;">Kolom 2</td></tr><tr><td style="padding: 5px;">Data A</td><td style="padding: 5px;">Data B</td></tr></tbody></table>',
    },
    {
        id: "pagebreak",
        name: "Page Break",
        svgPath:
            "M19.5 21a3 3 0 003-3V6a3 3 0 00-3-3H4.5A2.25 2.25 0 002.25 5.25v13.5A2.25 2.25 0 004.5 21h15zm-6.75-2.25a.75.75 0 00-1.5 0v.001a.75.75 0 001.5 0v-.001zm0-3a.75.75 0 00-1.5 0v.001a.75.75 0 001.5 0v-.001z",
        defaultHtml: '<p style="page-break-before: always;"></p>',
    },
]);

const handleDragStart = (event, block) => {
    event.dataTransfer.setData("text/html", block.defaultHtml);
    event.dataTransfer.effectAllowed = "copy";
};

const tinyMceConfig = {
    height: 650,

    menubar: true,

    plugins: [
        "advlist",
        "autolink",
        "lists",
        "link",
        "image",
        "charmap",
        "preview",

        "anchor",
        "searchreplace",
        "visualblocks",
        "code",
        "fullscreen",

        "insertdatetime",
        "media",
        "table",
        "help",
        "wordcount",

        "pagebreak",
    ],

    toolbar:
        "undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table pagebreak | align lineheight | numlist bullist indent outdent | emoticons charmap | removeformat | help",

    automatic_uploads: true,
    images_file_types: 'jpeg,jpg,png,gif,webp,svg,avif',

    images_upload_handler: (blobInfo, success, failure) => {
        const formData = new FormData();
        formData.append("file", blobInfo.blob(), blobInfo.filename());

        axios
            .post(route("templates.upload_image"), formData)
            .then((res) => {
                success(res.data.location);
            })
            .catch((err) => {
                let errorMessage = "Upload gagal: ";
                if (
                    err.response &&
                    err.response.data &&
                    err.response.data.message
                ) {
                    errorMessage += err.response.data.message;
                } else {
                    errorMessage += err.message;
                }

                failure({ message: errorMessage, remove: true });
            });
    },

    setup: (editor) => {
        editor.on("drop", (event) => {
            event.preventDefault();

            const htmlContent = event.dataTransfer.getData("text/html");

            if (htmlContent) {
                editor.execCommand("mceInsertContent", false, htmlContent);
            }
        });

        editor.on("dragover", (event) => {
            event.preventDefault();
        });
    },
};

const saveTemplate = () => {
    form.post(route("templates.store"), {
        preserveScroll: true,
        onError: (errors) => {
            if (errors.name) {
                alert("Gagal Menyimpan: " + errors.name);
            } else {
                alert("Terjadi error. Periksa konsol.");
            }
            console.error("Error:", errors);
        },
        onSuccess: () => {},
    });
};

const isDownloading = ref(false);

const downloadPreview = () => {
    isDownloading.value = true;
    axios
        .post(
            route("templates.preview"),
            {
                body_html: form.body_html,
            },
            {
                responseType: "blob",
            }
        )
        .then((response) => {
            const url = window.URL.createObjectURL(new Blob([response.data]));
            const link = document.createElement("a");
            link.href = url;
            link.setAttribute("download", "template-preview.pdf");
            document.body.appendChild(link);
            link.click();

            window.URL.revokeObjectURL(url);
            document.body.removeChild(link);
        })
        .catch((error) => {
            console.error("Gagal download PDF:", error);
            alert("Gagal membuat preview PDF. Periksa konsol.");
        })
        .finally(() => {
            isDownloading.value = false;
        });
};
</script>

<template>
    <Head title="Add Template" />

    <div class="flex flex-col h-screen bg-gray-100 font-sans">
        <header
            class="bg-white p-4 flex justify-between items-center shrink-0 border-b border-gray-200 z-10"
        >
            <div class="flex items-center space-x-4">
                <Link
                    :href="route('templates.index')"
                    title="Back to Templates"
                >
                    <img
                        src="/images/logorimba.png"
                        alt="Logo Rimba"
                        class="w-12 h-8"
                    />
                </Link>
                <input
                    type="text"
                    v-model="form.name"
                    placeholder="Beri nama template Anda"
                    class="text-xl font-semibold text-gray-800 border-b-2 border-transparent focus:border-teal-500 outline-none"
                />
            </div>
            <div class="flex items-center space-x-2">
                <button
                    @click="saveTemplate"
                    :disabled="form.processing"
                    class="bg-teal-600 text-white font-bold py-2 px-4 rounded-lg shadow-md hover:bg-teal-700 inline-flex items-center"
                >
                    <svg
                        class="w-5 h-5 mr-2"
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                    >
                        <path
                            fill-rule="evenodd"
                            d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.052-.143z"
                            clip-rule="evenodd"
                        />
                    </svg>
                    {{ form.processing ? "Menyimpan..." : "Save Template" }}
                </button>
                <button
                    @click="downloadPreview"
                    :disabled="isDownloading"
                    class="bg-red-500 text-white font-bold py-2 px-4 rounded-lg shadow-md hover:bg-red-600 inline-flex items-center"
                >
                    <svg
                        class="w-5 h-5 mr-2"
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
                    {{ isDownloading ? "Loading..." : "Download" }}
                </button>
                <button
                    class="p-2 text-gray-500 hover:bg-gray-100 rounded-full"
                >
                    <svg
                        class="w-6 h-6"
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                    >
                        <path
                            d="M10 3a1.5 1.5 0 110 3 1.5 1.5 0 010-3zM10 8.5a1.5 1.5 0 110 3 1.5 1.5 0 010-3zM11.5 15.5a1.5 1.5 0 10-3 0 1.5 1.5 0 003 0z"
                        />
                    </svg>
                </button>
            </div>
        </header>

        <div class="flex-1 flex overflow-hidden min-h-0">
            <div class="flex-1 flex flex-col p-6 overflow-y-auto">
                <div
                    class="bg-white rounded-lg shadow-lg p-2 flex-1 flex flex-col"
                >
                    <Editor
                        :api-key="tinyMceApiKey"
                        v-model="form.body_html"
                        :init="tinyMceConfig"
                        class="flex-1 w-full h-full"
                    />
                </div>
            </div>

            <aside
                class="w-80 bg-white p-6 border-l border-gray-200 overflow-y-auto"
            >
                <h3 class="font-bold text-lg mb-4">Content</h3>
                <draggable
                    :list="contentBlocks"
                    item-key="id"
                    :group="{
                        name: 'contentBlocks',
                        pull: 'clone',
                        put: false,
                    }"
                    :sort="false"
                    class="grid grid-cols-2 gap-4"
                >
                    <template #item="{ element }">
                        <div
                            class="bg-gray-100 border border-gray-300 p-4 rounded-lg flex flex-col items-center justify-center cursor-grab hover:bg-gray-200 transition-colors h-24"
                            draggable="true"
                            @dragstart="handleDragStart($event, element)"
                        >
                            <svg
                                class="w-8 h-8 text-gray-600"
                                xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke-width="1.5"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    :d="element.svgPath"
                                />
                            </svg>
                            <p class="font-semibold text-xs mt-2 text-gray-700">
                                {{ element.name }}
                            </p>
                        </div>
                    </template>
                </draggable>
            </aside>
        </div>
    </div>
</template>
