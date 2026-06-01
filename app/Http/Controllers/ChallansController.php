<?php

namespace App\Http\Controllers;

use App\Models\Challans;
use Illuminate\Http\Request;
use DB;

class ChallansController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index()
    {
        $tenantId = auth()->check() ? (auth()->user()->tenant_id ?? null) : null;

        $feeTypeList = DB::table('feetypes')->get();
        $classList = DB::table('classes')
            ->when($tenantId, function ($q) use ($tenantId) {
                $q->where('tenant_id', $tenantId);
            })
            ->get();
        $schoolList = DB::table('schools')
            ->when($tenantId, function ($q) use ($tenantId) {
                $q->where('id', $tenantId);
            })
            ->get();
        $feesList = DB::table('fees')
            ->when($tenantId, function ($q) use ($tenantId) {
                $q->where('tenant_id', $tenantId);
            })
            ->get();
        $studentList = DB::table('students')
            ->leftJoin('classes', 'students.class_id', '=', 'classes.id')
            ->select('students.*', 'classes.className')
            ->when($tenantId, function ($q) use ($tenantId) {
                $q->where('students.tenant_id', $tenantId);
            })
            ->whereIn('students.status', ['active', 'Active'])
            ->get();

        // Determine active session (if any) and show latest challans first
        $academicTable = \Illuminate\Support\Facades\Schema::hasTable('academicyears') ? 'academicyears' : (\Illuminate\Support\Facades\Schema::hasTable('academic_years') ? 'academic_years' : 'academicYears');
        $activeSession = \Illuminate\Support\Facades\DB::table($academicTable)->where('is_active', 'yes')->value(\Illuminate\Support\Facades\Schema::hasColumn($academicTable, 'academicYear') ? 'academicYear' : 'label');
        $challansQuery = DB::table('challans')
            ->when($tenantId, function ($q) use ($tenantId) {
                $q->where('tenant_id', $tenantId);
            })
            ->orderByDesc('id');
        if ($activeSession) {
            $challansQuery->where('session', $activeSession);
        }
        $challanList = $challansQuery->limit(500)->get();

        return view('challan', compact('feeTypeList', 'classList', 'feesList', 'schoolList', 'challanList', 'studentList'));
    }

    public function classWiseChallan()
    {
        $tenantId = auth()->check() ? (auth()->user()->tenant_id ?? null) : null;
        $feeTypeList = DB::table('feetypes')->get();
        $classList = DB::table('classes')
            ->when($tenantId, function ($q) use ($tenantId) {
                $q->where('tenant_id', $tenantId);
            })
            ->get();
        $feesList = DB::table('fees')
            ->when($tenantId, function ($q) use ($tenantId) {
                $q->where('tenant_id', $tenantId);
            })
            ->get();
        $schoolList = DB::table('schools')
            ->when($tenantId, function ($q) use ($tenantId) {
                $q->where('id', $tenantId);
            })
            ->get();
        $challanList = DB::table('challans')
            ->when($tenantId, function ($q) use ($tenantId) {
                $q->where('tenant_id', $tenantId);
            })
            ->get();
        return view('classWiseChallan', compact('feeTypeList', 'classList', 'schoolList', 'feesList', 'challanList'));
    }

    public function printClassWiseChallans(Request $request)
    {

        $data = $request->all();

        $feeTypeList = DB::table('feetypes')->get();
        $classList = DB::table('classes')->get();
        $feesList = DB::table('fees')->get();
        $schoolList = DB::table('schools')->get();
        $tenantId = auth()->check() ? (auth()->user()->tenant_id ?? null) : null;
        $academicTable = \Illuminate\Support\Facades\Schema::hasTable('academicyears') ? 'academicyears' : (\Illuminate\Support\Facades\Schema::hasTable('academic_years') ? 'academic_years' : 'academicYears');
        $activeSession = \Illuminate\Support\Facades\DB::table($academicTable)->where('is_active', 'yes')->value(\Illuminate\Support\Facades\Schema::hasColumn($academicTable, 'academicYear') ? 'academicYear' : 'label');
        $fallbackSession = (date('n') < 8) ? ((date('Y') - 1) . '-' . date('Y')) : (date('Y') . '-' . (date('Y') + 1));
        $sessionToUse = $activeSession ?: $fallbackSession;

        $challanList = DB::table('challans')
            ->when($tenantId, function ($q) use ($tenantId) {
                $q->where('tenant_id', $tenantId);
            })
            ->where('school_id', '=', $data['school_id'])
            ->where('month', '=', $data['month'])
            ->where('grno', '!=', '2909')
            ->where('year', '=', $data['year'])
            ->where('session', '=', $sessionToUse)
            ->where('class_name', '=', $data['class_id'])
            //->limit(30)

            ->get();
        return view('printClassWiseChallans', compact('feeTypeList', 'classList', 'schoolList', 'feesList', 'challanList'));
    }



    public function addChallan(Request $request)
    {
        // Retrieve selected class, month, and year from the form

        $class_name = $request->input('class_id');
        $month = $request->input('month');
        $year = $request->input('year');
        $howManyMonth = $request->input('howManyMonth');
        $howManyStudents = $request->input('howManyStudents');

        $fromMonth = $request->input('fromMonth');
        $fromYear = $request->input('fromYear');
        $toMonth = $request->input('toMonth');
        $toYear = $request->input('toYear');

        $academicTable = \Illuminate\Support\Facades\Schema::hasTable('academicyears') ? 'academicyears' : (\Illuminate\Support\Facades\Schema::hasTable('academic_years') ? 'academic_years' : 'academicYears');
        $session = \Illuminate\Support\Facades\DB::table($academicTable)->where('is_active', 'yes')->first();
        if ($session && !\Illuminate\Support\Facades\Schema::hasColumn($academicTable, 'academicYear') && isset($session->label)) {
            $session->academicYear = $session->label;
        }
        if (!$session) {
            $year = date('Y');
            $month = date('n');
            $fallbackSession = ($month < 8) ? (($year - 1) . '-' . $year) : ($year . '-' . ($year + 1));
            $session = (object)['academicYear' => $fallbackSession]; // fallback
        }

        $tenantId = auth()->check() ? (auth()->user()->tenant_id ?? null) : null;

        // Helpers to safely resolve fee values and read fee_value from nullable rows
        $val = function ($row) {
            return (is_object($row) && isset($row->fee_value)) ? (float)$row->fee_value : 0.0;
        };
        $getFeeValue = function (string $className, string $monthName, int $yearVal, string $name) use ($tenantId) {
            // alias map for common naming differences
            $aliasMap = [
                'Tution Fee'    => ['Tuition Fee', 'Tuition', 'Tution'],
                'Exams'         => ['Exam', 'Examination', 'Exam Fee'],
                'IDF'           => ['IDF Fund', 'IDF Fee'],
                'CSF'           => ['CSF Fund', 'CSF Fee'],
                'RDF / CDF'     => ['RDF/CDF', 'RDF', 'CDF', 'RDF CDF'],
                'Security Fund' => ['Security', 'Security Fee', 'Security Charges'],
                'Admission'     => ['Admission Fee'],
                'Breakage'      => ['Breakage Fee'],
                'Misc'          => ['Miscellaneous', 'Misc Fee'],
                'CLC'           => ['SLC', 'School Leaving Certificate'],
                'IT'            => ['I.T', 'Computer', 'Computer Fee', 'IT Fee'],
            ];
            $candidates = array_unique(array_merge([$name], $aliasMap[$name] ?? []));
            foreach ($candidates as $nm) {
                $ft = DB::table('feetypes')->where('name', $nm)->first();
                $base = DB::table('fees')
                    ->where('class_name', $className)
                    ->when($tenantId, function ($q) use ($tenantId) {
                        $q->where('tenant_id', $tenantId);
                    })
                    ->where('year', $yearVal)
                    ->where(function ($q) use ($monthName) {
                        $q->where('month_name', $monthName)->orWhere('month', $monthName);
                    });
                if ($ft) {
                    $row = (clone $base)->where('fee_type_id', $ft->id)->orderByDesc('id')->first();
                    if ($row && isset($row->fee_value)) {
                        return (float)$row->fee_value;
                    }
                }
                $row = (clone $base)->where('fee_name', $nm)->orderByDesc('id')->first();
                if ($row && isset($row->fee_value)) {
                    return (float)$row->fee_value;
                }
            }
            return 0.0;
        };

        // Classifier that aggregates all fees for a class/month/year into challan fields using fuzzy labels
        $collectComponents = function (string $className, string $monthName, int $yearVal) use ($tenantId) {
            $rows = DB::table('fees')
                ->where('class_name', $className)
                ->when($tenantId, function ($q) use ($tenantId) {
                    $q->where('tenant_id', $tenantId);
                })
                ->where('year', $yearVal)
                ->where(function ($q) use ($monthName) {
                    $q->where('month_name', $monthName)->orWhere('month', $monthName);
                })
                ->orderByDesc('id')
                ->get();
            $map = [
                'admission' => 0.0,
                'breakage' => 0.0,
                'misc' => 0.0,
                'clc' => 0.0,
                'tution_fee' => 0.0,
                'idf' => 0.0,
                'exams' => 0.0,
                'it' => 0.0,
                'csf' => 0.0,
                'rdfcdf' => 0.0,
                'security_fund' => 0.0,
                'csf_label' => 'CSF'
            ];
            $label = function ($s) {
                return strtoupper(trim(preg_replace('/\s+/', ' ', (string)$s)));
            };
            foreach ($rows as $r) {
                $name = $label($r->fee_name ?? '');
                if (!$name && isset($r->fee_type_id)) {
                    $ft = DB::table('feetypes')->where('id', $r->fee_type_id)->first();
                    $name = $label($ft->name ?? '');
                }
                $v = (float)($r->fee_value ?? 0);
                if ($v == 0) continue;
                if (strpos($name, 'TUITION') !== false || strpos($name, 'TUTION') !== false) {
                    $map['tution_fee'] += $v;
                    continue;
                }
                if (strpos($name, 'ADMISSION') !== false) {
                    $map['admission'] += $v;
                    continue;
                }
                if (strpos($name, 'BREAKAGE') !== false) {
                    $map['breakage'] += $v;
                    continue;
                }
                if (strpos($name, 'MISC') !== false) {
                    $map['misc'] += $v;
                    continue;
                }
                if (strpos($name, 'CLC') !== false || strpos($name, 'SLC') !== false) {
                    $map['clc'] += $v;
                    continue;
                }
                if (strpos($name, 'EXAM') !== false) {
                    $map['exams'] += $v;
                    continue;
                }
                if (strpos($name, 'IDF') !== false) {
                    $map['idf'] += $v;
                    continue;
                }
                if (strpos($name, 'TRANSPORT') !== false) {
                    $map['csf'] += $v;
                    $map['csf_label'] = 'Transport';
                    continue;
                }
                if (strpos($name, 'CSF') !== false) {
                    $map['csf'] += $v;
                    continue;
                }
                if (strpos($name, 'RDF') !== false || strpos($name, 'CDF') !== false) {
                    $map['rdfcdf'] += $v;
                    continue;
                }
                if (strpos($name, 'SECURITY') !== false) {
                    $map['security_fund'] += $v;
                    continue;
                }
                if (strpos($name, 'IT') !== false || strpos($name, 'COMPUTER') !== false) {
                    $map['it'] += $v;
                    continue;
                }
            }
            return (object)$map;
        };

        if ($howManyMonth == "more" && $howManyStudents == "allClass") {

            // Build one aggregated challan for the whole class across the selected month range
            $studentsBase = DB::table('students')
                ->join('classes', 'students.class_id', '=', 'classes.id')
                ->leftJoin('parents', 'students.parent_id', '=', 'parents.id')
                ->where('classes.className', $class_name)
                ->whereIn('students.status', ['active', 'Active']);
            $totalStudents = (clone $studentsBase)->count();
            $empCount = (clone $studentsBase)->where('parents.is_commandercityschool_employee', 'Yes')->count();
            $nonEmpCount = max(0, $totalStudents - $empCount);

            $totalMonths = intval($request->howManyMonths);
            $fromMonthNumber = date('n', strtotime($fromMonth));
            $currentYear = (int)$fromYear;

            // Accumulators across months (per-student fee * counts)
            $sum = [
                'tution_fee' => 0.0,
                'exams' => 0.0,
                'idf' => 0.0,
                'csf' => 0.0,
                'rdfcdf' => 0.0,
                'security_fund' => 0.0,
                'admission' => 0.0,
                'breakage' => 0.0,
                'misc' => 0.0,
                'clc' => 0.0,
                'it' => 0.0
            ];
            for ($i = 0; $i <= $totalMonths; $i++) {
                $monthCalc = date('F', mktime(0, 0, 0, $fromMonthNumber + $i, 1));
                $yearCalc = $currentYear + intval(floor(($fromMonthNumber - 1 + $i) / 12));
                $monthIndex = (($fromMonthNumber - 1 + $i) % 12) + 1;
                $monthCalc = date('F', mktime(0, 0, 0, $monthIndex, 1));

                $comp = $collectComponents($class_name, $monthCalc, (int)$yearCalc);
                if (($comp->tution_fee + $comp->exams + $comp->idf + $comp->csf + $comp->rdfcdf + $comp->security_fund + $comp->admission + $comp->breakage + $comp->misc + $comp->clc + $comp->it) == 0) {
                    $comp = (object) [
                        'tution_fee' => $getFeeValue($class_name, $monthCalc, (int)$yearCalc, 'Tution Fee'),
                        'exams' => $getFeeValue($class_name, $monthCalc, (int)$yearCalc, 'Exams'),
                        'idf' => $getFeeValue($class_name, $monthCalc, (int)$yearCalc, 'IDF'),
                        'csf' => $getFeeValue($class_name, $monthCalc, (int)$yearCalc, 'CSF'),
                        'rdfcdf' => $getFeeValue($class_name, $monthCalc, (int)$yearCalc, 'RDF / CDF'),
                        'security_fund' => $getFeeValue($class_name, $monthCalc, (int)$yearCalc, 'Security Fund'),
                        'admission' => $getFeeValue($class_name, $monthCalc, (int)$yearCalc, 'Admission'),
                        'breakage' => $getFeeValue($class_name, $monthCalc, (int)$yearCalc, 'Breakage'),
                        'misc' => $getFeeValue($class_name, $monthCalc, (int)$yearCalc, 'Misc'),
                        'clc' => $getFeeValue($class_name, $monthCalc, (int)$yearCalc, 'CLC'),
                        'it' => $getFeeValue($class_name, $monthCalc, (int)$yearCalc, 'IT'),
                    ];
                }
                // Employees pay tuition only; others pay all
                $sum['tution_fee'] += (float)$comp->tution_fee * $totalStudents;
                foreach (['exams', 'idf', 'csf', 'rdfcdf', 'security_fund', 'admission', 'breakage', 'misc', 'clc', 'it'] as $k) {
                    $sum[$k] += (float)$comp->$k * $nonEmpCount;
                }
            }

            $govTotal = $sum['tution_fee'] + $sum['admission'] + $sum['breakage'] + $sum['misc'] + $sum['clc'];
            $fundTotal = $sum['exams'] + $sum['idf'] + $sum['csf'] + $sum['rdfcdf'] + $sum['security_fund'] + $sum['it'];
            $grand = $govTotal + $fundTotal;

            // Insert a single class-level challan
            $tenantId = auth()->check() ? (auth()->user()->tenant_id ?? null) : null;
            DB::table('challans')->insert([
                'tenant_id' => $tenantId,
                'session' => $session->academicYear,
                'school_id' => $request->input('school_id'),
                'class_name' => $class_name,
                'student_id' => null,
                'student_name' => 'ALL STUDENTS',
                'grno' => null,
                'month' => $fromMonth,
                'issued_date' => date('d-m-Y'),
                'due_date' => $request->due_date,
                'totalMonth' => $totalMonths + 1,
                'year' => (int)$fromYear,
                'fromYear' => $fromYear,
                'fromMonth' => $fromMonth,
                'toYear' => $toYear,
                'toMonth' => $toMonth,
                'exams' => $sum['exams'],
                'total_fee' => $grand,
                'idf' => $sum['idf'],
                'tution_fee' => $sum['tution_fee'],
                'total' => $govTotal,
                'csf' => $sum['csf'],
                'rdfcdf' => $sum['rdfcdf'],
                'security_fund' => $sum['security_fund'],
                'admission' => $sum['admission'],
                'breakage' => $sum['breakage'],
                'misc' => $sum['misc'],
                'clc' => $sum['clc'],
                'it' => $sum['it'],
                'slc' => 0,
                'debit' => $grand,
                'type' => 'd',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        if ($howManyMonth == "one" && $howManyStudents == "allClass") {

            // Retrieve fee information and build ONE challan for the whole class for the selected month/year
            $studentsBase = DB::table('students')
                ->join('classes', 'students.class_id', '=', 'classes.id')
                ->leftJoin('parents', 'students.parent_id', '=', 'parents.id')
                ->where('classes.className', $class_name)
                ->whereIn('students.status', ['active', 'Active']);
            $totalStudents = (clone $studentsBase)->count();
            if ($totalStudents == 0) {
                return redirect()->back()->with('message', 'No students found for the selected class.');
            }
            $empCount = (clone $studentsBase)->where('parents.is_commandercityschool_employee', 'Yes')->count();
            $nonEmpCount = max(0, $totalStudents - $empCount);

            // Prefer classifier aggregation (per-student values)
            $comp1 = $collectComponents($class_name, $month, (int)$year);
            if (($comp1->tution_fee + $comp1->exams + $comp1->idf + $comp1->csf + $comp1->rdfcdf + $comp1->security_fund + $comp1->admission + $comp1->breakage + $comp1->misc + $comp1->clc + $comp1->it) == 0) {
                // fallback to name-based resolver
                $resolver = function ($nm) use ($class_name, $month, $year) {
                    $aliasMap = [
                        'Tution Fee'    => ['Tuition Fee', 'Tuition', 'Tution'],
                        'Exams'         => ['Exam'],
                        'IDF'           => ['IDF Fund'],
                        'CSF'           => [],
                        'RDF / CDF'     => ['RDF/CDF', 'RDF', 'CDF'],
                        'Security Fund' => ['Security', 'Security Fee'],
                        'Admission'     => ['Admission Fee'],
                        'Breakage'      => [],
                        'Misc'          => ['Miscellaneous'],
                        'CLC'           => ['SLC'],
                        'IT'            => ['I.T', 'Computer', 'Computer Fee'],
                    ];
                    $candidates = array_unique(array_merge([$nm], $aliasMap[$nm] ?? []));
                    foreach ($candidates as $n) {
                        $ft = DB::table('feetypes')->where('name', $n)->first();
                        $base = DB::table('fees')->where('class_name', $class_name)->where('year', $year)->where(function ($q) use ($month) {
                            $q->where('month_name', $month)->orWhere('month', $month);
                        });
                        if ($ft) {
                            $row = (clone $base)->where('fee_type_id', $ft->id)->orderByDesc('id')->first();
                            if ($row && isset($row->fee_value)) return (float)$row->fee_value;
                        }
                        $row = (clone $base)->where('fee_name', $n)->orderByDesc('id')->first();
                        if ($row && isset($row->fee_value)) return (float)$row->fee_value;
                    }
                    return 0.0;
                };
                $comp1 = (object) [
                    'exams' => $resolver('Exams'),
                    'tution_fee' => $resolver('Tution Fee'),
                    'idf' => $resolver('IDF'),
                    'csf' => $resolver('CSF'),
                    'rdfcdf' => $resolver('RDF / CDF'),
                    'security_fund' => $resolver('Security Fund'),
                    'admission' => $resolver('Admission'),
                    'breakage' => $resolver('Breakage'),
                    'misc' => $resolver('Misc'),
                    'clc' => $resolver('CLC'),
                    'it' => $resolver('IT'),
                ];
            }
            // Aggregate: employees pay tuition only; others pay all items
            $agg = [];
            $agg['tution_fee'] = (float)$comp1->tution_fee * $totalStudents;
            foreach (['exams', 'idf', 'csf', 'rdfcdf', 'security_fund', 'admission', 'breakage', 'misc', 'clc', 'it'] as $k) {
                $agg[$k] = (float)$comp1->$k * $nonEmpCount;
            }
            $govTotal = $agg['tution_fee'] + $agg['admission'] + $agg['breakage'] + $agg['misc'] + $agg['clc'];
            $fundTotal = $agg['exams'] + $agg['idf'] + $agg['csf'] + $agg['rdfcdf'] + $agg['security_fund'] + $agg['it'];
            $grand = $govTotal + $fundTotal;

            // Prevent duplicates
            $existingChallans = DB::table('challans')
                ->where('class_name', $class_name)
                ->where('month', $month)
                ->where('year', $year)
                ->exists();
            if ($existingChallans) {
                return redirect()->back()->with('message', 'Challan already exists for the selected class, month, and year.');
            }

            // Insert a single class-level challan
            $tenantId = auth()->check() ? (auth()->user()->tenant_id ?? null) : null;
            DB::table('challans')->insert([
                'tenant_id' => $tenantId,
                'session' => $session->academicYear,
                'school_id' => $request->input('school_id'),
                'class_name' => $class_name,
                'student_id' => null,
                'student_name' => 'ALL STUDENTS',
                'grno' => null,
                'month' => $month,
                'issued_date' => date('d-m-Y'),
                'due_date' => $request->due_date,
                'totalMonth' => 1,
                'year' => (int)$year,
                'exams' => $agg['exams'],
                'total_fee' => $grand,
                'idf' => $agg['idf'],
                'tution_fee' => $agg['tution_fee'],
                'total' => $govTotal,
                'csf' => $agg['csf'],
                'rdfcdf' => $agg['rdfcdf'],
                'security_fund' => $agg['security_fund'],
                'admission' => $agg['admission'],
                'breakage' => $agg['breakage'],
                'misc' => $agg['misc'],
                'clc' => $agg['clc'],
                'it' => $agg['it'],
                'slc' => 0,
                'debit' => $grand,
                'type' => 'd',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        if ($howManyMonth == "more" && $howManyStudents == "oneStudent") {


                // Multi-month per student
                $students = DB::table('students')
                    ->join('classes', 'students.class_id', '=', 'classes.id')
                    ->join('parents', 'students.parent_id', '=', 'parents.id')
                    ->select('parents.*', 'students.studentName', 'students.id as studentID', 'students.grno', 'students.school_id')
                    ->where('classes.className', $class_name)
                    ->where('students.id', $studentID)
                    ->when($tenantId, function ($q) use ($tenantId) {
                        $q->where('students.tenant_id', $tenantId);
                    })
                    ->get();
                $totalMonths = intval($request->howManyMonths);
                $fromMonthNumber = date('n', strtotime($request->fromMonth));
                $currentYear = intval($request->fromYear);

                foreach ($students as $student) {
                    for ($i = 0; $i <= $totalMonths; $i++) {
                        $time = mktime(0, 0, 0, $fromMonthNumber + $i, 1, $currentYear);
                        $month = date('F', $time);
                        $year = date('Y', $time);

                        $comp2 = $collectComponents($class_name, $month, (int)$year);
                        if (($comp2->tution_fee + $comp2->exams + $comp2->idf + $comp2->csf + $comp2->rdfcdf + $comp2->security_fund + $comp2->admission + $comp2->breakage + $comp2->misc + $comp2->clc + $comp2->it) == 0) {
                            $comp2 = (object)[
                                'exams' => $getFeeValue($class_name, $month, (int)$year, 'Exams'),
                                'tution_fee' => $getFeeValue($class_name, $month, (int)$year, 'Tution Fee'),
                                'idf' => $getFeeValue($class_name, $month, (int)$year, 'IDF'),
                                'csf' => $getFeeValue($class_name, $month, (int)$year, 'CSF'),
                                'rdfcdf' => $getFeeValue($class_name, $month, (int)$year, 'RDF / CDF'),
                                'security_fund' => $getFeeValue($class_name, $month, (int)$year, 'Security Fund'),
                                'admission' => $getFeeValue($class_name, $month, (int)$year, 'Admission'),
                                'breakage' => $getFeeValue($class_name, $month, (int)$year, 'Breakage'),
                                'misc' => $getFeeValue($class_name, $month, (int)$year, 'Misc'),
                                'clc' => $getFeeValue($class_name, $month, (int)$year, 'CLC'),
                                'it' => $getFeeValue($class_name, $month, (int)$year, 'IT'),
                            ];
                        }
                        $exams = $comp2->exams;
                        $tutionFee = $comp2->tution_fee;
                        $idf = $comp2->idf;
                        $csf = $comp2->csf;
                        $rdfcdf = $comp2->rdfcdf;
                        $security_fund = $comp2->security_fund;
                        $admission = $comp2->admission;
                        $breakage = $comp2->breakage;
                        $misc = $comp2->misc;
                        $clc = $comp2->clc;
                        $it = $comp2->it;

                    if ($student->is_commandercityschool_employee == 'Yes') {
                        DB::table('challans')->insert([
                            'tenant_id' => auth()->check() ? (auth()->user()->tenant_id ?? null) : null,
                            'session' => $session->academicYear,
                            'school_id' => $student->school_id,
                            'class_name' => $class_name,
                            'student_id' => $student->studentID,
                            'student_name' => $student->studentName,
                            'grno' => $student->grno,
                            'month' => $month,
                            'totalMonth' => $totalMonths + 1,
                            'year' => $year,
                            'fromYear' => $fromYear,
                            'fromMonth' => $fromMonth,
                            'toYear' => $toYear,
                            'toMonth' => $toMonth,
                            'exams' => 0,
                            'total' => ($val($admission) + $val($breakage) + $val($misc) + $val($clc) + $val($tutionFee)),
                            'total_fee' => $val($tutionFee),
                            'idf' => 0,
                            'tution_fee' => $val($tutionFee),
                            'csf' => 0,
                            'rdfcdf' => 0,
                            'security_fund' => 0,
                            'admission' => 0,
                            'breakage' => 0,
                            'misc' => 0,
                            'clc' => 0,
                            'it' => 0,
                            'slc' => 0,
                            'debit' => $val($tutionFee),
                            'type' => 'd',
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    } else {
                        $insertData = [
                            'session' => $session->academicYear,
                            'school_id' => $student->school_id,
                            'class_name' => $class_name,
                            'student_id' => $student->studentID,
                            'student_name' => $student->studentName,
                            'grno' => $student->grno,
                            'month' => $month,
                            'totalMonth' => $totalMonths + 1,
                            'year' => $year,
                            'fromYear' => $fromYear,
                            'fromMonth' => $fromMonth,
                            'toYear' => $toYear,
                            'toMonth' => $toMonth,
                            'admission' => $val($admission),
                            'clc' => $val($clc),
                            'breakage' => $val($breakage),
                            'misc' => $val($misc),
                            'tution_fee' => $val($tutionFee),
                            'exams' => $val($exams),
                            'idf' => $val($idf),
                            'csf' => $val($csf),
                            'rdfcdf' => $val($rdfcdf),
                            'security_fund' => $val($security_fund),
                            'it' => $val($it),
                            'debit' => $fees,
                            'total_fee' => $fees,
                            'type' => 'd',
                            'created_at' => now(),
                            'updated_at' => now(),

                            // Calculate total
                            'total' => ($admission->fee_value ?? 0) +
                                ($clc->fee_value ?? 0) +
                                ($breakage->fee_value ?? 0) +
                                ($misc->fee_value ?? 0) +
                                ($tutionFee->fee_value ?? 0),
                        ];
                        $insertData['tenant_id'] = auth()->check() ? (auth()->user()->tenant_id ?? null) : null;
                        DB::table('challans')->insert($insertData);
                    }
                }
            }
        }

        if ($howManyMonth == "one" && $howManyStudents == "oneStudent") {

            // Use a robust resolver for single-student/one-month
            $resolve = function ($nm) use ($class_name, $month, $year, $tenantId) {
                $aliasMap = [
                    'Tution Fee'    => ['Tuition Fee', 'Tuition', 'Tution'],
                    'Exams'         => ['Exam'],
                    'IDF'           => ['IDF Fund'],
                    'CSF'           => [],
                    'RDF / CDF'     => ['RDF/CDF', 'RDF', 'CDF'],
                    'Security Fund' => ['Security', 'Security Fee'],
                    'Admission'     => ['Admission Fee'],
                    'Breakage'      => [],
                    'Misc'          => ['Miscellaneous'],
                    'CLC'           => ['SLC'],
                    'IT'            => ['I.T', 'Computer', 'Computer Fee'],
                ];
                $cands = array_unique(array_merge([$nm], $aliasMap[$nm] ?? []));
                foreach ($cands as $n) {
                    $ft = DB::table('feetypes')->where('name', $n)->first();
                    $base = DB::table('fees')
                        ->where('class_name', $class_name)
                        ->when($tenantId, function ($q) use ($tenantId) {
                            $q->where('tenant_id', $tenantId);
                        })
                        ->where('year', $year)
                        ->where(function ($q) use ($month) {
                            $q->where('month_name', $month)->orWhere('month', $month);
                        });
                    if ($ft) {
                        $row = (clone $base)->where('fee_type_id', $ft->id)->orderByDesc('id')->first();
                        if ($row) return $row;
                    }
                    $row = (clone $base)->where('fee_name', $n)->orderByDesc('id')->first();
                    if ($row) return $row;
                }
                return (object)['fee_value' => 0];
            };
            $compS = $collectComponents($class_name, $month, (int)$year);
            if (($compS->tution_fee + $compS->exams + $compS->idf + $compS->csf + $compS->rdfcdf + $compS->security_fund + $compS->admission + $compS->breakage + $compS->misc + $compS->clc + $compS->it) == 0) {
                $compS = (object)[
                    'exams'         => $resolve('Exams'),
                    'tution_fee'    => $resolve('Tution Fee'),
                    'idf'           => $resolve('IDF'),
                    'csf'           => $resolve('CSF'),
                    'rdfcdf'        => $resolve('RDF / CDF'),
                    'security_fund' => $resolve('Security Fund'),
                    'admission'     => $resolve('Admission'),
                    'breakage'      => $resolve('Breakage'),
                    'misc'          => $resolve('Misc'),
                    'clc'           => $resolve('CLC'),
                    'it'            => $resolve('IT'),
                ];
            } else {
                // map to objects for compatibility with existing if/else logic below
                foreach(['exams','tution_fee','idf','csf','rdfcdf','security_fund','admission','breakage','misc','clc','it'] as $key) {
                    if (is_numeric($compS->$key)) $compS->$key = (object)['fee_value' => $compS->$key];
                }
            }
            $exams = $compS->exams;
            $tutionFee = $compS->tution_fee;
            $idf = $compS->idf;
            $csf = $compS->csf;
            $rdfcdf = $compS->rdfcdf;
            $security_fund = $compS->security_fund;
            $admission = $compS->admission;
            $breakage = $compS->breakage;
            $misc = $compS->misc;
            $clc = $compS->clc;
            $it = $compS->it;
            $fees = ($tutionFee->fee_value ?? 0) + ($exams->fee_value ?? 0) + ($idf->fee_value ?? 0) + ($csf->fee_value ?? 0) + ($rdfcdf->fee_value ?? 0) + ($security_fund->fee_value ?? 0) + ($admission->fee_value ?? 0) + ($breakage->fee_value ?? 0) + ($misc->fee_value ?? 0) + ($clc->fee_value ?? 0) + ($it->fee_value ?? 0);

            $studentDetail = DB::table('students')->where('id', '=', $request->student_id)->first();
            $existingPaidChallans = DB::table('challans')
                ->where('class_name', $class_name)
                ->where('student_id', $request->student_id)
                ->where('grno', $studentDetail->grno)
                ->where('month', $month)
                ->where('year', $year)
                ->where('status', 'un-paid')
                ->where('paid', 'NO')
                ->exists();

            if ($existingPaidChallans) {
                return redirect()->back()->with('message', '<strong>' . $studentDetail->studentName . '</strong> for  class <strong>' . $class_name . '</strong> already exist un-paid challan.');
            } else {

                // Retrieve student information based on the selected class
                $students = DB::table('students')->where('students.id', $request->student_id)->where('students.grno', $studentDetail->grno)
                    ->join('classes', 'students.class_id', '=', 'classes.id')
                    ->join('parents', 'students.parent_id', '=', 'parents.id')
                    ->select('parents.*', 'students.studentName', 'students.id as studentID', 'students.grno', 'students.school_id')
                    ->where('classes.className', $class_name)
                    ->get();
                // Check if any students were retrieved

                if ($students->isEmpty()) {
                    return redirect()->back()->with('message', '<strong>' . $studentDetail->studentName . '</strong> is not in selected class <strong>' . $class_name . '</strong> .');
                }

                // Retrieve student information based on the selected class
                $ifChallanExist = DB::table('fees')
                    ->where('class_name', $class_name)
                    ->where('month_name', $month)
                    ->where('year', $year)
                    ->exists();
                // Check if any students were retrieved

                if (!$ifChallanExist) {
                    return redirect()->back()->with('message', 'Fees is not generated yet for <strong>' . $class_name . '</strong> in <strong>' . $month . '</strong> of <strong>' . $year . '</strong> Please generate fee first then create challan <a class="btn btn-success btn-sm" href="' . route("feesManagement") . '"> Generate Fees </a>.');
                }



                // Insert data into the challans table
                foreach ($students as $student) {
                    if ($student->is_commandercityschool_employee == 'Yes') {
                        DB::table('challans')->insert([
                            'tenant_id' => auth()->check() ? (auth()->user()->tenant_id ?? null) : null,
                            'session' => $session->academicYear,
                            'school_id' => $student->school_id,
                            'class_name' => $class_name,
                            'student_id' => $student->studentID,
                            'student_name' => $student->studentName,
                            'grno' => $student->grno,
                            'month' => $month,
                            'totalMonth' => 1,
                            'year' => $year,
                            'exams' => 0,
                            'total' => $tutionFee->fee_value,
                            'total_fee' => $tutionFee->fee_value,
                            'idf' => 0,
                            'tution_fee' => $tutionFee->fee_value,
                            'csf' => 0,
                            'rdfcdf' => 0,
                            'security_fund' => 0,
                            'admission' => 0,
                            'breakage' => 0,
                            'misc' => 0,
                            'clc' => 0,
                            'it' => 0,
                            'slc' => 0,
                            'debit' => $tutionFee->fee_value,
                            'type' => 'd',
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    } else {

                        DB::table('challans')->insert([
                            'tenant_id' => auth()->check() ? (auth()->user()->tenant_id ?? null) : null,
                            'session' => $session->academicYear,
                            'school_id' => $student->school_id,
                            'class_name' => $class_name,
                            'student_id' => $student->studentID,
                            'student_name' => $student->studentName,
                            'grno' => $student->grno,
                            'month' => $month,
                            'totalMonth' => 1,
                            'year' => $year,
                            'exams' => $val($exams),
                            'total_fee' => $fees,
                            'idf' => $val($idf),
                            'tution_fee' => $val($tutionFee),
                            'total' => $val($tutionFee),
                            'csf' => $val($csf),
                            'rdfcdf' => $val($rdfcdf),
                            'security_fund' => $val($security_fund),
                            'admission' => $val($admission),
                            'breakage' => $val($breakage),
                            'misc' => $val($misc),
                            'clc' => $val($clc),
                            'it' => $val($it),
                            'slc' => 0,
                            'debit' => $fees,
                            'type' => 'd',
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            }
        }

        return redirect()->back()->with('message', 'Challan Generated Successfully.');
    }

    public function viewChallan($id)
    {
        $tenantId = auth()->check() ? (auth()->user()->tenant_id ?? null) : null;
        
        // 1. Fetch the main challan record
        $challan = DB::table('challans')->where('id', (int)$id)->first();
        
        if (!$challan) {
            // Try fallback search by identifier if not a direct numeric match (e.g. GRNO)
            $challan = DB::table('challans')
                ->where(function($q) use ($id) {
                    $q->where('grno', $id)->orWhere('student_id', $id);
                })
                ->when($tenantId, function ($q) use ($tenantId) { $q->where('tenant_id', $tenantId); })
                ->orderByDesc('id')
                ->first();
        }

        if (!$challan) {
            return redirect()->route('challan')->with('error', 'Challan not found.');
        }

        // 2. Fetch school details
        $school = DB::table('schools')->where('id', $challan->school_id)->first();
        if (!$school && $tenantId) {
            $school = DB::table('schools')->where('id', $tenantId)->first();
        }

        // 3. Robust Student & Parent Discovery
        $student = null;
        if (is_numeric($challan->student_id)) {
            $student = DB::table('students')->where('id', (int)$challan->student_id)->first();
        }
        if (!$student) {
            $q = DB::table('students')->where('studentName', $challan->student_id);
            if (!empty($challan->grno)) { $q->where('grno', $challan->grno); }
            $student = $q->first();
        }
        $parent = ($student && !empty($student->parent_id))
            ? DB::table('parents')->where('id', $student->parent_id)->first()
            : null;

        // 4. Force Fee Sync if data is missing
        // This handles cases where challan was generated with 0 defaults
        $hasData = (float)($challan->tution_fee ?? 0) > 0 || (float)($challan->exams ?? 0) > 0;
        if (!$hasData || $challan->student_name == 'ALL STUDENTS') {
            $feeRows = DB::table('fees')
                ->where('class_name', $challan->class_name)
                ->when($tenantId, function ($q) use ($tenantId) { $q->where('tenant_id', $tenantId); })
                ->where(function ($q) use ($challan) {
                    $q->whereRaw('LOWER(month_name) = ?', [strtolower((string)$challan->month)])
                      ->orWhereRaw('LOWER(month) = ?', [strtolower((string)$challan->month)]);
                })
                ->where('year', $challan->year)
                ->get();

            $map = [
                'admission' => 0.0, 'breakage' => 0.0, 'misc' => 0.0, 'clc' => 0.0,
                'tution_fee' => 0.0, 'idf' => 0.0, 'exams' => 0.0, 'it' => 0.0,
                'csf' => 0.0, 'rdfcdf' => 0.0, 'security_fund' => 0.0
            ];

            foreach ($feeRows as $r) {
                $name = strtoupper(trim(preg_replace('/\s+/', ' ', (string)($r->fee_name ?? ''))));
                if (!$name && isset($r->fee_type_id)) {
                    $ft = DB::table('feetypes')->where('id', $r->fee_type_id)->first();
                    $name = strtoupper(trim(preg_replace('/\s+/', ' ', (string)($ft->name ?? ''))));
                }
                
                $v = (float)($r->fee_value ?? 0);
                if ($v == 0) continue;

                if (str_contains($name, 'TUITION') || str_contains($name, 'TUTION')) { $map['tution_fee'] += $v; }
                elseif (str_contains($name, 'ADMISSION')) { $map['admission'] += $v; }
                elseif (str_contains($name, 'BREAKAGE')) { $map['breakage'] += $v; }
                elseif (str_contains($name, 'MISC')) { $map['misc'] += $v; }
                elseif (str_contains($name, 'CLC') || str_contains($name, 'SLC')) { $map['clc'] += $v; }
                elseif (str_contains($name, 'EXAM')) { $map['exams'] += $v; }
                elseif (str_contains($name, 'IDF')) { $map['idf'] += $v; }
                elseif (str_contains($name, 'TRANSPORT') || str_contains($name, 'CSF')) { $map['csf'] += $v; }
                elseif (str_contains($name, 'RDF') || str_contains($name, 'CDF')) { $map['rdfcdf'] += $v; }
                elseif (str_contains($name, 'SECURITY')) { $map['security_fund'] += $v; }
                elseif (str_contains($name, 'IT') || str_contains($name, 'COMPUTER')) { $map['it'] += $v; }
            }

            foreach ($map as $k => $mv) {
                if ($mv > 0) $challan->$k = $mv;
            }
        }

        // 5. Final Calculations
        $fund4_label = 'CSF';
        $feeRowsForLabel = DB::table('fees')->where('class_name', $challan->class_name)->where('year', $challan->year)->where('tenant_id', $tenantId)->pluck('fee_name');
        foreach ($feeRowsForLabel as $nm) {
            if (stripos((string)$nm, 'transport') !== false) { $fund4_label = 'Transport'; break; }
        }

        $fundsTotal = (float)($challan->exams ?? 0) + (float)($challan->idf ?? 0) + (float)($challan->csf ?? 0) + (float)($challan->rdfcdf ?? 0) + (float)($challan->security_fund ?? 0) + (float)($challan->it ?? 0);
        $govTotal = (float)($challan->tution_fee ?? 0) + (float)($challan->admission ?? 0) + (float)($challan->breakage ?? 0) + (float)($challan->misc ?? 0) + (float)($challan->clc ?? 0);
        
        $challan->total = $govTotal;
        $challan->total_fee = $govTotal + $fundsTotal;
        $totalFee = $fundsTotal; // For backward compat with view
        $GTotal = $challan->total_fee;

        $total_fee_in_words = $this->numberToWords($challan->total_fee);

        return view('viewChallan', [
            'challanView' => $challan,
            'challanViewByID' => $challan, // compatibility
            'school' => $school,
            'student' => $student,
            'parent' => $parent,
            'fund4_label' => $fund4_label,
            'totalFee' => $totalFee,
            'GTotal' => $GTotal,
            'total_fee_in_words' => $total_fee_in_words
        ]);
    }

    public function challanPaidByID($id)
    {
        $existing = DB::table('challans')->where('id', $id)->first();
        if ($existing) {
            DB::table('challans')->where('id', $id)->update(['status' => 'paid', 'paid' => 'Yes', 'type' => 'c']);
            return redirect()->back()->with('message', 'Challan Paid Successfully.');
        }
        return redirect()->back()->with('message', 'Challan not found.');
    }

    public function editChallan($id)
    {
        $feeTypeList = DB::table('feetypes')->get();
        $classList = DB::table('classes')->get();
        $feesList = DB::table('fees')->get();
        $challanFees = '';
        $challanList = DB::table('challans')->get();
        $challanView = DB::table('challans')->where('id', '=', $id)->first();
        $school = null;
        if ($challanView && $challanView->school_id) {
            $school = DB::table('schools')->where('id', $challanView->school_id)->first();
        }

        $tenantId = auth()->check() ? (auth()->user()->tenant_id ?? null) : null;

        if ($challanView) {
            $getFeeValue = function (string $name) use ($challanView, $tenantId) {
                $aliasMap = [
                    'Tution Fee'    => ['Tuition Fee', 'Tuition', 'Tution'],
                    'Exams'         => ['Exam', 'Examination', 'Exam Fee'],
                    'IDF'           => ['IDF Fund', 'IDF Fee'],
                    'CSF'           => ['CSF Fund', 'CSF Fee'],
                    'RDF / CDF'     => ['RDF/CDF', 'RDF', 'CDF', 'RDF CDF'],
                    'Security Fund' => ['Security', 'Security Fee', 'Security Charges'],
                    'Admission'     => ['Admission Fee'],
                    'Breakage'      => ['Breakage Fee'],
                    'Misc'          => ['Miscellaneous', 'Misc Fee'],
                    'CLC'           => ['SLC', 'School Leaving Certificate'],
                    'IT'            => ['I.T', 'Computer', 'Computer Fee', 'IT Fee'],
                ];
                $candidates = array_unique(array_merge([$name], $aliasMap[$name] ?? []));
                foreach ($candidates as $nm) {
                    $ft = DB::table('feetypes')->where('name', $nm)->first();
                    $base = DB::table('fees')
                        ->where('class_name', $challanView->class_name)
                        ->when($tenantId, function ($q) use ($tenantId) { $q->where('tenant_id', $tenantId); })
                        ->where('year', $challanView->year)
                        ->where(function ($q) use ($challanView) {
                            $q->whereRaw('LOWER(month_name) = ?', [strtolower((string)$challanView->month)])
                                ->orWhereRaw('LOWER(month) = ?', [strtolower((string)$challanView->month)]);
                        });
                    if ($ft) {
                        $row = (clone $base)->where('fee_type_id', $ft->id)->orderByDesc('id')->first();
                        if ($row && isset($row->fee_value)) return (float)$row->fee_value;
                    }
                    $row = (clone $base)->where('fee_name', $nm)->orderByDesc('id')->first();
                    if ($row && isset($row->fee_value)) return (float)$row->fee_value;
                }
                return 0.0;
            };

            foreach (['admission', 'breakage', 'misc', 'clc', 'tution_fee', 'exams', 'idf', 'csf', 'rdfcdf', 'security_fund', 'it'] as $key) {
                if ((float)($challanView->$key ?? 0) == 0) {
                    $challanView->$key = $getFeeValue(ucfirst(str_replace('_', ' ', $key)));
                }
            }
        }

        $fund4_label = 'CSF';
        $componentSum = (float)($challanView->exams ?? 0) + (float)($challanView->idf ?? 0) + (float)($challanView->csf ?? 0) + (float)($challanView->rdfcdf ?? 0) + (float)($challanView->security_fund ?? 0) + (float)($challanView->it ?? 0);
        $totalFee = $componentSum;
        $GTotal = $componentSum + (float)($challanView->tution_fee ?? 0);

        return view('editChallan', compact('totalFee', 'GTotal', 'feeTypeList', 'classList', 'feesList', 'challanFees', 'challanView', 'challanList', 'fund4_label', 'school'));
    }

    public function cashBook()
    {
        $tenantId = auth()->check() ? (auth()->user()->tenant_id ?? null) : null;
        $feeTypeList = DB::table('feetypes')->get();
        $classList = DB::table('classes')->get();
        $feesList = DB::table('fees')->get();

        $studentList = DB::table('students')
            ->when($tenantId, function ($q) use ($tenantId) {
                $q->where('tenant_id', $tenantId);
            })
            ->whereIn('status', ['active', 'Active'])
            ->select('id', 'studentName', 'grno')
            ->get();

        return view('cashBook', compact('feeTypeList', 'classList', 'feesList', 'studentList'));
    }

    public function cashBookData(Request $request)
    {
        $tenantId = auth()->user()->tenant_id ?? null;

        $query = DB::table('challans')
            ->join('schools', 'challans.school_id', '=', 'schools.id')
            ->leftJoin('students', 'challans.student_id', '=', 'students.id')
            ->select(
                'challans.*',
                'schools.schoolName as school_name',
                'students.studentName as sname',
                'students.grno as student_grno'
            )
            ->when($tenantId, function ($q) use ($tenantId) {
                $q->where('challans.tenant_id', $tenantId);
            });

        // Totals for the summary cards (calculated once on the filtered set)
        $summary = (clone $query)->select(
            DB::raw('SUM(tution_fee) as t_tuition'),
            DB::raw('SUM(admission + breakage + misc + clc + tution_fee) as t_govt'),
            DB::raw('SUM(idf + exams + it + csf + rdfcdf + security_fund) as t_funds'),
            DB::raw('SUM(total_fee) as t_grand')
        )->first();

        // DataTables paging & search
        $recordsTotal = (clone $query)->count();

        if ($request->has('search') && $request->input('search.value')) {
            $search = $request->input('search.value');
            $query->where(function ($q) use ($search) {
                $q->where('challans.month', 'like', "%$search%")
                    ->orWhere('students.studentName', 'like', "%$search%")
                    ->orWhere('students.grno', 'like', "%$search%")
                    ->orWhere('schools.schoolName', 'like', "%$search%");
            });
        }

        $recordsFiltered = (clone $query)->count();

        $data = $query->skip($request->input('start', 0))
            ->take($request->input('length', 10))
            ->orderBy('challans.id', 'desc')
            ->get();

        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
            'summary' => [
                'tuition' => $summary->t_tuition ?? 0,
                'govt' => $summary->t_govt ?? 0,
                'funds' => $summary->t_funds ?? 0,
                'total' => $summary->t_grand ?? 0
            ]
        ]);
    }


    public function editChallanByChallanID(Request $request)
    {


        $data = $request->all();

        // Assuming $chalan_id is the identifier of the chalan you want to update
        DB::table('challans')
            ->where('id', $data['challanID'])
            ->update([
                'exams' => isset($data['exams']) ? $data['exams'] : 0,
                'tution_fee' => isset($data['tution_fee']) ? $data['tution_fee'] : 0,
                'clc' => isset($data['slc']) ? $data['slc'] : 0,
                'admission' => isset($data['admission']) ? $data['admission'] : 0,
                'breakage' => isset($data['breakage']) ? $data['breakage'] : 0,
                'misc' => isset($data['misc']) ? $data['misc'] : 0,
                'total' => (
                    (isset($data['tution_fee']) ? $data['tution_fee'] : 0) +
                    (isset($data['slc']) ? $data['slc'] : 0) +
                    (isset($data['admission']) ? $data['admission'] : 0) +
                    (isset($data['breakage']) ? $data['breakage'] : 0) +
                    (isset($data['misc']) ? $data['misc'] : 0)
                ),
                'idf' => isset($data['idf']) ? $data['idf'] : 0,
                'csf' => isset($data['csf']) ? $data['csf'] : 0,
                'rdfcdf' => isset($data['rdfcdf']) ? $data['rdfcdf'] : 0,
                'security_fund' => isset($data['security_fund']) ? $data['security_fund'] : 0,
                'it' => isset($data['it']) ? $data['it'] : 0,
                'slc' => 0,
                'total_fee' => (
                    (isset($data['exams']) ? $data['exams'] : 0) +
                    (isset($data['tution_fee']) ? $data['tution_fee'] : 0) +
                    (isset($data['slc']) ? $data['slc'] : 0) +
                    (isset($data['misc']) ? $data['misc'] : 0) +
                    (isset($data['admission']) ? $data['admission'] : 0) +
                    (isset($data['breakage']) ? $data['breakage'] : 0) +
                    (isset($data['idf']) ? $data['idf'] : 0) +
                    (isset($data['csf']) ? $data['csf'] : 0) +
                    (isset($data['rdfcdf']) ? $data['rdfcdf'] : 0) +
                    (isset($data['security_fund']) ? $data['security_fund'] : 0) +
                    (isset($data['it']) ? $data['it'] : 0)
                ),
                'debit' => (
                    (isset($data['exams']) ? $data['exams'] : 0) +
                    (isset($data['tution_fee']) ? $data['tution_fee'] : 0) +
                    (isset($data['slc']) ? $data['slc'] : 0) +
                    (isset($data['misc']) ? $data['misc'] : 0) +
                    (isset($data['admission']) ? $data['admission'] : 0) +
                    (isset($data['breakage']) ? $data['breakage'] : 0) +
                    (isset($data['idf']) ? $data['idf'] : 0) +
                    (isset($data['csf']) ? $data['csf'] : 0) +
                    (isset($data['rdfcdf']) ? $data['rdfcdf'] : 0) +
                    (isset($data['security_fund']) ? $data['security_fund'] : 0) +
                    (isset($data['it']) ? $data['it'] : 0)
                ),
                'type' => 'd',
                'updated_at' => now(),
            ]);

        //return redirect('viewChallan')->with('message', 'Challans Edited Successfully.');
        return redirect()->route('viewChallan', ['id' => $data['challanID']])->with('message', 'Challans Edited Successfully.');
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Challans  $challans
     * @return \Illuminate\Http\Response
     */
    public function show(Challans $challans)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Challans  $challans
     * @return \Illuminate\Http\Response
     */
    public function edit(Challans $challans)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Challans  $challans
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Challans $challans)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Challans  $challans
     * @return \Illuminate\Http\Response
     */
    public function destroy(Challans $challans)
    {
        //
    }

    public function deleteChallanByChallanID($challanID)
    {

        $existingChallans = DB::table('challans')->where('id', '=', $challanID)
            ->where('status', 'un-paid')
            ->where('paid', 'NO')
            ->delete();
        return redirect()->back()->with('message', '<strong> Challan Deleted Successfully. </strong>');
    }

    private function numberToWords($number)
    {
        $number = (float) $number;
        $whole = floor($number);
        $fraction = round(($number - $whole) * 100);

        $words = $this->convertAmountToWords($whole);
        $result = $words . ' RUPEES';

        if ($fraction > 0) {
            $result .= ' AND ' . $this->convertAmountToWords($fraction) . ' PAISA';
        }

        return strtoupper($result);
    }

    private function convertAmountToWords($number)
    {
        $hyphen      = '-';
        $conjunction = ' AND ';
        $separator   = ', ';
        $negative    = 'NEGATIVE ';
        $decimal     = ' POINT ';
        $dictionary  = array(
            0                   => 'ZERO',
            1                   => 'ONE',
            2                   => 'TWO',
            3                   => 'THREE',
            4                   => 'FOUR',
            5                   => 'FIVE',
            6                   => 'SIX',
            7                   => 'SEVEN',
            8                   => 'EIGHT',
            9                   => 'NINE',
            10                  => 'TEN',
            11                  => 'ELEVEN',
            12                  => 'TWELVE',
            13                  => 'THIRTEEN',
            14                  => 'FOURTEEN',
            15                  => 'FIFTEEN',
            16                  => 'SIXTEEN',
            17                  => 'SEVENTEEN',
            18                  => 'EIGHTEEN',
            19                  => 'NINETEEN',
            20                  => 'TWENTY',
            30                  => 'THIRTY',
            40                  => 'FORTY',
            50                  => 'FIFTY',
            60                  => 'SIXTY',
            70                  => 'SEVENTY',
            80                  => 'EIGHTY',
            90                  => 'NINETY',
            100                 => 'HUNDRED',
            1000                => 'THOUSAND',
            1000000             => 'MILLION',
            1000000000          => 'BILLION'
        );

        if (!is_numeric($number)) return false;

        $string = null;
        $fraction = null;

        if (strpos((string)$number, '.') !== false) {
            list($number, $fraction) = explode('.', (string)$number);
        }

        switch (true) {
            case $number < 21:
                $string = $dictionary[$number];
                break;
            case $number < 100:
                $tens   = ((int) ($number / 10)) * 10;
                $units  = $number % 10;
                $string = $dictionary[$tens];
                if ($units) {
                    $string .= $hyphen . $dictionary[$units];
                }
                break;
            case $number < 1000:
                $hundreds  = (int)($number / 100);
                $remainder = $number % 100;
                $string = $dictionary[$hundreds] . ' ' . $dictionary[100];
                if ($remainder) {
                    $string .= $conjunction . $this->convertAmountToWords($remainder);
                }
                break;
            default:
                $baseUnit = pow(1000, floor(log($number, 1000)));
                $numBaseUnits = (int) ($number / $baseUnit);
                $remainder = $number % $baseUnit;
                $string = $this->convertAmountToWords($numBaseUnits) . ' ' . $dictionary[$baseUnit];
                if ($remainder) {
                    $string .= $remainder < 100 ? $conjunction : $separator;
                    $string .= $this->convertAmountToWords($remainder);
                }
                break;
        }

        return $string;
    }
}
