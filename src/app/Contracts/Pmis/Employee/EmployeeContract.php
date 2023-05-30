<?php

namespace App\Contracts\Pmis\Employee;


interface EmployeeContract
{
    public function findEmployeeInformation($employeeId);
}
