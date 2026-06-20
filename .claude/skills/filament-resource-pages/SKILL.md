---
name: filament-resource-pages
description: "Defines the Filament v4 resource page structure used in this project: Resource + Pages + Schemas + Tables split, action patterns, and BaseResource conventions."
license: MIT
metadata:
  author: project
---

# Filament Resource Pages

## Resource Directory Layout

Every resource lives under `Modules/{Name}/Filament/{Panel}/Resources/{Model}/`:

```
{Model}Resource.php          ← extends BaseResource; declares model, nav, pages
Pages/
  List{Model}.php             ← extends ListRecords
  Create{Model}.php           ← extends CreateRecord
  Edit{Model}.php             ← extends EditRecord
Schemas/
  {Model}Form.php             ← static configure(Schema $schema): Schema
Tables/
  {Model}sTable.php           ← static configure(Table $table): Table
RelationManagers/             ← optional
```

Schemas and Tables are **separate classes**, never defined inline inside the Resource.

---

## Resource Class

```php
class InvoiceResource extends BaseResource
{
    protected static ?string $model = Invoice::class;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;
    protected static ?int $navigationSort = 10;
    protected static bool $isScopedToTenant = true;

    public static function form(Schema $schema): Schema
    {
        return InvoiceForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return InvoicesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListInvoices::route('/'),
            'create' => Pages\CreateInvoice::route('/create'),
            'edit'   => Pages\EditInvoice::route('/{record}/edit'),
        ];
    }
}
```

`BaseResource` handles tenant-scoped queries automatically — do NOT add manual `company_id` filters.

---

## List Page

```php
class ListInvoices extends ListRecords
{
    protected static string $resource = InvoiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->modalWidth('full')
                ->action(function (array $data) {
                    app(InvoiceService::class)->createInvoice($data);
                }),
        ];
    }
}
```

---

## Edit Page

Override `save()` when you need to route the update through the service layer:

```php
class EditInvoice extends EditRecord
{
    protected static string $resource = InvoiceResource::class;

    public function save(bool $shouldRedirect = true, bool $shouldSendSavedNotification = true): void
    {
        $this->authorizeAccess();
        $this->callHook('beforeValidate');
        $data = $this->form->getState();
        $this->callHook('afterValidate');
        $data = $this->mutateFormDataBeforeSave($data);
        $this->callHook('beforeSave');

        app(InvoiceService::class)->updateInvoice($data, $this->getRecord());

        $this->callHook('afterSave');

        if ($shouldRedirect) {
            $this->redirect($this->getRedirectUrl());
        }
    }

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
```

---

## Schema Class

```php
class InvoiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Grid::make(2)->schema([
                Section::make('Details')->schema([
                    Select::make('customer_id')->relationship('customer', 'company_name')->required(),
                    DatePicker::make('invoice_date')->required(),
                ]),
            ]),
        ]);
    }
}
```

---

## Table Class

```php
class InvoicesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('invoice_number')->searchable()->sortable(),
                TextColumn::make('invoice_status')->badge(),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }
}
```

---

## Action Closure Rule

Filament action closures do NOT support constructor injection. Always use `app()`:

```php
->action(function (array $data) {
    app(InvoiceService::class)->createInvoice($data);
})
```

This is the only place `app()` is acceptable. Services themselves must never use it.

---

## Panel Registration

Resources are discovered per module in `CompanyPanelProvider`:

```php
->discoverResources(
    in: base_path('modules/invoices/src/Filament/Company/Resources'),
    for: 'Modules\\Invoices\\Filament\\Company\\Resources'
)
```

The `in` parameter uses the filesystem path (lowercase with `src/`), while `for` uses the PHP namespace.
