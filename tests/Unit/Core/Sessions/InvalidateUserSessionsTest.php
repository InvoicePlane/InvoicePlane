<?php

namespace Tests\Unit\Core\Sessions;

use Mdl_Sessions;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use stdClass;

/**
 * GHSA-hg6g-m4fp-7cj3 / GHSA-hw8f-c8c2-g4fj: Mdl_Sessions::invalidate_user_sessions() is what Users::form()
 * calls when an administrator changes a role or deactivates an account. It scans the files-driver save
 * path and unlinks the session files of that user. Nothing exercised it; no DB is needed here, only a
 * real directory of session files written in PHP's "php" serialize-handler format.
 *
 * The session double reproduces CI_Session::__get() faithfully: it returns $_SESSION[$key] or NULL.
 * CI_Session has NO `sess_save_path` property, so code that reads the save path from the session
 * library gets NULL, is_dir(NULL) is false and the method silently deletes nothing. The save path
 * has to come from configuration; both config_item() and $this->config->item() are armed here.
 */
final class InvalidateUserSessionsTest extends TestCase
{
    private string $dir;

    private mixed $ciBackup;

    private mixed $configBackup;

    private mixed $sessionBackup;

    protected function setUp(): void
    {
        $this->dir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'ip_sess_inval_' . bin2hex(random_bytes(6));
        mkdir($this->dir, 0700, true);

        $this->ciBackup      = $GLOBALS['unitCiInstance'] ?? null;
        $this->configBackup  = $GLOBALS['unitCiConfig'] ?? null;
        $this->sessionBackup = $_SESSION ?? null;
        unset($_SESSION['sess_save_path']);

        $GLOBALS['unitCiConfig'] = ['sess_save_path' => $this->dir, 'sess_cookie_name' => 'ip_session'];

        $config = new class () {
            public function item(string $key): mixed
            {
                return $GLOBALS['unitCiConfig'][$key] ?? null;
            }
        };

        $session = new class () {
            public function __get(string $key): mixed
            {
                return $_SESSION[$key] ?? null;
            }
        };

        $load = new class () {
            public function helper(string $name): void
            {
                require_once APPPATH . 'helpers/' . $name . '_helper.php';
            }
        };

        $ci          = new stdClass();
        $ci->config  = $config;
        $ci->session = $session;
        $ci->load    = $load;

        $GLOBALS['unitCiInstance'] = $ci;

        require_once BASEPATH . 'core/Model.php';
        require_once APPPATH . 'modules/sessions/models/Mdl_sessions.php';
    }

    protected function tearDown(): void
    {
        foreach (glob($this->dir . DIRECTORY_SEPARATOR . '*') ?: [] as $file) {
            @unlink($file);
        }
        @rmdir($this->dir);

        $GLOBALS['unitCiInstance'] = $this->ciBackup;
        $GLOBALS['unitCiConfig']   = $this->configBackup;
        $_SESSION                  = $this->sessionBackup ?? [];
    }

    /** @return array<string, array{string}> */
    public static function unreadableSessionContents(): array
    {
        return [
            'no entry separator'                  => ['garbage without a pipe'],
            'empty file'                          => [''],
            'user_id with no value'               => ['user_id|'],
            'truncated string value'              => ['user_id|s:5:"7"'],
            'truncated array before user_id'      => ['data|a:2:{i:0;i:1;user_id|s:1:"7";'],
            'array without its closing brace'     => ['data|a:1:{i:0;i:1;user_id|s:1:"7";'],
            'object value before user_id'         => ['obj|O:8:"stdClass":0:{}user_id|s:1:"7";'],
            'user_id that is not a plain integer' => ['user_id|s:3:"7;x";'],
        ];
    }

