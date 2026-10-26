<?php

// Runs the real email_quote_status() with stubbed collaborators and prints the subject and body it would send.
define('BASEPATH', '/');
function env($k, $d = null)
{
    return $d;
}
function base_url($p = '')
{
    return 'http://example.test' . $p . '?a=1&b="x"';
}
function lang($k)
{
    return $k;
}
function trans($k)
{
    return ['quote_status_email_subject' => 'Quote for %s was %s (%s)', 'quote_status_email_body' => "Client %s %s quote %s\n%s"][$k] ?? $k;
}
function phpmail_send($from, $to, $subject, $body)
{
    echo 'SUBJECT:' . $subject . "\nBODY:" . $body . "\n";

    return true;
}
$quote = (object) ['client_name' => $argv[1], 'user_email' => 'owner@example.test', 'quote_number' => 'Q-1'];
$ci    = new class ($quote) {
    public $mdl_quotes;

    public $load;

    public $mdl_settings;

    public function __construct($q)
    {
        $this->load = new class () {
            public function helper($h) {}
        };
        $this->mdl_settings = new class () {
            public function setting($k)
            {
                return $k === 'email_send_method' ? (($GLOBALS['argv'][2] ?? 'phpmail') === 'none' ? '' : 'phpmail') : null;
            }
        };
        $this->mdl_quotes = new class ($q) {
            public function __construct(private $q) {}

            public function where($a, $b = null)
            {
                return $this;
            }

            public function get()
            {
                return $this;
            }

            public function row()
            {
                return $this->q;
            }
        };
    }
};
function &get_instance()
{
    global $ci;

    return $ci;
}
require dirname(__DIR__, 2) . '/application/helpers/mailer_helper.php';
echo 'RETURN:' . var_export(email_quote_status('5', 'accepted'), true) . "\n";
