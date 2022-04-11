<?php

namespace App\Http\Controllers;

use App\Http\Resources\UrlsResource;
use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class FilesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $files = File::all();

        return response()->json($files);
    }

    public function getAllUrls()
    {
      $files = File::all();

      $filtered = $files->map(function ($file) {
          return new UrlsResource($file);
      });

      return response()->json($filtered);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function upload(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|mimes:doc,docx,pdf,txt,csv|max:2048',
        ]);

        if ($validator->fails()) {

            return response()->json(['error' => $validator->errors()], 400);
        }


        if ($file = $request->file('file')) {
            $path = $file->store('public/files');

            //store your file into directory and db
            $save = new File();
            $save->filename = $file->getClientOriginalName();
            $save->path = $path;
            $save->url = $_ENV['APP_URL'] . $file;
            $save->save();

            return response()->json([
                "success" => true,
                "message" => "File successfully uploaded",
                "file" => $file
            ]);
        }
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $file = File::find($id);

        if (!$file) {
            return response()->json([
                "success" => false,
                "message" => "File not found"
            ]);
        }

        return response()->json($file);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|mimes:doc,docx,pdf,txt,csv|max:2048',
        ]);

        if ($validator->fails()) {

            return response()->json(['error' => $validator->errors()], 400);
        }


        if ($file = $request->file('file')) {
            $path = $file->store('public/files');

            //store your file into directory and db
            $save = File::findOrFail($id);
            $save->filename = $file->getClientOriginalName();
            $save->path = $path;
            $save->url = $_ENV['APP_URL'] . $file;
            $save->save();

            return response()->json([
                "success" => true,
                "message" => "File successfully uploaded",
                "file" => $file
            ]);
        }
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $file = File::findOrFail($id);

        Storage::delete($file->path);

        $success = $file->delete();

        if (!$success) {
            return response()->json([
                "success" => true,
                "message" => "File failed to deleted"
            ]);
        }
        return response()->json([
            "success" => true,
            "message" => "File successfully deleted"
        ]);

    }

    public function download($id)
    {
        $file = File::findOrFail($id);

        return Storage::download($file->path);
    }
}