    #[Test]
    public function it_deletes_every_session_file_of_the_user_in_the_configured_save_path(): void
    {
        /* Arrange: two live sessions of user 7 (string and int user_id encodings, user_id not the first key) */
        $stringForm = $this->writeSession('ip_session' . bin2hex(random_bytes(8)), ['user_type' => '1', 'user_id' => '7', 'user_name' => 'Admin']);
        $intForm    = $this->writeSession('ip_session' . bin2hex(random_bytes(8)), ['__ci_last_regenerate' => 1700000000, 'user_id' => 7]);

        /* Act */
        (new Mdl_Sessions())->invalidate_user_sessions(7);

        /* Assert */
        self::assertFileDoesNotExist($stringForm, 'A session file holding user_id|s:1:"7"; must be removed.');
        self::assertFileDoesNotExist($intForm, 'A session file holding user_id|i:7; must be removed.');
    }

    #[Test]
    public function it_accepts_the_user_id_as_a_string_or_an_int(): void
    {
        /* Arrange */
        $file = $this->writeSession('ip_session' . bin2hex(random_bytes(8)), ['user_id' => '12']);

        /* Act */
        (new Mdl_Sessions())->invalidate_user_sessions('12');

        /* Assert */
        self::assertFileDoesNotExist($file);
    }

    #[Test]
    public function it_keeps_the_session_files_of_other_users_and_unrelated_files(): void
    {
        /* Arrange: the victim, plus every near miss the matcher must not touch */
        $victim    = $this->writeSession('ip_sessionvictim', ['user_id' => '7']);
        $survivors = [
            'user 70 (id starts with 7)'     => $this->writeSession('ip_sessionu70', ['user_id' => '70']),
            'user 17 (id ends with 7)'       => $this->writeSession('ip_sessionu17', ['user_id' => '17']),
            'user 8'                         => $this->writeSession('ip_sessionu8', ['user_id' => '8']),
            'guest without user_id'          => $this->writeSession('ip_sessionguest', ['__ci_last_regenerate' => 1700000000]),
            'other key ending in user_id'    => $this->writeSession('ip_sessionother', ['target_user_id' => '7']),
            'user_id as a value, not a key'  => $this->writeSession('ip_sessionvalue', ['note' => 'user_id|i:7;']),
            'non-session file in the folder' => $this->writeRaw('index.html', '<html></html>'),
        ];

        /* Act */
        (new Mdl_Sessions())->invalidate_user_sessions(7);

        /* Assert: positive control (the victim is gone) and every near miss is intact */
        self::assertFileDoesNotExist($victim);
        foreach ($survivors as $label => $path) {
            self::assertFileExists($path, 'Must not delete: ' . $label);
        }
    }

    #[Test]
    public function it_ignores_user_id_text_that_sits_inside_another_sessions_value(): void
    {
        /* Arrange: user 9 whose name (stored before the user_id entry) contains exactly what a user_id entry looks like, plus a nested user_id */
        $textInside = $this->writeSession('ip_sessiontextinside', ['user_name' => 'x;user_id|i:7;', 'user_id' => '9']);
        $nested     = $this->writeSession('ip_sessionnested', ['user_id' => '9', 'wrapper' => ['user_id' => '7']]);
        $victim     = $this->writeSession('ip_sessionrealvictim', ['note' => '}', '__ci_vars' => ['flash' => 'new'], 'user_id' => '7']);

        /* Act */
        (new Mdl_Sessions())->invalidate_user_sessions(7);

        /* Assert: only the session whose top-level user_id is 7 goes, wherever that entry sits in the file */
        self::assertFileExists($textInside, 'Text inside a value is not a user_id entry.');
        self::assertFileExists($nested, 'A nested user_id is not the session owner.');
        self::assertFileDoesNotExist($victim);
    }

    #[Test]
    public function it_only_deletes_files_named_like_a_session_of_the_configured_cookie(): void
    {
        /* Arrange: the save path can be a shared folder (it defaults to the system temp directory) */
        $foreign   = $this->writeSession('sess_foreignappsession', ['user_id' => '7']);
        $lookalike = $this->writeSession('ip_session.log', ['user_id' => '7']);
        $ours      = $this->writeSession('ip_session' . md5('127.0.0.1') . bin2hex(random_bytes(8)), ['user_id' => '7']);

        /* Act */
        (new Mdl_Sessions())->invalidate_user_sessions(7);

        /* Assert: another application's session and a log file survive, our IP-matched session does not */
        self::assertFileExists($foreign);
        self::assertFileExists($lookalike);
        self::assertFileDoesNotExist($ours);
    }

