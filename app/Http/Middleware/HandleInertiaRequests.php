<?php

namespace App\Http\Middleware;

use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */

    public function share(Request $request): array
    {
        $suppliersName = Supplier::select('code', 'data->name as name')
            ->orderBy('code')->get()->map(function ($supplier) {
                return [
                    'code' => $supplier->code,
                    'name' => "{$supplier->code} : {$supplier->name}",
                ];
            });

        return array_merge(parent::share($request), [
            '' => config('microzero.site_urls.zms'),
            'pms_url' => config('app.url'),
            'user' =>  Auth::user(),
            'menu' => Config::get('menu'),
            'flash' => [
                'message' => fn() => $request->session()->get('message'),
                'error' => fn() => $request->session()->get('error'),
            ],
            'zero_url' => config('microzero.site_urls.zero'),
            'suppliersName' => $suppliersName,
            'apps' => [
                [
                    'title' => 'ZMS',
                    'image' => '/images/svg/zms.svg',
                    'url' => Config::get('microzero.site_urls.zms', ''),
                ],
                [
                    'title' => 'PMS',
                    'image' => '/images/svg/pms.svg',
                    'url' => Config::get('app.url', ''),
                    'active' => true,
                ],
                [
                    'title' => 'WMS',
                    'image' => '/images/svg/wms.svg',
                    'url' => Config::get('microzero.site_urls.wms', ''),
                ],
                [
                    'title' => 'CRM',
                    'image' => '/images/icons/crm.png',
                    'url' => Config::get('microzero.site_urls.crm', ''),
                ],
            ],
            'countries' => getCountries(),
            'locations' => getLocations(),
        ]);
    }
}
