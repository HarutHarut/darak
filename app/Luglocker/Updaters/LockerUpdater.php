<?php


namespace App\Luglocker\Updaters;


use App\Models\Locker;

trait LockerUpdater
{
    public function lockerUpdate(Locker $locker, array $data): void
    {
        if (isset($data['name'])) {
            $locker->name = $data['name'];
        }

        if (isset($data['count'])) {
            $locker->count = $data['count'];
        }

        if (isset($data['size_id'])) {
            $locker->size_id = $data['size_id'];
        }

        if (isset($data['branch_id'])) {
            $locker->branch_id = $data['branch_id'];
        }

        $locker->save();
    }
}
