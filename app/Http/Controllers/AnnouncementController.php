<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\View\View;

class AnnouncementController extends Controller
{
    public function show(Announcement $announcement): View
    {
        abort_unless($announcement->is_active, 404);
        if ($announcement->published_at && $announcement->published_at->isFuture()) {
            abort(404);
        }

        return view('announcements.show', ['announcement' => $announcement]);
    }
}
