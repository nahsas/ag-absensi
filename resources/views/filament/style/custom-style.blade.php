@if (request()->routeIs('filament.admin.pages.dashboard'))
<form action="{{ route('unduh-excel-range') }}" method="GET" style="display: inline-block;">
    <x-filament::fieldset>
        <x-slot name="label">
            Export Excel dengan Rentang Tanggal
        </x-slot>
        <div style="display: flex; gap: 10px; align-items: center;">
            <div style="display: flex; flex-direction: column;">
                <label for="start_date_excel">Dari Tanggal:</label>
                <input type="date" id="start_date_excel" name="start_date" required style="padding: 8px; border-radius: 4px; border: 1px solid #ccc;">
            </div>
            <div style="display: flex; flex-direction: column;">
                <label for="end_date_excel">Sampai Tanggal:</label>
                <input type="date" id="end_date_excel" name="end_date" required style="padding: 8px; border-radius: 4px; border: 1px solid #ccc;">
            </div>
            <x-filament::button type="submit" color="primary">
                Export Excel
            </x-filament::button>
        </div>
    </x-filament::fieldset>
</form>
@endif
@if (request()->routeIs('filament.admin.pages.dashboard'))
<form action="{{ route('unduh-pdf') }}" method="GET" style="margin:10px;">
    <x-filament::fieldset>
        <x-slot name="label">
            Export PDF dengan Rentang Tanggal
        </x-slot>
        <div style="display: flex; gap: 10px; align-items: center;">
            <div style="display: flex; flex-direction: column;">
                <label for="start_date">Dari Tanggal:</label>
                <input type="date" id="start_date" name="start_date" required style="padding: 8px; border-radius: 4px; border: 1px solid #ccc;">
            </div>
            <div style="display: flex; flex-direction: column;">
                <label for="end_date">Sampai Tanggal:</label>
                <input type="date" id="end_date" name="end_date" required style="padding: 8px; border-radius: 4px; border: 1px solid #ccc;">
            </div>
            <x-filament::button type="submit" color="danger">
                Export PDF
            </x-filament::button>
        </div>
    </x-filament::fieldset>
</form>
@endif