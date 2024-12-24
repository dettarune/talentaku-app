<?php

namespace App\Repositories;

interface StudentReportRepositoryInterface
{
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

}
