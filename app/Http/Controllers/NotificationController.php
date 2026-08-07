<?php

namespace App\Http\Controllers;

use App\Models\MaintenanceSchedule;
use App\Models\AppNotification;
use App\Models\Kendaraan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class NotificationController extends Controller
{
    /**
     * Ambil peringatan jadwal perawatan & dokumen legalitas armada (STNK, Pajak, KIR).
     * Digabung per armada (kendaraan) agar tidak menumpuk banyak item berulang.
     */
    private function getAlertItems()
    {
        $alerts = collect();
        $kendaraans = Kendaraan::with('maintenanceSchedules')->get();

        foreach ($kendaraans as $k) {
            $issues = [];
            $hasOverdue = false;

            // 1. Check Document Dates (STNK, Pajak, KIR)
            $stnkDate = $k->tanggal_stnk ? Carbon::parse($k->tanggal_stnk) : null;
            $pajakDate = $k->pajak_tahunan ? Carbon::parse($k->pajak_tahunan) : null;
            $kirDate = $k->kir_bengkel ? Carbon::parse($k->kir_bengkel) : null;

            if ($stnkDate) {
                if ($stnkDate->isPast()) {
                    $issues[] = 'Pajak STNK Lewat (' . $stnkDate->diffInDays(now()) . ' hr)';
                    $hasOverdue = true;
                } elseif ($stnkDate->diffInDays(now()) <= 30) {
                    $issues[] = 'Pajak STNK (H-' . $stnkDate->diffInDays(now()) . ')';
                }
            }

            if ($pajakDate && (!$stnkDate || $pajakDate->format('Y-m-d') !== $stnkDate->format('Y-m-d'))) {
                if ($pajakDate->isPast()) {
                    $issues[] = 'Pajak Tahunan Terlambat';
                    $hasOverdue = true;
                } elseif ($pajakDate->diffInDays(now()) <= 30) {
                    $issues[] = 'Pajak Tahunan (H-' . $pajakDate->diffInDays(now()) . ')';
                }
            }

            if ($kirDate) {
                if ($kirDate->isPast()) {
                    $issues[] = 'Uji KIR Expired (' . $kirDate->diffInDays(now()) . ' hr)';
                    $hasOverdue = true;
                } elseif ($kirDate->diffInDays(now()) <= 30) {
                    $issues[] = 'Uji KIR (H-' . $kirDate->diffInDays(now()) . ')';
                }
            }

            // 2. Check Maintenance Schedules
            foreach ($k->maintenanceSchedules as $sch) {
                if ($sch->status() === 'terlambat') {
                    $issues[] = $sch->jenis_perawatan . ' (Terlambat)';
                    $hasOverdue = true;
                } elseif ($sch->status() === 'segera') {
                    $issues[] = $sch->jenis_perawatan . ' (Segera)';
                }
            }

            // Jika armada memiliki isu/tunggakan, buat 1 notifikasi terpadu ringkas per armada
            if (count($issues) > 0) {
                $statusTag = $hasOverdue ? 'Perlu Servis & Perhatian' : 'Mendekati Jatuh Tempo';
                $icon = $hasOverdue ? 'bi-exclamation-triangle-fill text-danger' : 'bi-clock-history text-warning';

                $alerts->push([
                    'source'     => 'dokumen_kendaraan',
                    'id'         => null,
                    'is_overdue' => $hasOverdue,
                    'icon'       => $icon,
                    'title'      => $k->nomor_polisi . ' (' . $k->merek . ') — ' . $statusTag,
                    'message'    => implode(' • ', array_slice($issues, 0, 3)),
                    'link'       => route('kendaraan.index'),
                    'is_read'    => false,
                    'created_at' => now()->toDateTimeString(),
                ]);
            }
        }

        return $alerts->sortByDesc('is_overdue')->values();
    }

    /**
     * Halaman semua notifikasi.
     */
    public function index()
    {
        $alertItems = $this->getAlertItems();

        $appNotifications = AppNotification::where('user_id', Auth::id())
            ->latest()
            ->get()
            ->map(function ($n) {
                return [
                    'source'  => 'app_notification',
                    'id'      => $n->id,
                    'icon'    => $n->iconClass(),
                    'title'   => $n->title,
                    'message' => $n->message,
                    'link'    => $n->link,
                    'is_read' => $n->is_read,
                    'created_at' => $n->created_at ? $n->created_at->toDateTimeString() : now()->toDateTimeString(),
                ];
            });

        $allNotifications = $alertItems->concat($appNotifications)->values();

        return view('notifikasi.index', compact('allNotifications'));
    }

    /**
     * Total badge = alert dokumen & jadwal + notifikasi belum dibaca.
     */
    public function count()
    {
        $alertCount = $this->getAlertItems()->count();
        $unreadCount = AppNotification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->count();

        return response()->json(['count' => $alertCount + $unreadCount]);
    }

    /**
     * Daftar gabungan untuk dropdown lonceng.
     */
    public function list()
    {
        $alertItems = $this->getAlertItems()->take(10);

        $notifItems = AppNotification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->latest()
            ->take(10)
            ->get()
            ->map(function ($n) {
                return [
                    'source'  => 'app_notification',
                    'id'      => $n->id,
                    'icon'    => $n->iconClass(),
                    'title'   => $n->title,
                    'message' => $n->message,
                    'link'    => $n->link,
                ];
            });

        $items = $alertItems->concat($notifItems)->values();

        return response()->json(['items' => $items]);
    }

    /**
     * Tandai satu notifikasi sudah dibaca.
     */
    public function markAsRead(Request $request, $id)
    {
        $notif = AppNotification::where('user_id', Auth::id())->find($id);
        if ($notif) {
            $notif->update(['is_read' => true]);
        }

        return response()->json(['success' => true]);
    }

    /**
     * Tandai semua notifikasi sudah dibaca.
     */
    public function markAllAsRead()
    {
        AppNotification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json(['success' => true]);
    }
}