<?php

namespace App\Http\Controllers\Training;

use App\Entities\Admin\LGeoCountry;
use App\Entities\Training\ForeignTour;
use App\Entities\Training\LTourSponsor;
use App\Entities\Training\LTourTypes;
use App\Http\Controllers\Controller;
use App\Traits\Security\HasPermission;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ForeignTourController extends Controller
{
    use HasPermission;

    public function index(Request $request)
    {
        return view('training.foreigntour.foreigntour', [
            'foreigntour' => null,
            'tourtypes' => LTourTypes::all(),
            'countrylist' => LGeoCountry::all(),
            'tourSponsorList' => LTourSponsor::all()
        ]);
    }

    public function edit(Request $request, $id)
    {
        $foreigntour = ForeignTour::select('*')
            ->where('f_tour_id', '=', $id)
            ->first();

        return view('training.foreigntour.foreigntour', [
            'foreigntour' => $foreigntour,
            'tourtypes' => LTourTypes::all(),
            'countrylist' => LGeoCountry::all(),
            'tourSponsorList' => LTourSponsor::all()
        ]);
    }

    public function dataTableList()
    {
        $queryResult = ForeignTour::select('foreign_tour.f_tour_id AS f_tour_id',
            'pmis.l_geo_country.country AS country_name',
            'foreign_tour.emp_name AS emp_name',
            'foreign_tour.tour_star_date AS tour_star_date',
            'foreign_tour.tour_end_date AS tour_end_date',
            'l_tour_sponser.sponser_name AS sponser_name')
            ->leftJoin('pmis.l_geo_country', 'pmis.l_geo_country.country_id', '=', 'foreign_tour.country_id')
            ->leftJoin('l_tour_sponser', 'l_tour_sponser.tour_sponser_id', '=', 'foreign_tour.tour_sponsor_id')
            ->orderBy('foreign_tour.insert_date', 'desc')
            ->get();
        return datatables()->of($queryResult)
            ->addColumn('tour_star_date', function ($query) {
                return Carbon::parse($query->tour_star_date)->format('Y-m-d');
            })
            ->addColumn('tour_end_date', function ($query) {
                return Carbon::parse($query->tour_end_date)->format('Y-m-d');
            })
            ->addColumn('action', function ($query) {
                return '<a href="' . route('foreign-tour.foreign-tour-edit', [$query->f_tour_id]) . '"><i class="bx bx-edit cursor-pointer"></i></a>';
            })
            ->addIndexColumn()
            ->make(true);
    }

    public function post(Request $request)
    {
        $response = $this->foreign_tour_api_ins($request);
        $message = $response['o_status_message'];
        if ($response['o_status_code'] != 1) {
            session()->flash('m-class', 'alert-danger');
            return redirect()->back()->with('message', 'error|' . $message);
        }

        session()->flash('m-class', 'alert-success');
        session()->flash('message', $message);

        return redirect()->route('foreign-tour.foreign-tour-index');
    }

    public function update(Request $request, $id)
    {
        $response = $this->foreign_tour_api_upd($request, $id);

        $message = $response['o_status_message'];
        if ($response['o_status_code'] != 1) {
            session()->flash('m-class', 'alert-danger');
            return redirect()->back()->with('message', 'error|' . $message);
        }

        session()->flash('m-class', 'alert-success');
        session()->flash('message', $message);

        return redirect()->route('foreign-tour.foreign-tour-index');
    }

    private function foreign_tour_api_ins(Request $request)
    {
        $postData = $request->post();

        $fromDate = isset($postData['from_date']) ? date('Y-m-d', strtotime($postData['from_date'])) : '';
        $toDate = isset($postData['to_date']) ? date('Y-m-d', strtotime($postData['to_date'])) : '';
        $approveDate = isset($postData['approve_date']) ? date('Y-m-d', strtotime($postData['approve_date'])) : '';

        $foreignTourOrderFileName = '';
        $foreignTourOrderFileType = '';
        $foreignTourOrderFileContent = '';
        $foreignTourOrderFile = $request->file('order_attachment');

        if ($foreignTourOrderFile) {
            $foreignTourOrderFileName = $foreignTourOrderFile->getClientOriginalName();
            $foreignTourOrderFileType = $foreignTourOrderFile->getMimeType();
            $foreignTourOrderFileContent = base64_encode(file_get_contents($foreignTourOrderFile->getRealPath()));
        }

        try {
            $f_tour_id = null;
            $status_code = sprintf("%4000s", "");
            $status_message = sprintf("%4000s", "");

            $params = [
                'P_F_TOUR_ID' => [
                    'value' => &$f_tour_id,
                    'type' => \PDO::PARAM_INPUT_OUTPUT,
                    'length' => 255
                ],
                'P_EMP_ID' => $postData['emp_id'],
                'P_EMP_NAME' => $postData['emp_name_post'],
                'P_DESIGNATION_ID' => $postData['designation_id'],
                'P_DEPARTMENT_ID' => $postData['department_id'],
                'P_TOUR_TYPE_ID' => $postData['tour_type'],
                'P_TOUR_DETAILS' => $postData['tour_details'],
                'P_CONTACT_NUMBER' => $postData['emp_mbl'],
                'P_E_MAIL_ADD' => $postData['emp_email'],
                'P_COUNTRY_ID' => $postData['country_id'],
                'P_COUNTRY_NAME' => '',
                'P_OFFICE_ORDER_NO' => $postData['office_order_no'],
                'P_TOUR_STAR_DATE' => $fromDate,
                'P_TOUR_END_DATE' => $toDate,
                'P_TOUR_APPROVAL_DATE' => $approveDate,
                'P_REFERECNE_NO' => $postData['ref_no'],
                'P_APPROVER_NOTE' => $postData['approver_note'],
                'P_TOUR_SPONSOR_ID' => $postData['tour_sponsor_id'],

                'P_ORDER_ATTACHMENT' => [
                    'value' => $foreignTourOrderFileContent,
                    'type' => SQLT_CLOB,
                ],
                'P_ORDER_ATTACHMENT_TYPE' => $foreignTourOrderFileType,
                'P_ORDER_ATTACHMENT_NAME' => $foreignTourOrderFileName,

                'P_REMARKS' => $postData['remarks'],
                'P_INSERT_BY' => auth()->id(),
                'o_status_code' => &$status_code,
                'o_status_message' => &$status_message,
            ];

            //dd($params);

            DB::executeProcedure('TIMS.TRAINING_MANAGEMENT_PKG.TRAINING_FOREIGN_TOUR_PR', $params);
        } catch (\Exception $e) {
            return ["exception" => true, "o_status_code" => 99, "o_status_message" => $e->getMessage()];
        }

        return $params;
    }

    private function foreign_tour_api_upd(Request $request, $id)
    {
        $postData = $request->post();

        $fromDate = isset($postData['from_date']) ? date('Y-m-d', strtotime($postData['from_date'])) : '';
        $toDate = isset($postData['to_date']) ? date('Y-m-d', strtotime($postData['to_date'])) : '';
        $approveDate = isset($postData['approve_date']) ? date('Y-m-d', strtotime($postData['approve_date'])) : '';

        $foreignTourOrderFile = $request->file('order_attachment');

        if ($foreignTourOrderFile) {
            $foreignTourOrderFileName = $foreignTourOrderFile->getClientOriginalName();
            $foreignTourOrderFileType = $foreignTourOrderFile->getMimeType();
            $foreignTourOrderFileContent = base64_encode(file_get_contents($foreignTourOrderFile->getRealPath()));
        } else {
            $foreignTour = ForeignTour::find($id);
            $foreignTourOrderFileName = $foreignTour->order_attachment_name;
            $foreignTourOrderFileType = $foreignTour->order_attachment_type;
            $foreignTourOrderFileContent = $foreignTour->order_attachment;
        }

        try {
            $status_code = sprintf("%4000s", "");
            $status_message = sprintf("%4000s", "");

            $params = [
                'P_F_TOUR_ID' => [
                    'value' => &$id,
                    'type' => \PDO::PARAM_INPUT_OUTPUT,
                    'length' => 255
                ],
                'P_EMP_ID' => $postData['emp_id'],
                'P_EMP_NAME' => $postData['emp_name_post'],
                'P_DESIGNATION_ID' => $postData['designation_id'],
                'P_DEPARTMENT_ID' => $postData['department_id'],
                'P_TOUR_TYPE_ID' => $postData['tour_type'],
                'P_TOUR_DETAILS' => $postData['tour_details'],
                'P_CONTACT_NUMBER' => $postData['emp_mbl'],
                'P_E_MAIL_ADD' => $postData['emp_email'],
                'P_COUNTRY_ID' => $postData['country_id'],
                'P_COUNTRY_NAME' => '',
                'P_OFFICE_ORDER_NO' => $postData['office_order_no'],
                'P_TOUR_STAR_DATE' => $fromDate,
                'P_TOUR_END_DATE' => $toDate,
                'P_TOUR_APPROVAL_DATE' => $approveDate,
                'P_REFERECNE_NO' => $postData['ref_no'],
                'P_APPROVER_NOTE' => $postData['approver_note'],
                'P_TOUR_SPONSOR_ID' => $postData['tour_sponsor_id'],

                'P_ORDER_ATTACHMENT' => [
                    'value' => $foreignTourOrderFileContent,
                    'type' => SQLT_CLOB,
                ],
                'P_ORDER_ATTACHMENT_TYPE' => $foreignTourOrderFileType,
                'P_ORDER_ATTACHMENT_NAME' => $foreignTourOrderFileName,

                'P_REMARKS' => $postData['remarks'],
                'P_INSERT_BY' => auth()->id(),
                'o_status_code' => &$status_code,
                'o_status_message' => &$status_message,
            ];

            DB::executeProcedure('TIMS.TRAINING_MANAGEMENT_PKG.TRAINING_FOREIGN_TOUR_PR', $params);

        } catch (\Exception $e) {
            return ["exception" => true, "o_status_code" => 99, "o_status_message" => $e->getMessage()];
        }

        return $params;
    }

}
