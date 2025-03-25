<?php

use Ffhs\Approvals\Approval\SimpleApprovalBy;
use Ffhs\Approvals\Contracts\Approvable;
use Ffhs\Approvals\Models\Approval;
use Illuminate\Support\Facades\Config;

describe('SimpleApprovalBy test', function () {
    beforeEach(function () {
        // Set up a custom table name for testing
        Config::set('filament-package_ffhs_approvals.tables.approvals', 'test_approvals');

        // Create an instance of the Approval model
        $this->approval = new Approval();
    });


    test('user can\'t approve', function () {
        $user = auth()->user();
        expect($user instanceof \App\Models\User)->toBeTrue();

        $approvable = Mockery::mock(Approvable::class);

        $approvalBy = Mockery::mock(SimpleApprovalBy::class);
        $approvalBy->shouldReceive('getName')->andReturn('approvalBy');
        $approvalBy->shouldReceive('isAny')->andReturn(false);
        $approvalBy->shouldNotHaveReceived('canApproveFromPermissions');

        $result = $approvalBy->canApprove($user, $approvable);
        expect($result)->toBeFalse();

        dd($approvalBy->canApprove($user, $approvable));
    });

    it('canApproveFromPermissions', function () {
    });

    it('approved', function () {
    });

    it('reachAtLeast', function () {
    });
})->only();
