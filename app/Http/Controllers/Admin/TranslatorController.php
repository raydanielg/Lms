<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mixins\Lang\TranslateService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TranslatorController extends Controller
{

    public function index(Request $request)
    {
        $this->authorize("admin_translator");

        $dir = base_path('lang/en');
        $langFiles = $this->getLangFolderFilesList($dir);

        $data = [
            'pageTitle' => trans('update.translator'),
            'langFiles' => $langFiles
        ];

        return view('admin.translator.index', $data);
    }

    public function translate(Request $request)
    {
        $this->authorize("admin_translator");
        $data = $request->all();

        $validator = Validator::make($data, [
            'language' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code' => 422,
                'errors' => $validator->errors(),
            ], 422);
        }

        $language = mb_strtolower($data['language']);
        $error = null;

        $specificFilesPath = [];
        if (!empty($data['specific_file']) and !empty($data['lang_files']) and is_array($data['lang_files'])) {
            $specificFilesPath = $this->getSelectedFilesPath($data['lang_files']);
        }

        try {
            $translateService = (new TranslateService());

            if (count($specificFilesPath)) {
                foreach ($specificFilesPath as $filePath) {
                    $translateService->to($language)->from($filePath, false, 'en')->translate();
                }
            } else {
                $translateService->to($language)->from('en')->translate();
            }
        } catch (\Exception $exception) {
            $error = "Error: " . $exception->getMessage();
        }

        return response()->json([
            'code' => 200,
            'error' => $error,
            'msg' => ' - Finished translation! (go to lang/' . $language . ' folder) ',
        ]);
    }

    private function getSelectedFilesPath($items, $folder = null)
    {
        $filesPath = [];

        foreach ($items as $key => $item) {
            if (is_array($item)) {
                $folder .= "/$key";

                $filesPathTmp = $this->getSelectedFilesPath($item, $folder);

                $filesPath = array_merge($filesPath, $filesPathTmp);
            } else {
                $filesPath[] = 'en' . ($folder ? "{$folder}/" : '/') . "{$item}.php";
            }
        }

        return $filesPath;
    }

    private function getLangFolderFilesList($dir)
    {
        $result = [];

        if (is_dir($dir)) {
            // Open the directory
            if ($dh = opendir($dir)) {
                // Read files and directories inside the directory
                while (($file = readdir($dh)) !== false) {
                    // Skip '.' and '..'
                    if ($file != "." && $file != "..") {
                        // Check if it's a directory
                        if (is_dir($dir . DIRECTORY_SEPARATOR . $file)) {
                            $result[$file] = $this->getLangFolderFilesList($dir . DIRECTORY_SEPARATOR . $file);
                        } else {
                            // It's a file
                            $result[] = pathinfo($file, PATHINFO_FILENAME);
                        }
                    }
                }

                // Close the directory
                closedir($dh);
            }
        }

        return $result;
    }

}
