<div id="headerbar">
    <h1><?php echo lang('yearly_report'); ?></h1>
</div>

<div id="content">

    <?php $this->layout->load_view('layout/alerts'); ?>

    <table class="table">
        <tr>
            <th><?= lang('year') ?></th>
            <th><?= lang('client') ?></th>
            <th class="text-right"><?= lang('invoiced') ?></th>
            <th class="text-right"><?= lang('invoice_tax') ?></th>
            <th class="text-right"><?= lang('item_tax') ?></th>
            <th class="text-right"><?= lang('paid') ?></th>
            <th class="text-right"><?= lang('expenses') ?></th>
            <th class="text-right"><?= lang('expense_tax') ?></th>
        </tr>
        <?php foreach($results as $year => $clients): ?>
            <tr>
                <th class="bg-info" colspan="8"><?= $year ?></th>
            </tr>
            <?php $total = []; ?>
            <?php foreach($clients as $client => $data): ?>
                <?php foreach($data as $key => $value) { if (!isset($total[$key])) { $total[$key] = 0; }; $total[$key] += $value; } ?>
                <tr>
                    <td></td>
                    <td><?= $client ?></td>
                    <td class="text-right"><?= format_currency($data['invoice_total']) ?></td>
                    <td class="text-right"><?= format_currency($data['invoice_tax_total']) ?></td>
                    <td class="text-right"><?= format_currency($data['item_tax_total']) ?></td>
                    <td class="text-right"><?= format_currency($data['invoice_paid_total']) ?></td>
                    <td class="text-right"><?= format_currency($data['expense_total']) ?></td>
                    <td class="text-right"><?= format_currency($data['expense_tax_total']) ?></td>
                </tr>
            <?php endforeach; ?>
            <tr>
                <td></td>
                <th><i>Total</i></th>
                <th class="text-right"><?= format_currency($total['invoice_total']) ?></th>
                <th class="text-right"><?= format_currency($total['invoice_tax_total']) ?></th>
                <th class="text-right"><?= format_currency($total['item_tax_total']) ?></th>
                <th class="text-right"><?= format_currency($total['invoice_paid_total']) ?></th>
                <th class="text-right"><?= format_currency($total['expense_total']) ?></th>
                <th class="text-right"><?= format_currency($total['expense_tax_total']) ?></th>
            </tr>
        <?php endforeach; ?>
    </table>
</div>