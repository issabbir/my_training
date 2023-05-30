<?php
/**
 * Created by PhpStorm.
 * User: Pavel
 * Date: 4/20/20
 * Time: 04:54 AM
 */

namespace App\Http\Controllers\Training;

use App\Entities\Admin\LDepartment;
use App\Entities\Admin\LDesignation;
use App\Entities\Admin\LGeoCountry;
use App\Entities\Pmis\Employee\Employee;
use App\Entities\Security\Report;
use App\Entities\Training\TrainingCalenderMaster;
use App\Entities\Training\TrainingInfo;
use App\Entities\Training\TrainingScheduleMaster;
use App\Enums\ModuleInfo;
use App\Http\Controllers\Controller;
use App\Managers\TrainingManager;
use Illuminate\Http\Request;
use App\Traits\Security\HasPermission;

class ReportGeneratorController extends Controller
{
    use HasPermission;

    public function __construct(TrainingManager $trainingManager)
    {
        $this->trainingManager = $trainingManager;
    }

    public function index(Request $request)
    {

        $module = ModuleInfo::TRAINING_MODULE_ID;

        $reportObject = new Report();

        if (auth()->user()->hasGrantAll()) {
            $reports = $reportObject->where('module_id', $module)->orderBy('report_name', 'ASC')->get();
        } else {
            $roles = auth()->user()->getRoles();
            $reports = array();
            foreach ($roles as $role) {
                if (count($role->reports)) {
                    $rpts = $role->reports->where('module_id', $module);
                    foreach ($rpts as $report) {
                        $reports[$report->report_id] = $report;
                    }
                }
            }
        }

        return view('training.reportgenerator.index', compact('reports'));
    }

    public function reportParams(Request $request, $id)
    {
        $report = Report::find($id);
        $lDept = LDepartment::all();
        $lDesignation = LDesignation::all();
        $lCountry = LGeoCountry::all();
        $trainingCalMst = TrainingCalenderMaster::all();
//        $employee = Employee::class;
        $trainingInfo = TrainingInfo::all();
        $traineeList = $this->trainingManager->traineeList();
        $scheduleMst = TrainingScheduleMaster::all();


        $reportForm = view('training.reportgenerator.report-params', compact('report', 'lDept', 'trainingCalMst', 'trainingInfo', 'traineeList','scheduleMst','lDesignation','lCountry'))->render();

        return $reportForm;
    }

}
