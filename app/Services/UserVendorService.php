<?php

namespace App\Services;

use App\Repositories\UserVendorRepository;

class UserVendorService
{
    public function __construct(protected UserVendorRepository $userVendorRepository)
    {
    }

    public function getUserVendorsQuery(array $filters = [])
    {
        return $this->userVendorRepository->getUserVendorsQuery($filters);
    }

    public function getUserVendorById(int $id)
    {
        return $this->userVendorRepository->getUserVendorById($id);
    }

    public function createUserVendor(array $data)
    {
        return $this->userVendorRepository->createUserVendor($data);
    }

    public function updateUserVendor(int $id, array $data)
    {
        return $this->userVendorRepository->updateUserVendor($id, $data);
    }

    public function deleteUserVendor(int $id)
    {
        return $this->userVendorRepository->deleteUserVendor($id);
    }

    public function changeStatus(int $id, $status, $type)
    {
        return $this->userVendorRepository->changeStatus($id, $status, $type);
    }
}
