<?php

use App\Approvals\TestApprovalStatuses;
use Ffhs\Approvals\Approval\SimpleApprovalFlow;
use Ffhs\Approvals\Contracts\HasApprovalStatuses;
use Ffhs\Approvals\Models\Approval;
use Illuminate\Support\Facades\Config;

describe('Approval Model', function () {
    beforeEach(function () {
        // Set up a custom table name for testing
        Config::set('filament-package_ffhs_approvals.tables.approvals', 'test_approvals');

        // Create an instance of the Approval model
        $this->approval = new Approval();
    });

    it('has the correct table name from configuration', function () {
        expect($this->approval->getTable())
            ->toBe('test_approvals');
    });


    it('set the status right', function (HasApprovalStatuses $status) {
        /** @var Approval $approval */
        $approval = $this->approval;
        $approval->status = $status;


        try {
            $jsonArray = json_decode(json_encode($approval, JSON_THROW_ON_ERROR), true, 4, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            expect($e)->toBeNull();
        }

        $rawStatus = $jsonArray['status'];

        expect($rawStatus)->toEqual($status->value);
    })
        ->with([
            [TestApprovalStatuses::APPROVED],
            [TestApprovalStatuses::DENIED],
            [TestApprovalStatuses::PENDING],
        ]);


    it('get the status right', function (HasApprovalStatuses $status) {
        $approval = Mockery::mock(Approval::class)
            ->shouldReceive('getApprovalFlow')
            ->andReturn(
                fn() => SimpleApprovalFlow::make()->approvalStatus(TestApprovalStatuses::cases())
            );

        $approval->status = $status;

        expect($approval->status)->toBe($status);
    })
        ->with([
            [TestApprovalStatuses::APPROVED],
            [TestApprovalStatuses::DENIED],
            [TestApprovalStatuses::PENDING],
        ]);
});
