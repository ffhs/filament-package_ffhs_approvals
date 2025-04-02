<?php

use Ffhs\Approvals\Approval\SimpleApprovalBy;
use Ffhs\Approvals\Concerns\HandlesApprovals;
use Ffhs\Approvals\Contracts\Approvable;
use Ffhs\Approvals\Contracts\ApprovalBy;
use Ffhs\Approvals\Models\Approval;
use Illuminate\Database\Eloquent\Model;

describe('HandlesApprovals Trait', tests: function () {
    beforeEach(closure: function () {
        // Create a class that uses the HandlesApprovals trait

        $this->approvable = Mockery::mock(Approvable::class);

        $this->action = new class() {
            use HandlesApprovals;

            public $mockeryRecord;

            public function getRecord(): Approvable
            {
                return $this->mockeryRecord;
            }

            public function evaluate($value)
            {
                return $value;
            }
        };

        $this->action->mockeryRecord = $this->approvable;
    });

    it('canApprove', function () {
        $approvalBy = Mockery::mock(ApprovalBy::class);
        $approvalBy->shouldReceive('canApprove')->andReturn(true);

        $this->action->approvalBy($approvalBy);
        expect($this->action->canApprove())->toBe(true);
    });

    it('getBoundApprovals', function () {
        $approvals = collect([
            Approval::make(['key' => 'key1', 'approval_by' => 'approval_by_1']),
            Approval::make(['key' => 'key1', 'approval_by' => 'approval_by_2']),
            Approval::make(['key' => 'key1', 'approval_by' => 'approval_by_3']),

            Approval::make(['key' => 'key2', 'approval_by' => 'approval_by_1']),
            Approval::make(['key' => 'key2', 'approval_by' => 'approval_by_2']),
            Approval::make(['key' => 'key2', 'approval_by' => 'approval_by_3']),

            Approval::make(['key' => 'key3', 'approval_by' => 'approval_by_1']),
            Approval::make(['key' => 'key3', 'approval_by' => 'approval_by_2']),
            Approval::make(['key' => 'key3', 'approval_by' => 'approval_by_2']),
        ]);

        $approvalBy = SimpleApprovalBy::make('approval_by_2');

        /**@var HandlesApprovals $action */
        $action = $this->action;
        $action->approvalKey('key3');
        $action->approvalBy($approvalBy);

        $approvable = Mockery::mock(Model::class, Approvable::class);
        $approvable
            ->shouldReceive('getAttribute')
            ->with('approvals')
            ->andReturn($approvals);

        $action->mockeryRecord = $approvable;

        $boundApprovals = $action->getBoundApprovals();
        expect($boundApprovals)
            ->toHaveCount(2);
        foreach ($boundApprovals as $boundApproval) {
            expect($boundApprovals->count())->toBe(2)
                ->and($boundApproval->key)->toBe('key3')
                ->and($boundApproval->approval_by)->toBe('approval_by_2');
        }
    });

    afterEach(function () {
        Mockery::close();
    });
})->skip()->todo();
