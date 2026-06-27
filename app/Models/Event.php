<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Event extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'about',
        'image',
        'location',
        'date',
        'time',
        'event_type',
        'presented_by',
        'hosted_by',
        'host_contact',
        'status',
        'created_by',
    ];

    protected $appends = [
        'image_url',
        'going'
    ];

    


 public function getImageUrlAttribute(): ?string
{
    return $this->image
        ? rtrim(config('app.url'), '/') . '/storage/' . ltrim($this->image, '/')
        : null;
}

    public function getGoingAttribute(): int
    {
        return $this->registrations_count
            ?? $this->registrations()->count();
    }

    public function registrations()
    {
        return $this->hasMany(EventRegistration::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

}
