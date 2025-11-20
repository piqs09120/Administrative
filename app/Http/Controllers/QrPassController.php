<?php

namespace App\Http\Controllers;

use App\Services\QrPassService;
use App\Models\VisitorQrPass;
use Illuminate\Http\Request;

class QrPassController extends Controller
{
    protected $qrPassService;

    public function __construct(QrPassService $qrPassService)
    {
        $this->qrPassService = $qrPassService;
    }

    /**
     * QR Pass scanning page
     */
    public function scanPage()
    {
        return view('admin.qr_pass.scan');
    }

    /**
     * Validate QR Pass
     */
    public function validate(Request $request)
    {
        $request->validate([
            'pass_code' => 'required|string',
        ]);

        $result = $this->qrPassService->validatePass($request->pass_code);

        return response()->json($result);
    }

    /**
     * Scan QR Pass at entry
     */
    public function scanEntry(Request $request)
    {
        $request->validate([
            'pass_code' => 'required|string',
            'scanned_by' => 'nullable|string',
            'location' => 'nullable|string',
        ]);

        $result = $this->qrPassService->scanPass(
            $request->pass_code,
            $request->scanned_by ?? auth()->user()->name ?? 'Security',
            $request->location ?? 'Main Entrance'
        );

        return response()->json($result);
    }

    /**
     * Rescan physical ID at arrival
     */
    public function rescanPhysicalId(Request $request)
    {
        $request->validate([
            'pass_code' => 'required|string',
            'physical_id_data' => 'required|string',
        ]);

        $qrPass = VisitorQrPass::where('pass_code', $request->pass_code)->firstOrFail();

        $result = $this->qrPassService->rescanPhysicalId($qrPass, $request->physical_id_data);

        return response()->json($result);
    }
}



