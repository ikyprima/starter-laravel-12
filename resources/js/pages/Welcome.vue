<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import { type SharedData } from '@/types';
import { ref, onMounted, onUnmounted } from 'vue';
import { 
    LayoutDashboard, 
    ArrowRight, 
    ShieldCheck, 
    Zap, 
    BarChart3, 
    CheckCircle2,
    Calendar,
    ChevronRight,
    Search
} from 'lucide-vue-next';

interface Props {
    canLogin?: boolean;
    canRegister?: boolean;
    laravelVersion?: string;
    phpVersion?: string;
}

defineProps<Props>();
const page = usePage<SharedData>();

const isScrolled = ref(false);
const isMenuOpen = ref(false);

const handleScroll = () => {
    isScrolled.value = window.scrollY > 20;
};

const toggleMenu = () => {
    isMenuOpen.value = !isMenuOpen.value;
};

onMounted(() => {
    window.addEventListener('scroll', handleScroll);
});

onUnmounted(() => {
    window.removeEventListener('scroll', handleScroll);
});
</script>

<template>
    <Head title="Rekap Pajak - Sistem Informasi Perpajakan Terintegrasi">
        <meta name="description" content="Platform modern untuk pengelolaan dan pelaporan rekap pajak SKPD secara efisien, akurat, dan transparan." />
    </Head>

    <div class="min-h-screen bg-[#F8FAFC] text-slate-900 font-sans selection:bg-blue-100 selection:text-blue-900 overflow-x-hidden">
        <!-- Background Mesh Gradient -->
        <div class="fixed inset-0 z-0 pointer-events-none overflow-hidden text-blue-500/20">
            <div class="absolute -top-[10%] -left-[10%] w-[40%] h-[40%] rounded-full bg-blue-400/30 blur-[120px]"></div>
            <div class="absolute top-[20%] -right-[5%] w-[45%] h-[45%] rounded-full bg-indigo-400/30 blur-[120px]"></div>
            <div class="absolute -bottom-[10%] left-[20%] w-[50%] h-[50%] rounded-full bg-sky-300/30 blur-[120px]"></div>
        </div>

        <!-- Dynamic Fixed Modern Navbar -->
    <nav 
        class="fixed top-0 left-0 right-0 z-[100] w-full transition-all duration-500 ease-in-out"
        :class="[
            isScrolled || isMenuOpen
            ? 'h-20 bg-white/90 backdrop-blur-2xl border-b border-slate-200 shadow-xl shadow-blue-500/5' 
            : 'h-24 bg-transparent border-b border-transparent'
        ]"
    >
        <div class="container mx-auto px-4 sm:px-6 lg:px-8 h-full">
            <div class="flex h-full items-center justify-between">
                <!-- Logo -->
                <div class="flex items-center gap-2 sm:gap-3">
                    <div class="flex h-9 w-9 sm:h-10 sm:w-10 items-center justify-center rounded-xl bg-blue-600 shadow-lg shadow-blue-500/20">
                        <BarChart3 class="h-5 w-5 sm:h-6 sm:w-6 text-white" />
                    </div>
                    <span class="text-xl sm:text-2xl font-black tracking-tight text-slate-800">Rekap<span class="text-blue-600">Pajak.</span></span>
                </div>

                <!-- Desktop Navigation -->
                <div class="hidden md:flex items-center gap-6 text-slate-600">
                    <template v-if="page.props.auth?.user">
                        <Link
                            :href="route('dashboard')"
                            class="inline-flex items-center gap-2 rounded-2xl bg-slate-900 px-6 py-2.5 text-sm font-bold text-white shadow-xl shadow-slate-900/20 transition-all hover:scale-105 active:scale-95"
                        >
                            <LayoutDashboard class="h-4 w-4" />
                            Dashboard
                        </Link>
                    </template>
                    <template v-else>
                        <Link
                            :href="route('login')"
                            class="text-sm font-bold transition-colors hover:text-blue-600 px-4"
                        >
                            Masuk
                        </Link>
                        <Link
                            :href="route('register')"
                            class="inline-flex items-center gap-2 rounded-2xl bg-blue-600 px-7 py-3 text-sm font-bold text-white shadow-xl shadow-blue-500/30 transition-all hover:bg-blue-700 hover:scale-105 active:scale-95"
                        >
                            Get Started
                            <ArrowRight class="h-4 w-4" />
                        </Link>
                    </template>
                </div>

                <!-- Mobile Menu Button -->
                <div class="flex md:hidden">
                    <button 
                        @click="toggleMenu"
                        class="p-2 rounded-xl bg-slate-100 text-slate-600 hover:bg-slate-200 transition-colors"
                        aria-label="Toggle Menu"
                    >
                        <div class="w-6 h-5 relative flex flex-col justify-between items-center transition-all duration-300">
                            <span 
                                class="w-full h-0.5 bg-current transition-all duration-300 origin-left"
                                :class="{ 'rotate-45 translate-x-1': isMenuOpen }"
                            ></span>
                            <span 
                                class="w-full h-0.5 bg-current transition-all duration-300"
                                :class="{ 'opacity-0 scale-0': isMenuOpen }"
                            ></span>
                            <span 
                                class="w-full h-0.5 bg-current transition-all duration-300 origin-left"
                                :class="{ '-rotate-45 translate-x-1': isMenuOpen }"
                            ></span>
                        </div>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu Drawer -->
        <div 
            v-if="isMenuOpen" 
            class="md:hidden absolute top-20 left-0 right-0 bg-white/95 backdrop-blur-2xl border-b border-slate-200 shadow-2xl overflow-hidden transition-all duration-500 animate-in fade-in slide-in-from-top-4"
        >
            <div class="flex flex-col p-6 gap-4">
                <template v-if="page.props.auth?.user">
                    <Link
                        :href="route('dashboard')"
                        class="flex items-center justify-center gap-2 rounded-2xl bg-slate-900 px-6 py-4 text-sm font-bold text-white shadow-xl shadow-slate-900/20"
                        @click="isMenuOpen = false"
                    >
                        <LayoutDashboard class="h-5 w-5" />
                        Dashboard
                    </Link>
                </template>
                <template v-else>
                    <Link
                        :href="route('login')"
                        class="flex items-center justify-center rounded-2xl border border-slate-200 bg-white px-6 py-4 text-sm font-bold text-slate-700"
                        @click="isMenuOpen = false"
                    >
                        Masuk
                    </Link>
                    <Link
                        :href="route('register')"
                        class="flex items-center justify-center gap-2 rounded-2xl bg-blue-600 px-6 py-4 text-sm font-bold text-white shadow-xl shadow-blue-500/30"
                        @click="isMenuOpen = false"
                    >
                        Get Started
                        <ArrowRight class="h-5 w-5" />
                    </Link>
                </template>
            </div>
        </div>
    </nav>

        <!-- Navbar Spacer -->
        <div class="h-24"></div>

        <!-- Hero Section -->
        <section class="relative z-10 pt-20 pb-16 lg:pt-32 lg:pb-24">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <div class="inline-flex items-center gap-2 rounded-full border border-blue-100 bg-blue-50/80 px-4 py-1.5 mb-8 animate-in fade-in slide-in-from-bottom-4 duration-1000">
                    <span class="relative flex h-2 w-2">
                        <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-blue-400 opacity-75"></span>
                        <span class="relative inline-flex h-2 w-2 rounded-full bg-blue-500"></span>
                    </span>
                    <span class="text-xs font-bold uppercase tracking-wider text-blue-600">Terintegrasi SIPD 2026</span>
                </div>
                
                <h1 class="mx-auto max-w-4xl text-4xl font-extrabold tracking-tight text-slate-900 sm:text-6xl lg:text-7xl animate-in fade-in slide-in-from-bottom-6 duration-1000 fill-mode-both leading-[1.2] sm:leading-[1.1]">
                    Kelola Pajak SKPD <br class="hidden sm:block" />
                    <span class="bg-gradient-to-r from-blue-500 via-blue-600 to-indigo-600 bg-clip-text text-transparent">Lebih Cepat & Akurat.</span>
                </h1>
                
                <p class="mx-auto mt-6 max-w-2xl text-base sm:text-lg leading-relaxed text-slate-600 animate-in fade-in slide-in-from-bottom-8 duration-1000 fill-mode-both">
                    Sistem otomatisasi rekapitulasi pajak yang terintegrasi dengan SIPD. Dirancang khusus untuk mempermudah bendahara dalam pelaporan dan rekonsiliasi data.
                </p>

                <div class="mt-10 flex flex-wrap items-center justify-center gap-4 animate-in fade-in slide-in-from-bottom-10 duration-1000 fill-mode-both">
                    <Link
                        v-if="!page.props.auth?.user"
                        :href="route('register')"
                        class="rounded-2xl bg-gradient-to-r from-blue-600 to-indigo-600 px-8 py-4 text-base font-bold text-white shadow-xl shadow-blue-500/25 transition-all hover:scale-[1.02] hover:shadow-blue-500/40 active:scale-95"
                    >
                        Buat Akun Gratis
                    </Link>
                    <Link
                        v-else
                        :href="route('dashboard')"
                        class="rounded-2xl bg-gradient-to-r from-blue-600 to-indigo-600 px-8 py-4 text-base font-bold text-white shadow-xl shadow-blue-500/25 transition-all hover:scale-[1.02] hover:shadow-blue-500/40 active:scale-95"
                    >
                        Buka Dashboard
                    </Link>
                    <button class="flex items-center gap-2 rounded-2xl border border-slate-200 bg-white/80 backdrop-blur-sm px-8 py-4 text-base font-bold text-slate-700 transition-all hover:border-slate-300 hover:bg-white active:scale-95">
                        Lihat Panduan
                        <ChevronRight class="h-5 w-5" />
                    </button>
                </div>
            </div>
        </section>

        <!-- Dynamic Visualization (Restored CSS Mockup) -->
        <section class="relative z-10 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto mb-32">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-start">
                <!-- Left Card (Planner Style) -->
                <div class="lg:col-span-12 xl:col-span-5 rounded-3xl sm:rounded-[2.5rem] bg-white p-6 sm:p-10 shadow-2xl shadow-blue-500/5 border border-white relative overflow-hidden group">
                    <div class="flex justify-between items-center mb-8">
                        <h3 class="text-xl font-bold text-slate-800">Tax Planner</h3>
                        <div class="flex gap-1.5">
                            <div class="w-2 h-2 rounded-full bg-slate-200"></div>
                            <div class="w-2 h-2 rounded-full bg-slate-200"></div>
                        </div>
                    </div>
                    <div class="space-y-6">
                        <div class="flex items-center justify-between p-6 rounded-3xl bg-blue-50/50 border border-blue-100/50 shadow-inner">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-2xl bg-white shadow-md flex items-center justify-center"><Search class="h-6 w-6 text-blue-500" /></div>
                                <span class="font-bold text-slate-700">Audit Rekapitulasi</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="bg-blue-600 text-white px-3 py-1 rounded-xl text-xs font-black shadow-lg shadow-blue-500/20">Active</span>
                                <ChevronRight class="h-5 w-5 text-slate-300" />
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-12 p-12 rounded-[2.5rem] bg-slate-50/50 border border-white shadow-inner flex flex-col items-center">
                      
                        <div class="text-center">
                            <div class="text-3xl font-black text-slate-800 tracking-tighter">Real-time Sync</div>
                            <div class="text-xs text-slate-400 font-bold uppercase tracking-widest mt-1">Sistem Terintegrasi 2026</div>
                        </div>
                    </div>
                </div>

                <!-- Right Card (Main Dashboard View) -->
                <div class="lg:col-span-12 xl:col-span-7 rounded-3xl sm:rounded-[2.5rem] bg-white/60 backdrop-blur-md p-6 sm:p-12 shadow-2xl border border-white/50 relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-64 h-64 bg-blue-500/5 blur-[100px] rounded-full"></div>
                    <div class="flex items-center justify-between mb-8 sm:mb-12">
                        <div class="flex items-center gap-4">
                            <div class="w-1.5 h-8 bg-blue-500 rounded-full"></div>
                            <h3 class="text-2xl font-black text-slate-800 tracking-tight">Data Transaksi Terkini</h3>
                        </div>
                        <div class="text-slate-300"><ChevronRight class="h-8 w-8" /></div>
                    </div>
                    
                    <div class="space-y-8">
                        <div class="p-4 sm:p-6 rounded-3xl bg-white shadow-xl shadow-blue-500/5 border border-blue-50 flex items-center justify-between hover:translate-x-2 transition-transform cursor-pointer group">
                            <div class="flex items-center gap-6">
                                <div class="w-12 h-12 rounded-2xl bg-blue-600 text-white flex items-center justify-center shadow-lg shadow-blue-500/20"><Calendar class="h-6 w-6" /></div>
                                <div>
                                    <div class="font-bold text-lg text-slate-800">Rekapitulasi LS Bulanan</div>
                                    <div class="text-sm text-slate-400">Terakhir disinkronkan 5 menit yang lalu</div>
                                </div>
                            </div>
                            <CheckCircle2 class="h-6 w-6 text-green-500 opacity-0 group-hover:opacity-100 transition-opacity" />
                        </div>

                        <div class="p-4 sm:p-6 rounded-3xl bg-white/40 border border-slate-100 flex items-center justify-between opacity-60">
                            <div class="flex items-center gap-6">
                                <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center"><Zap class="h-6 w-6" /></div>
                                <div>
                                    <div class="font-bold text-lg text-slate-400">Proses Validasi GU (Ongoing)</div>
                                    <div class="text-sm text-slate-300">Menunggu data SIPD RI</div>
                                </div>
                            </div>
                        </div>

                        <!-- Mini Chart Placeholder -->
                        <div class="pt-8 flex items-end gap-3 h-32">
                            <div class="flex-1 bg-blue-100 rounded-2xl h-[40%]"></div>
                            <div class="flex-1 bg-blue-200 rounded-2xl h-[60%]"></div>
                            <div class="flex-1 bg-blue-300 rounded-2xl h-[30%]"></div>
                            <div class="flex-1 bg-blue-500 rounded-2xl h-[90%] shadow-lg shadow-blue-500/20"></div>
                            <div class="flex-1 bg-blue-200 rounded-2xl h-[50%]"></div>
                            <div class="flex-1 bg-blue-100 rounded-2xl h-[70%]"></div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features Grid -->
        <section class="relative z-10 py-24 sm:py-32 text-slate-900">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-20">
                    <h2 class="text-3xl font-bold tracking-tight sm:text-4xl">Fitur Utama Platform</h2>
                    <p class="mt-4 text-lg text-slate-600">Semua yang Anda butuhkan untuk manajemen pajak yang efisien.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <!-- Feature 1 -->
                    <div class="group relative rounded-3xl border border-slate-200 bg-white p-8 transition-all hover:-translate-y-2 hover:border-blue-200 hover:shadow-xl">
                        <div class="mb-5 flex h-12 w-12 items-center justify-center rounded-2xl bg-primary/10 text-primary transition-colors group-hover:bg-gradient-to-br group-hover:from-blue-600 group-hover:to-indigo-600 group-hover:text-white">
                            <Zap class="h-6 w-6" />
                        </div>
                        <h3 class="text-xl font-bold">Sinkronisasi Otomatis</h3>
                        <p class="mt-3 text-slate-600 leading-relaxed text-sm">Integrasi data langsung dari SIPD RI. Cukup satu klik untuk menyinkronkan data BKU dan Pajak tanpa input manual.</p>
                    </div>

                    <!-- Feature 2 -->
                    <div class="group relative rounded-3xl border border-slate-200 bg-white p-8 transition-all hover:-translate-y-2 hover:border-blue-200 hover:shadow-xl">
                        <div class="mb-5 flex h-12 w-12 items-center justify-center rounded-2xl bg-primary/10 text-primary transition-colors group-hover:bg-gradient-to-br group-hover:from-blue-600 group-hover:to-indigo-600 group-hover:text-white">
                            <ShieldCheck class="h-6 w-6" />
                        </div>
                        <h3 class="text-xl font-bold">Validasi Data</h3>
                        <p class="mt-3 text-slate-600 leading-relaxed text-sm">Sistem validasi cerdas untuk mendeteksi ID Billing, NTPN, dan Kode Akun yang tidak sesuai sebelum pelaporan.</p>
                    </div>

                    <!-- Feature 3 -->
                    <div class="group relative rounded-3xl border border-slate-200 bg-white p-8 transition-all hover:-translate-y-2 hover:border-blue-200 hover:shadow-xl">
                        <div class="mb-5 flex h-12 w-12 items-center justify-center rounded-2xl bg-primary/10 text-primary transition-colors group-hover:bg-gradient-to-br group-hover:from-blue-600 group-hover:to-indigo-600 group-hover:text-white">
                            <BarChart3 class="h-6 w-6" />
                        </div>
                        <h3 class="text-xl font-bold">Rekapitulasi Instan</h3>
                        <p class="mt-3 text-slate-600 leading-relaxed text-sm">Hasilkan laporan rekapitulasi pajak per bulan atau per tahun secara instan dengan format yang siap digunakan.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Stats Section -->
        <section class="relative z-10 py-16 bg-blue-600 overflow-hidden text-white">
            <div class="absolute inset-0 bg-gradient-to-br from-blue-600 via-blue-700 to-indigo-800 opacity-90"></div>
            <div class="absolute top-0 right-0 -translate-y-1/2 translate-x-1/2 w-96 h-96 rounded-full border border-white/10"></div>
            <div class="absolute bottom-0 left-0 translate-y-1/2 -translate-x-1/2 w-64 h-64 rounded-full border border-white/5"></div>
            
            <div class="container mx-auto px-4 sm:px-6 lg:px-8 relative z-20">
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-8 text-center">
                    <div>
                        <div class="text-4xl font-extrabold mb-1">100%</div>
                        <div class="text-blue-100 text-sm">Akurat & Aman</div>
                    </div>
                    <div>
                        <div class="text-4xl font-extrabold mb-1">24/7</div>
                        <div class="text-blue-100 text-sm">Akses Kapan Saja</div>
                    </div>
                    <div>
                        <div class="text-4xl font-extrabold mb-1">SIPD</div>
                        <div class="text-blue-100 text-sm">Terintegrasi Penuh</div>
                    </div>
                    <div>
                        <div class="text-4xl font-extrabold mb-1">Free</div>
                        <div class="text-blue-100 text-sm">Bagi SKPD Terdaftar</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="relative z-10 bg-white border-t border-slate-200 pt-16 pb-8">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col md:flex-row justify-between items-start gap-12 mb-16">
                    <div class="max-w-xs">
                        <div class="flex items-center gap-2 mb-6">
                            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-primary">
                                <BarChart3 class="h-4 w-4 text-white" />
                            </div>
                            <span class="text-lg font-bold text-slate-900">Rekap<span class="text-blue-600">Pajak.</span></span>
                        </div>
                        <p class="text-slate-500 text-sm leading-relaxed text-balance">
                            Solusi terbaik untuk pengolahan data pajak pemerintah yang modern, transparan, dan terpercaya.
                        </p>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-8 sm:gap-16">
                        <div>
                            <h4 class="font-bold text-slate-900 mb-6 uppercase text-xs tracking-widest">Aplikasi</h4>
                            <ul class="space-y-4">
                                <li><Link :href="route('login')" class="text-slate-500 hover:text-blue-600 text-sm">Login</Link></li>
                                <li><Link :href="route('register')" class="text-slate-500 hover:text-blue-600 text-sm">Register</Link></li>
                            </ul>
                        </div>
                        <div>
                            <h4 class="font-bold text-slate-900 mb-6 uppercase text-xs tracking-widest">Informasi</h4>
                            <ul class="space-y-4">
                                <li><a href="#" class="text-slate-500 hover:text-blue-600 text-sm">Tentang</a></li>
                                <li><a href="#" class="text-slate-500 hover:text-blue-600 text-sm">Kontak</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <div class="flex flex-col md:flex-row items-center justify-between pt-8 border-t border-slate-100 gap-4">
                    <p class="text-slate-400 text-xs">
                        &copy; 2026 RekapPajak Integration. All rights reserved.
                    </p>
                </div>
            </div>
        </footer>
    </div>
</template>

<style scoped>
@keyframes fade-in {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes slide-in-from-bottom {
    from { transform: translateY(20px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}

.animate-in {
    animation-duration: 1000ms;
    animation-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    animation-fill-mode: both;
}

.fade-in {
    animation-name: fade-in;
}

.slide-in-from-bottom-4 {
    animation-name: slide-in-from-bottom;
}

.slide-in-from-bottom-6 {
    animation-name: slide-in-from-bottom;
    animation-delay: 200ms;
}

.slide-in-from-bottom-8 {
    animation-name: slide-in-from-bottom;
    animation-delay: 400ms;
}

.slide-in-from-bottom-10 {
    animation-name: slide-in-from-bottom;
    animation-delay: 600ms;
}

.fill-mode-both {
    animation-fill-mode: both;
}
</style>
