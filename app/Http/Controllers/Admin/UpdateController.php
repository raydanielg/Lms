<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class UpdateController extends Controller
{
    public function index()
    {
        $this->authorize("admin_settings_update_app");

        $data = [
            'pageTitle' => trans('update.update_app')
        ];

        return view('admin.settings.update_app.index', $data);
    }

    public function basicUpdate(Request $request)
    {
        $this->authorize("admin_settings_update_app");

        $data = $request->all();

        $validator = Validator::make($data, [
            'file' => 'required|mimes:zip',
            'basic_update_confirm' => 'required'
        ]);

        if ($validator->fails()) {
            return response([
                'code' => 422,
                'errors' => $validator->errors(),
            ], 422);
        }

        $file = $request->file('file');
        $zip = new \ZipArchive();
        $zip->open($file);
        $zip->extractTo(base_path());
        $zip->close();

        $this->handleClearCache();

        return response()->json([
            'code' => 200,
            'msg' => trans('update.app_updated_successful')
        ]);
    }

    public function customUpdate(Request $request)
    {
        $this->authorize("admin_settings_update_app");

        $data = $request->all();

        $validator = Validator::make($data, [
            'file' => 'required|mimes:zip',
            'custom_update_confirm' => 'required'
        ]);

        if ($validator->fails()) {
            return response([
                'code' => 422,
                'errors' => $validator->errors(),
            ], 422);
        }

        $path = base_path("/update_tmp");

        $file = $request->file('file');
        $zip = new \ZipArchive();
        $zip->open($file);
        $zip->extractTo($path);
        $zip->close();

        $json = json_decode(file_get_contents("$path/config.json"), true);

        if (!empty($json['directory']) and !empty($json['directory'][0]['name'])) {
            foreach ($json['directory'][0]['name'] as $directory) {
                if (!is_dir(base_path($directory))) {
                    mkdir(base_path($directory), 0777, true);
                }
            }
        }

        if (!empty($json['files'])) {
            foreach ($json['files'] as $file) {
                copy("$path/{$file['root_directory']}", base_path($file['update_directory']));
            }
        }

        // remove tmp dir
        File::deleteDirectory($path);

        $this->handleClearCache();

        return response()->json([
            'code' => 200,
            'msg' => trans('update.app_updated_successful_json')
        ]);
    }

    public function databaseUpdate(Request $request)
    {
        $this->authorize("admin_settings_update_app");

        $data = $request->all();

        $validator = Validator::make($data, [
            'database_update_confirm' => 'required'
        ]);

        if ($validator->fails()) {
            return response([
                'code' => 422,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            Artisan::call('migrate', [
                '--force' => true
            ]);
            $msg1 = Artisan::output();
        } catch (\Exception $exception) {
            $msg1 = "Migration Error: " . $exception->getMessage();
        }

        try {
            Artisan::call('db:seed', [
                '--force' => true
            ]);
            $msg2 = Artisan::output();
        } catch (\Exception $exception) {
            $msg2 = "Section Error: " . $exception->getMessage();
        }

        $this->handleClearCache();

        $html = "<div class='mb-3'><h4 class='font-16'>Migrations :</h4> <p class='mt-1 font-14 text-muted'>$msg1</p></div>";
        $html .= "<div class='mb-3'><h4 class='font-16'>Sections :</h4> <p class='mt-1 font-14 text-muted'>$msg2</p></div>";

        return response()->json([
            'code' => 200,
            'message' => $html
        ]);
    }

    private function handleClearCache()
    {
        Artisan::call('clear:all', [
            '--force' => true
        ]);
    }
}
