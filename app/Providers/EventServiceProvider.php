<?php

namespace IP\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'IP\Events\AttachmentCreating' => [
            'IP\Events\Listeners\AttachmentCreatingListener',
        ],

        'IP\Events\AttachmentDeleted' => [
            'IP\Events\Listeners\AttachmentDeletedListener',
        ],

        'IP\Events\CheckAttachment' => [
            'IP\Events\Listeners\CheckAttachmentListener',
        ],

        'IP\Events\ClientCreated' => [
            'IP\Events\Listeners\ClientCreatedListener',
        ],

        'IP\Events\ClientCreating' => [
            'IP\Events\Listeners\ClientCreatingListener',
        ],

        'IP\Events\ClientDeleted' => [
            'IP\Events\Listeners\ClientDeletedListener',
        ],

        'IP\Events\ClientSaving' => [
            'IP\Events\Listeners\ClientSavingListener',
        ],

        'IP\Events\CompanyProfileCreated' => [
            'IP\Events\Listeners\CompanyProfileCreatedListener',
        ],

        'IP\Events\CompanyProfileCreating' => [
            'IP\Events\Listeners\CompanyProfileCreatingListener',
        ],

        'IP\Events\CompanyProfileDeleted' => [
            'IP\Events\Listeners\CompanyProfileDeletedListener',
        ],

        'IP\Events\CompanyProfileSaving' => [
            'IP\Events\Listeners\CompanyProfileSavingListener',
        ],

        'IP\Events\ExpenseCreated' => [
            'IP\Events\Listeners\ExpenseCreatedListener',
        ],

        'IP\Events\ExpenseDeleting' => [
            'IP\Events\Listeners\ExpenseDeletingListener',
        ],

        'IP\Events\ExpenseSaved' => [],

        'IP\Events\ExpenseSaving' => [
            'IP\Events\Listeners\ExpenseSavingListener',
        ],

        'IP\Events\InvoiceCreated' => [
            'IP\Events\Listeners\InvoiceCreatedListener',
        ],

        'IP\Events\InvoiceCreating' => [
            'IP\Events\Listeners\InvoiceCreatingListener',
        ],

        'IP\Events\InvoiceCreatedRecurring' => [
            'IP\Events\Listeners\InvoiceCreatedRecurringListener',
        ],

        'IP\Events\InvoiceDeleted' => [
            'IP\Events\Listeners\InvoiceDeletedListener',
        ],

        'IP\Events\InvoiceEmailing' => [
            'IP\Events\Listeners\InvoiceEmailingListener',
        ],

        'IP\Events\InvoiceEmailed' => [
            'IP\Events\Listeners\InvoiceEmailedListener',
        ],

        'IP\Events\InvoiceItemSaving' => [
            'IP\Events\Listeners\InvoiceItemSavingListener',
        ],

        'IP\Events\InvoiceModified' => [
            'IP\Events\Listeners\InvoiceModifiedListener',
        ],

        'IP\Events\InvoiceViewed' => [
            'IP\Events\Listeners\InvoiceViewedListener',
        ],

        'IP\Events\NoteCreated' => [
            'IP\Events\Listeners\NoteCreatedListener',
        ],

        'IP\Events\OverdueNoticeEmailed' => [],

        'IP\Events\PaymentCreated' => [
            'IP\Events\Listeners\PaymentCreatedListener',
        ],

        'IP\Events\PaymentCreating' => [
            'IP\Events\Listeners\PaymentCreatingListener',
        ],

        'IP\Events\QuoteCreated' => [
            'IP\Events\Listeners\QuoteCreatedListener',
        ],

        'IP\Events\QuoteCreating' => [
            'IP\Events\Listeners\QuoteCreatingListener',
        ],

        'IP\Events\QuoteDeleted' => [
            'IP\Events\Listeners\QuoteDeletedListener',
        ],

        'IP\Events\QuoteItemSaving' => [
            'IP\Events\Listeners\QuoteItemSavingListener',
        ],

        'IP\Events\QuoteModified' => [
            'IP\Events\Listeners\QuoteModifiedListener',
        ],

        'IP\Events\QuoteEmailed' => [
            'IP\Events\Listeners\QuoteEmailedListener',
        ],

        'IP\Events\QuoteEmailing' => [
            'IP\Events\Listeners\QuoteEmailingListener',
        ],

        'IP\Events\QuoteApproved' => [
            'IP\Events\Listeners\QuoteApprovedListener',
        ],

        'IP\Events\QuoteRejected' => [
            'IP\Events\Listeners\QuoteRejectedListener',
        ],

        'IP\Events\QuoteViewed' => [
            'IP\Events\Listeners\QuoteViewedListener',
        ],

        'IP\Events\RecurringInvoiceCreated' => [
            'IP\Events\Listeners\RecurringInvoiceCreatedListener',
        ],

        'IP\Events\RecurringInvoiceCreating' => [
            'IP\Events\Listeners\RecurringInvoiceCreatingListener',
        ],

        'IP\Events\RecurringInvoiceDeleted' => [
            'IP\Events\Listeners\RecurringInvoiceDeletedListener',
        ],

        'IP\Events\RecurringInvoiceItemSaving' => [
            'IP\Events\Listeners\RecurringInvoiceItemSavingListener',
        ],

        'IP\Events\RecurringInvoiceModified' => [
            'IP\Events\Listeners\RecurringInvoiceModifiedListener',
        ],

        'IP\Events\SettingSaving' => [
            'IP\Events\Listeners\SettingSavingListener',
        ],

        'IP\Events\UserCreated' => [
            'IP\Events\Listeners\UserCreatedListener',
        ],

        'IP\Events\UserDeleted' => [
            'IP\Events\Listeners\UserDeletedListener',
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
