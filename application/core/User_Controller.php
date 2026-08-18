<?php

if ( ! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/*
 * InvoicePlane
 *
 * @author      InvoicePlane Developers & Contributors
 * @copyright   Copyright (c) 2012 - 2018 InvoicePlane.com
 * @license     https://invoiceplane.com/license.txt
 * @link        https://invoiceplane.com
 */

#[AllowDynamicProperties]
class User_Controller extends Base_Controller
{
    /**
     * User_Controller constructor.
     *
     * @param string $required_key
     * @param int    $required_val
     */
    public function __construct($required_key, $required_val)
    {
        parent::__construct();

        if ($this->session->userdata($required_key) !== (string) $required_val) {
            session_destroy();
            redirect('sessions/login');
        }

        // The session-based check above trusts the snapshot taken at login and
        // does not notice a role change (e.g. an administrator downgraded to a
        // guest) or a deactivated account applied by another administrator. To
        // enforce privilege revocation at the point it is expected to take
        // effect, re-validate the required role against the authoritative
        // ip_users record on every request.
        if ($required_key === 'user_type') {
            $this->revalidate_user_type((string) $required_val);
        }
    }

    /**
     * Re-read the acting user's role from the database and revoke the session
     * when it no longer matches the required value or the account is inactive.
     */
    private function revalidate_user_type(string $required_val): void
    {
        $user_id = $this->session->userdata('user_id');

        $current = null;
        if ($user_id) {
            $current = $this->db
                ->select('user_type, user_active')
                ->where('user_id', $user_id)
                ->get('ip_users')
                ->row();
        }

        if ( ! $current
            || (int) $current->user_active !== 1
            || (string) $current->user_type !== $required_val
        ) {
            session_destroy();
            redirect('sessions/login');

            return;
        }

        // Keep the session role in sync with the authoritative database value.
        if ((string) $this->session->userdata('user_type') !== (string) $current->user_type) {
            $this->session->set_userdata('user_type', $current->user_type);
        }
    }
}
