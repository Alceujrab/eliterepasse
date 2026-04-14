<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ModulePageController extends Controller
{
    public function __invoke(Request $request, string $module): View
    {
        $modules = config('admin_panel.modules', []);
        $moduleConfig = $modules[$module] ?? null;

        abort_if($moduleConfig === null, 404);

        return view('admin.module', [
            'moduleKey' => $module,
            'module' => $moduleConfig,
        ]);
    }
}
