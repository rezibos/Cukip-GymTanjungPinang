<?php

namespace App\Filament\Resources\Memberships;

use App\Enums\MembershipStatus;
use App\Enums\TransactionStatus;
use App\Filament\Resources\Memberships\Pages\CreateMembership;
use App\Filament\Resources\Memberships\Pages\EditMembership;
use App\Filament\Resources\Memberships\Pages\ListMemberships;
use App\Filament\Resources\Memberships\Schemas\MembershipForm;
use App\Filament\Resources\Memberships\Tables\MembershipsTable;
use App\Models\Membership;
use BackedEnum;
use Filament\Actions\CreateAction;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class MembershipResource extends Resource
{
    protected static ?string $model = Membership::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected static ?string $navigationLabel = 'Membership';

    protected static ?string $modelLabel = 'Membership';

    protected static ?string $pluralModelLabel = 'Membership';

    protected static string|\UnitEnum|null $navigationGroup = 'Operasional';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'id';

    public static function form(Schema $schema): Schema
    {
        return MembershipForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MembershipsTable::configure($table);
    }

    public static function makeCreateAction(): CreateAction
    {
        return CreateAction::make()
            ->label('Tambah')
            ->icon(Heroicon::Plus)
            ->modal()
            ->modalHeading('Tambah Membership')
            ->modalDescription('Lengkapi detail membership baru di bawah ini.')
            ->modalSubmitActionLabel('Simpan')
            ->modalCancelActionLabel('Batal')
            ->modalFooterActionsAlignment(Alignment::End)
            ->modalWidth(Width::FourExtraLarge)
            ->stickyModalHeader()
            ->stickyModalFooter()
            ->using(function (array $data): Membership {
                return DB::transaction(function () use ($data): Membership {
                    $membership = Membership::query()->create(Arr::except($data, [
                        'payment_method',
                        'payment_channel',
                    ]));
                    $membership->refresh();
                    $membership->loadMissing('plan');

                    $transactionStatus = match ($membership->status) {
                        MembershipStatus::PendingPayment => TransactionStatus::Pending,
                        MembershipStatus::Cancelled => TransactionStatus::Rejected,
                        MembershipStatus::Upcoming,
                        MembershipStatus::Active,
                        MembershipStatus::Expired => TransactionStatus::Paid,
                    };

                    $membership->transactions()->create([
                        'user_id' => $membership->user_id,
                        'quantity' => $membership->quantity,
                        'amount' => $membership->plan->price * $membership->quantity,
                        'payment_method' => $data['payment_method'],
                        'payment_channel' => $data['payment_method'] === 'manual'
                            ? $data['payment_channel']
                            : null,
                        'status' => $transactionStatus,
                        'paid_at' => $transactionStatus === TransactionStatus::Paid ? now() : null,
                        'admin_note' => 'Dibuat manual oleh admin.',
                    ]);

                    return $membership;
                });
            })
            ->createAnother(false);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMemberships::route('/'),
            'create' => CreateMembership::route('/create'),
            'edit' => EditMembership::route('/{record}/edit'),
        ];
    }
}
