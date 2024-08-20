<?php

namespace App\Filament\Resources;

use App\Actions\GetTitleFromURL;
use App\Filament\Resources\LinkResource\Pages;
use App\Filament\Resources\LinkResource\RelationManagers;
use App\Models\Link;
use Filament\Forms;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Columns\Layout\Grid;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextColumn\TextColumnSize;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\HtmlString;

class LinkResource extends Resource
{
    protected static ?string $model = Link::class;

    protected static ?string $navigationIcon = 'heroicon-o-link';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        TextInput::make('long_url')
                            ->url()
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (?string $state, Set $set) {
                                if(! is_null($state)) {
                                    $set('title', (new GetTitleFromURL)->execute($state));
                                }
                            }),
                        TextInput::make('title')
                            ->loadingIndicator('long_url'),
                        TextInput::make('short_id')
                            ->label('Short URL')
                            ->prefix('bit.ly/')
                            ->helperText('Leave blank to generate automatically.')
                            ->regex('/^[a-zA-Z0-9-_]+$/')
                            ->unique('links', 'short_id', ignoreRecord: true)
                            ->validationMessages([
                                'unique' => 'The link already exist on this domain.',
                            ])
                            ->disabled(fn(?Link $record) => $record != null)
                    ])
                    ->columns(2),
                Tabs::make()
                    ->tabs([
                        Tab::make('UTM tags')
                            ->icon('heroicon-o-tag')
                            ->schema([
                                Placeholder::make('description')
                                    ->content('All the parameters will be appended to destination URL while redirecting.')
                                    ->hiddenLabel(true)
                                    ->columnSpanFull(),
                                Toggle::make('has_utm_params')
                                    ->label('Add UTM parameters')
                                    ->dehydrated(false)
                                    ->live(),
                                TextInput::make('utm_source')
                                    ->maxLength(255)
                                    ->columnStart(1)
                                    ->visible(fn (Get $get): bool => $get('has_utm_params')),
                                TextInput::make('utm_medium')
                                    ->maxLength(255)
                                    ->visible(fn (Get $get): bool => $get('has_utm_params')),
                                TextInput::make('utm_campaign')
                                    ->maxLength(255)
                                    ->visible(fn (Get $get): bool => $get('has_utm_params')),
                                TextInput::make('utm_term')
                                    ->maxLength(255)
                                    ->visible(fn (Get $get): bool => $get('has_utm_params')),
                                TextInput::make('utm_content')
                                    ->maxLength(255)
                                    ->visible(fn (Get $get): bool => $get('has_utm_params'))
                            ])
                            ->columns(3),
                        Tab::make('Choices')
                            ->icon('heroicon-o-list-bullet')
                            ->schema([
                                Repeater::make('choices')
                                    ->hiddenLabel()
                                    ->relationship()
                                    ->schema([
                                        TextInput::make('title')
                                            ->maxLength(255),
                                        TextInput::make('destination_url')
                                            ->url()
                                    ])
                                    ->orderColumn('sort_order')
                                    ->reorderableWithButtons()
                                    ->collapsible()
                                    ->cloneable()
                                    ->addActionLabel('Add choice')
                                    ->defaultItems(2)
                                    ->grid(2)
                            ]),
                        Tab::make('Security')
                            ->icon('heroicon-o-shield-exclamation')
                            ->schema([
                                Placeholder::make('description')
                                    ->content('If enabled, user will be prompted to enter password before redirecting.')
                                    ->hiddenLabel(true)
                                    ->columnSpanFull(),
                                Toggle::make('is_password_protected')
                                    ->label('Enable password protection')
                                    ->live()
                                    ->loadingIndicator('is_password_protected'),
                                TextInput::make('password')
                                    ->password()
                                    ->revealable()
                                    ->visible(fn (Get $get): bool => $get('is_password_protected'))
                                    ->required(fn (Get $get): bool => $get('is_password_protected'))
                                    ->columnStart(1)
                            ])
                            ->columns(2),
                        Tab::make('Expiration')
                            ->icon('heroicon-o-calendar')
                            ->schema([
                                Placeholder::make('description')
                                    ->content('Set an expiration date to automatically deactivate your link after a specified time.')
                                    ->hiddenLabel(true)
                                    ->columnSpanFull(),
                                Toggle::make('has_expiry')
                                    ->label('Has expiry date?')
                                    ->dehydrated(false)
                                    ->default(fn(Get $get): bool => $get('expires_at') != null)
                                    ->live(),
                                DateTimePicker::make('expires_at')
                                    ->minDate(now('Asia/Kolkata'))
                                    ->timezone('Asia/Kolkata')
                                    ->visible(fn (Get $get): bool => $get('has_expiry'))
                                    ->required(fn (Get $get): bool => $get('has_expiry'))
                                    ->columnStart(1),
                                Toggle::make('delete_after_expired')
                                    ->label('Delete after expired')
                                    ->visible(fn (Get $get): bool => $get('has_expiry'))
                                    ->onIcon('heroicon-o-trash')
                                    ->onColor('danger')
                                    ->inline(false)
                            ])
                            ->columns(2)
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Stack::make([
                    TextColumn::make('title')
                        ->weight(FontWeight::Bold)
                        ->searchable(),
                    TextColumn::make('short_id')
                        ->formatStateUsing(
                            fn (string $state): string => config('lynx.domain') ."/{$state}"
                        )
                        ->copyable()
                        ->copyableState(
                            fn (string $state): string => config('lynx.domain') ."/{$state}"
                        ),
                    Grid::make()
                        ->schema([
                            TextColumn::make('visits_count')
                                ->counts('visits')
                                ->extraAttributes(['class' => "text-sm"])
                                ->formatStateUsing(
                                    fn (string $state): string => "{$state} views"
                                )
                                ->size(TextColumnSize::ExtraSmall)
                                ->icon('heroicon-o-eye')
                                ->sortable(),
                            TextColumn::make('created_at')
                                ->date('M d, Y')
                                ->icon('heroicon-o-calendar')
                                ->size(TextColumnSize::ExtraSmall)
                                ->sortable()
                        ])
                        ->columns(2)
                ])
            ])
            ->contentGrid([
                'md' => 2,
                'xl' => 3
            ])
            ->paginated([
                18,
                36,
                72,
                'all',
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Tables\Actions\Action::make('visit')
                    ->label('Visit link')
                    ->icon('heroicon-m-arrow-top-right-on-square')
                    ->color('gray')
                    ->url(fn (Link $record): string => $record->long_url),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => Pages\ListLinks::route('/'),
            'create' => Pages\CreateLink::route('/create'),
            'edit' => Pages\EditLink::route('/{record}/edit'),
        ];
    }
}
