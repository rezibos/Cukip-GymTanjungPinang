<?php

namespace App\Filament\Resources\Transactions\Schemas;

use App\Enums\TransactionStatus;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ViewField;
use Filament\Schemas\Schema;

class TransactionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('invoice_number')->label('Nomor Invoice')->disabled(),
            Select::make('user_id')->label('User')->relationship('user', 'name')->disabled(),
            TextInput::make('amount')->label('Jumlah')->disabled()
                ->formatStateUsing(fn ($state) => 'Rp'.number_format((int) $state, 0, ',', '.')),
            TextInput::make('quantity')->label('Kuantitas')->disabled(),

            ViewField::make('payment_proof')
                ->label('Bukti Transfer')
                ->view('filament.forms.payment-proof-preview'),

            Select::make('status')->label('Status')
                ->options([
                    TransactionStatus::Pending->value => 'Menunggu Konfirmasi',
                    TransactionStatus::Paid->value => 'Lunas',
                    TransactionStatus::Rejected->value => 'Ditolak',
                ])
                ->required(),

            Textarea::make('admin_note')->label('Catatan Admin')->rows(2)->columnSpanFull(),
        ]);
    }
}
