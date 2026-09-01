<?php

if ( ! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * UserAuthorizationService.
 *
 * Handles authorization logic for user management operations.
 * Pure business logic — no CI3 dependencies, no session access, no database.
 */
class UserAuthorizationService
{
    /**
     * Determine if an acting user can edit a target user.
     *
     * Rules:
     * - Primary admin (user_id=1) can edit any user
     * - Any user can edit themselves
     * - Secondary admins can only edit themselves
     *
     * @param int $acting_user_id The user performing the action
     * @param int $target_user_id The user being edited
     *
     * @return bool True if authorized, false otherwise
     */
    public function can_edit_user(int $acting_user_id, int $target_user_id): bool
    {
        $is_primary_admin = $acting_user_id === 1;
        $is_self_edit     = $acting_user_id === $target_user_id;

        // Primary admin can edit anyone; anyone can edit themselves
        return $is_primary_admin || $is_self_edit;
    }

    /**
     * Determine if a user can change another user's type (role).
     *
     * Rules:
     * - Primary admin can change any user's type
     * - Users cannot escalate their own type during self-edit
     *
     * @param int  $acting_user_id The user performing the action
     * @param int  $target_user_id The user being edited
     * @param bool $is_self_edit   Whether the acting user is editing themselves
     *
     * @return bool True if authorized to change type, false otherwise
     */
    public function can_change_user_type(int $acting_user_id, int $target_user_id, bool $is_self_edit): bool
    {
        $is_primary_admin = $acting_user_id === 1;

        // Only primary admin can change user types, not secondary admins or self-edits
        return $is_primary_admin && ! $is_self_edit;
    }

    /**
     * Determine if a user can view another user's edit form.
     *
     * Rules:
     * - Primary admin can view any user's form
     * - Any user can view their own form
     * - Secondary admins cannot view other users' forms
     *
     * @param int $acting_user_id The user performing the action
     * @param int $target_user_id The user whose form is being viewed
     *
     * @return bool True if authorized, false otherwise
     */
    public function can_view_user_form(int $acting_user_id, int $target_user_id): bool
    {
        return $this->can_edit_user($acting_user_id, $target_user_id);
    }
}
