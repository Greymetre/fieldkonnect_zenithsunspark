<?php

namespace App\Services;

use App\Models\{Lead, LeadLog, LeadNote, LeadOpportunity, LeadTask, User};
use Illuminate\Support\Facades\DB;

class LeadActivityFeed
{
    public function page(Lead $lead, User $user, int $page = 1)
    {
        $sources = [
            'note' => LeadNote::where('lead_id', $lead->id),
            'task' => LeadTask::visibleTo($user)->where('lead_id', $lead->id),
            'log' => LeadLog::where('lead_id', $lead->id),
            'opportunity' => LeadOpportunity::where('lead_id', $lead->id),
        ];
        $union = null;
        foreach ($sources as $type => $query) {
            $query = (clone $query)->select('id', 'created_at')->selectRaw("'{$type}' as activity_type")->toBase();
            $union = $union ? $union->unionAll($query) : $query;
        }
        $page = DB::query()->fromSub($union, 'activities')
            ->orderByDesc('created_at')->orderBy('activity_type')->orderByDesc('id')
            ->simplePaginate(30, ['*'], 'page', max(1, $page));

        $models = [];
        foreach ($page->getCollection()->groupBy('activity_type') as $type => $rows) {
            $query = clone $sources[$type];
            if (in_array($type, ['note', 'task', 'opportunity'], true)) {
                $query->with('createdby:id,name');
            }
            if ($type === 'opportunity') {
                $query->with('status_is');
            }
            $models[$type] = $query->whereIn('id', $rows->pluck('id'))->get()->keyBy('id');
        }
        $page->setCollection($page->getCollection()->map(function ($row) use ($models) {
            $item = $models[$row->activity_type]->get($row->id);
            $item->type = $row->activity_type;
            return $item;
        }));
        return $page;
    }
}
