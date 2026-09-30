<script setup>
import { ref } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { 
    Database, 
    HardDrive, 
    Download, 
    Trash2, 
    RefreshCw, 
    ShieldCheck, 
    AlertCircle, 
    FileArchive, 
    Layers,
    Clock,
    FileCheck
} from 'lucide-vue-next';

const props = defineProps({
    backups: {
        type: Array,
        default: () => []
    }
});

const isCreatingDb = ref(false);
const isCreatingFull = ref(false);
const deletingFile = ref(null);

const createDbBackup = () => {
    isCreatingDb.value = true;
    router.post(route('admin.backups.create_db'), {}, {
        preserveScroll: true,
        onFinish: () => {
            isCreatingDb.value = false;
        }
    });
};

const createFullBackup = () => {
    isCreatingFull.value = true;
    router.post(route('admin.backups.create_full'), {}, {
        preserveScroll: true,
        onFinish: () => {
            isCreatingFull.value = false;
        }
    });
};

const deleteBackup = (fileName) => {
    if (confirm(`Are you sure you want to permanently delete backup "${fileName}"?`)) {
        deletingFile.value = fileName;
        router.delete(route('admin.backups.destroy', { file: fileName }), {
            preserveScroll: true,
            onFinish: () => {
                deletingFile.value = null;
            }
        });
    }
};

