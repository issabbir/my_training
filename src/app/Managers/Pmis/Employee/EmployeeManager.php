<?php

namespace App\Managers\Pmis\Employee;


use App\Contracts\Pmis\Employee\EmployeeContract;
use App\Entities\Pmis\Employee\Employee;
use App\Enums\Pmis\Employee\Statuses;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Support\Facades\DB;

class EmployeeManager implements EmployeeContract
{
    protected $employee;

    protected $auth;

    public function __construct(Employee $employee, Guard $auth)
    {
        $this->employee = $employee;
        $this->auth = $auth;
    }

    public function findEmployeeCodesBy($searchTerm) {
//        return $this->employee->where(
//            [
//                ['emp_code', 'like', ''.$searchTerm.'%'],
////                ['emp_status_id', 'in', [1,13]],
//            ]
//        )->orWhere(
//            [
//                ['emp_name', 'like', ''.$searchTerm.'%'],
//            ]
//        )->whereIn(
//            'emp_status_id', [1,13]
//        )->orderBy('emp_code', 'ASC')->limit(10)->get(['emp_id', 'emp_code', 'emp_name']);

//Employee search by lower or upper case according to 10-Feb-22 CR by Rabbani
        return $this->employee->where(function($query) use ($searchTerm) {
            $query->where(DB::raw('LOWER(emp_name)'), 'like', strtolower('%' . trim($searchTerm) . '%'))
                ->orWhere('emp_code', 'like', '' . trim($searchTerm) . '%');
        }
        )->whereIn(
            'emp_status_id', [1,13]
        )->orderBy('emp_code', 'ASC')->limit(10)->get(['emp_id', 'emp_code', 'emp_name']);
    }

    public function findEmployeeInformation($employeeId)
    {
//        $query = <<<QUERY
//SELECT
//       emp.emp_id emp_id,
//       emp.emp_code emp_code,
//       emp.emp_emergency_contact_mobile contact,
//       emp.emp_name emp_name,
//       emp.emp_name_bng emp_name_bng,
//       emp.nid_no,
//       emp.emp_emergency_contact_mobile,
//       des.DESIGNATION designation,
//       des.DESIGNATION_ID,
//       dep.DEPARTMENT_NAME department,
//       dep.department_id,
//       sec.DPT_SECTION section,
//       sec.DPT_SECTION_ID,
//       (SELECT EMP_CONTACT_INFO FROM PMIS.EMP_CONTACTS WHERE EMP_CONTACT_TYPE_ID =1 AND EMP_ID = emp.EMP_ID)  emp_email,
//       (SELECT EMP_CONTACT_INFO FROM PMIS.EMP_CONTACTS WHERE EMP_CONTACT_TYPE_ID =2 AND EMP_ID = emp.EMP_ID)  emp_mbl,
//       (SELECT ADDRESS_LINE_1 FROM PMIS.EMP_ADDRESSES WHERE ADDRESS_TYPE_ID =2 AND EMP_ID = emp.EMP_ID)  emp_addr
//FROM
//     pmis.EMPLOYEE emp
//     LEFT JOIN pmis.L_DESIGNATION des
//       on emp.DESIGNATION_ID = des.DESIGNATION_ID
//     LEFT JOIN pmis.L_DEPARTMENT dep
//        on emp.DPT_DEPARTMENT_ID = dep.DEPARTMENT_ID
//     LEFT JOIN pmis.L_DPT_SECTION sec
//        on emp.SECTION_ID = sec.DPT_SECTION_ID
//WHERE
//  emp.emp_id = :emp_id
//  AND emp.EMP_STATUS_ID = :emp_status_id
//QUERY;

        //'dpt_department_id' changed to 'current_department_id' according to 7-Feb-22 CR by Rabbani
        $query = <<<QUERY
SELECT emp.emp_id,
       emp.emp_code emp_code,
       emp.emp_emergency_contact_mobile contact,
       emp.emp_name emp_name,
       emp.emp_name_bng emp_name_bng,
       emp.nid_no,
       emp.emp_emergency_contact_mobile,
       des.DESIGNATION designation,
       des.DESIGNATION_ID,
       dep.DEPARTMENT_NAME department,
       dep.department_id,
       sec.DPT_SECTION section,
       sec.DPT_SECTION_ID,
       (SELECT EMP_CONTACT_INFO
          FROM PMIS.EMP_CONTACTS
         WHERE EMP_CONTACT_TYPE_ID = 1 AND EMP_ID = emp.emp_id AND ROWNUM < 2)
          AS emp_email,
       (SELECT EMP_CONTACT_INFO
          FROM PMIS.EMP_CONTACTS
         WHERE EMP_CONTACT_TYPE_ID = 2 AND EMP_ID = emp.emp_id AND ROWNUM < 2)
          AS emp_mbl,
       (SELECT ADDRESS_LINE_1
          FROM PMIS.EMP_ADDRESSES
         WHERE ADDRESS_TYPE_ID = 2 AND EMP_ID = emp.emp_id AND ROWNUM < 2)
          AS emp_addr
  FROM pmis.EMPLOYEE emp
       LEFT JOIN pmis.L_DESIGNATION des
          ON emp.DESIGNATION_ID = des.DESIGNATION_ID
       LEFT JOIN pmis.L_DEPARTMENT dep
          ON emp.CURRENT_DEPARTMENT_ID = dep.DEPARTMENT_ID
       LEFT JOIN pmis.L_DPT_SECTION sec
          ON emp.SECTION_ID = sec.DPT_SECTION_ID
 WHERE emp.emp_id = :emp_id AND emp.EMP_STATUS_ID IN (:emp_status_id_1, :emp_status_id_2)
QUERY;

        /*$query = <<<QUERY
SELECT
       emp.emp_id emp_id,
       emp.emp_code emp_code,
       emp.emp_emergency_contact_mobile contact,
       emp.emp_name emp_name,
       emp.emp_name_bng emp_name_bng,
       emp.nid_no,
       emp.emp_emergency_contact_mobile,
       des.DESIGNATION designation,
       des.DESIGNATION_ID,
       dep.DEPARTMENT_NAME department,
       dep.department_id,
       sec.DPT_SECTION section,
       sec.DPT_SECTION_ID,
        (SELECT EMP_CONTACT_INFO FROM PMIS.EMP_CONTACTS WHERE EMP_CONTACT_TYPE_ID =1 AND EMP_ID = emp.EMP_ID)  emp_email,
        (SELECT EMP_CONTACT_INFO FROM PMIS.EMP_CONTACTS WHERE EMP_CONTACT_TYPE_ID =2 AND EMP_ID = emp.EMP_ID)  emp_mbl,
       (SELECT ADDRESS_LINE_1 FROM PMIS.EMP_ADDRESSES WHERE ADDRESS_TYPE_ID =2 AND EMP_ID = emp.EMP_ID)  emp_addr
FROM
     pmis.EMPLOYEE emp
     LEFT JOIN pmis.L_DESIGNATION des
       on emp.DESIGNATION_ID = des.DESIGNATION_ID
     LEFT JOIN pmis.L_DEPARTMENT dep
        on emp.CURRENT_DEPARTMENT_ID = dep.DEPARTMENT_ID
     LEFT JOIN pmis.L_DPT_SECTION sec
        on emp.SECTION_ID = sec.DPT_SECTION_ID
WHERE
  emp.emp_id = :emp_id
  AND emp.EMP_STATUS_ID = :emp_status_id
QUERY;*/

        $employee = DB::selectOne($query, ['emp_id' => $employeeId, 'emp_status_id_1' => Statuses::ON_ROLE, 'emp_status_id_2' => Statuses::DEPUTATION]);
        //dd(emp_status_id_2);
        if($employee) {
            $jsonEncodedEmployee = json_encode($employee);
            $employeeArray = json_decode($jsonEncodedEmployee, true);

            return $employeeArray;
        }

        return [];
    }

