<?php

namespace App\Services;

use App\Repositories\UserRepositoryInterface;

class UserService
{
    protected $userRepositoryInterface;

    public function __construct(UserRepositoryInterface $userRepositoryInterface)
    {
        $this->userRepositoryInterface = $userRepositoryInterface;
    }

    public function getAll(){
        return $this->userRepositoryInterface->getAll();
    }
    public function getById($U_ID){
        return $this->userRepositoryInterface->getById($U_ID);
    }
    public function create($data){
        return $this->userRepositoryInterface->create($data);
    }
    public function update($data, $U_ID){
        $user = $this->userRepositoryInterface->getById($U_ID);
        if(!$user){
            throw new \Exception("User not found");
        }
        return $this->userRepositoryInterface->update($data, $U_ID);
    }
    public function getUserByLoginToken($token)
    {
        $user = $this->userRepositoryInterface->getUserByLoginToken($token);
        return $user;
    }
    public function delete($U_ID){
        $user = $this->userRepositoryInterface->getById($U_ID);
        if(!$user){
            throw new \Exception("User not found");
        }
        return $this->userRepositoryInterface->delete($U_ID);
    }
    public function getDatatables($role)
    {
        return $this->userRepositoryInterface->getDatatables($role);
    }
    public function getUserByRole($id)
    {
        $this->userRepositoryInterface->getUserByRole($id);
    }
}
