<?php

namespace App\Filament\Resources\TeamResource\RelationManagers;

use App\Models\Role;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\AttachAction;
use Filament\Tables\Actions\DetachAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MembersRelationManager extends RelationManager
{
    protected static string $relationship = 'members';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('email')
                    ->required()
                    ->email()
                    ->maxLength(255),
                TextInput::make('password')
                    ->password(),
                Select::make('role')
                    ->options(Role::all()->pluck('name', 'key'))
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('role'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                AttachAction::make()
                    ->label('Add exisiting')
                    ->recordSelect(
                        fn (Select $select) => $select->placeholder('Search users by name or email')
                    )
                    ->recordSelectSearchColumns(['name', 'email'])
                    ->multiple()
                    ->form(fn (AttachAction $action): array => [
                        $action->getRecordSelect(),
                        Select::make('role')
                            ->options(Role::all()->pluck('name', 'key'))
                            ->required()
                    ]),
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                DetachAction::make()
                    ->modalDescription('Are you sure you\'d like to remove this user from this team?')
                    ->hidden(function (Model $record) {
                        if(auth()->user()->id == $record->id) {
                            return true;
                        }

                        return false;
                    }),
                Tables\Actions\EditAction::make()
                    ->hidden(function (Model $record) {
                        if(auth()->user()->id == $record->id) {
                            return true;
                        }

                        return false;
                    }),
                Tables\Actions\DeleteAction::make()
                    ->modalDescription('Are you sure you\'d like to delete this user? The user will be removed from the system.')
                    ->hidden(function (Model $record) {
                        if(auth()->user()->id == $record->id) {
                            return true;
                        }

                        return false;
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateDescription('Create a new user or add an existing one');
    }
}