    public function findOpInEmployees($searchTerm) {
        return $this->employee->where(
            [
                ['emp_status_id', '=', Statuses::ON_ROLE],
                ['emp_type_id', '=', '1'],
            ]
        )->where(function($query) use ($searchTerm) {
            $query->where(DB::raw('LOWER(employee.emp_name)'),'like',strtolower('%'.trim($searchTerm).'%'))
                ->orWhere('employee.emp_code', 'like', ''.trim($searchTerm).'%' );
        })->orderBy('emp_code', 'ASC')->limit(10)->get(['emp_id', 'emp_code', 'emp_name']);
    }

    public function findAreaInsEmployees($searchTerm) {
        $designations = ['9','476', '471'];

        return $this->employee->where(
            [
                ['emp_status_id', '=', Statuses::ON_ROLE],
                ['dpt_department_id', '=', '16']
            ]
        )->whereIn('designation_id', $designations)
            ->where(function ($query) use ($searchTerm) {
                $query->where(DB::raw('LOWER(employee.emp_name)'), 'like', strtolower('%' . trim($searchTerm) . '%'))
                    ->orWhere('employee.emp_code', 'like', '' . trim($searchTerm) . '%');
            })->orderBy('emp_code', 'ASC')->limit(10)->get(['emp_id', 'emp_code', 'emp_name']);
    }

    public function findSecOffEmployees($searchTerm) {
        return $this->employee->where(
            [
                ['emp_status_id', '=', Statuses::ON_ROLE],
                ['designation_id', '=', '253'],
                ['dpt_department_id', '=', '16']
            ]
        )->where(function($query) use ($searchTerm){
            $query->where(DB::raw('LOWER(employee.emp_name)'),'like',strtolower('%'.trim($searchTerm).'%'))
                ->orWhere('employee.emp_code', 'like', ''.trim($searchTerm).'%' );
        })->orderBy('emp_code', 'ASC')->limit(10)->get(['emp_id', 'emp_code', 'emp_name']);
    }

