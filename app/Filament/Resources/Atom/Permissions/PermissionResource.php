<?php

namespace App\Filament\Resources\Atom\Permissions;

use App\Filament\Resources\Atom\Permissions\Pages\CreatePermission;
use App\Filament\Resources\Atom\Permissions\Pages\EditPermission;
use App\Filament\Resources\Atom\Permissions\Pages\ListPermissions;
use App\Filament\Resources\Atom\Permissions\Pages\ViewPermission;
use App\Filament\Tables\Columns\HabboBadgeColumn;
use App\Filament\Traits\TranslatableResource;
use App\Models\Game\Role;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HtmlString;
use Str;

class PermissionResource extends Resource
{
    use TranslatableResource;

    protected static ?string $model = Role::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-shield-check';

    protected static string|\UnitEnum|null $navigationGroup = 'Website';

    protected static ?string $slug = 'website/permissions';

    public static string $translateIdentifier = 'permissions';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    public static function form(\Filament\Schemas\Schema $schema): \Filament\Schemas\Schema
    {
        return $schema->components([
            Tabs::make('Main')
                ->tabs([
                    Tab::make(__('filament::resources.tabs.General Information'))
                        ->schema([
                            TextInput::make('name')
                                ->label(__('filament::resources.inputs.name'))
                                ->maxLength(255)
                                ->required(),

                            TextInput::make('badge')
                                ->label(__('filament::resources.inputs.badge_code'))
                                ->maxLength(255)
                                ->nullable(),

                            Grid::make(['default' => 2])
                                ->schema([
                                    Toggle::make('hidden_rank')
                                        ->label(__('filament::resources.columns.is_hidden'))
                                        ->columnSpan(1),

                                    Toggle::make('hidden_staff')
                                        ->label(__('filament::resources.columns.hidden_staff') === 'filament::resources.columns.hidden_staff'
                                            ? 'Hidden staff'
                                            : __('filament::resources.columns.hidden_staff'))
                                        ->columnSpan(1),
                                ]),

                            TextInput::make('job_description')
                                ->label(__('filament::resources.inputs.job_description') === 'filament::resources.inputs.job_description'
                                    ? 'Job description'
                                    : __('filament::resources.inputs.job_description'))
                                ->maxLength(255)
                                ->required(),

                            Grid::make(['default' => 2])
                                ->schema([
                                    ColorPicker::make('staff_color')
                                        ->label(__('filament::resources.inputs.staff_color') === 'filament::resources.inputs.staff_color'
                                            ? 'Staff color'
                                            : __('filament::resources.inputs.staff_color'))
                                        ->required(),

                                    TextInput::make('staff_background')
                                        ->label(__('filament::resources.inputs.staff_background') === 'filament::resources.inputs.staff_background'
                                            ? 'Staff background'
                                            : __('filament::resources.inputs.staff_background'))
                                        ->maxLength(255)
                                        ->required(),
                                ]),
                        ]),

                    Tab::make(__('filament::resources.tabs.In-game Permissions'))
                        ->schema([
                            Select::make('permissions')
                                ->label(__('filament::resources.inputs.permissions') === 'filament::resources.inputs.permissions'
                                    ? 'Permissions'
                                    : __('filament::resources.inputs.permissions'))
                                ->relationship('permissions', 'name')
                                ->multiple()
                                ->preload()
                                ->searchable()
                                ->helperText(new HtmlString(
                                    __('filament::resources.sections.permissions.description') === 'filament::resources.sections.permissions.description'
                                        ? 'Select which permissions belong to this role.'
                                        : __('filament::resources.sections.permissions.description')
                                )),
                        ]),
                ])
                ->columnSpanFull()
                ->persistTabInQueryString(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('id', 'desc')
            ->columns([
                TextColumn::make('id')
                    ->label(__('filament::resources.columns.id')),

                HabboBadgeColumn::make('badge')
                    ->alignCenter()
                    ->label(__('filament::resources.columns.image')),

                TextColumn::make('name')
                    ->label(__('filament::resources.columns.name'))
                    ->description(fn (Model $record) => Str::limit((string) ($record->job_description ?? ''), 40))
                    ->tooltip(function (Model $record): ?string {
                        $desc = (string) ($record->job_description ?? '');
                        return strlen($desc) > 40 ? $desc : null;
                    })
                    ->searchable(),

                TextColumn::make('staff_color')
                    ->label(__('filament::resources.columns.staff_color') === 'filament::resources.columns.staff_color'
                        ? 'Staff color'
                        : __('filament::resources.columns.staff_color'))
                    ->searchable(),

                ToggleColumn::make('hidden_rank')
                    ->label(__('filament::resources.columns.is_hidden')),

                ToggleColumn::make('hidden_staff')
                    ->label(__('filament::resources.columns.hidden_staff') === 'filament::resources.columns.hidden_staff'
                        ? 'Hidden staff'
                        : __('filament::resources.columns.hidden_staff')),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                //
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
            'index' => ListPermissions::route('/'),
            'create' => CreatePermission::route('/create'),
            'view' => ViewPermission::route('/{record}'),
            'edit' => EditPermission::route('/{record}/edit'),
        ];
    }
}
