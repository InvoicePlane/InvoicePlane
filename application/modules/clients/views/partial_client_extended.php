<?php 
// check class and property, then print
// mixed $class, string $prop
function _xpr($class, $prop) {
	if ($class) {
		if (property_exists($class, $prop)) {
			_htmlsc($class->$prop);
		}
	}
}
function _xpr2($class, $prop) {
	if ($class) {
		if (property_exists($class, $prop)) {
			return $class->$prop;
		} else {
			return 0;
		}
	}
}
?>
    <table class="table no-margin">
	    <tr>
		<th><?php _trans('customer_no'); ?></th>
		<td><?php _xpr($client_extended,'client_extended_customer_no'); ?></td>
	    </tr>
            <tr>
                <th><?php _trans('salutation'); ?></th>
                <td><?php _xpr($client_extended,'client_extended_salutation'); ?></td>
            </tr>
	    <tr>
		<th><?php _trans('contact_person'); ?></th>
		<td><?php _xpr($client_extended,'client_extended_contact_person'); ?></td>
	    </tr>
	    <tr>
		<th><?php _trans('flags'); ?></th>
		<td>
			
			<?php switch (_xpr2($client_extended,'client_extended_flags')) {
				case 0:
					echo _trans('no');
					break;
				case 1:
					echo _trans('open');
					break;
				case 2:
					echo _trans('yes');
					break;
			} ?>
		</td>
	    </tr>
	    <tr>
		<th><?php _trans('contract'); ?></th>
		<td><?php _xpr($client_extended,'client_extended_contract'); ?></td>
	    </tr>
	    <tr>
		<th><?php _trans('direct_debit'); ?></th>
		<td><?php _xpr($client_extended,'client_extended_direct_debit'); ?></td>
	    </tr>
	    <tr>
		<th><?php _trans('bank_name'); ?></th>
		<td><?php _xpr($client_extended,'client_extended_bank_name'); ?></td>
	    </tr>
	    <tr>
		<th><?php _trans('bank_bic'); ?></th>
		<td><?php _xpr($client_extended,'client_extended_bank_bic'); ?></td>
	    </tr>
	    <tr>
		<th><?php _trans('bank_iban'); ?></th>
		<td><?php _xpr($client_extended,'client_extended_bank_iban'); ?></td>
	    </tr>
	    <tr>
		<th><?php _trans('payment_terms'); ?></th>
		<td><?php _xpr($client_extended,'client_extended_payment_terms'); ?></td>
	    </tr>
	    <tr>
		<th><?php _trans('delivery_terms'); ?></th>
		<td><?php _xpr($client_extended,'client_extended_delivery_terms'); ?></td>
	    </tr>
    </table>

