@extends('admin.layouts.master')

@section('title', 'System Backups — Admin')

@section('content')

    <!-- Header -->
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-gray-800 dark:text-white">System Backups</h1>
            <p class="text-xs text-gray-500 dark:text-gray-400">Automated and manual database & store asset backups</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <form action="{{ route('admin.backups.create_db') }}" method="POST">
                @csrf
                <button type="submit" class="btn inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2.5 text-xs font-bold text-white shadow-md hover:bg-primary-hover transition-all">
                    <i class="fa-solid fa-database"></i> Create DB Backup
                </button>
            </form>

            <form action="{{ route('admin.backups.create_full') }}" method="POST">
                @csrf
                <button type="submit" class="btn inline-flex items-center gap-2 rounded-lg bg-secondary px-4 py-2.5 text-xs font-bold text-white shadow-md hover:bg-secondary-hover transition-all">
                    <i class="fa-solid fa-file-zipper"></i> Full Backup (DB + Media)
                </button>
            </form>
        </div>
    </div>

    <!-- Info Banner -->
    <div class="panel p-4 mb-6 bg-purple-50 dark:bg-[#1f1b3c] border border-primary/20 flex items-start gap-3">
        <div class="h-9 w-9 rounded-xl bg-primary text-white flex items-center justify-center shrink-0 text-base">
            <i class="fa-solid fa-shield-halved"></i>
        </div>
        <div>
            <h4 class="text-xs font-bold text-gray-800 dark:text-white">Secure Automated Backups Powered by Spatie</h4>
            <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-0.5">
                All database tables (products, orders, customers, coupons) and uploaded WebP product catalog assets are zipped safely. You can download archives directly or keep them stored on disk.
            </p>
        </div>
    </div>

    <!-- Backups Table -->
    <div class="panel">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-gray-100 dark:border-[#192a43] text-gray-400 uppercase font-semibold">
                        <th class="pb-3 px-3">#</th>
                        <th class="pb-3 px-3">Archive File Name</th>
                        <th class="pb-3 px-3">Archive Size</th>
                        <th class="pb-3 px-3">Created Date</th>
                        <th class="pb-3 px-3">Age</th>
                        <th class="pb-3 px-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-[#192a43]">
                    @forelse($backups as $index => $backup)
                        <tr class="hover:bg-gray-50 dark:hover:bg-[#14233c]/50 transition-colors">
                            <td class="py-3 px-3 text-gray-400 font-mono">{{ $index + 1 }}</td>
                            <td class="py-3 px-3">
                                <span class="inline-flex items-center gap-2 font-mono font-bold text-gray-800 dark:text-white">
                                    <i class="fa-solid fa-file-zipper text-primary"></i> {{ $backup['file_name'] }}
                                </span>
                            </td>
                            <td class="py-3 px-3">
                                <span class="badge badge-outline-primary text-[11px] font-mono">{{ $backup['file_size'] }}</span>
                            </td>
                            <td class="py-3 px-3 font-semibold text-gray-700 dark:text-gray-300">
                                {{ $backup['last_modified'] }}
                            </td>
                            <td class="py-3 px-3 text-gray-400">
                                {{ $backup['age'] }}
                            </td>
                            <td class="py-3 px-3 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.backups.download', ['file' => $backup['file_name']]) }}" 
                                       class="btn inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-primary text-white font-bold text-[11px] hover:bg-primary-hover shadow-sm transition-all" title="Download Zip File">
                                        <i class="fa-solid fa-download text-xs"></i> Download
                                    </a>
                                    
                                    <form action="{{ route('admin.backups.destroy', ['file' => $backup['file_name']]) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete backup file {{ $backup['file_name'] }}?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="h-8 w-8 rounded-lg bg-red-50 dark:bg-red-950/40 text-danger hover:bg-danger hover:text-white flex items-center justify-center transition-all" title="Delete Archive">
                                            <i class="fa-solid fa-trash text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-gray-400">
                                <i class="fa-solid fa-file-zipper text-3xl mb-2 block"></i>
                                No backups found. Click "Create DB Backup" to create your first backup archive.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

@endsection

