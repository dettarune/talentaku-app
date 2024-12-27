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

           return $studentReports = $this->studentReportRepository->customGetStudentReports($date,$parentId, $teacherId);
    }
     public function getReportById($id){
        $studentReports = $this->studentReportRepository->getStudentReportById($id);
         return $studentReports;
    }
    public function createReport($data)
    {
        try {
            Log::info('Incoming Data:', $data);

            // Buat student report
            $report = $this->studentReportRepository->createStudentReport($data);

            // Loop data ACTIVITIES dari request
            foreach ($data['ACTIVITIES'] as $activity) {
                // Simpan data aktivitas utama
                $activityData = [
                    'SR_ID' => $report->SR_ID,
                    'ACTIVITY_NAME' => $activity['ACTIVITY_NAME'],
                    'SYS_CREATE_USER' => $data['SYS_CREATE_USER'] ?? 'System',
                ];

                $savedActivity = $this->studentReportRepository->createStudentReportActivity($activityData);

                // Simpan data REF_ACTIVITIES jika ada
                if (isset($activity['REF_ACTIVITIES']) && is_array($activity['REF_ACTIVITIES'])) {
                    foreach ($activity['REF_ACTIVITIES'] as $refActivity) {
                        $refActivityData = [
                            'SRA_ID' => $savedActivity->SRA_ID,
                            'ACTIVITY_TYPE' => $refActivity['ACTIVITY_TYPE'] ?? 'Undefined',
                            'ACTIVITY_NAME' => $refActivity['ACTIVITY_NAME'] ?? 'Undefined',
                            'STATUS' => $refActivity['STATUS'] ?? 'BELUM MUNCUL',
                            'SYS_CREATE_USER' => $data['SYS_CREATE_USER'] ?? 'System',
                        ];

                        $this->studentReportRepository->createRefReportActivity($refActivityData);
                    }
                }
            }

            // Ambil laporan lengkap setelah dibuat
            return $this->getreportById($report->SR_ID);
        } catch (\Exception $e) {
            throw new \Exception('Failed to create report: ' . $e->getMessage());
        }
    }

//    public function updateReport($data, $reportId){
//        try {
//            $this->studentReportRepository->updateStudentReport($data, $reportId);
//            if (isset($data['ACTIVITIES']) && is_array($data['ACTIVITIES'])) {
//                foreach ($data['ACTIVITIES'] as $activityData) {
//                    $activityData['SYS_UPDATE_USER'] = $data['SYS_UPDATE_USER'];
//                    if (isset($activityData['SRA_ID'])) {
//                        $this->studentReportRepository->updateStudentReportActivity($activityData, $activityData['SRA_ID']);
//                    } else {
//                        $activityData['SR_ID'] = $reportId;
//                        $this->studentReportRepository->createStudentReportActivity($activityData);
//                    }
//                }
//            }
//            return $this->getReportById($reportId);
//        } catch (\Exception $e) {
//            throw new \Exception('Failed to update report');
//        }
//    }
    public function deleteReport($student){

    }
    private function markReportAsRead($reportId)
    {
        $update = ['SR_IS_READ' => 'Y'];
        $this->studentReportRepository->updateStudentReport($update, $reportId);
    }
}
