<?php

namespace FI\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'FI\Events\AttachmentCreating' => [
            'FI\Events\Listeners\AttachmentCreatingListener',
        ],

        'FI\Events\AttachmentDeleted' => [
            'FI\Events\Listeners\AttachmentDeletedListener',
        ],

        'FI\Events\CheckAttachment' => [
            'FI\Events\Listeners\CheckAttachmentListener',
        ],

        'FI\Events\ClientCreated' => [
            'FI\Events\Listeners\ClientCreatedListener',
        ],

        'FI\Events\ClientCreating' => [
            'FI\Events\Listeners\ClientCreatingListener',
        ],

        'FI\Events\ClientDeleted' => [
            'FI\Events\Listeners\ClientDeletedListener',
        ],

        'FI\Events\ClientSaving' => [
            'FI\Events\Listeners\ClientSavingListener',
        ],

        'FI\Events\CompanyProfileCreated' => [
            'FI\Events\Listeners\CompanyProfileCreatedListener',
        ],

        'FI\Events\CompanyProfileCreating' => [
            'FI\Events\Listeners\CompanyProfileCreatingListener',
        ],

        'FI\Events\CompanyProfileDeleted' => [
            'FI\Events\Listeners\CompanyProfileDeletedListener',
        ],

        'FI\Events\CompanyProfileSaving' => [
            'FI\Events\Listeners\CompanyProfileSavingListener',
        ],

        'FI\Events\ExpenseCreated' => [
            'FI\Events\Listeners\ExpenseCreatedListener',
        ],

        'FI\Events\ExpenseDeleting' => [
            'FI\Events\Listeners\ExpenseDeletingListener',
        ],

        'FI\Events\ExpenseSaved' => [],

        'FI\Events\ExpenseSaving' => [
            'FI\Events\Listeners\ExpenseSavingListener',
        ],

        'FI\Events\InvoiceCreated' => [
            'FI\Events\Listeners\InvoiceCreatedListener',
        ],

        'FI\Events\InvoiceCreating' => [
            'FI\Events\Listeners\InvoiceCreatingListener',
        ],

        'FI\Events\InvoiceCreatedRecurring' => [
            'FI\Events\Listeners\InvoiceCreatedRecurringListener',
        ],

        'FI\Events\InvoiceDeleted' => [
            'FI\Events\Listeners\InvoiceDeletedListener',
        ],

        'FI\Events\InvoiceEmailing' => [
            'FI\Events\Listeners\InvoiceEmailingListener',
        ],

        'FI\Events\InvoiceEmailed' => [
            'FI\Events\Listeners\InvoiceEmailedListener',
        ],

        'FI\Events\InvoiceItemSaving' => [
            'FI\Events\Listeners\InvoiceItemSavingListener',
        ],

        'FI\Events\InvoiceModified' => [
            'FI\Events\Listeners\InvoiceModifiedListener',
        ],

        'FI\Events\InvoiceViewed' => [
            'FI\Events\Listeners\InvoiceViewedListener',
        ],

        'FI\Events\NoteCreated' => [
            'FI\Events\Listeners\NoteCreatedListener',
        ],

        'FI\Events\OverdueNoticeEmailed' => [],

        'FI\Events\PaymentCreated' => [
            'FI\Events\Listeners\PaymentCreatedListener',
        ],

        'FI\Events\PaymentCreating' => [
            'FI\Events\Listeners\PaymentCreatingListener',
        ],

        'FI\Events\QuoteCreated' => [
            'FI\Events\Listeners\QuoteCreatedListener',
        ],

        'FI\Events\QuoteCreating' => [
            'FI\Events\Listeners\QuoteCreatingListener',
        ],

        'FI\Events\QuoteDeleted' => [
            'FI\Events\Listeners\QuoteDeletedListener',
        ],

        'FI\Events\QuoteItemSaving' => [
            'FI\Events\Listeners\QuoteItemSavingListener',
        ],

        'FI\Events\QuoteModified' => [
            'FI\Events\Listeners\QuoteModifiedListener',
        ],

        'FI\Events\QuoteEmailed' => [
            'FI\Events\Listeners\QuoteEmailedListener',
        ],

        'FI\Events\QuoteEmailing' => [
            'FI\Events\Listeners\QuoteEmailingListener',
        ],

        'FI\Events\QuoteApproved' => [
            'FI\Events\Listeners\QuoteApprovedListener',
        ],

        'FI\Events\QuoteRejected' => [
            'FI\Events\Listeners\QuoteRejectedListener',
        ],

        'FI\Events\QuoteViewed' => [
            'FI\Events\Listeners\QuoteViewedListener',
        ],

        'FI\Events\RecurringInvoiceCreated' => [
            'FI\Events\Listeners\RecurringInvoiceCreatedListener',
        ],

        'FI\Events\RecurringInvoiceCreating' => [
            'FI\Events\Listeners\RecurringInvoiceCreatingListener',
        ],

        'FI\Events\RecurringInvoiceDeleted' => [
            'FI\Events\Listeners\RecurringInvoiceDeletedListener',
        ],

        'FI\Events\RecurringInvoiceItemSaving' => [
            'FI\Events\Listeners\RecurringInvoiceItemSavingListener',
        ],

        'FI\Events\RecurringInvoiceModified' => [
            'FI\Events\Listeners\RecurringInvoiceModifiedListener',
        ],

        'FI\Events\SettingSaving' => [
            'FI\Events\Listeners\SettingSavingListener',
        ],

        'FI\Events\UserCreated' => [
            'FI\Events\Listeners\UserCreatedListener',
        ],

        'FI\Events\UserDeleted' => [
            'FI\Events\Listeners\UserDeletedListener',
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