    public function findDeputyDirAdmEmployees($searchTerm) {
        return $this->employee->where(
            [
                ['emp_status_id', '=', Statuses::ON_ROLE],
                ['designation_id', '=', '31'],
                ['dpt_department_id', '=', '16']
            ]
        )->where(function($query) use ($searchTerm){
            $query->where(DB::raw('LOWER(employee.emp_name)'),'like',strtolower('%'.trim($searchTerm).'%'))
                ->orWhere('employee.emp_code', 'like', ''.trim($searchTerm).'%' );
        })->orderBy('emp_code', 'ASC')->limit(10)->get(['emp_id', 'emp_code', 'emp_name']);
    }

    public function findDeputyDirOpEmployees($searchTerm) {
        return $this->employee->where(
            [
                ['emp_status_id', '=', Statuses::ON_ROLE],
                ['designation_id', '=', '217'],
                ['dpt_department_id', '=', '16']
            ]
        )->where(function($query) use ($searchTerm){
            $query->where(DB::raw('LOWER(employee.emp_name)'),'like',strtolower('%'.trim($searchTerm).'%'))
                ->orWhere('employee.emp_code', 'like', ''.trim($searchTerm).'%' );
        })->orderBy('emp_code', 'ASC')->limit(10)->get(['emp_id', 'emp_code', 'emp_name']);
    }

    public function findDirSecEmployees($searchTerm) {
        return $this->employee->where(
            [
                ['emp_status_id', '=', Statuses::ON_ROLE],
                ['designation_id', '=', '524'],
                ['dpt_department_id', '=', '16']
            ]
        )->where(function($query) use ($searchTerm){
            $query->where(DB::raw('LOWER(employee.emp_name)'),'like',strtolower('%'.trim($searchTerm).'%'))
                ->orWhere('employee.emp_code', 'like', ''.trim($searchTerm).'%' );
        })->orderBy('emp_code', 'ASC')->limit(10)->get(['emp_id', 'emp_code', 'emp_name']);
    }

    public function findDeptWiseEmployeeCodesBy($searchTerm,$empDept) {
        $empDeptArr = explode(',',$empDept);
        if(count($empDeptArr)>0){   // department wise show employee code
            return $this->employee->where(
                [
                    ['emp_code', 'like', ''.$searchTerm.'%'],
                    ['emp_status_id', '=', Statuses::ON_ROLE],
                ]
            )->whereIn('dpt_department_id',$empDeptArr)->orderBy('emp_code', 'ASC')->limit(10)->get(['emp_id', 'emp_code']);

        }else{  // to show all employee code
            return $this->employee->where(
                [
                    ['emp_code', 'like', ''.$searchTerm.'%'],
                    ['emp_status_id', '=', Statuses::ON_ROLE],
                ]
            )->orderBy('emp_code', 'ASC')->limit(10)->get(['emp_id', 'emp_code']);

        }
    }

    public function findInstEmployees($searchTerm) {
        return $this->employee->where(
            [
                ['emp_status_id', '=', Statuses::ON_ROLE],
                ['emp_type_id', '=', '1'],
            ]
        )->where(function($query) use ($searchTerm) {
            $query->where(DB::raw('LOWER(employee.emp_name)'),'like',strtolower('%'.trim($searchTerm).'%'))
                ->orWhere('employee.emp_code', 'like', ''.trim($searchTerm).'%' );
        })->orderBy('emp_code', 'ASC')->limit(10)->get(['emp_id', 'emp_code', 'emp_name']);
    }

    public function findEmployeesWithNameBy($searchTerm) {
        return $this->employee->where(
            [
                ['emp_status_id', '=', Statuses::ON_ROLE],
            ]
        )->where(function($query) use ($searchTerm) {
            $query->where(DB::raw('LOWER(employee.emp_name)'),'like',strtolower('%'.trim($searchTerm).'%'))
                ->orWhere('employee.emp_code', 'like', ''.trim($searchTerm).'%' );
        })->orderBy('emp_code', 'ASC')->limit(10)->get(['emp_id', 'emp_code', 'emp_name']);
    }


    public function findLoanApprovedEmployees($searchTerm) {
        return $this->employee->where(
            [
                ['emp_status_id', '=', Statuses::ON_ROLE],
                ['emp_type_id', '=', '1'],
                ['dpt_department_id', '=', '16']
            ]
        )->where(function($query) use ($searchTerm) {
            $query->where(DB::raw('LOWER(employee.emp_name)'),'like',strtolower('%'.trim($searchTerm).'%'))
                ->orWhere('employee.emp_code', 'like', ''.trim($searchTerm).'%' );
        })->orderBy('emp_code', 'ASC')->limit(10)->get(['emp_id', 'emp_code', 'emp_name']);
    }
}
