<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
    <Head title="Forgot Password" />

    <div class="flex min-h-screen">
        
        <div class="hidden lg:flex w-1/2 bg-teal-500 justify-center items-center">
            <img src="\images\forgetpassword.png" alt="Illustration" class="w-3/4">
        </div>

        <div class="flex flex-col justify-center items-center w-full lg:w-1/2 p-8">
            <div class="w-full max-w-md">
                <div class="mb-8">
                      <img src="/images/logorimba.png" alt="Logo Rimba" class="w-10 h-8">
                </div>

                <h1 class="text-3xl font-bold text-gray-900">Forgot Password</h1>
                <p class="mt-2 text-gray-600">
                    Make sure to use your active email to reset your password
                </p>

                <div v-if="status" class="mt-4 mb-4 font-medium text-sm text-green-600">
                    {{ status }}
                </div>

                <form @submit.prevent="submit" class="mt-8">
                    <div>
                        <InputLabel for="email" value="Email" class="font-semibold" />
                        <div class="relative mt-1">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                                    <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                                </svg>
                            </div>
                            <TextInput
                                id="email"
                                type="email"
                                class="block w-full pl-10 p-2.5"
                                v-model="form.email"
                                required
                                autofocus
                                autocomplete="username"
                                placeholder="Enter your email address"
                            />
                        </div>
                        <InputError class="mt-2" :message="form.errors.email" />
                    </div>

                    <div class="mt-6">
                        <PrimaryButton class="w-full justify-center py-3 bg-teal-600 hover:bg-teal-700" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                            Email Password Reset Link
                        </PrimaryButton>
                    </div>
                </form>

                <div class="mt-6 text-center">
                    <Link :href="route('login')" class="text-sm text-gray-600 hover:text-gray-900 font-semibold inline-flex items-center">
                        <svg class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                          <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                        Back to log in
                    </Link>
                </div>
            </div>
        </div>
    </div>
</template>
