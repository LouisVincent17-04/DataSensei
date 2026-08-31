<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Services\StudentNotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(Request $request, StudentNotificationService $service): View
    {
        $service->syncDeadlineNotifications($request->user());

        $filter = $request->input('filter') === 'unread' ? 'unread' : 'all';
        $query = Notification::query()
            ->where('user_id', $request->user()->id)
            ->latest('created_at');

        if ($filter === 'unread') {
            $query->where('is_read', false);
        }

        $notifications = $query->paginate(20)->withQueryString();
        $unreadCount = Notification::query()
            ->where('user_id', $request->user()->id)
            ->where('is_read', false)
            ->count();

        return view('student.notifications.index', compact('notifications', 'unreadCount', 'filter'));
    }

    public function feed(Request $request, StudentNotificationService $service): JsonResponse
    {
        $service->syncDeadlineNotifications($request->user());

        $filter = $request->input('filter') === 'unread' ? 'unread' : 'all';
        $query = Notification::query()
            ->where('user_id', $request->user()->id)
            ->latest('created_at');

        if ($filter === 'unread') {
            $query->where('is_read', false);
        }

        $notifications = $query->limit(12)->get();
        $unreadCount = Notification::query()
            ->where('user_id', $request->user()->id)
            ->where('is_read', false)
            ->count();

        return response()->json([
            'unread_count' => $unreadCount,
            'notifications' => $notifications->map(fn (Notification $notification): array => [
                'id' => $notification->id,
                'type' => $notification->type,
                'category' => $notification->category,
                'title' => $notification->display_title,
                'message' => $notification->notification_text,
                'is_read' => (bool) $notification->is_read,
                'time' => $notification->created_at?->diffForHumans() ?? '',
                'open_url' => route('student.notifications.open', $notification),
                'read_url' => route('student.notifications.read', $notification),
                'unread_url' => route('student.notifications.unread', $notification),
                'delete_url' => route('student.notifications.destroy', $notification),
            ])->values(),
        ]);
    }

    public function unreadCount(Request $request): JsonResponse
    {
        return response()->json([
            'unread_count' => Notification::query()
                ->where('user_id', $request->user()->id)
                ->where('is_read', false)
                ->count(),
        ]);
    }

    public function open(Request $request, Notification $notification): RedirectResponse
    {
        $this->ensureOwnership($request, $notification);
        $notification->markRead();

        $target = trim((string) $notification->action_url);
        if ($target === '' || ! Str::startsWith($target, '/') || Str::startsWith($target, '//')) {
            return redirect()->route('student.notifications.index');
        }

        return redirect()->to($target);
    }

    public function markRead(Request $request, Notification $notification): JsonResponse|RedirectResponse
    {
        $this->ensureOwnership($request, $notification);
        $notification->markRead();

        return $request->expectsJson()
            ? response()->json(['ok' => true])
            : back()->with('success', 'Notification marked as read.');
    }

    public function markUnread(Request $request, Notification $notification): JsonResponse|RedirectResponse
    {
        $this->ensureOwnership($request, $notification);
        $notification->markUnread();

        return $request->expectsJson()
            ? response()->json(['ok' => true])
            : back()->with('success', 'Notification marked as unread.');
    }

    public function markAllRead(Request $request): JsonResponse|RedirectResponse
    {
        Notification::query()
            ->where('user_id', $request->user()->id)
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now(), 'updated_at' => now()]);

        return $request->expectsJson()
            ? response()->json(['ok' => true, 'unread_count' => 0])
            : back()->with('success', 'All notifications marked as read.');
    }

    public function destroy(Request $request, Notification $notification): JsonResponse|RedirectResponse
    {
        $this->ensureOwnership($request, $notification);
        $notification->delete();

        return $request->expectsJson()
            ? response()->json(['ok' => true])
            : back()->with('success', 'Notification deleted.');
    }

    public function clearRead(Request $request): JsonResponse|RedirectResponse
    {
        $deleted = Notification::query()
            ->where('user_id', $request->user()->id)
            ->where('is_read', true)
            ->delete();

        return $request->expectsJson()
            ? response()->json(['ok' => true, 'deleted' => $deleted])
            : back()->with('success', $deleted . ' read notification(s) cleared.');
    }

    private function ensureOwnership(Request $request, Notification $notification): void
    {
        abort_unless((int) $notification->user_id === (int) $request->user()->id, 404);
    }
}
