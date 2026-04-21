<?php

namespace Tests\Kernel;

trait DatabaseTrait
{
    protected function refreshDatabase(): void
    {
        $ci = &get_instance();

        if ( ! isset($ci->db)) {
            return;
        }

        $ci->db->trans_begin();
    }

    protected function commit(): void
    {
        $ci = &get_instance();

        if (isset($ci->db)) {
            $ci->db->trans_commit();
        }
    }

    protected function rollback(): void
    {
        $ci = &get_instance();

        if (isset($ci->db)) {
            $ci->db->trans_rollback();
        }
    }
}
