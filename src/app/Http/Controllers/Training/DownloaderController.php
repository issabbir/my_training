<?php

namespace App\Http\Controllers\Training;

use App\Entities\Training\ForeignTour;
use App\Entities\Training\LCourse;
use App\Entities\Training\LTrainer;
use App\Entities\Training\LTrainerEducation;
use App\Entities\Training\LTrainerExp;
use App\Entities\Training\LTrainerTrainingInfo;
use App\Entities\Training\TrainingInfo;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\Security\HasPermission;

class DownloaderController extends Controller
{
    use HasPermission;

    public function trainingInfoDownload(Request $request, $id)
    {
        $trainingInfo = TrainingInfo::find($id);

        if ($trainingInfo) {
            if ($trainingInfo->attachment && $trainingInfo->attachment_name && $trainingInfo->attachment_type) {
                $content = base64_decode($trainingInfo->attachment);

                return response()->make($content, 200, [
                    'Content-Type' => $trainingInfo->attachment_type,
                    'Content-Disposition' => 'attachment; filename="' . $trainingInfo->attachment_name . '"'
                ]);
            }
        }
    }

    public function courseEntryDownload(Request $request, $id)
    {
        $courseEntry = LCourse::find($id);

        if ($courseEntry) {
            if ($courseEntry->course_file && $courseEntry->course_file_name && $courseEntry->course_file_type) {
                $content = base64_decode($courseEntry->course_file);

                return response()->make($content, 200, [
                    'Content-Type' => $courseEntry->course_file_type,
                    'Content-Disposition' => 'attachment; filename="' . $courseEntry->course_file_name . '"'
                ]);
            }
        }
    }

    public function trainerCertDownload(Request $request, $id)
    {
        $trainerCer = LTrainerEducation::find($id);

        if ($trainerCer) {
            if ($trainerCer->certifficte_photo && $trainerCer->certificate_name && $trainerCer->certificate_type) {
                $content = base64_decode($trainerCer->certifficte_photo);

                return response()->make($content, 200, [
                    'Content-Type' => $trainerCer->certificate_type,
                    'Content-Disposition' => 'attachment; filename="' . $trainerCer->certificate_name . '"'
                ]);
            }
        }
    }

    public function trainerTransDownload(Request $request, $id)
    {
        $trainerTrans = LTrainerEducation::find($id);

        if ($trainerTrans) {
            if ($trainerTrans->transcript_photo && $trainerTrans->transcript_name && $trainerTrans->transcript_type) {
                $content = base64_decode($trainerTrans->transcript_photo);

                return response()->make($content, 200, [
                    'Content-Type' => $trainerTrans->transcript_type,
                    'Content-Disposition' => 'attachment; filename="' . $trainerTrans->transcript_name . '"'
                ]);
            }
        }
    }

    public function trainerExpeDownload(Request $request, $id)
    {
        $trainerExpe = LTrainerExp::find($id);

        if ($trainerExpe) {
            if ($trainerExpe->exp_letter_photo && $trainerExpe->exp_letter_photo_name && $trainerExpe->exp_letter_photo_type) {
                $content = base64_decode($trainerExpe->exp_letter_photo);

                return response()->make($content, 200, [
                    'Content-Type' => $trainerExpe->exp_letter_photo_type,
                    'Content-Disposition' => 'attachment; filename="' . $trainerExpe->exp_letter_photo_name . '"'
                ]);
            }
        }
    }

    public function trainerRelsDownload(Request $request, $id)
    {
        $trainerRels = LTrainerExp::find($id);

        if ($trainerRels) {
            if ($trainerRels->release_letter_photo && $trainerRels->release_letter_p_name && $trainerRels->release_letter_p_type) {
                $content = base64_decode($trainerRels->release_letter_photo);

                return response()->make($content, 200, [
                    'Content-Type' => $trainerRels->release_letter_p_type,
                    'Content-Disposition' => 'attachment; filename="' . $trainerRels->release_letter_p_name . '"'
                ]);
            }
        }
    }

    public function foreignTourOrderDownload(Request $request, $id)
    {
        $foreignTourOrder = ForeignTour::find($id);

        if ($foreignTourOrder) {
            if ($foreignTourOrder->order_attachment && $foreignTourOrder->order_attachment_name && $foreignTourOrder->order_attachment_type) {
                $content = base64_decode($foreignTourOrder->order_attachment);

                return response()->make($content, 200, [
                    'Content-Type' => $foreignTourOrder->order_attachment_type,
                    'Content-Disposition' => 'attachment; filename="' . $foreignTourOrder->order_attachment_name . '"'
                ]);
            }
        }
    }

    public function trainerTrainingAttaDownload(Request $request, $id)
    {
        $trainerTrainingAttach = LTrainerTrainingInfo::find($id);

        if ($trainerTrainingAttach) {
            if ($trainerTrainingAttach->training_attachment && $trainerTrainingAttach->training_attachment_name && $trainerTrainingAttach->training_attachment_type) {
                $content = base64_decode($trainerTrainingAttach->training_attachment);

                return response()->make($content, 200, [
                    'Content-Type' => $trainerTrainingAttach->training_attachment_type,
                    'Content-Disposition' => 'attachment; filename="' . $trainerTrainingAttach->training_attachment_name . '"'
                ]);
            }
        }
    }

    public function trainerPhotoDownload(Request $request, $id)
    {
        $trainerPhoto = LTrainer::find($id);

        if ($trainerPhoto) {
            if ($trainerPhoto->trainer_photo && $trainerPhoto->trainer_photo_name && $trainerPhoto->trainer_photo_type) {
                $content = base64_decode($trainerPhoto->trainer_photo);

                return response()->make($content, 200, [
                    'Content-Type' => $trainerPhoto->trainer_photo_type,
                    'Content-Disposition' => 'attachment; filename="' . $trainerPhoto->trainer_photo_name . '"'
                ]);
            }
        }
    }

}

