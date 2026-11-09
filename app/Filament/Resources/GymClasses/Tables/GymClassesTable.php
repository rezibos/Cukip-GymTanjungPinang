<?php

namespace App\Filament\Resources\GymClasses\Tables;

use App\Enums\DayOfWeek;
use App\Models\ClassSession;
use App\Models\GymClass;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\Width;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Carbon;

class GymClassesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Nama Kelas')->searchable(),
                TextColumn::make('day_of_week')->label('Hari')
                    ->formatStateUsing(fn (DayOfWeek $state) => $state->label()),
                TextColumn::make('time_range')->label('Jam'),
                TextColumn::make('instructor')->label('Instruktur')->placeholder('-'),
                TextColumn::make('capacity')->label('Kapasitas'),
                IconColumn::make('is_active')->label('Aktif')->boolean(),
            ])
            ->recordUrl(null)
            ->headerActions([
                Action::make('generateSessions')
                    ->label('Generate Sesi')
                    ->icon('heroicon-o-calendar-days')
                    ->color('success')
                    ->schema([
                        TextInput::make('weeks')->label('Berapa minggu ke depan?')
                            ->numeric()->integer()->minValue(1)->maxValue(12)->default(4)->required(),
                    ])
                    ->action(function (array $data) {
                        $weeks = (int) $data['weeks'];
                        $classes = GymClass::where('is_active', true)->get();
                        $created = 0;

                        for ($i = 0; $i < $weeks * 7; $i++) {
                            $date = Carbon::today()->addDays($i);
                            $dayEnum = DayOfWeek::fromCarbon($date);

                            foreach ($classes as $class) {
                                if ($class->day_of_week !== $dayEnum) {
                                    continue;
                                }

                                $session = ClassSession::firstOrNew([
                                    'gym_class_id' => $class->id,
                                    'date' => $date->toDateString(),
                                ]);

                                if (! $session->exists) {
                                    $session->capacity = $class->capacity;
                                    $session->save();
                                    $created++;
                                }
                            }
                        }

                        Notification::make()
                            ->title($created.' sesi baru berhasil dibuat')
                            ->body($created === 0 ? 'Semua sesi pada periode ini sudah ada sebelumnya.' : null)
                            ->success()
                            ->send();
                    }),
            ])
            ->recordActions([
                EditAction::make()
                    ->label('Ubah')
                    ->modal()
                    ->modalHeading('Ubah Jenis Kelas')
                    ->modalDescription('Perbarui data jenis kelas di bawah ini.')
                    ->modalSubmitActionLabel('Simpan')
                    ->modalCancelActionLabel('Batal')
                    ->modalFooterActionsAlignment(Alignment::End)
                    ->modalWidth(Width::FourExtraLarge)
                    ->stickyModalHeader()
                    ->stickyModalFooter(),
                DeleteAction::make(),
            ])
            ->recordActionsColumnLabel('Action')
            ->recordActionsAlignment('center');
    }
}
