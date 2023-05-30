<?php
//app/Helpers/HelperClass.php
namespace App\Helpers;

use App\Entities\Admin\LGeoDistrict;
use App\Entities\Admin\LGeoThana;
use App\Entities\Security\Menu;
use App\Managers\Authorization\AuthorizationManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class HelperClass
{

    public $id;
    public $links;

    public static function breadCrumbs($routeName)
    {
        if (in_array($routeName, ['course-entry.course-entry-edit'])) {
            return [
                ['submenu_name' => 'Setup', 'action_name' => ''],
                ['submenu_name' => ' Course Entry', 'action_name' => '']
            ];
        } else if (in_array($routeName, ['exam-type.exam-type-edit'])) {
            return [
                ['submenu_name' => 'Setup', 'action_name' => ''],
                ['submenu_name' => ' Exam Type Entry', 'action_name' => '']
            ];
        }else if (in_array($routeName, ['create-calender.create-calender-edit'])) {
            return [
                ['submenu_name' => 'Setup', 'action_name' => ''],
                ['submenu_name' => ' Exam Type Entry', 'action_name' => '']
            ];
        } else if (in_array($routeName, ['trainee-type.trainee-type-edit'])) {
            return [
                ['submenu_name' => 'Setup', 'action_name' => ''],
                ['submenu_name' => ' Trainee Type Entry', 'action_name' => '']
            ];
        } else if (in_array($routeName, ['training-media.training-media-edit'])) {
            return [
                ['submenu_name' => 'Setup', 'action_name' => ''],
                ['submenu_name' => ' Training Media Entry', 'action_name' => '']
            ];
        } else if (in_array($routeName, ['training-type.training-type-edit'])) {
            return [
                ['submenu_name' => 'Setup', 'action_name' => ''],
                ['submenu_name' => ' Training Type Entry', 'action_name' => '']
            ];
        } else if (in_array($routeName, ['location-entry.location-entry-edit'])) {
            return [
                ['submenu_name' => 'Setup', 'action_name' => ''],
                ['submenu_name' => ' Location Entry', 'action_name' => '']
            ];
        } else if (in_array($routeName, ['expertise-entry.expertise-entry-edit'])) {
            return [
                ['submenu_name' => 'Setup', 'action_name' => ''],
                ['submenu_name' => ' Expertise Entry', 'action_name' => '']
            ];
        } else if (in_array($routeName, ['pre-requisite-entry.pre-requisite-entry-edit'])) {
            return [
                ['submenu_name' => 'Setup', 'action_name' => ''],
                ['submenu_name' => ' Pre Requisite Entry', 'action_name' => '']
            ];
        } else if (in_array($routeName, ['training-requisition.training-requisition-edit'])) {
            return [
                ['submenu_name' => ' Training Requisition', 'action_name' => '']
            ];
        } else if (in_array($routeName, ['training-information.training-information-edit'])) {
            return [
                ['submenu_name' => ' Training Information', 'action_name' => '']
            ];
        } else if (in_array($routeName, ['assign-dept.assign-dept-edit'])) {
            return [
                ['submenu_name' => ' Assign Department', 'action_name' => '']
            ];
        } else if (in_array($routeName, ['assign-trainee.assign-trainee-edit'])) {
            return [
                ['submenu_name' => 'Assign Trainee', 'action_name' => ''],
                ['submenu_name' => ' Assign', 'action_name' => '']
            ];
        } else if (in_array($routeName, ['assign-trainee-update.assign-trainee-update-edit'])) {
            return [
                ['submenu_name' => 'Assign Trainee', 'action_name' => ''],
                ['submenu_name' => ' Update', 'action_name' => '']
            ];
        } else if (in_array($routeName, ['trainee-information.trainee-information-edit'])) {
            return [
                ['submenu_name' => ' Trainee Information', 'action_name' => '']
            ];
        } else if (in_array($routeName, ['calendar-mst.calendar-mst-edit'])) {
            return [
                ['submenu_name' => ' Calendar Setup', 'action_name' => '']
            ];
        } else if (in_array($routeName, ['trainer-information.trainer-address-get','trainer-information.trainer-education-get','trainer-information.trainer-experience-get','trainer-information.trainer-training-get','trainer-information.trainer-expertise-get'])) {
            return [
                ['submenu_name' => 'Trainer Info', 'action_name' => ''],
                ['submenu_name' => ' Register', 'action_name' => '']
            ];
        } else if (in_array($routeName, ['training-schedule.training-schedule-edit'])) {
            return [
                ['submenu_name' => 'Training Schedule', 'action_name' => ''],
                ['submenu_name' => ' Entry', 'action_name' => '']
            ];
        } else if (in_array($routeName, ['foreign-tour.foreign-tour-edit'])) {
            return [
                ['submenu_name' => ' Foreign Tour', 'action_name' => '']
            ];
        } else {
            $breadMenus = [];

            try {
                $authorizationManager = new AuthorizationManager();
                $getRouteMenuId = $authorizationManager->findSubMenuId($routeName);
                if ($getRouteMenuId && !empty($getRouteMenuId)) {
                    $breadMenus[] = $bm = $authorizationManager->findParentMenu($getRouteMenuId);
                    if ($bm && isset($bm['parent_submenu_id']) && !empty($bm['parent_submenu_id'])) {
                        $breadMenus[] = $authorizationManager->findParentMenu($bm['parent_submenu_id']);
                    }
                }
            } catch (\Exception $e) {
                return false;
            }

            return is_array($breadMenus) ? array_reverse($breadMenus) : false;
        }
    }

    public static function findDistrictByDivision($divisionId)
    {
        return LGeoDistrict::where('geo_division_id', $divisionId)->get();
    }

    public static function findDivisionByThana ($districtId)
    {
        return LGeoThana::where('geo_district_id', $districtId)->get();
    }

    public static function customTimeFormat($datetime)
    {
        return date("h:i A", strtotime($datetime));
    }

}
