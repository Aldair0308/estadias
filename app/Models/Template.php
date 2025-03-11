<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Template extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'original_name',
        'path',
        'mime_type',
        'size',
        'version',
        'parent_id',
        'description',
        'content_hash',
        'observations'
    ];

    /**
     * Get the parent template that this version belongs to
     */
    public function parent()
    {
        return $this->belongsTo(Template::class, 'parent_id');
    }

    /**
     * Get all versions of this template
     */
    public function versions()
    {
        return $this->hasMany(Template::class, 'parent_id');
    }

    /**
     * Check if the template is a PDF
     */
    public function isPdf()
    {
        return $this->mime_type === 'application/pdf';
    }

    /**
     * Check if content matches any previous version
     *
     * @param string $content_hash
     * @return array|null Returns matching version info or null if no match found
     */
    public function findMatchingVersion(string $content_hash)
    {
        $matching_version = $this->versions()->where('content_hash', $content_hash)->first();
        
        if ($matching_version) {
            return [
                'version' => $matching_version->version,
                'date' => $matching_version->created_at->format('Y-m-d'),
                'id' => $matching_version->id
            ];
        }

        return null;
    }

    /**
     * Check if the template is an Excel file
     */
    public function isExcel()
    {
        return in_array($this->mime_type, [
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        ]);
    }

    /**
     * Check if the template is a Word document
     */
    public function isWord()
    {
        return in_array($this->mime_type, [
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
        ]);
    }

    /**
     * Convert Word document to HTML for preview
     */
    public function getHtmlContent()
    {
        if (!$this->isWord()) {
            return null;
        }

        try {
            $filePath = Storage::path('public/' . $this->path);
            return \App\View\Components\EditorWord::convertWordToHtml($filePath);
        } catch (\Exception $e) {
            \Log::error('Word preview error: ' . $e->getMessage());
            return null;
        }
    }
}