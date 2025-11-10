<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { useForm, usePage } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';
import Editor from '@tinymce/tinymce-vue'; // Untuk preview hasil

const page = usePage();

console.log('Data Flash TES Saat Halaman Dimuat:', JSON.stringify(page.props.flash));

// Form utama untuk mengirim file/teks ke backend
const form = useForm({
    document_file: null,
    raw_text: '',
});

// Computed property untuk akses data flash dengan aman
const processedData = computed(() => {
    // Cek dulu apakah page.props.flash ada, baru akses isinya
    return page.props.flash && page.props.flash.processedData
           ? page.props.flash.processedData
           : null;
});

// State untuk menyimpan hasil review
const previewHtml = ref('');
const customFields = ref([]);

// Watcher untuk update state lokal saat data flash berubah
watch(processedData, (newData) => {
    console.log('Watcher Berjalan. Data Baru:', JSON.parse(JSON.stringify(newData)));
    if (newData) {
        previewHtml.value = newData.body_html || '';
        customFields.value = newData.custom_fields || [];
    } else {
        // Kosongkan jika data flash null (misal: saat load awal)
        previewHtml.value = '';
        customFields.value = [];
    }
}, { immediate: true }); // immediate: true agar dijalankan saat pertama kali load

// Fungsi submit form input
const submit = () => {
    form.post(route('ai.generate.template'), {
        preserveScroll: true,
        forceFormData: true, // Penting jika ada upload file
        onSuccess: () => {
            // Data akan diperbarui otomatis via computed property & watch
            form.reset(); // Kosongkan form setelah sukses
            // Reset input file secara manual
            const fileInput = document.getElementById('file_input');
            if (fileInput) fileInput.value = null;
        },
        onError: (errors) => {
            console.error('Error Generating:', errors);
            // Anda bisa menampilkan notifikasi error di sini
        }
    });
};

// Form kedua untuk menyimpan template hasil review
const saveTemplateForm = useForm({
    name: 'Template Hasil AI (Edit Nama Ini)', // Default name
    description: 'Dibuat otomatis oleh AI Importer', // Default description
    body_html: '',
    // Tambahkan field lain jika perlu (header, footer)
    custom_fields: [],
});

// Fungsi untuk menyimpan template hasil review
const saveGeneratedTemplate = () => {
    saveTemplateForm.body_html = previewHtml.value; // Ambil HTML dari preview
    saveTemplateForm.custom_fields = customFields.value; // Ambil daftar fields yang mungkin sudah diedit

    saveTemplateForm.post(route('templates.store'), { // Panggil route simpan template biasa
       onSuccess: () => {
            alert('Template berhasil disimpan!');
            // Kosongkan area review setelah berhasil disimpan
            previewHtml.value = '';
            customFields.value = [];
            if(page.props.flash) { // Pastikan flash ada sebelum di-null kan
                page.props.flash.processedData = null; // Hapus data flash
            }
       },
       onError: (errors) => alert('Gagal menyimpan template: ' + JSON.stringify(errors)),
    });
}

const tinyMceApiKey = 'cv91g7yskmqat5acgisde00o4hsp8lf4r50348gl34v6jfa3'; // Ganti dengan API Key Anda

</script>

