<!-- resources/views/details-modal.blade.php -->
<div class="space-y-4">
    <div>
        <label class="font-medium">Name</label>
        <div>{{ $record->name }}</div>
    </div>

    <div>
        <label class="font-medium">Email</label>
        <div>{{ $record->email }}</div>
    </div>

    <div>
        <label class="font-medium">Additional Information</label>
        <div>{{ $record->additional_info }}</div>
    </div>
</div>
