<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDocumentRequest;
use App\Http\Requests\UpdateDocumentRequest;
use App\Http\Resources\DocumentResource;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Document::class);

        $user = $request->user();
        $query = Document::with('verifiedBy');

        // Filtrar por rol
        if ($user->role === 'guest') {
            $query->where('documentable_type', 'App\Models\Guest')
                  ->where('documentable_id', $user->guest?->id);
        } elseif ($user->role === 'owner') {
            $query->where(function($q) use ($user) {
                $q->where('documentable_type', 'App\Models\Owner')
                  ->whereHas('documentable', fn($q) => $q->where('email', $user->email));
            });
        }

        if ($request->has('documentable_type') && $request->has('documentable_id')) {
            $query->where('documentable_type', $request->documentable_type)
                  ->where('documentable_id', $request->documentable_id);
        }

        if ($request->has('document_type')) {
            $query->where('document_type', $request->document_type);
        }

        return DocumentResource::collection($query->paginate(15));
    }

    public function store(StoreDocumentRequest $request)
    {
        $this->authorize('create', Document::class);

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

        return new DocumentResource($document->refresh());
    }

    public function show(Document $document)
    {
        $this->authorize('view', $document);
        $document->load('verifiedBy');
        return new DocumentResource($document);
    }

    public function update(UpdateDocumentRequest $request, Document $document)
    {
        $this->authorize('update', $document);
        $document->update($request->validated());
        $document->load('verifiedBy');
        return new DocumentResource($document);
    }

    public function destroy(Document $document)
    {
        $this->authorize('delete', $document);
        Storage::disk('public')->delete($document->file_path);
        $document->delete();
        return response()->json(null, 204);
    }

    public function verify(Request $request, Document $document)
    {
        $this->authorize('verify', $document);
        $document->markAsVerified($request->user()->id);
        return new DocumentResource($document->fresh());
    }

    public function download(Document $document)
    {
        $this->authorize('view', $document);
        /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
        $disk = Storage::disk('public');
        return $disk->download($document->file_path, $document->file_name);
    }
}