<template>
    <AppLayout title="AI Template Generator">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <div>
                <h2 class="text-xl font-semibold mb-4">Input Dokumen</h2>
                <form @submit.prevent="submit" class="space-y-4 bg-white p-6 rounded-lg shadow">
                    <div>
                        <label for="file_input" class="block mb-2 text-sm font-medium text-gray-900">Unggah File (.docx, .pdf, .txt)</label>
                        <input @input="form.document_file = $event.target.files[0]" class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none" id="file_input" type="file">
                        <progress v-if="form.progress" :value="form.progress.percentage" max="100" class="w-full mt-1">
                          {{ form.progress.percentage }}%
                        </progress>
                        <p v-if="form.errors.document_file" class="mt-1 text-sm text-red-600">{{ form.errors.document_file }}</p>
                    </div>

                    <div class="text-center my-2 text-gray-500 font-semibold">atau</div>

                    <div>
                        <label for="raw_text_input" class="block mb-2 text-sm font-medium text-gray-900">Tempel Teks Mentah</label>
                        <textarea v-model="form.raw_text" id="raw_text_input" rows="10" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500" placeholder="Salin dan tempel teks dokumen Anda di sini..."></textarea>
                        <p v-if="form.errors.raw_text" class="mt-1 text-sm text-red-600">{{ form.errors.raw_text }}</p>
                    </div>

                     <p v-if="form.errors.input" class="mt-1 text-sm text-red-600">{{ form.errors.input }}</p>

                    <button type="submit" :disabled="form.processing" class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed">
                        {{ form.processing ? 'Memproses...' : 'Generate Template' }}
                    </button>
                </form>
            </div>

            <div>
                 <h2 class="text-xl font-semibold mb-4">Hasil Generate & Review</h2>
                 <div v-if="processedData" class="bg-white p-6 rounded-lg shadow space-y-6">
                     <div>
                         <label for="template_name" class="block text-sm font-medium text-gray-700 mb-1">Nama Template</label>
                         <input type="text" id="template_name" v-model="saveTemplateForm.name" class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-blue-500 focus:border-blue-500">
                         <p v-if="saveTemplateForm.errors.name" class="mt-1 text-sm text-red-600">{{ saveTemplateForm.errors.name }}</p>
                     </div>
                     <div>
                         <h3 class="font-medium mb-2">Preview Template (Edit jika perlu)</h3>
                         <Editor
                            v-model="previewHtml"
                            :api-key="tinyMceApiKey"
                            :init="{ height: 350, menubar: true, plugins: 'lists link image table code help wordcount', toolbar: 'undo redo | blocks | bold italic | alignleft aligncenter alignright | bullist numlist outdent indent | link image | code' }"
                         />
                         <p v-if="saveTemplateForm.errors.body_html" class="mt-1 text-sm text-red-600">{{ saveTemplateForm.errors.body_html }}</p>
                     </div>
                     <div>
                         <h3 class="font-medium mb-2">Custom Fields Terdeteksi (Edit jika perlu)</h3>
                         <div v-if="customFields.length > 0" class="space-y-2">
                             <div class="grid grid-cols-3 gap-2 text-xs font-semibold text-gray-500">
                                 <span>Nama Field (Variabel)</span>
                                 <span>Label Formulir</span>
                                 <span>Tipe Data</span>
                             </div>
                             <div v-for="(field, index) in customFields" :key="index" class="grid grid-cols-3 gap-2 items-center">
                                 <input type="text" v-model="field.field_name" placeholder="cth: nama_klien" class="col-span-1 border border-gray-300 rounded p-1 text-sm focus:ring-blue-500 focus:border-blue-500">
                                 <input type="text" v-model="field.field_label" placeholder="cth: Nama Klien" class="col-span-1 border border-gray-300 rounded p-1 text-sm focus:ring-blue-500 focus:border-blue-500">
                                 <select v-model="field.field_type" class="col-span-1 border border-gray-300 rounded p-1 text-sm bg-white focus:ring-blue-500 focus:border-blue-500">
                                     <option value="text">Text</option>
                                     <option value="textarea">Textarea</option>
                                     <option value="date">Date</option>
                                     <option value="number">Number</option>
                                 </select>
                             </div>
                         </div>
                         <p v-else class="text-sm text-gray-500 italic">Tidak ada custom field terdeteksi oleh AI.</p>
                         <p v-if="saveTemplateForm.errors.custom_fields" class="mt-1 text-sm text-red-600">{{ saveTemplateForm.errors.custom_fields }}</p>

                     </div>
                     <button @click="saveGeneratedTemplate" :disabled="saveTemplateForm.processing" class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 disabled:opacity-50 disabled:cursor-not-allowed">
                        {{ saveTemplateForm.processing ? 'Menyimpan...' : 'Simpan Template Ini' }}
                     </button>
                 </div>
                 <div v-else class="text-center text-gray-500 mt-10 p-6 bg-white rounded-lg shadow italic">
                    Hasil generate akan muncul di sini setelah Anda menginput dokumen dan klik "Generate Template".
                </div>
            </div>
        </div>
    </AppLayout>
</template>