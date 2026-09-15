<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Department;
use App\Models\User;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $depts = [
            ['dept_code' => 'CETE', 'dept_name' => 'College of Engineering and Technology Education'],
            ['dept_code' => 'CTE',  'dept_name' => 'College of Teacher Education'],
            ['dept_code' => 'CBMA', 'dept_name' => 'College of Business Management and Accountancy'],
            ['dept_code' => 'CCJE', 'dept_name' => 'College of Criminal Justice Education'],
            ['dept_code' => 'CAS',  'dept_name' => 'College of Arts and Sciences'],
        ];

        foreach ($depts as $d) {
            $dept = Department::firstOrCreate(['dept_code' => $d['dept_code']], $d);

            // Automatically link existing users whose 'department' string matches this code
            User::where('department', $d['dept_code'])->update(['department_id' => $dept->id]);
        }
    }
}
