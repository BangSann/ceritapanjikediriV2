<?php

namespace App\Http\Controllers;

use App\Models\newsModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class newsController extends Controller
{
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'judul' => 'required|string',
                'gambar' => 'required|image|mimes:jpeg,png,jpg,gif',
                'description' => 'required|string',
            ]);

            if($validator->fails()){
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $gambar = $request->file('gambar'); 
            $destinationPath = 'news/';
            $profileImage = date('YmdHis') . "." . $gambar->getClientOriginalExtension();
            $gambar->move($destinationPath, $profileImage);
            
            newsModel::create([
                'judul' => $request->judul,
                'gambar' => $profileImage,
                'description' => $request->description,
            ]);

            return response()->json(["message" => "berhasil"], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Internal Server Error: ' . $e->getMessage(),
            ], 500);
        }    
    }

    public function index(Request $request)
    {
        try {
            if($request->id){
                $data = newsModel::find($request->id);
                return response()->json($data, 200);
            }
            $data = newsModel::all();
            return response()->json($data, 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Internal Server Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request)
    {
        $data = newsModel::find($request->id);
        if(!$data){
            return response()->json(["message" => "news tidak tersedia!"], 404);
        }

        try{
            $validator = Validator::make($request->all(), [
                'judul' => 'required|string',
                'description' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => $validator->errors()
                ], 422);
            }

            $data->judul = $request->judul;
            $data->description = $request->description;

            if($request->file("gambar"))
            {
                if (File::exists("news/" . $data['gambar'])) {
                    File::delete("news/" . $data['gambar']);
                }

                $gambar = $request->file('gambar'); 
                $destinationPath = 'news/';
                $filename = date('YmdHis') . "." . $gambar->getClientOriginalExtension();
                $gambar->move($destinationPath, $filename);

                $data->gambar = $filename;
            }

            $data->save();
            return response()->json(["message" => "news berhasil diperbarui!"]);

        }catch(\Exception $e){
            return response()->json([
                'message' => 'Internal Server Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function destroy(Request $request)
    {
        try {
            $data = newsModel::find($request->id);
            if (File::exists("news/" . $data['gambar'])) {
                File::delete("news/" . $data['gambar']);
            }
            $data->delete();
            return response()->json(["message" => "berhasil"], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Internal Server Error: ' . $e->getMessage(),
            ], 500);
        }    
    }
}

