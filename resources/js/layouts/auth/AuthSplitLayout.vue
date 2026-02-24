<script setup lang="ts">
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import { Link, usePage } from '@inertiajs/vue3';
import type { SharedData } from '@/types';

const page = usePage<SharedData>();
const name = page.props.name;
const quote = page.props.quote;

defineProps<{
    title?: string;
    description?: string;
}>();
</script>

<template>
    <div class="relative grid h-dvh flex-col items-center justify-center px-8 sm:px-0 lg:max-w-none lg:grid-cols-2 lg:px-0">
        <div class="relative hidden h-full flex-col p-10 text-white dark:border-r lg:flex overflow-hidden">
            <!-- Premium Gradient Background -->
            <div class="absolute inset-0 bg-[#0F172A]" />
            <div class="absolute inset-0 bg-gradient-to-br from-indigo-600/20 via-blue-600/10 to-transparent" />
            
            <!-- Mesh Gradient Elements -->
            <div class="absolute -top-[10%] -left-[10%] w-[60%] h-[60%] rounded-full bg-blue-500/10 blur-[120px] animate-pulse" />
            <div class="absolute bottom-[10%] right-[0%] w-[50%] h-[50%] rounded-full bg-indigo-500/10 blur-[120px] animate-pulse" style="animation-delay: 1s" />
            
            <Link :href="route('home')" class="relative z-20 flex items-center text-lg font-bold tracking-tight">
                <div class="mr-3 flex h-10 w-10 items-center justify-center rounded-xl bg-white shadow-xl">
                    <AppLogoIcon class="size-6 fill-current text-indigo-600" />
                </div>
                <span class="text-2xl font-black">Rekap<span class="text-indigo-400">Pajak.</span></span>
            </Link>

            <div v-if="quote" class="relative z-20 mt-auto">
                <div class="p-8 rounded-[2.5rem] bg-white/5 backdrop-blur-md border border-white/10 shadow-2xl">
                    <blockquote class="space-y-4">
                        <p class="text-xl font-medium leading-relaxed italic text-indigo-50">&ldquo;{{ quote.message }}&rdquo;</p>
                        <footer class="flex items-center gap-3">
                            <div class="h-1 w-8 bg-indigo-500 rounded-full"></div>
                            <span class="text-sm font-bold text-indigo-300 uppercase tracking-widest">{{ quote.author }}</span>
                        </footer>
                    </blockquote>
                </div>
            </div>
        </div>
        <div class="lg:p-8">
            <div class="mx-auto flex w-full flex-col justify-center space-y-6 sm:w-[350px]">
                <div class="flex flex-col space-y-2 text-center">
                    <h1 class="text-xl font-medium tracking-tight" v-if="title">{{ title }}</h1>
                    <p class="text-sm text-muted-foreground" v-if="description">{{ description }}</p>
                </div>
                <slot />
            </div>
        </div>
    </div>
</template>
