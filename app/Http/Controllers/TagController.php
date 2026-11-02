<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TagController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = trim((string) $request->query('q', ''));

        if ($query === '') {
            return response()->json([]);
        }

        $escaped = addcslashes(mb_strtolower($query), '%_\\');

        return response()->json(
            Tag::where('name', 'like', "%{$escaped}%")
                ->orderByRaw('name like ? desc', ["{$escaped}%"])
                ->orderBy('name')
                ->limit(10)
                ->pluck('name')
        );
    }
}
