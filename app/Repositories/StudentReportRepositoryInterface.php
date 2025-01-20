<?php

namespace App\Repositories;

interface StudentReportRepositoryInterface
{
    public function customGetStudentReports($date = null, $parent = null, $teacher = null, $studentId = null);

    public function getStudentReports($date = null);
    public function getStudentReportById($id);
    public function getStudentReportByParentId($id, $date = null);

    public function createStudentReport(array $data);
    public function updateStudentReport(array $data, $id);
    public function deleteStudentReport($id);

    //custom
    public function getAllStudentReportByStudentId($id, $date = null);
    public function getAllStudentReportsByTeacherId($id, $date = null);

    //report activity
    public function getStudentReportsActivity();
    public function getStudentReportsActivityById($id);
    public function getAllStudentReportActivityByReportId($id);
    public function createStudentReportActivity(array $data);
    public function updateStudentReportActivity(array $data, $id);
    public function deleteStudentReportActivity($id);

    public function getAllRefReportActivity();
    public function getAllRefReportActivityById($id);
    public function createRefReportActivity(array $data);
    public function updateRefReportActivity($id, array $data);
    public function deleteRefReportActivity();

    public function getDatatables($S_ID, $date = null);

}
