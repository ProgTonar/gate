<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class NewsController extends Controller
{

  public function proxyWithFiles(Request $request)
{
    $fastApiUrl = 'http://host.docker.internal:8000/api/news';
    $user = Auth()->user();

    if (!$user) {
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    $http = Http::withHeaders([
        'X-Internal-Secret' => config('app.client_secret'),
    ]);

    $postData = array_merge(
        $request->except(array_keys($request->allFiles())),
        ['user_id' => $user->id]
    );

    foreach ($request->allFiles() as $key => $file) {
        $http->attach($key, fopen($file->path(), 'r'), $file->getClientOriginalName());
    }

    try {
        $response = $http->post($fastApiUrl, $postData);

        return response()->json(
            $response->json(),
            $response->status()
        );
    } catch (\Exception $e) {
        Log::error("Proxy request failed: " . $e->getMessage());
        return response()->json(['error' => 'Service unavailable'], 503);
    }
}
}
