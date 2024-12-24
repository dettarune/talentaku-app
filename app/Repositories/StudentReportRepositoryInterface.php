<?php

namespace App\Repositories;

interface StudentReportRepositoryInterface
{
    public function getStudentReports();
    public function getStudentReportById($id);
    public function getStudentReportByParentId($id);

    public function createStudentReport(array $data);
    public function updateStudentReport(array $data, $id);
    public function deleteStudentReport($id);

    //custom
    public function getAllStudentReportByStudentId($id);
    public function getAllStudentReportsByTeacherId($id);

    //report activity
    public function getStudentReportsActivity();
    public function getStudentReportsActivityById($id);
    public function getAllStudentReportActivityByReportId($id);
    public function createStudentReportActivity(array $data);
    public function updateStudentReportActivity(array $data, $id);
    public function deleteStudentReportActivity($id);

}
