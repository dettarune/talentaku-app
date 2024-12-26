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
    public function getAllReport($studentId = null, $teacherId = null, $parentId = null, $date = null) {
//        if ($studentId !== null) {
//            $studentReports = $this->studentReportRepository->getAllStudentReportByStudentId($studentId, $date);
//        } elseif ($teacherId !== null) {
//            $studentReports = $this->studentReportRepository->getAllStudentReportsByTeacherId($teacherId, $date);
//        } elseif ($parentId !== null) {
//            $studentReports = $this->studentReportRepository->getStudentReportByParentId($parentId, $date);
//        }
//        else {
           return $studentReports = $this->studentReportRepository->customGetStudentReports($date,$parentId, $teacherId);
//        }
//        if(!$studentReports || empty($studentReports)) {
//            throw new \Exception("Report not found");
//        }
//        $response = [];
//        foreach ($studentReports as $report) {
//            $activities = $this->studentReportRepository->getAllStudentReportActivityByReportId($report['SR_ID']);
//            $response[] = [
//                'REPORT' => $report,
//                'ACTIVITIES' => $activities,
//            ];
//        }
//        return $response;
//        return $studentReports = $this->studentReportRepository->customGetStudentReports($date);

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
            log::info($data);
            // Buat student report
            $report = $this->studentReportRepository->createStudentReport($data);

            // Data statis untuk kegiatan utama
            $staticActivities = [
                ['ACTIVITY_NAME' => 'Kegiatan Awal'],
                ['ACTIVITY_NAME' => 'Kegiatan Inti'],
                ['ACTIVITY_NAME' => 'Kegiatan Akhir'],
            ];

            foreach ($staticActivities as  $index => $staticActivity) {
                // Tambahkan informasi dari frontend
                $activityData = [
                    'SR_ID' => $report->SR_ID,
                    'ACTIVITY_NAME' => $staticActivity['ACTIVITY_NAME'],
                    'SYS_CREATE_USER' => $data['SYS_CREATE_USER'] ?? 'System',
                ];

                // Simpan ke database
                $activity = $this->studentReportRepository->createStudentReportActivity($activityData);

                // Pastikan REF_ACTIVITIES diterima sebagai array dari frontend
                if (isset($data['ACTIVITIES'][$index]['REF_ACTIVITIES']) && is_array($data['ACTIVITIES'][$index]['REF_ACTIVITIES'])) {
                    foreach ($data['ACTIVITIES'][$index]['REF_ACTIVITIES'] as $refActivity) {
                        $refActivityData = [
                            'SRA_ID' => $activity->SRA_ID,
                            'ACTIVITY_TYPE' => $refActivity['ACTIVITY_TYPE'] ?? 'Undefined', // Jika ACTIVITY_TYPE tidak dikirim
                            'ACTIVITY_NAME' => $refActivity['ACTIVITY_NAME'] ?? 'Undefined', // Jika ACTIVITY_NAME tidak dikirim
                            'STATUS' => $refActivity['STATUS'] ?? 'Belum Muncul', // Default status jika tidak dikirim
                            'SYS_CREATE_USER' => $data['SYS_CREATE_USER'] ?? 'System',
                        ];
                        $this->studentReportRepository->createRefReportActivity($refActivityData);
                    }
                }
            }

            return $this->getreportById($report->SR_ID);
        } catch (\Exception $e) {
            throw new \Exception('Failed to create report'.$e->getMessage());
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
