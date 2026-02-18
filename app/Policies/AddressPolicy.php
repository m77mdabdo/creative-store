<?php

namespace App\Policies;

use App\Models\Address;
use App\Models\User;

/**
 * A saved address can only be seen or changed by the customer it belongs to.
 */
class AddressPolicy
{
    public function update(User $user, Address $address): bool
    {
        return (int) $address->user_id === (int) $user->id;
    }

    public function delete(User $user, Address $address): bool
    {
        return $this->update($user, $address);
    }
}
