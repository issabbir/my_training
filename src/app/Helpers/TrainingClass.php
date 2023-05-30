<?php
//app/Helpers/ImsClass.php
namespace App\Helpers;

use App\Entities\Admin\LGeoDistrict;
use App\Entities\Security\Menu;
use App\Enums\ModuleInfo;
use App\Managers\Authorization\AuthorizationManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class TrainingClass
{

    public $id;
    public $links;

    /**
     * @return mixed
     */
    public static function menuSetup()
    {
        if (Auth::user()->hasGrantAll()) {
            $moduleId = ModuleInfo::TRAINING_MODULE_ID;
            $menus = Menu::where('module_id', $moduleId)->orderBy('menu_order_no')->get();

            return $menus;
        } else {
            $allMenus = Auth::user()->getRoleMenus();
            $menus = [];

            if($allMenus) {
                foreach($allMenus as $menu) {
                    if($menu->module_id == ModuleInfo::TRAINING_MODULE_ID) {
                        $menus[] = $menu;
                    }
                }
            }

            return $menus;
        };
    }

    public static function getActiveRouteNameWrapping($routeName)
    {
        //dd($routeName); training-requisition-edit
        /*if (in_array($routeName, ['training-requisition.training-requisition-edit'])) {
            return 'training-requisition.training-requisition-index';
        }*/
        if (in_array($routeName, ['course-entry.course-entry-edit'])) {
            return 'course-entry.course-entry-index';
        } else if (in_array($routeName, ['exam-type.exam-type-edit'])) {
            return 'exam-type.exam-type-index';
        } else if (in_array($routeName, ['trainee-type.trainee-type-edit'])) {
            return 'trainee-type.trainee-type-index';
        } else if (in_array($routeName, ['training-media.training-media-edit'])) {
            return 'training-media.training-media-index';
        } else if (in_array($routeName, ['training-type.training-type-edit'])) {
            return 'training-type.training-type-index';
        } else if (in_array($routeName, ['location-entry.location-entry-edit'])) {
            return 'location-entry.location-entry-index';
        } else if (in_array($routeName, ['expertise-entry.expertise-entry-edit'])) {
            return 'expertise-entry.expertise-entry-index';
        } else if (in_array($routeName, ['pre-requisite-entry.pre-requisite-entry-edit'])) {
            return 'pre-requisite-entry.pre-requisite-entry-index';
        } else if (in_array($routeName, ['training-requisition.training-requisition-edit'])) {
            return 'training-requisition.training-requisition-index';
        } else if (in_array($routeName, ['training-information.training-information-edit'])) {
            return 'training-information.training-information-index';
        } else if (in_array($routeName, ['assign-dept.assign-dept-edit'])) {
                return 'assign-dept.assign-dept-index';
        } else if (in_array($routeName, ['assign-trainee.assign-trainee-edit'])) {
            return 'assign-trainee.assign-trainee-index';
        } else if (in_array($routeName, ['assign-trainee-update.assign-trainee-update-edit'])) {
            return 'assign-trainee-update.assign-trainee-update-index';
        }  else if (in_array($routeName, ['trainee-information.trainee-information-edit'])) {
            return 'trainee-information.trainee-information-index';
        } else if (in_array($routeName, ['calendar-mst.calendar-mst-edit'])) {
            return 'calendar-mst.calendar-mst-index';
        } else if (in_array($routeName, ['trainer-information.trainer-address-get','trainer-information.trainer-education-get','trainer-information.trainer-experience-get','trainer-information.trainer-training-get','trainer-information.trainer-expertise-get'])) {
            return 'trainer-information.trainer-info-get';
        } else if (in_array($routeName, ['trainer-information.trainer-info-datatable'])) {
            return 'trainer-information.trainer-info-datatable';
        } else if (in_array($routeName, ['training-schedule.training-schedule-edit'])) {
                return 'training-schedule.training-schedule-index';
        } else if (in_array($routeName, ['training-re-schedule.training-re-schedule-index'])) {
            return 'training-re-schedule.training-re-schedule-index';
        } else if (in_array($routeName, ['trainee-evaluation.trainee-evaluation-index'])) {
            return 'trainee-evaluation.trainee-evaluation-index';
        } else if (in_array($routeName, ['trainee-evaluation-update.trainee-evaluation-update-index'])) {
            return 'trainee-evaluation-update.trainee-evaluation-update-index';
        } else if (in_array($routeName, ['trainee-feedback.trainee-feedback-edit'])) {
            return 'trainee-feedback.trainee-feedback-index';
        } else if (in_array($routeName, ['foreign-tour.foreign-tour-edit'])) {
            return 'foreign-tour.foreign-tour-index';
        } else if (in_array($routeName, ['trainee-attendance.trainee-attendance-index'])) {
            return 'trainee-attendance.trainee-attendance-index';
        } else if (in_array($routeName, ['trainee-attendance-update.trainee-attendance-update-index'])) {
            return 'trainee-attendance-update.trainee-attendance-update-index';
        }else if (in_array($routeName, ['create-calender.create-calender-edit'])) {
            return 'create-calender.create-calender-index';
        } else {
            return [
                [
                    'submenu_name' => $routeName,
                ]
            ];
        }
    }

    public static function activeMenus($routeName)
    {
        //$menus = [];
        try {
            $authorizationManager = new AuthorizationManager();
            $menus[] = $getRouteMenuId = $authorizationManager->findSubMenuId(self::getActiveRouteNameWrapping($routeName));

            if ($getRouteMenuId && !empty($getRouteMenuId)) {
                $bm = $authorizationManager->findParentMenu($getRouteMenuId);
                $menus[] = $bm['parent_submenu_id'];
                if ($bm && isset($bm['parent_submenu_id']) && !empty($bm['parent_submenu_id'])) {
                    $m = $authorizationManager->findParentMenu($bm['parent_submenu_id']);
                    if (!empty($m['submenu_id'])) {
                        $menus[] = $m['submenu_id'];
                    }
                }
            }
        } catch (\Exception $e) {
            $menus = [];
        }
        return is_array($menus) ? $menus : false;
    }

    public static function hasChildMenu($routeName)
    {
        $authorizationManager = new AuthorizationManager();
        $getRouteMenuId = $authorizationManager->findSubMenuId($routeName);
        return $authorizationManager->hasChildMenu($getRouteMenuId);
    }
}
