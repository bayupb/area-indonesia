<?php

namespace App\Repositories\indonesia;

use Illuminate\Support\Str;
use App\Helpers\ResponsesHelpers;
use App\Models\indonesia\KepulauanModel;

class KepulauanRepository
{
    public function aksiGetAll()
    {
        try {
            $data = KepulauanModel::query()
                ->with('provinsi')
                ->selectRaw('*, ROW_NUMBER() over(ORDER BY cuti_id) nomor')
                ->get();
            return ResponsesHelpers::getResponseSucces(200, $data);
        } catch (\Exception $e) {
            return ResponsesHelpers::getResponseError(500, $e->getMessage());
        }
    }

    public function aksiGetPostData($params)
    {
        try {
            $nama = isset($params['nama']) ? $params['nama'] : '';

            if (strlen($nama) == 0) {
                return ResponsesHelpers::getResponseError(
                    500,
                    'nama tidak boleh kosong'
                );
            }
            $kepulauanId = isset($params['kepulauan_id'])
                ? $params['kepulauan_id']
                : '';
            if (strlen($kepulauanId) == 0) {
                $data = new KepulauanModel();
            } else {
                $data = KepulauanModel::find($kepulauanId);
                if (!$data) {
                    return ResponsesHelpers::getResponseError(
                        500,
                        'Kepulauan tidak ditemukan'
                    );
                }
            }
            $data->nama = Str::ucfirst($nama);
            $data->slug = Str::slug($nama);
            $data->save();

            return ResponsesHelpers::getResponseSucces(200, $data);
        } catch (\Exception $e) {
            return ResponsesHelpers::getResponseError(500, $e->getMessage());
        }
    }

    public function aksiGetSearch($params)
    {
        try {
            $data = KepulauanModel::query();
            $cari = isset($params['cari']) ? $params['cari'] : '';
            if (strlen($cari) > 0) {
                $data->where(function ($query) use ($cari) {
                    $query->whereRaw(
                        "lower(slug) LIKE '%" . strtolower($cari) . "%'"
                    );
                });
            }
            $data = $data->with('provinsi')->get();
            return ResponsesHelpers::getResponseSucces(200, $data);
        } catch (\Exception $e) {
            return ResponsesHelpers::getResponseError(500, $e->getMessage());
        }
    }
}