const getBackupType = (fileName) => {
    if (fileName.includes('-full-')) {
        return { label: 'Full System (DB + Media)', color: 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-300 dark:border-emerald-800' };
    }
    return { label: 'Database SQL', color: 'bg-primary/10 text-primary border-primary/20 dark:bg-primary/20 dark:text-primary-light dark:border-primary/30' };
};
</script>

<template>
    <Head title="System Backups — Admin" />

    <AdminLayout>
        <div class="space-y-6">
            <!-- Header with Action Buttons -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-black tracking-tight text-neutral-900 dark:text-white">Database & System Backups</h1>
                    <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-1">Generate complete SQL database dumps and media archives to keep your store data secure</p>
                </div>

                <div class="flex items-center gap-3">
                    <button
                        type="button"
                        @click="createDbBackup"
                        :disabled="isCreatingDb || isCreatingFull"
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-primary text-white font-bold text-xs shadow-md shadow-primary/25 hover:bg-primary/95 transition-all disabled:opacity-50 cursor-pointer"
                    >
                        <RefreshCw v-if="isCreatingDb" class="w-4 h-4 animate-spin" />
                        <Database v-else class="w-4 h-4" />
                        <span>{{ isCreatingDb ? 'Dumping DB...' : 'Create DB Backup' }}</span>
                    </button>

                    <button
                        type="button"
                        @click="createFullBackup"
                        :disabled="isCreatingDb || isCreatingFull"
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-secondary text-white font-bold text-xs shadow-md shadow-secondary/25 hover:bg-secondary/95 transition-all disabled:opacity-50 cursor-pointer"
                    >
                        <RefreshCw v-if="isCreatingFull" class="w-4 h-4 animate-spin" />
                        <HardDrive v-else class="w-4 h-4" />
                        <span>{{ isCreatingFull ? 'Archiving Full...' : 'Create Full Backup' }}</span>
                    </button>
                </div>
            </div>

            <!-- Information Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white dark:bg-neutral-900 p-5 rounded-2xl border border-neutral-100 dark:border-neutral-800 flex items-start gap-4 shadow-sm">
                    <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center text-primary shrink-0">
                        <Database class="w-5 h-5" />
                    </div>
                    <div>
                        <h4 class="text-xs font-black text-neutral-900 dark:text-white uppercase tracking-wider">Database Dump</h4>
                        <p class="text-[11px] text-neutral-500 dark:text-neutral-400 mt-1">Captures all tables, orders, products, customers, and coupons into a compressed SQL archive.</p>
                    </div>
                </div>

                <div class="bg-white dark:bg-neutral-900 p-5 rounded-2xl border border-neutral-100 dark:border-neutral-800 flex items-start gap-4 shadow-sm">
                    <div class="w-10 h-10 rounded-xl bg-secondary/10 flex items-center justify-center text-secondary shrink-0">
                        <HardDrive class="w-5 h-5" />
                    </div>
                    <div>
                        <h4 class="text-xs font-black text-neutral-900 dark:text-white uppercase tracking-wider">Full Store Archive</h4>
                        <p class="text-[11px] text-neutral-500 dark:text-neutral-400 mt-1">Includes the full SQL database plus all product images, sliders, and uploaded media files.</p>
                    </div>
                </div>

                <div class="bg-white dark:bg-neutral-900 p-5 rounded-2xl border border-neutral-100 dark:border-neutral-800 flex items-start gap-4 shadow-sm">
                    <div class="w-10 h-10 rounded-xl bg-emerald-500/10 flex items-center justify-center text-emerald-600 shrink-0">
                        <ShieldCheck class="w-5 h-5" />
                    </div>
                    <div>
                        <h4 class="text-xs font-black text-neutral-900 dark:text-white uppercase tracking-wider">Secure Storage</h4>
                        <p class="text-[11px] text-neutral-500 dark:text-neutral-400 mt-1">Archives are stored in isolated private storage directories and downloadable only by authenticated admins.</p>
                    </div>
                </div>
            </div>

            <!-- Backups Table -->
            <div class="bg-white dark:bg-neutral-900 rounded-2xl border border-neutral-100 dark:border-neutral-800 overflow-hidden shadow-sm">
                <div class="p-5 border-b border-neutral-100 dark:border-neutral-800 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <FileArchive class="w-4 h-4 text-primary" />
                        <h3 class="text-xs font-black uppercase tracking-wider text-neutral-900 dark:text-white">Existing Backup Archives ({{ backups.length }})</h3>
                    </div>
                </div>

                <div v-if="backups.length === 0" class="p-12 text-center">
                    <div class="w-16 h-16 rounded-2xl bg-neutral-100 dark:bg-neutral-800 flex items-center justify-center mx-auto text-neutral-400 mb-4">
                        <Layers class="w-8 h-8" />
                    </div>
                    <h3 class="text-sm font-bold text-neutral-800 dark:text-neutral-200">No Backup Archives Found</h3>
                    <p class="text-xs text-neutral-400 max-w-sm mx-auto mt-1 mb-6">Create your first database or full store backup using the action buttons above.</p>
                    <button
                        type="button"
                        @click="createDbBackup"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-primary text-white font-bold text-xs shadow hover:bg-primary/95 transition-all cursor-pointer"
                    >
                        <Database class="w-4 h-4" />
                        <span>Create DB Backup Now</span>
                    </button>
                </div>

                <div v-else class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-neutral-50 dark:bg-neutral-800/50 text-[10px] font-black uppercase tracking-wider text-neutral-500 dark:text-neutral-400 border-b border-neutral-100 dark:border-neutral-800">
                            <tr>
                                <th class="px-5 py-3.5">Archive File</th>
                                <th class="px-5 py-3.5">Backup Type</th>
                                <th class="px-5 py-3.5">Size</th>
                                <th class="px-5 py-3.5">Date Created</th>
                                <th class="px-5 py-3.5 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-100 dark:divide-neutral-800 font-medium">
                            <tr v-for="backup in backups" :key="backup.file_name" class="hover:bg-neutral-50/50 dark:hover:bg-neutral-800/30 transition-colors">
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-xl bg-neutral-100 dark:bg-neutral-800 flex items-center justify-center text-neutral-500 dark:text-neutral-300 shrink-0">
                                            <FileArchive class="w-4 h-4 text-primary" />
                                        </div>
                                        <div>
                                            <p class="font-mono font-bold text-xs text-neutral-900 dark:text-white">{{ backup.file_name }}</p>
                                            <span class="text-[10px] text-neutral-400 font-normal flex items-center gap-1 mt-0.5">
                                                <Clock class="w-3 h-3" />
                                                {{ backup.age }}
                                            </span>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-5 py-4">
                                    <span 
                                        :class="['inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[10px] font-bold border', getBackupType(backup.file_name).color]"
                                    >
                                        <FileCheck class="w-3 h-3" />
                                        {{ getBackupType(backup.file_name).label }}
                                    </span>
                                </td>

                                <td class="px-5 py-4 font-mono font-bold text-neutral-700 dark:text-neutral-300 text-xs">
                                    {{ backup.file_size }}
                                </td>

                                <td class="px-5 py-4 text-neutral-600 dark:text-neutral-400 text-xs font-medium">
                                    {{ backup.last_modified }}
                                </td>

                                <td class="px-5 py-4 text-right">
                                    <div class="inline-flex items-center gap-2">
                                        <a
                                            :href="route('admin.backups.download', { file: backup.file_name })"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 hover:bg-emerald-500/20 text-xs font-bold transition-colors"
                                            title="Download Archive"
                                        >
                                            <Download class="w-3.5 h-3.5" />
                                            <span>Download</span>
                                        </a>

                                        <button
                                            type="button"
                                            @click="deleteBackup(backup.file_name)"
                                            :disabled="deletingFile === backup.file_name"
                                            class="p-1.5 rounded-lg text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-950/40 transition-colors disabled:opacity-50 cursor-pointer"
                                            title="Delete Archive"
                                        >
                                            <RefreshCw v-if="deletingFile === backup.file_name" class="w-4 h-4 animate-spin" />
                                            <Trash2 v-else class="w-4 h-4" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
