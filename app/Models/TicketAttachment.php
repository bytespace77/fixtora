<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class TicketAttachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_id',
        'comment_id',
        'user_id',
        'original_name',
        'stored_path',
        'mime_type',
        'size',
    ];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    public function comment()
    {
        return $this->belongsTo(TicketComment::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Human-readable file size e.g. "1.4 MB"
    public function getFormattedSizeAttribute(): string
    {
        $bytes = $this->size;
        if ($bytes < 1024)    return $bytes . ' B';
        if ($bytes < 1048576) return round($bytes / 1024, 1) . ' KB';
        return round($bytes / 1048576, 1) . ' MB';
    }

    // Public URL e.g. /storage/ticket-attachments/5/abc.png
    public function getUrlAttribute(): string
    {
        return Storage::disk('public')->url($this->stored_path);
    }
}