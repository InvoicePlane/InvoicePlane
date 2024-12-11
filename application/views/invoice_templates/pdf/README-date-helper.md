# Helpers
## `invoice_get_locale_by_displayname` helper

This helper finds the shortest locale for a customers language and does a fall back to the default locale 'en' if none could be found.
The local is usually a two digit code like `en`, `de` etc.

## `invoice_replace_date_tags` helper

This helper is intended to set date items in your item description wich are relative and dependant to the new invoice date.
Original the helper was intended for recurring invoices where date parts within the item text needs changes relative to the new invoice date.
Implemented now as a helper it can be used for any item description and may be thought as a template for a date descrition part.
It will look for tags in your item description surrounded with three braces `{{{...}}}`
* if you use `{{{Date}}}` it will print a full date in the customers locale format.
* if you use `{{{Month}}}`you will get the 3-chars month and 4-digit year in the customers locale format.
* if you use `{{{Year}}}`you will get a 4-digit year.
> You can attach a plus or minus sign `+/-` directly followed by a number to the tag, this will increment/decrement the date/month/year. The helper also takes care of the year overflow.

Possible construction of `{{{Month+1}}}` - `{{{Month+3}}}` could be used if you need to invoice a period of "the next three months". 
In a German customers setup it will print `Juli 2024 - Sept. 2024` if the invoice date is in June of 2024.

If you simply want a "next month" the short version `{{{Month+}}}` rsp. `{{{Year-}}}` for last year is valid, too.

# Templates

## `Invoiceplane*.php`

These templates introduce the `invoice_replace_date_tags` helper in your final invoices.


## `debugShowVars.php`

This template is not meant for production but to see available variables if you need to customize your invoices.
