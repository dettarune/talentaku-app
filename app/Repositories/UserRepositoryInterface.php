<?php

namespace App\Repositories;

interface UserRepositoryInterface
{
    public function getAll();
    public function getById($U_ID);
    public function create(array $data);
    public function update(array $data, $U_ID);
    public function delete($U_ID);

    public function getUserByLoginToken($token);
    public function getUserByRole($roleId);
    public function getDatatables($role);
}
