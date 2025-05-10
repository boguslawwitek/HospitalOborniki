<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Employee;

class EmployeeController extends Controller
{
    public function index()
    {
        $slug = request('slug');

        $query = Employee::query();
        if ($slug) {
            $query->where('slug', $slug);
        }
        $employees = $query->orderBy('sort_order')->get();

        $mainSections = [];
        $extraSections = [];

        foreach ($employees as $employee) {
            foreach ($employee->employees as $person) {
                $targetSection = $person['data']['other-section'] ?? null;
                if ($targetSection) {
                    if (!isset($extraSections[$targetSection])) {
                        $extraSections[$targetSection] = [
                            'name' => $targetSection,
                            'slug' => $employee->slug,
                            'sort_order' => $employee->sort_order,
                            'employees' => [],
                        ];
                    }
                    $extraSections[$targetSection]['employees'][] = $person;
                }
            }
        }

        foreach ($employees as $employee) {
            $mainPersons = array_filter($employee->employees, function($person) {
                return empty($person['data']['other-section']);
            });
            if (count($mainPersons) === 0) continue;
            $sectionName = $employee->section;
            if (!isset($mainSections[$sectionName])) {
                $mainSections[$sectionName] = [
                    'name' => $sectionName,
                    'slug' => $employee->slug,
                    'sort_order' => $employee->sort_order,
                    'employees' => [],
                ];
            }
            foreach ($mainPersons as $person) {
                $mainSections[$sectionName]['employees'][] = $person;
            }
        }

        return response()->json([
            'main' => $mainSections,
            'extra' => $extraSections,
        ]);
    }
}