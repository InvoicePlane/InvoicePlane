<?php

/**
 * Class Permission_Controller
 *
 * After the initial check if the user is logged in the function
 * checks if the user has the permission defined in the controller's
 * $controller_permission variable (if it's set).
 *
 * To be able to specify more detailed permission schemes each controller
 * method is able to call out a different permission by calling
 *      $this->permission('permission_key');
 *
 * If the user is not alled to acces the controller or the method he
 * is shown the permission denied error page.
 * If the current controller is an AJAX or API controller a JSON
 * response is sent instead.
 *
 * @package      InvoicePlane
 * @author       InvoicePlane Developers & Contributors
 * @copyright    Copyright (c) 2012 - 2018, InvoicePlane (https://invoiceplane.com/)
 * @license      http://opensource.org/licenses/MIT	MIT License
 * @link         https://invoiceplane.com
 *
 */
class Permission_Controller extends Base_Controller
{

    /** @var string */
    protected $controller_permission;

    /** @var string */
    protected $login_route = 'sessions/login';

    /** @var bool */
    public $ajax_controller = false;

    /**
     * Permission_Controller constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->checkIfLoggedIn();

        // Automatically check if the user is allowed to access the controller
        if ($this->controller_permission) {
            $this->permission($this->controller_permission);
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
     * If the user has no permissions set, redirect him to the login
     */
    protected function checkIfLoggedIn()
    {
        if (empty($this->session->userdata('user_permissions'))) {
            redirect($this->login_route);
            exit;
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

    private function sendJsonResponse()
    {
        $this->output
            ->set_status_header(403)
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'success' => false,
                'code' => 403,
            ]))
            ->_display();

        exit;
    }
}
