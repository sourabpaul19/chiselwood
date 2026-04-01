<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Document;
use setasign\Fpdi\Fpdi;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    public function index() {
        $documents = Document::where('client_id', auth()->id())->get();
        return view('client.documents.index', compact('documents'));
    }

    public function show($id) {
        $document = Document::where('id',$id)->where('client_id', auth()->id())->firstOrFail();
        return view('client.documents.show', compact('document'));
    }

    // public function sign(Request $request, $id) {
    //     $document = Document::where('id',$id)->where('client_id', auth()->id())->firstOrFail();

    //     $request->validate([
    //         'signed_file'=>'required|file|mimes:pdf,doc,docx'
    //     ]);

    //     $signedPath = $request->file('signed_file')->store('signed_documents');

    //     $document->update([
    //         'is_signed'=>true,
    //         'signed_file_path'=>$signedPath
    //     ]);

    //     return redirect()->back()->with('success','Document signed successfully.');
    // }

    public function sign(Request $request, $id)
{
    $document = Document::where('id', $id)
        ->where('client_id', auth()->id())
        ->firstOrFail();

    $request->validate([
        'signature_image' => 'required|string'
    ]);

    $pdfPath = storage_path('app/public/'.$document->file_path);

    // Make sure signed_documents folder exists
    $signedDir = storage_path('app/public/signed_documents');
    if (!file_exists($signedDir)) {
        mkdir($signedDir, 0755, true); // recursive create
    }

    // Save signature as PNG
    $signature = $request->signature_image;
    $signature = str_replace('data:image/png;base64,', '', $signature);
    $signature = str_replace(' ', '+', $signature);
    $signatureFile = $signedDir.'/sign_'.$document->id.'.png';
    file_put_contents($signatureFile, base64_decode($signature));

    // Merge signature into PDF
    $signedPath = 'signed_documents/'.$document->id.'_signed.pdf';
    $signedFullPath = storage_path('app/public/'.$signedPath);

    $pdf = new \setasign\Fpdi\Fpdi();
    $pageCount = $pdf->setSourceFile($pdfPath);

    for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
        $templateId = $pdf->importPage($pageNo);
        $size = $pdf->getTemplateSize($templateId);
        $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
        $pdf->useTemplate($templateId);

        if ($pageNo == $pageCount) {
            // Add signature at bottom-right
            $pdf->Image($signatureFile, $size['width'] - 60, $size['height'] - 40, 50, 20);
        }
    }

    $pdf->Output('F', $signedFullPath);

    $document->update([
        'is_signed' => true,
        'signed_file_path' => $signedPath
    ]);

    return redirect()->back()->with('success', 'Document signed successfully.');
}
}