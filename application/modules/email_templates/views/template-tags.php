<div class="panel panel-default">
    <div class="panel-heading"><?php _trans('email_template_tags'); ?></div>
    <div class="panel-body">

        <p class="small"><?php _trans('email_template_tags_instructions'); ?></p>

        <div class="form-group">
            <label for="tags_client"><?php _trans('client'); ?></label>
            <select id="tags_client" class="tag-select form-control">
                <option value="{{{client_name}}}">
                    <?php _trans('client_name'); ?>
                </option>
                <option value="{{{client_surname}}}">
                    <?php _trans('client_surname'); ?>
                </option>
                <optgroup label="<?php _trans('address'); ?>">
                    <option value="{{{client_address_1}}}">
                        <?php _trans('street_address'); ?>
                    </option>
                    <option value="{{{client_address_2}}}">
                        <?php _trans('street_address_2'); ?>
                    </option>
                    <option value="{{{client_city}}}">
                        <?php _trans('city'); ?>
                    </option>
                    <option value="{{{client_state}}}">
                        <?php _trans('state'); ?>
                    </option>
                    <option value="{{{client_zip}}}">
                        <?php _trans('zip'); ?>
                    </option>
                    <option value="{{{client_country}}}">
                        <?php _trans('country'); ?>
                    </option>
                </optgroup>
                <optgroup label="<?php _trans('contact_information'); ?>">
                    <option value="{{{client_phone}}}">
                        <?php _trans('phone'); ?>
                    </option>
                    <option value="{{{client_fax}}}">
                        <?php _trans('fax'); ?>
                    </option>
                    <option value="{{{client_mobile}}}">
                        <?php _trans('mobile'); ?>
                    </option>
                    <option value="{{{client_email}}}">
                        <?php _trans('email'); ?>
                    </option>
                    <option value="{{{client_web}}}">
                        <?php _trans('web_address'); ?>
                    </option>
                </optgroup>
                <optgroup label="<?php _trans('tax_information'); ?>">
                    <option value="{{{client_vat_id}}}">
                        <?php _trans('vat_id'); ?>
                    </option>
                    <option value="{{{client_tax_code}}}">
                        <?php _trans('tax_code'); ?>
                    </option>
                    <option value="{{{client_avs}}}">
                        <?php _trans('sumex_ssn'); ?>
                    </option>
                    <option value="{{{client_insurednumber}}}">
                        <?php _trans('sumex_insurednumber'); ?>
                    </option>
                    <option value="{{{client_weka}}}">
                        <?php _trans('sumex_veka'); ?>
                    </option>
                </optgroup>
                <optgroup label="<?php _trans('custom_fields'); ?>">
                    <?php foreach ($custom_fields['ip_client_custom'] as $custom) { ?>
                        <option value="{{{<?php echo 'ip_cf_' . $custom->custom_field_id; ?>}}}">
                            <?php echo $custom->custom_field_label . ' (ID ' . $custom->custom_field_id . ')'; ?>
                        </option>
                    <?php } ?>
                </optgroup>
            </select>
        </div>

        <div class="form-group">
            <label for="tags_user"><?php _trans('user'); ?></label>
            <select id="tags_user" class="tag-select form-control">
                <option value="{{{user_name}}}">
                    <?php _trans('name'); ?>
                </option>
                <option value="{{{user_company}}}">
                    <?php _trans('company'); ?>
                </option>
                <optgroup label="<?php _trans('address'); ?>">
                    <option value="{{{user_address_1}}}">
                        <?php _trans('street_address'); ?>
                    </option>
                    <option value="{{{user_address_2}}}">
                        <?php _trans('street_address_2'); ?>
                    </option>
                    <option value="{{{user_city}}}">
                        <?php _trans('city'); ?>
                    </option>
                    <option value="{{{user_state}}}">
                        <?php _trans('state'); ?>
                    </option>
                    <option value="{{{user_zip}}}">
                        <?php _trans('zip'); ?>
                    </option>
                    <option value="{{{user_country}}}">
                        <?php _trans('country'); ?>
                    </option>
                </optgroup>
                <optgroup label="<?php _trans('contact_information'); ?>">
                    <option value="{{{user_phone}}}">
                        <?php _trans('phone'); ?>
                    </option>
                    <option value="{{{user_fax}}}">
                        <?php _trans('fax'); ?>
                    </option>
                    <option value="{{{user_mobile}}}">
                        <?php _trans('mobile'); ?>
                    </option>
                    <option value="{{{user_email}}}">
                        <?php _trans('email'); ?>
                    </option>
                    <option value="{{{user_web}}}">
                        <?php _trans('web_address'); ?>
                    </option>
                </optgroup>
                <optgroup label="<?php _trans('sumex_information'); ?>">
                    <option value="{{{user_subscribernumber}}}">
                        <?php _trans('user_subscriber_number'); ?>
                    </option>
                    <option value="{{{user_iban}}}">
                        <?php _trans('user_iban'); ?>
                    </option>
                    <option value="{{{user_gln}}}">
                        <?php _trans('gln'); ?>
                    </option>
                    <option value="{{{user_rcc}}}">
                        <?php _trans('sumex_rcc'); ?>
                    </option>
                </optgroup>
                <optgroup label="<?php _trans('custom_fields'); ?>">
                    <?php foreach ($custom_fields['ip_user_custom'] as $custom) { ?>
                        <option value="{{{<?php echo 'ip_cf_' . $custom->custom_field_id; ?>}}}">
                            <?php echo $custom->custom_field_label . ' (ID ' . $custom->custom_field_id . ')'; ?>
                        </option>
                    <?php } ?>
                </optgroup>
            </select>
        </div>

        <div class="form-group">
            <label for="tags_invoice"><?php _trans('invoices'); ?></label>
            <select id="tags_invoice" class="tag-select form-control">
                <option value="{{{invoice_number}}}">
                    <?php _trans('id'); ?>
                </option>
                <option value="{{{invoice_status}}}">
                    <?php _trans('status'); ?>
                </option>
                <optgroup label="<?php _trans('invoice_dates'); ?>">
                    <option value="{{{invoice_date_due}}}">
                        <?php _trans('due_date'); ?>
                    </option>
                    <option value="{{{invoice_date_created}}}">
                        <?php _trans('invoice_date'); ?>
                    </option>
                </optgroup>
                <optgroup label="<?php _trans('invoice_amounts'); ?>">
                    <option value="{{{invoice_item_subtotal}}}">
                        <?php _trans('subtotal'); ?>
                    </option>
                    <option value="{{{invoice_item_tax_total}}}">
                        <?php _trans('invoice_tax'); ?>
                    </option>
                    <option value="{{{invoice_total}}}">
                        <?php _trans('total'); ?>
                    </option>
                    <option value="{{{invoice_paid}}}">
                        <?php _trans('total_paid'); ?>
                    </option>
                    <option value="{{{invoice_balance}}}">
                        <?php _trans('balance'); ?>
                    </option>
                </optgroup>
                <optgroup label="<?php _trans('extra_information'); ?>">
                    <option value="{{{invoice_terms}}}">
                        <?php _trans('invoice_terms'); ?>
                    </option>
                <option value="{{{invoice_guest_url}}}">
                    <?php _trans('guest_url'); ?>
                </option>
