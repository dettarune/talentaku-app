<?php

namespace App\Repositories;

interface StudentRepositoryInterface
{
    public function getAll();
    public function getById($S_ID);
    public function create(array $data);
    public function update($S_ID, $attributes);
    public function delete($S_ID);

    //custom
    public function getByParentUID($parentUid);
    public function getByClassroomId($CLSRM_ID);

    public function validateParentId($parentId);

    public function getStudentByClassroomType($type);
    public function getStudentByClassroomName($name);
    public function getStudentByClassroomGrade($grade);

    public function getDatatables();

}
