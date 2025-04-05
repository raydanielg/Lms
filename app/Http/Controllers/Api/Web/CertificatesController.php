<?php

namespace App\Http\Controllers\Api\Web;

use App\Http\Controllers\Controller;
use App\Models\Api\Certificate;
use Illuminate\Http\Request;

class CertificatesController extends Controller
{
    public function checkValidate(Request $request)
    {
        validateParam($request->all(), [
            'certificate_id' => 'required|numeric',
        ]);

        $certificateId = $request->input('certificate_id');

        $certificate = Certificate::where('id', $certificateId)->first();

        if (!empty($certificate)) {
            $result = [
                'student' => $certificate->student->full_name,
                'webinar_title' => $certificate->quiz->webinar->title,
                'date' => dateTimeFormat($certificate->created_at, 'j F Y'),
            ];
            return apiResponse2(1, 'retrieved', 'api.public.retrieved',
                [
                    'is_valid' => true,
                    'certificate' => $certificate->details
                ]
            );
        }
        return apiResponse2(1, 'retrieved', 'api.public.retrieved',
            [
                'is_valid' => false,
                'certificate' => null
            ]
        );

    }
}
