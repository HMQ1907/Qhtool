<?php

namespace App\Actions\Webike;

use App\Actions\HttpApiAction;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Throwable;

class GetOrderProductsAction extends HttpApiAction
{
    public function handle(string $sku): array|null
    {
        try {
            $result = [
                'suppliers' => [],
                'product' => null
            ];

            $endpoint = Config::get('api.webike.endpoints.fetch_product_order');
            $response = $this->httpRequest($endpoint, [
                'endpoint' => $endpoint,
            ])->get('{+endpoint}?sku=' . $sku);

            if ($response->collect()->isEmpty()) {
                $endpoint = Config::get('api.webike.endpoints.fetch_product');
                $response = $this->httpRequest($endpoint, [
                    'endpoint' => $endpoint,
                ])->get('{+endpoint}?sku=' . $sku);
            }

            $data = $response->collect()->first();

            if (is_null($data)) {
                return null;
            }

            $this->formatSuppliers($data['suppliers']);
            $result['suppliers'] = $data['suppliers'];
            unset($data['suppliers']);
            $result['product'] = $data;

            return $result;
        } catch (Throwable $th) {
            Log::error(__FUNCTION__, [
                'sku' => $sku,
            ]);

            throw $th;
        }
    }

    public function formatSuppliers(array &$suppliers)
    {
        foreach ($suppliers as $index => $supplier) {
            if (
                $supplier['key'] == null &&
                $supplier['channel'] == 'RCJAutoOrderAPI'
            ) {
                $suppliers[$index]['key'] = '0000';
                $suppliers[$index]['name'] = 'RiverCrane';
            }

            if ($suppliers[$index]['key'] == null) {
                return false;
            }
        }
    }
}
