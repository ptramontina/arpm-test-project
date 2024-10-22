<?php
$employees = collect([
    ['name' => 'John', 'city' => 'Dallas'],
    ['name' => 'Jane', 'city' => 'Austin'],
    ['name' => 'Jake', 'city' => 'Dallas'],
    ['name' => 'Jill', 'city' => 'Dallas'],
]);

$offices = collect([
    ['office' => 'Dallas HQ', 'city' => 'Dallas'],
    ['office' => 'Dallas South', 'city' => 'Dallas'],
    ['office' => 'Austin Branch', 'city' => 'Austin'],
]);

return $offices->mapToGroups(function (array $item, int $key) {
    /**
     * This first call maps the offices to groups
     * 
     * "Dallas": [
     *   "Dallas HQ",
     *   "Dallas South"
     *  ],
     *   "Austin": [
     *   "Austin Branch"
     *  ]
     */
    return [$item['city'] => $item['office']];
})->map(function ($offices, $city) use ($employees) {  
    /**
     * This second map looks for each employee and add to relevant offices
     */      
    $array = [];
    $offices->each(function ($office) use ($employees, &$array, $city) {
        $officeEmployees = $employees->reduce(function ($acc, $employee) use ($office, $city) {
            if ($employee['city'] === $city) {
                array_push($acc, $employee['name']);
            }
            return $acc;
        }, []);

        $array[$office] = $officeEmployees;
    });
    return $array;
});
