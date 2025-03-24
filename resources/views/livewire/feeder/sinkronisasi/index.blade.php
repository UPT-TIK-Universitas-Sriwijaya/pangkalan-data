<div>
    @include('livewire.feeder.sinkronisasi.create')
    <flux:main container>
        <flux:heading size="xl" level="1" class="mb-5"><i class="fa fa-cloud-arrow-down me-4"></i>Sinkronisasi Feeder
        </flux:heading>

        <flux:separator variant="subtle" class="mb-5"/>
        <flux:modal.trigger name="edit-profile">
            <flux:button variant="primary" icon="plus" class="bg-indigo-500 hover:bg-indigo-700 text-white">Create Data Sync</flux:button>
        </flux:modal.trigger>
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
                            <th class="p-4 border-b border-slate-600 bg-slate-700 text-center">
                                <p class="text-sm font-normal leading-none text-slate-300">
                                    Name
                                </p>
                            </th>
                            <th class="p-4 border-b border-slate-600 bg-slate-700 text-center">
                                <p class="text-sm font-normal leading-none text-slate-300">
                                    ACT
                                </p>
                            </th>
                            <th class="p-4 border-b border-slate-600 bg-slate-700 text-center">
                                <p class="text-sm font-normal leading-none text-slate-300">
                                    Status Sync
                                </p>
                            </th>

                        </tr>
                    </thead>
                    <tbody @if($hasActiveBatch) wire:poll.1000ms="updateProgress" @endif>
                        @foreach ($sinkronisasiItems as $item)
                            <tr>
                                <td class="border px-4 py-2">{{ $item['nama'] }}</td>
                                <td class="border px-4 py-4 text-nowrap">
                                    <div class="h-full flex items-center justify-center gap-6">
                                        {{-- @include('livewire.feeder.sinkronisasi.delete') --}}
                                        <flux:button variant="danger" size="sm" icon="trash" wire:click="delete({{$item['id']}})"></flux:button>
                                        <flux:separator vertical class="my-2"/>
                                        <flux:button variant="primary" size="sm" wire:click="{{$item['function']}}"
                                            wire:confirm="Apakah Kamu Yakin?"><i class="fas fa-sync me-3"></i> Sync data
                                        </flux:button>
                                    </div>
                                </td>
                                <td class="border px-4 py-2 text-center">
                                    @if ($item['batch_id'])
                                        <div class="w-full bg-gray-200 rounded-full h-4 dark:bg-gray-700">
                                            <div class="bg-blue-600 h-4 rounded-full transition-all duration-500"
                                                style="width: {{ $item['progress'] ?? 0 }}%">
                                            </div>
                                        </div>
                                        <p class="text-sm text-gray-600 mt-1">{{ $item['progress'] ?? 0 }}%</p>
                                    @elseif($item['terakhir_sinkronisasi'])
                                        <span class="text-green
                                        -500">Last Sync ({{$item['terakhir_sinkronisasi']}})</span>
                                    @else
                                        <span class="text-gray-500">Belum ada proses Sinkronisasi</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </flux:main>
</div>
