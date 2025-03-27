<?php

use Ffhs\Approvals\Approval\SimpleApprovalBy;
use Ffhs\Approvals\Contracts\Approvable;
use Ffhs\Approvals\Models\Approval;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Gate;

describe('SimpleApprovalFlow test', function () {
    beforeEach(function () {
        // Set up a custom table name for testing
        Config::set('filament-package_ffhs_approvals.tables.approvals', 'test_approvals');

        // Create an instance of the Approval model
        $this->approval = new Approval();
    });


    describe('canApprove', function () {
        test('user can\'t approve by CanApproveUsing', function () {
            $user = Mockery::mock(\App\Models\User::class);
            $approvable = Mockery::mock(Approvable::class);
            $approvalBy = SimpleApprovalBy::make('approvalBy')
                ->canApproveUsing(fn($approver) => $approver !== $user);

            Gate::shouldReceive('allows')
                ->never()
                ->with('can_approve_by', $approvalBy);

            $result = $approvalBy->canApprove($user, $approvable);
            expect($result)->toBeFalse();
        });
    });
});
