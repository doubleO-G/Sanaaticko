<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Event extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'user_id',
        'type',
        'address',
        'category_id',
        'start_time',
        'end_time',
        'image',
        'gallery',
        'people',
        'lat',
        'lang',
        'description',
        'security',
        'status',
        'event_status',
        'is_deleted',
        'scanner_id',
        'tags',
        'url',
    ];

    protected $table = 'events';
    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];
    protected $appends = ['imagePath', 'rate', 'totalTickets', 'soldTickets'];

    public function category()
    {
        return $this->hasOne('App\Models\Category', 'id', 'category_id');
    }

    public function organization()
    {
        return $this->hasOne('App\Models\User', 'id', 'user_id');
    }
    public function ticket()
    {
        return $this->hasMany('App\Models\Ticket', 'event_id', 'id');
    }

    public function faqs()
    {
        return $this->hasMany('App\Models\EventFaq', 'event_id', 'id');
    }

    public function getImagePathAttribute()
    {
        return url('images/upload') . '/';
    }

    /**
     * Get total of Tickets Count
     * @return int
     */
    public function getTotalTicketsAttribute()
    {
        $timezone = Setting::find(1)->timezone;
        $date = Carbon::now($timezone);
        return intval(Ticket::where([['event_id', $this->attributes['id']], ['is_deleted', 0], ['status', 1]])->sum('quantity'));
    }

    /**
     * Get total of Sold Tickets Count
     * @return int
     */
    public function getSoldTicketsAttribute()
    {
        // (new AppHelper)->eventStatusChange();
        return  intval(Order::where('event_id', $this->attributes['id'])->sum('quantity'));
        // return  Order::where('event_id', $this->attributes['id'])->sum('quantity');
    }

    public function getRateAttribute()
    {
        $review =  Review::where('event_id', $this->attributes['id'])->get(['rate']);
        if (count($review) > 0) {
            $totalRate = 0;
            foreach ($review as $r) {
                $totalRate = $totalRate + $r->rate;
            }
            return  round($totalRate / count($review));
        } else {
            return 0;
        }
    }

    public function scopeDurationData($query, $start, $end)
    {
        $data =  $query->whereBetween('start_time', [$start,  $end]);
        return $data;
    }

    /**
     * Get Available Tickets Count, based on ordered tickets
     * @return int
     */
    public function getAvailableTicketsAttribute()
    {
        return $this->totalTickets - $this->soldTickets;
    }

    /**
     * Get Auto Generated Tag.
     * Tags: Sold Out, Almost Full, Sales ending soon
     * Intentionally returning just one tag to fit in UI, but it can be multiple.
     * @return String
     */
    public function getAutoGeneratedTagAttribute()
    {
        if($this->availableTickets <= 0){
            return "sold out";
        }

        if($this->totalTickets - $this->soldTickets <= 10){
            return "almost full";
        }

        // ticket whose end date is closing at the last.
        $ticket = Ticket::where('event_id', $this->id)->orderBy('end_time', 'desc')->first();
        if($ticket->end_time->diffInDays(now()) <= 3){
            return "sales ending soon";
        }
        return '';
    }
}
