<?php

namespace App\Filament\Resources;

use App\Actions\GetTitleFromURL;
use App\Filament\Resources\LinkResource\Pages;
use App\Filament\Resources\LinkResource\RelationManagers;
use App\Models\Domain;
use App\Models\Link;
use Filament\Forms;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables;
use Filament\Tables\Columns\Layout\Grid;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextColumn\TextColumnSize;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Arr;
use Illuminate\Support\HtmlString;
use Illuminate\Validation\Rules\Unique;

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
                        Select::make('domain_id')
                            ->label('Domain')
                            ->options(
                                Arr::prepend(
                                    Domain::all()
                                        ->pluck("name", "id")
                                        ->toArray(),
                                    app_domain(),
                                    -1
                                )                                
                            )
                            ->dehydrateStateUsing(fn (int $state): int|null => 
                                    $state > 0 ? $state : null
                            )
                            ->hidden(function (string $operation) {

                                if($operation != 'create') {
                                    return true;
                                }

                                return Domain::count() == 0;
                            })
                            ->required(fn () => Domain::count() > 0)
                            ->disabled(fn(?Link $record) => $record != null)
                            ->live(onBlur: true),
                        TextInput::make('short_id')
                            ->label('Short URL')
                            ->prefix(function (Get $get): string { 

                                if(empty($get('domain_id')) || $get('domain_id') < 0)
                                {
                                    return app_domain() . '/';
                                } 
                                
                                return str(
                                    Domain::select("name")
                                        ->findOrFail($get('domain_id'))
                                        ->name
                                    )
                                    ->append('/')
                                    ->value;
                            })
                            ->helperText('Leave blank to generate automatically.')
                            ->regex('/^[a-zA-Z0-9-_]+$/') // Should be according to config
                            ->unique(
                                table: 'links', 
                                column: 'short_id', 
                                ignoreRecord: true,
                                modifyRuleUsing: function (Unique $rule, Get $get) {
                                    return $rule->where('domain_id', $get('domain_id'));
                                }
                            )
                            ->validationMessages([
                                'unique' => 'The link already exist on this domain.',
                            ])
                            ->loadingIndicator('domain_id')
                            ->disabled(fn(?Link $record) => $record != null)
                            ->hidden(fn(?Link $record) => $record != null),
                        TextInput::make('short_url')
                            ->label('Short URL')
                            ->dehydrated(false)
                            ->afterStateHydrated(function (TextInput $component, ?string $state, ?Link $link) {
                                $component->state($link->getShortURL());
                            })
                            ->disabled()
                            ->hidden(fn(string $operation) => $operation == 'create')
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
                                FileUpload::make('choice_page_image')
                                    ->imageEditor()
                                    ->maxSize(1024 * 1024 * 5),
                                TextInput::make('choice_page_title')
                                    ->label('Title')
                                    ->helperText('Give a title to the choice page'),
                                Textarea::make('choice_page_description')
                                    ->label('Description')
                                    ->helperText('Give a short description to the choice page'),
                                Repeater::make('choices')
                                    ->hiddenLabel()
                                    ->relationship()
                                    ->schema([
                                        TextInput::make('title')
                                            ->required()
                                            ->maxLength(255),
                                        TextInput::make('destination_url')
                                            ->url()
                                            ->required(),
                                        Textarea::make('description')
                                    ])
                                    ->orderColumn('sort_order')
                                    ->reorderableWithButtons()
                                    ->collapsible()
                                    ->cloneable()
                                    ->addActionLabel('Add choice')
                                    ->defaultItems(0)
                                    ->grid(2)
                            ]),
                        Tab::make('Security')
                            ->icon('heroicon-o-shield-exclamation')
                            ->schema([
                                Placeholder::make('description')
                                    ->content(function(?Link $link) {
                                        if($link->isPasswordProtected()) {
                                            return  new HtmlString(view('components.password-enabled-label'));
                                        }
                                        
                                        return new HtmlString('If enabled, user will be prompted to enter password before redirecting.'); 
                                    })
                                    ->hiddenLabel(true)
                                    ->columnSpanFull(),
                                Toggle::make('is_password_protected')
                                    ->label('Enable password protection')
                                    ->dehydrated(false)
                                    ->live()
                                    ->afterStateUpdated(fn (Set $set, ?string $state) => $set('password', ''))
                                    ->loadingIndicator('is_password_protected')
                                    ->hidden(function (string $operation, ?Link $record): bool {
                                        if($operation == 'edit' && $record->isPasswordProtected()) {
                                            return true;
                                        }

                                        return false;
                                    }),
                                TextInput::make('password')
                                    ->password()
                                    ->revealable()
                                    ->hidden(function (string $operation, ?Link $record, Get $get): bool {
                                        if($operation == 'edit' && $record->isPasswordProtected()) {
                                            return true;
                                        }

                                        return ! $get('is_password_protected');
                                    })
                                    ->required(fn (Get $get): bool =>  $get('is_password_protected'))
                                    ->columnStart(1),
                                Actions::make([
                                    Action::make('reset_password')
                                        ->label('Reset password')
                                        ->form([
                                            TextInput::make('password')
                                                ->password()
                                                ->revealable()
                                                ->required()
                                        ])
                                        ->modalWidth(MaxWidth::Large)
                                        ->action(function(array $data, Link $record) {
                                            $record->password = $data['password'];
                                            $record->save();
                                        }),
                                    Action::make('remove_password')
                                        ->label('Remove password')
                                        ->requiresConfirmation()
                                        ->color('danger')
                                        ->modalIcon('heroicon-o-shield-exclamation')
                                        ->modalHeading('Remove password')
                                        ->modalDescription('Are you sure you\'d like to remove password protection from this link? Anyone can access the target link if you remove the password.')
                                        ->action(function(Link $record) {
                                            $record->password = null;
                                            $record->save();
                                        })
                                ])
                                ->hidden(function (string $operation, ?Link $link): bool {
                                    if($operation == 'create') 
                                    {
                                        return true;
                                    }

                                    return ! isset($link->password);
                                })
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
