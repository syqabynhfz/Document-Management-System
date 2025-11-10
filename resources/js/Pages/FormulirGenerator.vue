<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { useForm } from '@inertiajs/vue3';
import { computed } from 'vue'; 
import TextInput from '@/Components/TextInput.vue'; 
import InputLabel from '@/Components/InputLabel.vue'; 
import TextAreaInput from '@/Components/TextAreaInput.vue'; 

// Menerima data template dan custom fields dari controller
const props = defineProps({
    template: {
        type: Object,
        required: true,
    },
    customFields: {
        type: Array,
        default: () => [],
    },
});

// Siapkan objek kosong untuk form data
const initialFormData = {};
// Isi objek form data berdasarkan custom fields yang diterima
props.customFields.forEach(field => {
    initialFormData[field.field_name] = ''; // Inisialisasi dengan string kosong '' untuk menghindari error prop type
});

// Gunakan useForm dari Inertia untuk mengelola state isian formulir
const form = useForm(initialFormData);

// 2. Buat URL untuk link download secara dinamis
const generateUrl = computed(() => {
    // Ambil data form yang valid (bukan null atau string kosong)
    const validFormData = {};
    for (const key in form.data()) {
        if (form[key] !== null && form[key] !== undefined && form[key] !== '') {
            validFormData[key] = form[key];
        }
    }
    // Buat query string dari data form
    const queryParams = new URLSearchParams(validFormData).toString();
    
    // Gabungkan dengan base route dan ID template
    // Ini akan memanggil route 'generator.generate'
    return route('generator.generate', { template: props.template.id }) + '?' + queryParams;
});

</script>

<template>
    <AppLayout :title="'Generate Dokumen: ' + (template ? template.name : 'Loading...')">

        <div v-if="template" class="max-w-4xl mx-auto bg-white p-6 md:p-8 rounded-lg shadow-md">
            <h2 class="text-2xl font-semibold mb-6 text-gray-800">Isi Data untuk Template: {{ template.name }}</h2>

            <form class="space-y-4">
                <div v-for="field in customFields" :key="field.id">
                    <InputLabel :for="field.field_name">
                        {{ field.field_label }}
                        <span v-if="field.is_required" class="text-red-500 ml-1">*</span>
                    </InputLabel>

                    <TextInput
                        v-if="field.field_type === 'text' || field.field_type === 'number' || field.field_type === 'date'"
                        :id="field.field_name"
                        :type="field.field_type === 'date' ? 'date' : (field.field_type === 'number' ? 'number' : 'text')"
                        class="mt-1 block w-full"
                        v-model="form[field.field_name]"
                        :required="field.is_required"
                        :step="field.field_type === 'number' ? 'any' : null"
                    />

                    <TextAreaInput
                        v-else-if="field.field_type === 'textarea'"
                        :id="field.field_name"
                        class="mt-1 block w-full"
                        v-model="form[field.field_name]"
                        :required="field.is_required"
                        rows="4"
                    />
                    <p v-if="form.errors[field.field_name]" class="mt-1 text-sm text-red-600">
                        {{ form.errors[field.field_name] }}
                    </p>
                </div>

                <div class="flex justify-end pt-4">
                    <a
                        :href="generateUrl"
                        class="inline-flex items-center px-6 py-2 bg-blue-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-800 focus:outline-none focus:border-blue-800 focus:ring ring-blue-300 disabled:opacity-50 transition ease-in-out duration-150"
                        :class="{ 'opacity-25 cursor-not-allowed': form.processing }"
                        :disabled="form.processing"
                    >
                        {{ form.processing ? 'Memproses...' : 'Generate PDF' }}
                    </a>
                </div>
            </form>
        </div>
        
        <div v-else class="text-center text-red-500 p-6">
            Error: Data template tidak dapat dimuat. Pastikan Anda mengakses halaman ini dari tombol "Gunakan" di daftar template.
        </div>
    </AppLayout>
</template>