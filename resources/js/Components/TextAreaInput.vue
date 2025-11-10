<script setup>
import { onMounted, ref } from 'vue';

// Definisikan props yang diterima (seperti 'modelValue' untuk v-model)
const props = defineProps(['modelValue']);

// Definisikan emit untuk mengirim update kembali ke parent
const emit = defineEmits(['update:modelValue']);

const input = ref(null);

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

// Fungsi untuk meng-handle input dan mengirim emit
const handleInput = (event) => {
    emit('update:modelValue', event.target.value);
};

defineExpose({ focus: () => input.value.focus() });
</script>

<template>
    <textarea
        class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full"
        :value="modelValue"
        @input="handleInput"
        ref="input"
    ></textarea>
</template>