<?php

namespace App\Services;

use App\Repositories\StudentReportRepositoryInterface;
use Illuminate\Support\Facades\Log;

class StudentReportService
{
    protected $studentReportRepository;

    /**
     * Constructor to inject the repository.
     */
    public function __construct(StudentReportRepositoryInterface $studentReportRepository)
    {
        $this->studentReportRepository = $studentReportRepository;
    }
    public function getAllReport($studentId = null, $teacherId = null, $parentId = null) {
        if ($studentId !== null) {
            $studentReports = $this->studentReportRepository->getAllStudentReportByStudentId($studentId);
        } elseif ($teacherId !== null) {
            $studentReports = $this->studentReportRepository->getAllStudentReportsByTeacherId($teacherId);
        } elseif ($parentId !== null) {
            $studentReports = $this->studentReportRepository->getStudentReportByParentId($parentId);
        }
        else {
            $studentReports = $this->studentReportRepository->getStudentReports();
        }
        if(!$studentReports || empty($studentReports)) {
            throw new \Exception("Report not found");
        }
        $response = [];
        foreach ($studentReports as $report) {
            $activities = $this->studentReportRepository->getAllStudentReportActivityByReportId($report['SR_ID']);
            $response[] = [
                'REPORT' => $report,
                'ACTIVITIES' => $activities,
            ];
        }
        return $response;
    }
     public function getReportById($id){
        $studentReports = $this->studentReportRepository->getStudentReportById($id);
         if(!$studentReports) {
             throw new \Exception("Report not found");
         }
         if($studentReports['SR_IS_READ'] === 'N') {
             $this->markReportAsRead($studentReports['SR_ID']);
             $studentReports = $this->studentReportRepository->getStudentReportById($id);
         }
         $response = [];
             $activities = $this->studentReportRepository->getAllStudentReportActivityByReportId($studentReports['SR_ID']);
             $response[] = [
                 'REPORT' => $studentReports,
                 'ACTIVITIES' => $activities,
             ];
         return $response;
    }
    public function createReport($data){
        try {
            $report = $this->studentReportRepository->createStudentReport($data);
            if (isset($data['ACTIVITIES']) && is_array($data['ACTIVITIES'])) {
                foreach ($data['ACTIVITIES'] as $activityData) {
                    $activityData['SYS_CREATE_USER'] = $data['SYS_CREATE_USER'];
                    $activityData['SR_ID'] = $report->SR_ID;
                    $this->studentReportRepository->createStudentReportActivity($activityData);
                }
            }
            return $this->getreportById($report->SR_ID);
        } catch (\Exception $e) {
            throw new \Exception('Failed to create report');
        }
    }
    public function updateReport($data, $reportId){
        try {
            $this->studentReportRepository->updateStudentReport($data, $reportId);
            if (isset($data['ACTIVITIES']) && is_array($data['ACTIVITIES'])) {
                foreach ($data['ACTIVITIES'] as $activityData) {
                    $activityData['SYS_UPDATE_USER'] = $data['SYS_UPDATE_USER'];
                    if (isset($activityData['SRA_ID'])) {
                        $this->studentReportRepository->updateStudentReportActivity($activityData, $activityData['SRA_ID']);
                    } else {
                        $activityData['SR_ID'] = $reportId;
                        $this->studentReportRepository->createStudentReportActivity($activityData);
                    }
                }
            }
            return $this->getReportById($reportId);
        } catch (\Exception $e) {
            throw new \Exception('Failed to update report');
        }
    }
    public function deleteReport($student){

    }
    private function markReportAsRead($reportId)
    {
        $update = ['SR_IS_READ' => 'Y'];
        $this->studentReportRepository->updateStudentReport($update, $reportId);
    }
}
