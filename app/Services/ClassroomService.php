<?php

namespace App\Services;

use App\Repositories\ClassroomRepositoryInterface;

class ClassroomService
{
    protected $classroomRepository;

    public function __construct(ClassroomRepositoryInterface $classroomRepository)
    {
        $this->classroomRepository = $classroomRepository;
    }

    public function getAllClassrooms()
    {
        return $this->classroomRepository->getAll();
    }

    public function getClassroomById($id)
    {
        return $this->classroomRepository->getById($id);
    }

    public function createClassroom(array $data)
    {
        return $this->classroomRepository->create($data);
    }

    public function updateClassroom(array $data, $id)
    {
        return $this->classroomRepository->update($data, $id);
    }

    public function deleteClassroom($id)
    {
        return $this->classroomRepository->delete($id);
    }
    public function getDatatables()
    {
        return $this->classroomRepository->getDatatables();
    }
}
