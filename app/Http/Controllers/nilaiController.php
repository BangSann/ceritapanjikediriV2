<?php

namespace App\Http\Controllers;

use App\Models\artikelModel;
use App\Models\nilaiModel;
use Illuminate\Http\Request;

class nilaiController extends Controller
{
    //
    public function getNilai(Request $request)
    {
        try {
            // Ambil berdasarkan ID Nilai
            if ($request->id_nilai) {
                $nilai = nilaiModel::with(['artikel', 'user'])->find($request->id_nilai);
                if (!$nilai) {
                    return response()->json([
                        'message' => 'Nilai not found',
                    ], 404);
                }
                return response()->json([
                    'success' => true,
                    'data' => $nilai
                ], 200);
            }

            // Ambil berdasarkan kombinasi id_artikel dan id_user
            if ($request->id_artikel && $request->id_user) {
                $nilai = nilaiModel::with(['artikel', 'user'])
                    ->where('id_artikel', $request->id_artikel)
                    ->where('id_user', $request->id_user)
                    ->first();

                if (!$nilai) {
                    return response()->json([
                        'message' => 'Nilai not found',
                    ], 404);
                }

                return response()->json([
                    'success' => true,
                    'data' => $nilai
                ], 200);
            }

            // Ambil berdasarkan ID Artikel
            if ($request->id_artikel) {
                $artikel = artikelModel::with('nilai.user')
                    ->where('id', $request->id_artikel)
                    ->first();

                if (!$artikel) {
                    return response()->json([
                        'message' => 'Artikel not found',
                    ], 404);
                }

                return response()->json([
                    'success' => true,
                    'data' => $artikel
                ], 200);
            }

            // Ambil semua nilai dengan artikel & user
            $artikel = artikelModel::with('nilai.user')->get();

            return response()->json([
                'success' => true,
                'data' => $artikel
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Internal Server Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function createNilai(Request $request)
    {
        $validated = $request->validate([
            'nilai' => 'required|numeric|min:0|max:100',
            'id_user' => 'required|exists:users,id',
            'id_artikel' => 'required|exists:artikel,id',
        ]);

        try {
            $nilai = nilaiModel::create($validated);
            return response()->json([
                'success' => true,
                'data' => $nilai,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Internal Server Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function updateNilai(Request $request, $id)
    {
        try {
            $nilai = nilaiModel::find($id);
            if (!$nilai) {
                return response()->json([
                    'message' => 'Nilai not found',
                ], 404);
            }
            $nilai->update($request->all());
            return response()->json([
                'success' => true,
                'data' => $nilai
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Internal Server Error: ' . $e->getMessage(),
            ], 500);
        }
    }
    public function deleteNilai($id)
    {
        try {
            $nilai = nilaiModel::find($id);
            if (!$nilai) {
                return response()->json([
                    'message' => 'Nilai not found',
                ], 404);
            }
            $nilai->delete();
            return response()->json([
                'success' => true,
                'message' => 'Nilai deleted successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Internal Server Error: ' . $e->getMessage(),
            ], 500);
        }
    }
}
