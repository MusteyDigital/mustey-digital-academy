<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LessonResource extends Model
{
    protected $fillable = [
        'lesson_id',
        'title',
        'file_path',
        'file_name',
        'file_type',
        'file_size',
        'download_count',
    ];

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }

    public function getHumanFileSizeAttribute(): string
    {
        if (!$this->file_size) {
            return 'Unknown size';
        }

        if ($this->file_size >= 1048576) {
            return number_format($this->file_size / 1048576, 2) . ' MB';
        }

        return number_format($this->file_size / 1024, 1) . ' KB';
    }

    public function getSimpleTypeAttribute(): string
    {
        $name = strtolower($this->file_name ?? '');
        $type = strtolower($this->file_type ?? '');

        if (str_ends_with($name, '.pdf') || str_contains($type, 'pdf')) {
            return 'PDF';
        }

        if (
            str_ends_with($name, '.xlsx') ||
            str_ends_with($name, '.xls') ||
            str_contains($type, 'spreadsheet') ||
            str_contains($type, 'excel')
        ) {
            return 'Excel';
        }

        if (
            str_ends_with($name, '.doc') ||
            str_ends_with($name, '.docx') ||
            str_contains($type, 'word')
        ) {
            return 'Word';
        }

        if (
            str_ends_with($name, '.ppt') ||
            str_ends_with($name, '.pptx') ||
            str_contains($type, 'presentation') ||
            str_contains($type, 'powerpoint')
        ) {
            return 'Slides';
        }

        if (
            str_ends_with($name, '.zip') ||
            str_ends_with($name, '.rar') ||
            str_contains($type, 'zip') ||
            str_contains($type, 'compressed')
        ) {
            return 'ZIP';
        }

        if (str_starts_with($type, 'image/')) {
            return 'Image';
        }

        return 'File';
    }

    public function getIsPdfAttribute(): bool
    {
        $name = strtolower($this->file_name ?? '');
        $type = strtolower($this->file_type ?? '');

        return str_ends_with($name, '.pdf') || str_contains($type, 'pdf');
    }
}
