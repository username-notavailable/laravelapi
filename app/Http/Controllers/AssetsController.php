<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AssetsController extends Controller
{
    public function getPubItem(Request $request) : mixed
    {
        $cacheControl = [
            'max_age' => 3600,
            's_maxage' => 3600,
            'stale_while_revalidate' => 300,
            'stale_if_error' => 86400,
            'public' => true
        ];

        return $this->getItem($request, 'pub', true, $cacheControl);
    }

    public function getPvtItem(Request $request) : mixed
    {
        $cacheControl = [
            'max_age' => 3600,
            's_maxage' => 3600,
            'stale_while_revalidate' => 300,
            'stale_if_error' => 86400,
            'public' => true
        ];

        return $this->getItem($request, 'pvt', true, $cacheControl);
    }

    public function getPubImage(Request $request) : mixed
    {
        $cacheControl = [
            'max_age' => 3600,
            's_maxage' => 3600,
            'stale_while_revalidate' => 300,
            'stale_if_error' => 86400,
            'public' => true
        ];

        return $this->getItem($request, 'pub/imgs', true, $cacheControl);
    }

    protected function getItem(Request $request, string $subDir, bool $serveResizedAssets, array $cacheControl) : mixed
    {
        $validator = Validator::make($request->all(), [
            'e' => 'required|string|regex:/^[a-z0-9A-Z\_]{3,70}$/',
            'f' => 'required|string|regex:/^[a-z0-9A-Z\-_.]{3,70}$/',
            's' => 'required|string|regex:/^[a-z]{2,3}$/'
        ]);

        if ($validator->fails()) {
            return response('', 422);
        }
        else {
            $requestedFile = storage_path('assets/' . $subDir . '/' . $request->e . 's/' . $request->f);

            if ($serveResizedAssets && file_exists($requestedFile . '___' . $request->s)) {
                $requestedFile .= '___' . $request->s;
            }
    
            if (file_exists($requestedFile)) {
                $fileEtag = md5_file($requestedFile);
                $cacheControl = [
                    'etag' => $fileEtag
                ];
                
                if ($request->hasHeader('if-none-match')) {
                    $requestEtag = str_replace('"', '', $request->header('if-none-match'));
    
                    if ($requestEtag === $fileEtag) {
                        return response('', 304)->setCache($cacheControl)->setNotModified();
                    }
                }

                return response()->file($requestedFile)->setCache($cacheControl)->setLastModified(null);
            }
            else {
                return response('', 404);
            }
        }
    }
}
