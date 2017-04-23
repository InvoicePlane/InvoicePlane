<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * InvoicePlane
 *
 * @author		InvoicePlane Developers & Contributors
 * @copyright	Copyright (c) 2012 - 2017 InvoicePlane.com
 * @license		https://invoiceplane.com/license.txt
 * @link		https://invoiceplane.com
 */

/**
 * Class Mdl_Quote_Amounts
 */
class Mdl_Quote_Amounts extends CI_Model
{
    /**
     * IP_QUOTE_AMOUNTS
     * quote_amount_id
     * quote_id
     * quote_item_subtotal      SUM(item_subtotal)
     * quote_item_tax_total     SUM(item_tax_total)
     * quote_tax_total
     * quote_total              quote_item_subtotal + quote_item_tax_total + quote_tax_total
     *
     * IP_QUOTE_ITEM_AMOUNTS
     * item_amount_id
     * item_id
     * item_tax_rate_id
     * item_subtotal             item_quantity * item_price
     * item_tax_total            item_subtotal * tax_rate_percent
     * item_total                item_subtotal + item_tax_total
     *
     * @param $quote_id
     */
    public function calculate($quote_id)
    {
        // Get the basic totals
        $query = $this->db->query("
            SELECT SUM(item_subtotal) AS quote_item_subtotal,
		        SUM(item_tax_total) AS quote_item_tax_total,
		        SUM(item_subtotal) + SUM(item_tax_total) AS quote_total,
		        SUM(item_discount) AS quote_item_discount
		    FROM ip_quote_item_amounts
		    WHERE item_id
		        IN (SELECT item_id FROM ip_quote_items WHERE quote_id = " . $this->db->escape($quote_id) . ")
            ");

        $quote_amounts = $query->row();

        $quote_item_subtotal = $quote_amounts->quote_item_subtotal - $quote_amounts->quote_item_discount;
        $quote_subtotal = $quote_item_subtotal + $quote_amounts->quote_item_tax_total;
        $quote_total = $this->calculate_discount($quote_id, $quote_subtotal);

        // Create the database array and insert or update
        $db_array = array(
            'quote_id' => $quote_id,
            'quote_item_subtotal' => $quote_item_subtotal,
            'quote_item_tax_total' => $quote_amounts->quote_item_tax_total,
            'quote_total' => $quote_total,
        );

        $this->db->where('quote_id', $quote_id);
        if ($this->db->get('ip_quote_amounts')->num_rows()) {
            // The record already exists; update it
            $this->db->where('quote_id', $quote_id);
            $this->db->update('ip_quote_amounts', $db_array);
        } else {
            // The record does not yet exist; insert it
            $this->db->insert('ip_quote_amounts', $db_array);
        }

        // Calculate the quote taxes
        $this->calculate_quote_taxes($quote_id);
    }

    /**
     * @param $quote_id
     * @param $quote_total
     * @return float
     */
    public function calculate_discount($quote_id, $quote_total)
    {
        $this->db->where('quote_id', $quote_id);
        $quote_data = $this->db->get('ip_quotes')->row();

        $total = (float)number_format($quote_total, 2, '.', '');
        $discount_amount = (float)number_format($quote_data->quote_discount_amount, 2, '.', '');
        $discount_percent = (float)number_format($quote_data->quote_discount_percent, 2, '.', '');

        $total = $total - $discount_amount;
        $total = $total - round(($total / 100 * $discount_percent), 2);

        return $total;
    }

    /**
     * @param $quote_id
     */
    public function calculate_quote_taxes($quote_id)
    {
        // First check to see if there are any quote taxes applied
        $this->load->model('quotes/mdl_quote_tax_rates');
        $quote_tax_rates = $this->mdl_quote_tax_rates->where('quote_id', $quote_id)->get()->result();

        if ($quote_tax_rates) {
            // There are quote taxes applied
            // Get the current quote amount record
            $quote_amount = $this->db->where('quote_id', $quote_id)->get('ip_quote_amounts')->row();

            // Loop through the quote taxes and update the amount for each of the applied quote taxes
            foreach ($quote_tax_rates as $quote_tax_rate) {
                if ($quote_tax_rate->include_item_tax) {
                    // The quote tax rate should include the applied item tax
                    $quote_tax_rate_amount = ($quote_amount->quote_item_subtotal + $quote_amount->quote_item_tax_total) * ($quote_tax_rate->quote_tax_rate_percent / 100);
                } else {
                    // The quote tax rate should not include the applied item tax
                    $quote_tax_rate_amount = $quote_amount->quote_item_subtotal * ($quote_tax_rate->quote_tax_rate_percent / 100);
                }

                // Update the quote tax rate record
                $db_array = array(
                    'quote_tax_rate_amount' => $quote_tax_rate_amount
                );
                $this->db->where('quote_tax_rate_id', $quote_tax_rate->quote_tax_rate_id);
                $this->db->update('ip_quote_tax_rates', $db_array);
            }

            // Update the quote amount record with the total quote tax amount
            $this->db->query("
                UPDATE ip_quote_amounts SET quote_tax_total =
                (
                    SELECT SUM(quote_tax_rate_amount)
                    FROM ip_quote_tax_rates
                    WHERE quote_id = " . $this->db->escape($quote_id) . "
                )
                WHERE quote_id = " . $this->db->escape($quote_id)
            );

            // Get the updated quote amount record
            $quote_amount = $this->db->where('quote_id', $quote_id)->get('ip_quote_amounts')->row();

            // Recalculate the quote total
            $quote_total = $quote_amount->quote_item_subtotal + $quote_amount->quote_item_tax_total + $quote_amount->quote_tax_total;

            $quote_total = $this->calculate_discount($quote_id, $quote_total);

            // Update the quote amount record
            $db_array = array(
                'quote_total' => $quote_total
            );

            $this->db->where('quote_id', $quote_id);
            $this->db->update('ip_quote_amounts', $db_array);
        } else {
            // No quote taxes applied

            $db_array = array(
                'quote_tax_total' => '0.00'
            );

            $this->db->where('quote_id', $quote_id);
            $this->db->update('ip_quote_amounts', $db_array);
        }
    }

    /**
     * @param null $period
     * @return mixed
     */
    public function get_total_quoted($period = null)
    {
        switch ($period) {
            case 'month':
                return $this->db->query("
					SELECT SUM(quote_total) AS total_quoted 
					FROM ip_quote_amounts
					WHERE quote_id IN 
					(SELECT quote_id FROM ip_quotes
					WHERE MONTH(quote_date_created) = MONTH(NOW()) 
					AND YEAR(quote_date_created) = YEAR(NOW()))")->row()->total_quoted;
            case 'last_month':
                return $this->db->query("
					SELECT SUM(quote_total) AS total_quoted 
					FROM ip_quote_amounts
					WHERE quote_id IN 
					(SELECT quote_id FROM ip_quotes
					WHERE MONTH(quote_date_created) = MONTH(NOW() - INTERVAL 1 MONTH)
					AND YEAR(quote_date_created) = YEAR(NOW() - INTERVAL 1 MONTH))")->row()->total_quoted;
            case 'year':
                return $this->db->query("
					SELECT SUM(quote_total) AS total_quoted 
					FROM ip_quote_amounts
					WHERE quote_id IN 
					(SELECT quote_id FROM ip_quotes WHERE YEAR(quote_date_created) = YEAR(NOW()))")->row()->total_quoted;
            case 'last_year':
                return $this->db->query("
					SELECT SUM(quote_total) AS total_quoted 
					FROM ip_quote_amounts
					WHERE quote_id IN 
					(SELECT quote_id FROM ip_quotes WHERE YEAR(quote_date_created) = YEAR(NOW() - INTERVAL 1 YEAR))")->row()->total_quoted;
            default:
                return $this->db->query("SELECT SUM(quote_total) AS total_quoted FROM ip_quote_amounts")->row()->total_quoted;
        }
    }

    /**
     * @param string $period
     * @return array
     */
    public function get_status_totals($period = '')
    {
        switch ($period) {
            default:
            case 'this-month':
                $results = $this->db->query("
					SELECT quote_status_id,
					    SUM(quote_total) AS sum_total,
					    COUNT(*) AS num_total
					FROM ip_quote_amounts
					JOIN ip_quotes ON ip_quotes.quote_id = ip_quote_amounts.quote_id
                        AND MONTH(ip_quotes.quote_date_created) = MONTH(NOW())
                        AND YEAR(ip_quotes.quote_date_created) = YEAR(NOW())
					GROUP BY ip_quotes.quote_status_id")->result_array();
                break;
            case 'last-month':
                $results = $this->db->query("
					SELECT quote_status_id,
					    SUM(quote_total) AS sum_total,
					    COUNT(*) AS num_total
					FROM ip_quote_amounts
					JOIN ip_quotes ON ip_quotes.quote_id = ip_quote_amounts.quote_id
                        AND MONTH(ip_quotes.quote_date_created) = MONTH(NOW() - INTERVAL 1 MONTH)
                        AND YEAR(ip_quotes.quote_date_created) = YEAR(NOW())
					GROUP BY ip_quotes.quote_status_id")->result_array();
                break;
            case 'this-quarter':
                $results = $this->db->query("
					SELECT quote_status_id,
					    SUM(quote_total) AS sum_total,
					    COUNT(*) AS num_total
					FROM ip_quote_amounts
					JOIN ip_quotes ON ip_quotes.quote_id = ip_quote_amounts.quote_id
                        AND QUARTER(ip_quotes.quote_date_created) = QUARTER(NOW())
                        AND YEAR(ip_quotes.quote_date_created) = YEAR(NOW())
					GROUP BY ip_quotes.quote_status_id")->result_array();
                break;
            case 'last-quarter':
                $results = $this->db->query("
					SELECT quote_status_id,
					    SUM(quote_total) AS sum_total,
					    COUNT(*) AS num_total
					FROM ip_quote_amounts
					JOIN ip_quotes ON ip_quotes.quote_id = ip_quote_amounts.quote_id
                        AND QUARTER(ip_quotes.quote_date_created) = QUARTER(NOW() - INTERVAL 1 QUARTER)
                        AND YEAR(ip_quotes.quote_date_created) = YEAR(NOW())
					GROUP BY ip_quotes.quote_status_id")->result_array();
                break;
            case 'this-year':
                $results = $this->db->query("
					SELECT quote_status_id,
					    SUM(quote_total) AS sum_total,
					    COUNT(*) AS num_total
					FROM ip_quote_amounts
					JOIN ip_quotes ON ip_quotes.quote_id = ip_quote_amounts.quote_id
                        AND YEAR(ip_quotes.quote_date_created) = YEAR(NOW())
					GROUP BY ip_quotes.quote_status_id")->result_array();
                break;
            case 'last-year':
                $results = $this->db->query("
					SELECT quote_status_id,
					    SUM(quote_total) AS sum_total,
					    COUNT(*) AS num_total
					FROM ip_quote_amounts
					JOIN ip_quotes ON ip_quotes.quote_id = ip_quote_amounts.quote_id
                        AND YEAR(ip_quotes.quote_date_created) = YEAR(NOW() - INTERVAL 1 YEAR)
					GROUP BY ip_quotes.quote_status_id")->result_array();
                break;
        }

        $return = array();

        foreach ($this->mdl_quotes->statuses() as $key => $status) {
            $return[$key] = array(
                'quote_status_id' => $key,
                'class' => $status['class'],
                'label' => $status['label'],
                'href' => $status['href'],
                'sum_total' => 0,
                'num_total' => 0
            );
        }

        foreach ($results as $result) {
            $return[$result['quote_status_id']] = array_merge($return[$result['quote_status_id']], $result);
        }

        return $return;
    }

}
