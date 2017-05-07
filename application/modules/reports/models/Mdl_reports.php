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
 * Class Mdl_Reports
 */
class Mdl_Reports extends CI_Model
{
    /**
     * @param null $from_date
     * @param null $to_date
     * @return mixed
     */
    public function sales_by_client($from_date = null, $to_date = null)
    {
        $this->db->select('client_name, client_surname, CONCAT(client_name," ", client_surname) AS client_namesurname');

        if ($from_date and $to_date) {

            $from_date = date_to_mysql($from_date);
            $to_date = date_to_mysql($to_date);

            $this->db->select("
            (
                SELECT COUNT(*) FROM ip_invoices
                    WHERE ip_invoices.client_id = ip_clients.client_id 
                        AND invoice_date_created >= " . $this->db->escape($from_date) . "
                        AND invoice_date_created <= " . $this->db->escape($to_date) . "
            ) AS invoice_count");

            $this->db->select("
            (
                SELECT SUM(invoice_item_subtotal) FROM ip_invoice_amounts
                    WHERE ip_invoice_amounts.invoice_id IN
                    (
                        SELECT invoice_id FROM ip_invoices
                            WHERE ip_invoices.client_id = ip_clients.client_id
                                AND invoice_date_created >= " . $this->db->escape($from_date) . "
                                AND invoice_date_created <= " . $this->db->escape($to_date) . "
                    )
            ) AS sales");

            $this->db->select("
            (
                SELECT SUM(invoice_total) FROM ip_invoice_amounts
                    WHERE ip_invoice_amounts.invoice_id IN
                    (
                        SELECT invoice_id FROM ip_invoices
                            WHERE ip_invoices.client_id = ip_clients.client_id
                                AND invoice_date_created >= " . $this->db->escape($from_date) . "
                                AND invoice_date_created <= " . $this->db->escape($to_date) . "
                    )
            ) AS sales_with_tax");

            $this->db->where('
                client_id IN
                (
                    SELECT client_id FROM ip_invoices
                        WHERE invoice_date_created >=' . $this->db->escape($from_date) . '
                            AND invoice_date_created <= ' . $this->db->escape($to_date) . '
                )');

        } else {

            $this->db->select('
            (
                SELECT COUNT(*) FROM ip_invoices
                    WHERE ip_invoices.client_id = ip_clients.client_id
            ) AS invoice_count');

            $this->db->select('
            (
                SELECT SUM(invoice_item_subtotal) FROM ip_invoice_amounts 
                    WHERE ip_invoice_amounts.invoice_id IN
                    (
                        SELECT invoice_id FROM ip_invoices
                            WHERE ip_invoices.client_id = ip_clients.client_id
                    )
            ) AS sales');

            $this->db->select('
            (
                SELECT SUM(invoice_total) FROM ip_invoice_amounts 
                    WHERE ip_invoice_amounts.invoice_id IN
                    (
                        SELECT invoice_id FROM ip_invoices
                            WHERE ip_invoices.client_id = ip_clients.client_id
                    )
            ) AS sales_with_tax');

            $this->db->where('client_id IN (SELECT client_id FROM ip_invoices)');

        }

        $this->db->order_by('client_namesurname');

        return $this->db->get('ip_clients')->result();
    }

    /**
     * @param null $from_date
     * @param null $to_date
     * @return mixed
     */
    public function payment_history($from_date = null, $to_date = null)
    {
        $this->load->model('payments/mdl_payments');

        if ($from_date and $to_date) {
            $from_date = date_to_mysql($from_date);
            $to_date = date_to_mysql($to_date);

            $this->mdl_payments->where('payment_date >=', $from_date);
            $this->mdl_payments->where('payment_date <=', $to_date);
        }

        return $this->mdl_payments->get()->result();
    }

    /**
     * @return mixed
     */
    public function invoice_aging()
    {
        $this->db->select('client_name, client_surname');

        $this->db->select('
        (
            SELECT SUM(invoice_balance) FROM ip_invoice_amounts 
                WHERE invoice_id IN
                (
                    SELECT invoice_id FROM ip_invoices
                        WHERE ip_invoices.client_id = ip_clients.client_id 
                            AND invoice_date_due <= DATE_SUB(NOW(),INTERVAL 1 DAY) 
                            AND invoice_date_due >= DATE_SUB(NOW(), INTERVAL 15 DAY)
                )
        ) AS range_1', false);

        $this->db->select('
        (
            SELECT SUM(invoice_balance) FROM ip_invoice_amounts 
                WHERE invoice_id IN
                (
                    SELECT invoice_id FROM ip_invoices
                        WHERE ip_invoices.client_id = ip_clients.client_id 
                            AND invoice_date_due <= DATE_SUB(NOW(),INTERVAL 16 DAY) 
                            AND invoice_date_due >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                )
        ) AS range_2', false);

        $this->db->select('
        (
            SELECT SUM(invoice_balance) FROM ip_invoice_amounts 
                WHERE invoice_id IN
                (
                    SELECT invoice_id FROM ip_invoices
                        WHERE ip_invoices.client_id = ip_clients.client_id 
                            AND invoice_date_due <= DATE_SUB(NOW(),INTERVAL 31 DAY)
                )
        ) AS range_3', false);

        $this->db->select('
        (
            SELECT SUM(invoice_balance) FROM ip_invoice_amounts 
                WHERE invoice_id IN
                (
                    SELECT invoice_id FROM ip_invoices
                        WHERE ip_invoices.client_id = ip_clients.client_id 
                            AND invoice_date_due <= DATE_SUB(NOW(), INTERVAL 1 DAY)
                )
        ) AS total_balance', false);

        $this->db->having('range_1 >', 0);
        $this->db->or_having('range_2 >', 0);
        $this->db->or_having('range_3 >', 0);
        $this->db->or_having('total_balance >', 0);

        return $this->db->get('ip_clients')->result();
    }

    /**
     * @param null $from_date
     * @param null $to_date
     * @param null $minQuantity
     * @param null $maxQuantity
     * @param bool $taxChecked
     * @return mixed
     */
    public function sales_by_year($from_date = null, $to_date = null, $minQuantity = null, $maxQuantity = null, $taxChecked = False)
    {
        if ($minQuantity == "") {
            $minQuantity = 0;
        }

        if ($from_date == "") {
            $from_date = date("Y-m-d");
        } else {
            $from_date = date_to_mysql($from_date);
        }

        if ($to_date == "") {
            $to_date = date("Y-m-d");
        } else {
            $to_date = date_to_mysql($to_date);
        }

        $from_date_year = intval(substr($from_date, 0, 4));
        $to_date_year = intval(substr($to_date, 0, 4));

        $this->db->select('client_name as Name');
        $this->db->select('client_name');
        $this->db->select('client_surname');
        $this->db->select('CONCAT(client_name," ", client_surname) AS client_namesurname');

        if ($taxChecked == false) {

            if ($maxQuantity) {

                $this->db->select('client_id');
                $this->db->select('client_vat_id AS VAT_ID');
                $this->db->select('
                (
                    SELECT SUM(amounts.invoice_item_subtotal) FROM ip_invoice_amounts amounts
                        WHERE amounts.invoice_id IN
                        (
                            SELECT inv.invoice_id FROM ip_invoices inv
                                WHERE inv.client_id=ip_clients.client_id
                                    AND ' . $this->db->escape($from_date) . '<= inv.invoice_date_created
                                    AND ' . $this->db->escape($to_date) . '>= inv.invoice_date_created
                        )
                ) AS total_payment', false);

                for ($index = $from_date_year; $index <= $to_date_year; $index++) {
                    $this->db->select('
                    (
                        SELECT SUM(invoice_item_subtotal) FROM ip_invoice_amounts
                            WHERE invoice_id IN
                            (
                                SELECT invoice_id FROM ip_invoices inv
                                    WHERE inv.client_id=ip_clients.client_id 
                                        AND ' . $this->db->escape($from_date) . '<= inv.invoice_date_created 
                                        AND ' . $this->db->escape($to_date) . '>= inv.invoice_date_created 
                                        AND
                                        (
                                            inv.invoice_date_created LIKE \'%' . $index . '-01-%\' 
                                            OR inv.invoice_date_created LIKE \'%' . $index . '-02-%\' 
                                            OR inv.invoice_date_created LIKE \'%' . $index . '-03-%\'
                                        )
                            )
                    ) AS payment_t1_' . $index . '', false);

                    $this->db->select('
                    (
                        SELECT SUM(invoice_item_subtotal) FROM ip_invoice_amounts
                            WHERE invoice_id IN
                            (
                                SELECT invoice_id FROM ip_invoices inv
                                    WHERE inv.client_id=ip_clients.client_id 
                                        AND ' . $this->db->escape($from_date) . '<= inv.invoice_date_created 
                                        AND ' . $this->db->escape($to_date) . '>= inv.invoice_date_created 
                                        AND
                                        (
                                            inv.invoice_date_created LIKE \'%' . $index . '-04-%\' 
                                            OR inv.invoice_date_created LIKE \'%' . $index . '-05-%\' 
                                            OR inv.invoice_date_created LIKE \'%' . $index . '-06-%\'
                                        )
                            )
                    ) AS payment_t2_' . $index . '', false);

                    $this->db->select('
                    (
                        SELECT SUM(invoice_item_subtotal) FROM ip_invoice_amounts
                            WHERE invoice_id IN
                            (
                                SELECT invoice_id FROM ip_invoices inv
                                    WHERE inv.client_id=ip_clients.client_id 
                                        AND ' . $this->db->escape($from_date) . '<= inv.invoice_date_created 
                                        AND ' . $this->db->escape($to_date) . '>= inv.invoice_date_created 
                                        AND
                                        (
                                            inv.invoice_date_created LIKE \'%' . $index . '-07-%\' 
                                            OR inv.invoice_date_created LIKE \'%' . $index . '-08-%\' 
                                            OR inv.invoice_date_created LIKE \'%' . $index . '-09-%\'
                                        )
                            )
                    ) AS payment_t3_' . $index . '', false);

                    $this->db->select('
                    (
                        SELECT SUM(invoice_item_subtotal) FROM ip_invoice_amounts
                            WHERE invoice_id IN
                            (
                                SELECT invoice_id FROM ip_invoices inv
                                    WHERE inv.client_id=ip_clients.client_id 
                                        AND ' . $this->db->escape($from_date) . '<= inv.invoice_date_created 
                                        AND ' . $this->db->escape($to_date) . '>= inv.invoice_date_created 
                                        AND
                                        (
                                            inv.invoice_date_created LIKE \'%' . $index . '-10-%\' 
                                            OR inv.invoice_date_created LIKE \'%' . $index . '-11-%\' 
                                            OR inv.invoice_date_created LIKE \'%' . $index . '-12-%\'
                                        )
                            )
                    ) AS payment_t4_' . $index . '', false);

                }

                $this->db->where('
                (
                    SELECT SUM(amounts.invoice_item_subtotal) FROM ip_invoice_amounts amounts 
                        WHERE amounts.invoice_id IN 
                        (
                            SELECT inv.invoice_id FROM ip_invoices inv
                                WHERE inv.client_id=ip_clients.client_id 
                                    AND ' . $this->db->escape($from_date) . ' <= inv.invoice_date_created 
                                    AND ' . $this->db->escape($to_date) . ' >= inv.invoice_date_created 
                                    AND ' . $minQuantity . ' <= 
                                    (
                                        SELECT SUM(amounts2.invoice_item_subtotal) FROM ip_invoice_amounts amounts2 
                                            WHERE amounts2.invoice_id IN 
                                            (
                                                SELECT inv2.invoice_id FROM ip_invoices inv2 
                                                    WHERE inv2.client_id=ip_clients.client_id 
                                                        AND ' . $this->db->escape($from_date) . ' <= inv2.invoice_date_created 
                                                        AND ' . $this->db->escape($to_date) . ' >= inv2.invoice_date_created
                                            )
                                    ) AND ' . $maxQuantity . ' >= 
                                    (
                                        SELECT SUM(amounts3.invoice_item_subtotal) FROM ip_invoice_amounts amounts3 
                                            WHERE amounts3.invoice_id IN 
                                            (
                                                SELECT inv3.invoice_id FROM ip_invoices inv3 
                                                    WHERE inv3.client_id=ip_clients.client_id 
                                                        AND ' . $this->db->escape($from_date) . ' <= inv3.invoice_date_created 
                                                        AND ' . $this->db->escape($to_date) . ' >= inv3.invoice_date_created
                                            )
                                    )
                        )
                ) <>0');

            } else {

                $this->db->select('client_id');
                $this->db->select('client_vat_id AS VAT_ID');
                $this->db->select('client_name as Name');

                $this->db->select('
                (
                    SELECT SUM(amounts.invoice_item_subtotal) FROM ip_invoice_amounts amounts 
                        WHERE amounts.invoice_id IN 
                        (
                            SELECT inv.invoice_id FROM ip_invoices inv 
                                WHERE inv.client_id=ip_clients.client_id 
                                    AND ' . $this->db->escape($from_date) . '<= inv.invoice_date_created 
                                    AND ' . $this->db->escape($to_date) . '>= inv.invoice_date_created
                        )
                ) AS total_payment', false);

                for ($index = $from_date_year; $index <= $to_date_year; $index++) {

                    $this->db->select('
                    (
                        SELECT SUM(invoice_item_subtotal) FROM ip_invoice_amounts 
                            WHERE invoice_id IN 
                            (
                                SELECT invoice_id FROM ip_invoices inv
                                    WHERE inv.client_id=ip_clients.client_id 
                                        AND ' . $this->db->escape($from_date) . '<= inv.invoice_date_created 
                                        AND ' . $this->db->escape($to_date) . '>= inv.invoice_date_created 
                                        AND 
                                        (
                                            inv.invoice_date_created LIKE \'%' . $index . '-01-%\' 
                                            OR inv.invoice_date_created LIKE \'%' . $index . '-02-%\' 
                                            OR inv.invoice_date_created LIKE \'%' . $index . '-03-%\'
                                        )
                            )
                    ) AS payment_t1_' . $index . '', false);

                    $this->db->select('
                    (
                        SELECT SUM(invoice_item_subtotal) FROM ip_invoice_amounts 
                            WHERE invoice_id IN 
                            (
                                SELECT invoice_id FROM ip_invoices inv
                                    WHERE inv.client_id=ip_clients.client_id 
                                        AND ' . $this->db->escape($from_date) . '<= inv.invoice_date_created 
                                        AND ' . $this->db->escape($to_date) . '>= inv.invoice_date_created 
                                        AND 
                                        (
                                            inv.invoice_date_created LIKE \'%' . $index . '-04-%\' 
                                            OR inv.invoice_date_created LIKE \'%' . $index . '-05-%\' 
                                            OR inv.invoice_date_created LIKE \'%' . $index . '-06-%\'
                                        )
                            )
                    ) AS payment_t2_' . $index . '', false);

                    $this->db->select('
                    (
                        SELECT SUM(invoice_item_subtotal) FROM ip_invoice_amounts 
                            WHERE invoice_id IN 
                            (
                                SELECT invoice_id FROM ip_invoices inv
                                    WHERE inv.client_id=ip_clients.client_id 
                                        AND ' . $this->db->escape($from_date) . '<= inv.invoice_date_created 
                                        AND ' . $this->db->escape($to_date) . '>= inv.invoice_date_created 
                                        AND 
                                        (
                                            inv.invoice_date_created LIKE \'%' . $index . '-07-%\' 
                                            OR inv.invoice_date_created LIKE \'%' . $index . '-08-%\' 
                                            OR inv.invoice_date_created LIKE \'%' . $index . '-09-%\'
                                        )
                            )
                    ) AS payment_t3_' . $index . '', false);

                    $this->db->select('
                    (
                        SELECT SUM(invoice_item_subtotal) FROM ip_invoice_amounts 
                            WHERE invoice_id IN 
                            (
                                SELECT invoice_id FROM ip_invoices inv
                                    WHERE inv.client_id=ip_clients.client_id 
                                        AND ' . $this->db->escape($from_date) . '<= inv.invoice_date_created 
                                        AND ' . $this->db->escape($to_date) . '>= inv.invoice_date_created 
                                        AND 
                                        (
                                            inv.invoice_date_created LIKE \'%' . $index . '-10-%\' 
                                            OR inv.invoice_date_created LIKE \'%' . $index . '-11-%\' 
                                            OR inv.invoice_date_created LIKE \'%' . $index . '-12-%\'
                                        )
                            )
                    ) AS payment_t4_' . $index . '', false);

                }

                $this->db->where('
                (
                    SELECT SUM(amounts.invoice_item_subtotal) FROM ip_invoice_amounts amounts 
                        WHERE amounts.invoice_id IN 
                        (
                            SELECT inv.invoice_id FROM ip_invoices inv 
                                WHERE inv.client_id=ip_clients.client_id 
                                    AND ' . $this->db->escape($from_date) . ' <= inv.invoice_date_created 
                                    AND ' . $this->db->escape($to_date) . ' >= inv.invoice_date_created 
                                    AND ' . $minQuantity . ' <= 
                                    (
                                        SELECT SUM(amounts2.invoice_item_subtotal) FROM ip_invoice_amounts amounts2 
                                            WHERE amounts2.invoice_id IN 
                                            (
                                                SELECT inv2.invoice_id FROM ip_invoices inv2 
                                                WHERE inv2.client_id=ip_clients.client_id 
                                                    AND ' . $this->db->escape($from_date) . ' <= inv2.invoice_date_created 
                                                    AND ' . $this->db->escape($to_date) . ' >= inv2.invoice_date_created
                                            )
                                    )
                        )
                ) <>0');

            }

        } else if ($taxChecked == true) {

            if ($maxQuantity) {

                $this->db->select('client_id');
                $this->db->select('client_vat_id AS VAT_ID');
                $this->db->select('client_name as Name');

                $this->db->select('
                (
                    SELECT SUM(amounts.invoice_total) FROM ip_invoice_amounts amounts 
                        WHERE amounts.invoice_id IN 
                        (
                            SELECT inv.invoice_id FROM ip_invoices inv 
                                WHERE inv.client_id=ip_clients.client_id 
                                    AND ' . $this->db->escape($from_date) . '<= inv.invoice_date_created 
                                    AND ' . $this->db->escape($to_date) . '>= inv.invoice_date_created
                        )
                ) AS total_payment', false);

                for ($index = $from_date_year; $index <= $to_date_year; $index++) {

                    $this->db->select('
                    (
                        SELECT SUM(invoice_total) FROM ip_invoice_amounts 
                            WHERE invoice_id IN 
                            (
                                SELECT invoice_id FROM ip_invoices inv 
                                    WHERE inv.client_id=ip_clients.client_id 
                                        AND ' . $this->db->escape($from_date) . '<= inv.invoice_date_created 
                                        AND ' . $this->db->escape($to_date) . '>= inv.invoice_date_created 
                                        AND 
                                        (
                                            inv.invoice_date_created LIKE \'%' . $index . '-01-%\' 
                                            OR inv.invoice_date_created LIKE \'%' . $index . '-02-%\' 
                                            OR inv.invoice_date_created LIKE \'%' . $index . '-03-%\'
                                        )
                            )
                    ) AS payment_t1_' . $index . '', false);

                    $this->db->select('
                    (
                        SELECT SUM(invoice_total) FROM ip_invoice_amounts 
                            WHERE invoice_id IN 
                            (
                                SELECT invoice_id FROM ip_invoices inv 
                                    WHERE inv.client_id=ip_clients.client_id 
                                        AND ' . $this->db->escape($from_date) . '<= inv.invoice_date_created 
                                        AND ' . $this->db->escape($to_date) . '>= inv.invoice_date_created 
                                        AND 
                                        (
                                            inv.invoice_date_created LIKE \'%' . $index . '-04-%\' 
                                            OR inv.invoice_date_created LIKE \'%' . $index . '-05-%\' 
                                            OR inv.invoice_date_created LIKE \'%' . $index . '-06-%\'
                                        )
                            )
                    ) AS payment_t2_' . $index . '', false);

                    $this->db->select('
                    (
                        SELECT SUM(invoice_total) FROM ip_invoice_amounts 
                            WHERE invoice_id IN 
                            (
                                SELECT invoice_id FROM ip_invoices inv 
                                    WHERE inv.client_id=ip_clients.client_id 
                                        AND ' . $this->db->escape($from_date) . '<= inv.invoice_date_created 
                                        AND ' . $this->db->escape($to_date) . '>= inv.invoice_date_created 
                                        AND 
                                        (
                                            inv.invoice_date_created LIKE \'%' . $index . '-07-%\' 
                                            OR inv.invoice_date_created LIKE \'%' . $index . '-08-%\' 
                                            OR inv.invoice_date_created LIKE \'%' . $index . '-09-%\'
                                        )
                            )
                    ) AS payment_t3_' . $index . '', false);

                    $this->db->select('
                    (
                        SELECT SUM(invoice_total) FROM ip_invoice_amounts 
                            WHERE invoice_id IN 
                            (
                                SELECT invoice_id FROM ip_invoices inv 
                                    WHERE inv.client_id=ip_clients.client_id 
                                        AND ' . $this->db->escape($from_date) . '<= inv.invoice_date_created 
                                        AND ' . $this->db->escape($to_date) . '>= inv.invoice_date_created 
                                        AND 
                                        (
                                            inv.invoice_date_created LIKE \'%' . $index . '-10-%\' 
                                            OR inv.invoice_date_created LIKE \'%' . $index . '-11-%\' 
                                            OR inv.invoice_date_created LIKE \'%' . $index . '-12-%\'
                                        )
                            )
                    ) AS payment_t4_' . $index . '', false);

                }

                $this->db->where('
                (
                    SELECT SUM(amounts.invoice_total) FROM ip_invoice_amounts amounts 
                        WHERE amounts.invoice_id IN 
                        (
                            SELECT inv.invoice_id FROM ip_invoices inv 
                                WHERE inv.client_id=ip_clients.client_id 
                                    AND ' . $this->db->escape($from_date) . ' <= inv.invoice_date_created 
                                    AND ' . $this->db->escape($to_date) . ' >= inv.invoice_date_created 
                                    AND ' . $minQuantity . ' <= 
                                    (
                                        SELECT SUM(amounts2.invoice_total) FROM ip_invoice_amounts amounts2 
                                            WHERE amounts2.invoice_id IN 
                                            (
                                                SELECT inv2.invoice_id FROM ip_invoices inv2 
                                                    WHERE inv2.client_id=ip_clients.client_id 
                                                        AND ' . $this->db->escape($from_date) . ' <= inv2.invoice_date_created 
                                                        AND ' . $this->db->escape($to_date) . ' >= inv2.invoice_date_created
                                            )
                                    ) AND ' . $maxQuantity . ' >= 
                                    (
                                        SELECT SUM(amounts3.invoice_total) FROM ip_invoice_amounts amounts3 
                                            WHERE amounts3.invoice_id IN 
                                            (
                                                SELECT inv3.invoice_id FROM ip_invoices inv3 
                                                    WHERE inv3.client_id=ip_clients.client_id 
                                                        AND ' . $this->db->escape($from_date) . ' <= inv3.invoice_date_created 
                                                        AND ' . $this->db->escape($to_date) . ' >= inv3.invoice_date_created
                                            )
                                    )
                        )
                ) <>0');

            } else {

                $this->db->select('client_id');
                $this->db->select('client_vat_id AS VAT_ID');
                $this->db->select('client_name as Name');

                $this->db->select('
                (
                    SELECT SUM(amounts.invoice_total) FROM ip_invoice_amounts amounts 
                        WHERE amounts.invoice_id IN 
                        (
                            SELECT inv.invoice_id FROM ip_invoices inv 
                                WHERE inv.client_id=ip_clients.client_id 
                                    AND ' . $this->db->escape($from_date) . '<= inv.invoice_date_created 
                                    AND ' . $this->db->escape($to_date) . '>= inv.invoice_date_created
                        )
                ) AS total_payment', false);

                for ($index = $from_date_year; $index <= $to_date_year; $index++) {

                    $this->db->select('
                    (
                        SELECT SUM(invoice_total) FROM ip_invoice_amounts 
                            WHERE invoice_id IN 
                            (
                                SELECT invoice_id FROM ip_invoices inv 
                                    WHERE inv.client_id=ip_clients.client_id 
                                        AND ' . $this->db->escape($from_date) . '<= inv.invoice_date_created 
                                        AND ' . $this->db->escape($to_date) . '>= inv.invoice_date_created 
                                        AND 
                                        (
                                            inv.invoice_date_created LIKE \'%' . $index . '-01-%\' 
                                            OR inv.invoice_date_created LIKE \'%' . $index . '-02-%\' 
                                            OR inv.invoice_date_created LIKE \'%' . $index . '-03-%\'
                                        )
                            )
                    ) AS payment_t1_' . $index . '', false);

                    $this->db->select('
                    (
                        SELECT SUM(invoice_total) FROM ip_invoice_amounts 
                            WHERE invoice_id IN 
                            (
                                SELECT invoice_id FROM ip_invoices inv 
                                    WHERE inv.client_id=ip_clients.client_id 
                                        AND ' . $this->db->escape($from_date) . '<= inv.invoice_date_created 
                                        AND ' . $this->db->escape($to_date) . '>= inv.invoice_date_created 
                                        AND 
                                        (
                                            inv.invoice_date_created LIKE \'%' . $index . '-04-%\' 
                                            OR inv.invoice_date_created LIKE \'%' . $index . '-05-%\' 
                                            OR inv.invoice_date_created LIKE \'%' . $index . '-06-%\'
                                        )
                            )
                    ) AS payment_t2_' . $index . '', false);

                    $this->db->select('
                    (
                        SELECT SUM(invoice_total) FROM ip_invoice_amounts 
                            WHERE invoice_id IN 
                            (
                                SELECT invoice_id FROM ip_invoices inv 
                                    WHERE inv.client_id=ip_clients.client_id 
                                        AND ' . $this->db->escape($from_date) . '<= inv.invoice_date_created 
                                        AND ' . $this->db->escape($to_date) . '>= inv.invoice_date_created 
                                        AND 
                                        (
                                            inv.invoice_date_created LIKE \'%' . $index . '-07-%\' 
                                            OR inv.invoice_date_created LIKE \'%' . $index . '-08-%\' 
                                            OR inv.invoice_date_created LIKE \'%' . $index . '-09-%\'
                                        )
                            )
                    ) AS payment_t3_' . $index . '', false);

                    $this->db->select('
                    (
                        SELECT SUM(invoice_total) FROM ip_invoice_amounts 
                            WHERE invoice_id IN 
                            (
                                SELECT invoice_id FROM ip_invoices inv 
                                    WHERE inv.client_id=ip_clients.client_id 
                                        AND ' . $this->db->escape($from_date) . '<= inv.invoice_date_created 
                                        AND ' . $this->db->escape($to_date) . '>= inv.invoice_date_created 
                                        AND 
                                        (
                                            inv.invoice_date_created LIKE \'%' . $index . '-10-%\' 
                                            OR inv.invoice_date_created LIKE \'%' . $index . '-11-%\' 
                                            OR inv.invoice_date_created LIKE \'%' . $index . '-12-%\'
                                        )
                            )
                    ) AS payment_t4_' . $index . '', false);

                }

                $this->db->where('
                (
                    SELECT SUM(amounts.invoice_total) FROM ip_invoice_amounts amounts 
                        WHERE amounts.invoice_id IN 
                        (
                            SELECT inv.invoice_id FROM ip_invoices inv 
                                WHERE inv.client_id=ip_clients.client_id 
                                    AND ' . $this->db->escape($from_date) . ' <= inv.invoice_date_created 
                                    AND ' . $this->db->escape($to_date) . ' >= inv.invoice_date_created 
                                    AND ' . $minQuantity . ' <= 
                                    (
                                        SELECT SUM(amounts2.invoice_total) FROM ip_invoice_amounts amounts2 
                                            WHERE amounts2.invoice_id IN 
                                            (
                                                SELECT inv2.invoice_id FROM ip_invoices inv2 
                                                    WHERE inv2.client_id=ip_clients.client_id 
                                                        AND ' . $this->db->escape($from_date) . ' <= inv2.invoice_date_created 
                                                        AND ' . $this->db->escape($to_date) . ' >= inv2.invoice_date_created
                                            )
                                    )
                        )
                ) <>0');

            }

        }

        $this->db->order_by('client_namesurname');
        return $this->db->get('ip_clients')->result();
    }

}