<!--                 <option value="{{{payment_method}}}"> -->
<!--                     <?php _trans('payment_method'); ?> -->
<!--                 </option> -->
                </optgroup>

                <optgroup label="<?php _trans('custom_fields'); ?>">
                    <?php foreach ($custom_fields['ip_invoice_custom'] as $custom) { ?>
                        <option value="{{{<?php echo 'ip_cf_' . $custom->custom_field_id; ?>}}}">
                            <?php echo $custom->custom_field_label . ' (ID ' . $custom->custom_field_id . ')'; ?>
                        </option>
                    <?php } ?>
                </optgroup>
            </select>
        </div>

        <div class="form-group">
            <label for="tags_quote"><?php _trans('quotes'); ?></label>
            <select id="tags_quote" class="tag-select form-control">
                <option value="{{{quote_number}}}">
                    <?php _trans('id'); ?>
                </option>
                <optgroup label="<?php _trans('quote_dates'); ?>">
                    <option value="{{{quote_date_created}}}">
                        <?php _trans('quote_date'); ?>
                    </option>
                    <option value="{{{quote_date_expires}}}">
                        <?php _trans('expires'); ?>
                    </option>
                </optgroup>
                <optgroup label="<?php _trans('quote_amounts'); ?>">
                    <option value="{{{quote_item_subtotal}}}">
                        <?php _trans('subtotal'); ?>
                    </option>
                    <option value="{{{quote_tax_total}}}">
                        <?php _trans('quote_tax'); ?>
                    </option>
                    <option value="{{{quote_item_discount}}}">
                        <?php _trans('discount'); ?>
                    </option>
                    <option value="{{{quote_total}}}">
                        <?php _trans('total'); ?>
                    </option>
                </optgroup>

                <optgroup label="<?php _trans('extra_information'); ?>">
                    <option value="{{{quote_guest_url}}}">
                        <?php _trans('guest_url'); ?>
                    </option>
                </optgroup>

                <optgroup label="<?php _trans('custom_fields'); ?>">
                    <?php foreach ($custom_fields['ip_quote_custom'] as $custom) { ?>
                        <option value="{{{<?php echo 'ip_cf_' . $custom->custom_field_id; ?>}}}">
                            <?php echo $custom->custom_field_label . ' (ID ' . $custom->custom_field_id . ')'; ?>
                        </option>
                    <?php } ?>
                </optgroup>
            </select>
        </div>

        <div class="form-group">
            <label for="tags_sumex"><?php _trans('invoice_sumex'); ?></label>
            <select id="tags_sumex" class="tag-select form-control">
                <option value="{{{sumex_reason}}}">
                    <?php _trans('reason'); ?>
                </option>
                <option value="{{{sumex_diagnosis}}}">
                    <?php _trans('invoice_sumex_diagnosis'); ?>
                </option>
                <option value="{{{sumex_observations}}}">
                    <?php _trans('sumex_observations'); ?>
                </option>
                <option value="{{{sumex_treatmentstart}}}">
                    <?php _trans('treatment_start'); ?>
                </option>
                <option value="{{{sumex_treatmentend}}}">
                    <?php _trans('treatment_end'); ?>
                </option>
                <option value="{{{sumex_casedate}}}">
                    <?php _trans('case_date'); ?>
                </option>
                <option value="{{{sumex_casenumber}}}">
                    <?php _trans('case_number'); ?>
                </option>
            </select>
        </div>

    </div>
</div>
