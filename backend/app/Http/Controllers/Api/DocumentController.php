<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDocumentRequest;
use App\Http\Requests\UpdateDocumentRequest;
use App\Http\Resources\DocumentResource;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Document::with('verifiedBy');

        if ($request->has('documentable_type') && $request->has('documentable_id')) {
            $query->where('documentable_type', $request->documentable_type)
                  ->where('documentable_id', $request->documentable_id);
        }

        if ($request->has('document_type')) {
            $query->where('document_type', $request->document_type);
        }

        $documents = $query->paginate(15);
        return DocumentResource::collection($documents);
    }

    public function store(StoreDocumentRequest $request)
    {
        if (!Gate::allows('create', Document::class)) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $file = $request->file('file');
        $path = $file->store('documents/' . date('Y/m'), 'public');

        $document = Document::create([
            'documentable_type' => $request->documentable_type,
            'documentable_id' => $request->documentable_id,
            'document_type' => $request->document_type,
            'title' => $request->title,
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'valid_from' => $request->valid_from,
            'valid_until' => $request->valid_until,
            'notes' => $request->notes,
        ]);

        $document->refresh();
        return new DocumentResource($document);
    }

    public function show(Document $document)
    {
        if (!Gate::allows('view', $document)) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $document->load('verifiedBy');
        return new DocumentResource($document);
    }

    public function update(UpdateDocumentRequest $request, Document $document)
    {
        if (!Gate::allows('update', $document)) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $document->update($request->validated());
        $document->load('verifiedBy');

        return new DocumentResource($document);
    }

    public function destroy(Document $document)
    {
        if (!Gate::allows('delete', $document)) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        Storage::disk('public')->delete($document->file_path);
        $document->delete();

        return response()->json(null, 204);
    }

    public function verify(Request $request, Document $document)
    {
        if (!Gate::allows('verify', $document)) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $document->update([
            'is_verified' => true,
            'verified_at' => now(),
            'verified_by_user_id' => $request->user()->id,
        ]);

        return new DocumentResource($document);
    }

    public function download(Document $document)
    {
        if (!Gate::allows('view', $document)) {
            return response()->json(['message' => 'No autorizado'], 403);
        }
        /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
        $disk = Storage::disk('public');
        return $disk->download($document->file_path, $document->file_name);
    }
}
