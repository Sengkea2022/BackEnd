<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\KhProvince;
use App\Models\KhDistrict;
use App\Models\KhCommune;
use App\Models\KhVillage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class KhLocationController extends Controller
{
    /** All provinces */
    public function provinces(): JsonResponse
    {
        return response()->json([
            'data' => KhProvince::orderBy('name')->get(['code','name','name_km','type']),
        ]);
    }

    /** Districts by province code */
    public function districts(string $provinceCode): JsonResponse
    {
        return response()->json([
            'data' => KhDistrict::where('province_code', $provinceCode)
                ->orderBy('name')
                ->get(['code','province_code','name','name_km','type']),
        ]);
    }

    /** Communes by district code */
    public function communes(string $districtCode): JsonResponse
    {
        return response()->json([
            'data' => KhCommune::where('district_code', $districtCode)
                ->orderBy('name')
                ->get(['code','district_code','name','name_km','type']),
        ]);
    }

    /** Villages by commune code */
    public function villages(string $communeCode): JsonResponse
    {
        return response()->json([
            'data' => KhVillage::where('commune_code', $communeCode)
                ->orderBy('name')
                ->get(['code','commune_code','name','name_km']),
        ]);
    }

    /**
     * Reverse-lookup: search villages (or communes) by name and return
     * the full hierarchy: village → commune → district → province
     *
     * GET /api/kh-location/search?q=Ang+Kra+Sang&type=village
     * GET /api/kh-location/search?q=Prey+Lvea&type=commune
     */
    public function search(Request $request): JsonResponse
    {
        $q    = trim($request->input('q', ''));
        $type = $request->input('type', 'village'); // village | commune | district

        if (strlen($q) < 2) {
            return response()->json(['data' => []]);
        }

        if ($type === 'village') {
            $results = KhVillage::where('name', 'like', "%{$q}%")
                ->orWhere('name_km', 'like', "%{$q}%")
                ->with([
                    'commune.district.province',
                ])
                ->limit(15)
                ->get()
                ->map(fn ($v) => [
                    'village'  => $v->name,
                    'commune'  => $v->commune?->name,
                    'district' => $v->commune?->district?->name,
                    'province' => $v->commune?->district?->province?->name,
                    // codes (for cascaded dropdown state)
                    'commune_code'  => $v->commune?->code,
                    'district_code' => $v->commune?->district?->code,
                    'province_code' => $v->commune?->district?->province?->code,
                ]);
        } elseif ($type === 'commune') {
            $results = KhCommune::where('name', 'like', "%{$q}%")
                ->orWhere('name_km', 'like', "%{$q}%")
                ->with(['district.province'])
                ->limit(15)
                ->get()
                ->map(fn ($c) => [
                    'village'  => null,
                    'commune'  => $c->name,
                    'district' => $c->district?->name,
                    'province' => $c->district?->province?->name,
                    'commune_code'  => $c->code,
                    'district_code' => $c->district?->code,
                    'province_code' => $c->district?->province?->code,
                ]);
        } else {
            $results = KhDistrict::where('name', 'like', "%{$q}%")
                ->orWhere('name_km', 'like', "%{$q}%")
                ->with(['province'])
                ->limit(15)
                ->get()
                ->map(fn ($d) => [
                    'village'  => null,
                    'commune'  => null,
                    'district' => $d->name,
                    'province' => $d->province?->name,
                    'commune_code'  => null,
                    'district_code' => $d->code,
                    'province_code' => $d->province?->code,
                ]);
        }

        return response()->json(['data' => $results]);
    }
}
