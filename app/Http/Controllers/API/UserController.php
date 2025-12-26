<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Filters\QueryFilter;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        $filters = new QueryFilter($query, $request);
        $users = $filters->apply()->paginate(
            $request->get('limit', 10)
        );

        return response()->json([
            'status' => 'success',
            'current_page' => $users->currentPage(),
            'total_pages' => $users->lastPage(),
            'total_records' => $users->total(),
            'data' => $users->items(),
        ]);
    }
}
