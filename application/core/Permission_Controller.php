<?php

/**
 * Class Permission_Controller
 *
 * After the initial check if the user is logged in the controller
 * checks if the user has the permission defined in the controller's
 * $module_permission variable (if it's set).
 *
 * To be able to specify more detailed permission schemes each module
 * controller is able to check a different permission by calling
 *      $this->permission('permission_key');
 * directly inside the method.
 *
 * If the user is not allowed to access the controller or the method
 * the permission denied error page is shown.
 * If the current controller is an AJAX or API controller a JSON
 * response is sent instead.
 *
 * @TODO    Implement logging for failed access tries, check if throttling makes sense
 *
 * @package     InvoicePlane
 * @author      InvoicePlane Developers & Contributors
 * @copyright   Copyright (c) 2012 - 2018, InvoicePlane
 * @license     http://opensource.org/licenses/MIT     MIT License
 * @link        https://invoiceplane.com
 */
class Permission_Controller extends Base_Controller
{

    /** @var bool */
    public $ajax_controller = false;
    /** @var string */
    protected $module_permission;
    /** @var string */
    protected $login_route = 'sessions/login';

    /**
     * Permission_Controller constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->checkIfLoggedIn();

        // Automatically check if the user is allowed to access the controller
        if ($this->module_permission) {
            $this->permission($this->module_permission);
        }
    }

    /**
     * If the user has no permissions set, redirect him to the login
     */
    protected function checkIfLoggedIn()
    {
        if (empty($this->session->userdata('user_permissions'))) {
            redirect($this->login_route);
        }
    }

    /**
     * @param string $permission_key
     */
    protected function permission(string $permission_key)
    {
        if (!$this->hasPermission($permission_key)) {
            if ($this->ajax_controller) {
                $this->sendJsonResponse();
            }

            show_error('Permission ' . $permission_key . ' is missing!', 403);
        }
    }

    /**
     * Check if the user has the specified permissions
     * @TODO We may move this into the user model or a helper later for global access
     *
     * @param string $permission_key
     * @return bool
     */
    private function hasPermission(string $permission_key)
    {
        $user_permissions = $this->session->userdata('user_permissions');

        if (!is_array($user_permissions)) {
            return false;
        }

        // Check for access to the controller method
        if (!in_array($permission_key, $user_permissions)) {
            return false;
        }

        return true;
    }

    /**
     * Send a JSON response to AJAX and API controllers
     *
     * @return void
     */
    private function sendJsonResponse()
    {
        $this->output
            ->set_status_header(403)
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status' => 'Permission denied',
                'code' => 403,
            ]))
            ->_display();
    }
}
