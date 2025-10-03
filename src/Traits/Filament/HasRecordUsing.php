<?php

namespace Ffhs\Approvals\Traits\Filament;

use Closure;
use Ffhs\Approvals\Contracts\Approvable;
use Illuminate\Database\Eloquent\Model;

trait HasRecordUsing
{
    private null|Closure|Model $recordUsing = null;

    public function getRecordFromUsing(): null|Model|Approvable
    {
        return once(function (): null|Model|Approvable {
            $recordUsing = $this->evaluate($this->recordUsing, ['record' => parent::getRecord()]);
            if (is_null($recordUsing)) {
                return parent::getRecord();
            }
            return $recordUsing;
        });
    }

    public function recordUsing(Closure|null|Model $record): static
    {
        $this->recordUsing = $record;

        return $this;
    }
}
