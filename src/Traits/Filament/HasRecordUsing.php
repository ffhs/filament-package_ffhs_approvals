<?php

namespace Ffhs\Approvals\Traits\Filament;

use Closure;
use Ffhs\Approvals\Contracts\Approvable;
use Illuminate\Database\Eloquent\Model;

trait HasRecordUsing
{

    private null|Closure|Model $record = null;

    public function getRecordFromUsing(): null|Model|Approvable
    {
        return once(function () {
            $recordUsing = $this->evaluate($this->record, ['record' => parent::getRecord()]);
            if (is_null($recordUsing)) {
                return parent::getRecord();
            }
            return $recordUsing;
        });
    }

    public function recordUsing(Closure|null|Model $record): static
    {
        $this->record = $record;

        return $this;
    }
}