    #[Test]
    public function it_deletes_nothing_when_the_session_cookie_name_is_not_configured(): void
    {
        /* Arrange */
        unset($GLOBALS['unitCiConfig']['sess_cookie_name']);
        $file = $this->writeSession('ip_sessionnoname', ['user_id' => '7']);

        /* Act */
        (new Mdl_Sessions())->invalidate_user_sessions(7);

        /* Assert: without a name there is no way to tell a session file from any other file */
        self::assertFileExists($file);
    }

    #[Test]
    #[\PHPUnit\Framework\Attributes\DataProvider('unreadableSessionContents')]
    public function it_leaves_a_file_alone_when_its_content_cannot_be_read_as_a_session(string $contents): void
    {
        /* Arrange */
        $file = $this->writeRaw('ip_session' . bin2hex(random_bytes(8)), $contents);

        /* Act */
        (new Mdl_Sessions())->invalidate_user_sessions(7);

        /* Assert: fail safe, nothing is deleted on a guess */
        self::assertFileExists($file);
    }

    #[Test]
    public function it_reads_values_nested_up_to_the_depth_limit_and_no_further(): void
    {
        /* Arrange: the user_id comes after a value nested 16 levels deep (readable) or 17 (refused) */
        $nest = static function (int $levels): array {
            $value = ['leaf' => 'x'];
            for ($i = 1; $i < $levels; $i++) {
                $value = ['n' => $value];
            }

            return $value;
        };
        $readable = $this->writeSession('ip_sessiondeep16', ['data' => $nest(16), 'user_id' => '7']);
        $refused  = $this->writeSession('ip_sessiondeep17', ['data' => $nest(17), 'user_id' => '7']);

        /* Act */
        (new Mdl_Sessions())->invalidate_user_sessions(7);

        /* Assert */
        self::assertFileDoesNotExist($readable, 'A value nested 16 levels deep is still readable.');
        self::assertFileExists($refused, 'Nesting beyond the limit is refused rather than followed.');
    }

    #[Test]
    public function it_does_not_try_to_read_a_directory_that_is_named_like_a_session_file(): void
    {
        /* Arrange */
        $directory = $this->dir . DIRECTORY_SEPARATOR . 'ip_sessiondirectory';
        mkdir($directory);
        $victim = $this->writeSession('ip_sessionrealone', ['user_id' => '7']);

        /* Act */
        (new Mdl_Sessions())->invalidate_user_sessions(7);

        /* Assert: the directory is skipped and the real session is still handled */
        self::assertDirectoryExists($directory);
        self::assertFileDoesNotExist($victim);
        rmdir($directory);
    }

    #[Test]
    public function it_leaves_a_save_path_that_does_not_exist_alone_without_throwing(): void
    {
        /* Arrange */
        $survivor                                  = $this->writeSession('ip_sessionkeep', ['user_id' => '7']);
        $GLOBALS['unitCiConfig']['sess_save_path'] = $this->dir . DIRECTORY_SEPARATOR . 'missing';

        /* Act */
        (new Mdl_Sessions())->invalidate_user_sessions(7);

        /* Assert: pointing at the wrong directory touches nothing (the files of the real one stay) */
        self::assertFileExists($survivor);
    }

    /** @param array<string, mixed> $data */
    private function writeSession(string $name, array $data): string
    {
        $encoded = '';
        foreach ($data as $key => $value) {
            $encoded .= $key . '|' . serialize($value);
        }

        return $this->writeRaw($name, $encoded);
    }

    private function writeRaw(string $name, string $contents): string
    {
        $path = $this->dir . DIRECTORY_SEPARATOR . $name;
        file_put_contents($path, $contents);

        return $path;
    }
}
