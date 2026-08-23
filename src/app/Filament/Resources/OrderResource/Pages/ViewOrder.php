<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewOrder extends ViewRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('proformaPdf')
                ->label(__('store.order_confirmation.download_pdf'))
                ->icon('heroicon-o-document-arrow-down')
                ->url(fn () => route('store.proforma.pdf', $this->record))
                ->openUrlInNewTab(),
            Actions\Action::make('proformaExcel')
                ->label(__('store.order_confirmation.download_excel'))
                ->icon('heroicon-o-table-cells')
                ->url(fn () => route('store.proforma.excel', $this->record))
                ->openUrlInNewTab(),
        ];
    }
}
