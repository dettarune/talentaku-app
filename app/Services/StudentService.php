<?php

namespace App\Services;

use App\Repositories\StudentRepositoryInterface;

class StudentService
{
    protected $studentRepositoryInterface;

    public function __construct(StudentRepositoryInterface $studentRepositoryInterface)
    {
        $this->studentRepositoryInterface = $studentRepositoryInterface;
    }
    public function getAll($className = null, $classType = null, $classGrade = null, $parentId = null){
        if ($className !== null) {
            $student = $this->studentRepositoryInterface->getStudentByClassroomName($className);
        } elseif ($classType !== null) {
            $student = $this->studentRepositoryInterface->getStudentByClassroomType($classType);
        } elseif ($classGrade !== null) {
            $student = $this->studentRepositoryInterface->getStudentByClassroomGrade($classGrade);
        } elseif ($parentId !== null) {
            $student = $this->studentRepositoryInterface->getByParentUID($parentId);
        }
        else {
            $student = $this->studentRepositoryInterface->getAll();
        }
        return $student;
    }
    public function getById($id){
        return $this->studentRepositoryInterface->getById($id);
    }
    public function create($data){
        if (!$this->studentRepositoryInterface->validateParentId($data['STUDENT_PARENT_U_ID'])) {
            throw new \Exception("ParentIdIsNotValid");
        }
        try {
            return $this->studentRepositoryInterface->create($data);
        } catch (\Exception $e) {
            throw $e;
        }
    }
    public function update($id,$data){
        try {
            if (!$this->studentRepositoryInterface->getById($id)) {
                throw new \Exception("StudentNotFound");
            }
            $updatedStudent = $this->studentRepositoryInterface->update($id, $data);
            if (!$updatedStudent) {
                throw new \Exception("generic.FailedToUpdateStudent");
            }
            return $updatedStudent;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function delete($S_ID)
    {
        $this->studentRepositoryInterface->delete($S_ID);
    }
    public function getDatatables()
    {
        return $this->studentRepositoryInterface->getDatatables();
    }
}
