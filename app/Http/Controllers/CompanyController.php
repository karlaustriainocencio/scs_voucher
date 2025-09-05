<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CompanyController extends Controller
{
    public function switch(Request $request)
    {
        $company = $request->input('company');
        
        if (in_array($company, ['CIS', 'SCS'])) {
            session(['selected_company' => $company]);
            
            return redirect()->back()->with('success', "Switched to {$company}");
        }
        
        return redirect()->back()->with('error', 'Invalid company selected');
    }
    
    public function getCurrentCompany()
    {
        return response()->json([
            'company' => session('selected_company', 'SCS')
        ]);
    }
}
