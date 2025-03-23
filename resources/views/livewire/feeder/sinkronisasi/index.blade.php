<div>
    <flux:main container>
        <flux:heading size="xl" level="1" class="mb-5"><i class="fa fa-cloud-arrow-down me-4"></i>Sinkronisasi Feeder
        </flux:heading>

        <flux:separator variant="subtle" />
        <div class="mt-5">
            @if ($batchId)
            <div wire:poll.1000ms="getBatchProgress" class="mt-4 mb-5">
                <p class="text-lg font-medium text-gray-700">Progress: {{ $progress }}%</p>

                <div class="w-full bg-gray-200 rounded-full h-4 dark:bg-gray-700 mt-2">
                    <div class="bg-blue-600 h-4 rounded-full transition-all duration-500"
                        style="width: {{ $progress }}%"></div>
                </div>

                @if ($completed)
                <p class="mt-2 text-green-600 font-semibold">Batch Selesai!</p>
                @endif
            </div>
            @endif
            <div class="overflow-x-auto">
                {{-- buat halaman dimana terdapat 2 kolom yaitu title dan tombol sync --}}
                <table class="w-full text-left table-auto min-w-max">
                    <thead>
                        <tr>
                            <th class="p-4 border-b border-slate-600 bg-slate-700">
                                <p class="text-sm font-normal leading-none text-slate-300">
                                    Name
                                </p>
                            </th>
                            <th class="p-4 border-b border-slate-600 bg-slate-700">
                                <p class="text-sm font-normal leading-none text-slate-300">
                                    ACT
                                </p>
                            </th>
                            <th class="p-4 border-b border-slate-600 bg-slate-700">
                                <p class="text-sm font-normal leading-none text-slate-300">
                                    Status Sync
                                </p>
                            </th>

                        </tr>
                    </thead>
                    <tbody>
                        <tr class="hover:bg-slate-100 dark:hover:bg-slate-700">
                            <td
                                class="p-4 border-b border-slate-700 text-sm text-neutral-950 dark:text-slate-100 font-semibold">
                                Referensi
                            </td>
                            <td class="p-4 border-b border-slate-700 text-sm text-neutral-950 dark:text-slate-300">
                                <flux:button variant="primary" size="sm" wire:click="sync_referensi"
                                    wire:confirm="Apakah Kamu Yakin?"><i class="fas fa-sync me-3"></i> Sync data
                                </flux:button>
                            </td>
                            <td class="p-4 border-b border-slate-700 text-sm text-neutral-950 dark:text-slate-300">
                                <flux:button variant="primary" size="sm" wire:click="sync_referensi"
                                    wire:confirm="Apakah Kamu Yakin?"><i class="fas fa-sync me-3"></i> Sync data
                                </flux:button>
                            </td>
                        </tr>
                        <tr class="hover:bg-slate-100 dark:hover:bg-slate-700">
                            <td
                                class="p-4 border-b border-slate-700 text-sm text-neutral-950 dark:text-slate-100 font-semibold">
                                Data Mahasiswa
                            </td>
                            <td class="p-4 border-b border-slate-700 text-sm text-neutral-950 dark:text-slate-300">
                                <flux:button variant="primary" size="sm" wire:click="sync_mahasiswa"
                                    wire:confirm="Apakah Kamu Yakin?"><i class="fas fa-sync me-3"></i> Sync data
                                </flux:button>
                            </td>
                            <td class="p-4 border-b border-slate-700 text-sm text-neutral-950 dark:text-slate-300">
                                {{$batchId}}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </flux:main>
</div>
