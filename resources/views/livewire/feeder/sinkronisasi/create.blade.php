<flux:modal name="edit-profile" class="md:w-lg" :dismissible="false" wire:model.self="showConfirmModal">
    <div class="space-y-6">
        <div>
            <flux:heading size="lg">Create Data Sync</flux:heading>
            <flux:text class="mt-2">Pahami fungsi dari table ini terlebih dahulu sebelum membuat data</flux:text>
        </div>
        <flux:separator variant="subtle" class="mt-5" />
        <form wire:submit.prevent="create">
            <flux:input label="Name" placeholder="Nama Sinkronisasi"  wire:model.blur="name_create" />
            <flux:separator variant="subtle" class="mt-5 mb-4" />
            <flux:input label="Batch Name" placeholder="Nama Batch" wire:model.blur="batch_name_create" />
            <flux:separator variant="subtle" class="mt-5 mb-4" />
            <flux:input label="Function Name" placeholder="Nama Fungsi Controller" wire:model.blur="function_name_create" />
            <flux:separator variant="subtle" class="mt-5 mb-4" />
            <div class="flex">
                <flux:spacer />

                <flux:button type="submit" variant="primary" x-on:click="$wire.showConfirmModal = false">Save</flux:button>
            </div>
        </form>
    </div>
</flux:modal>
